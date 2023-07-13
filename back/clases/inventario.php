<?php

class Inventario extends Conectar{

    public function listarInventario(){
        try {
            $conectar = parent::db();
            $query = "SELECT * FROM inventario WHERE inventario_stock > 0";

            $query = $conectar->prepare($query);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            if ($result == null || !$result) {
                $result = array();
            }
            return json_encode($result);
        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function actualizar_producto_inventario($data){
        try {
            // Validación de datos
            if(!isset($data['inventario_id'])){return json_encode(["mensaje" => "Falta inventario_id"]);}
            if(!isset($data['inventario_codigo'])){return json_encode(["mensaje" => "Falta inventario_codigo"]);}
            if(!isset($data['inventario_ubicacion'])){return json_encode(["mensaje" => "Falta inventario_ubicacion"]);}
            if(!isset($data['inventario_descripcion'])){return json_encode(["mensaje" => "Falta inventario_descripcion"]);}
            if(!isset($data['inventario_lote'])){return json_encode(["mensaje" => "Falta inventario_lote"]);}
            if(!isset($data['inventario_stock'])){return json_encode(["mensaje" => "Falta inventario_stock"]);}
            if(!isset($data['inventario_proveedor'])){return json_encode(["mensaje" => "Falta inventario_proveedor"]);}
            if(!isset($data['inventario_precio_promedio'])){return json_encode(["mensaje" => "Falta inventario_precio_promedio"]);}
            if(!isset($data['inventario_costo_total'])){return json_encode(["mensaje" => "Falta inventario_costo_total"]);}

            $conectar = parent::db();
            $conectar->beginTransaction();
            $query = "UPDATE inventario SET inventario_ubicacion = '{$data['inventario_ubicacion']}',
            inventario_descripcion = '{$data['inventario_descripcion']}',
            inventario_lote = '{$data['inventario_lote']}',
            inventario_proveedor = '{$data['inventario_proveedor']}',
            inventario_precio_promedio = {$data['inventario_precio_promedio']},
            inventario_stock = {$data['inventario_stock']},
            inventario_costo_total = {$data['inventario_costo_total']},
            inventario_update = NOW()
            WHERE inventario_id = '{$data['inventario_id']}'";

            $query = $conectar->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {

                $query_producto = "UPDATE producto SET producto_precio = {$data['inventario_precio_promedio']} WHERE producto_codigo = {$data['inventario_codigo']}";
                $query_producto = $conectar->prepare($query_producto);
                $query_producto->execute();

                $conectar->commit();

                $result = ["mensaje" => "Producto actualizado exitosamente."];
                
            } else {
                $result = ["mensaje" => "No hubo cambios en los datos del Producto."];
            }

            return json_encode($result);

        } catch(Exception $e){
            $conectar->rollBack();
            return json_encode(["error" => $e->getMessage()]);
        }
    }

}

?>