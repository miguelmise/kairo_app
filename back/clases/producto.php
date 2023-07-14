<?php

class Producto extends Conectar{

    public function listar_productos(){
        try {
            $conectar = parent::db();
            $query = "SELECT * FROM producto p
            LEFT JOIN (SELECT inventario_codigo , SUM(inventario_stock) as stock FROM inventario GROUP BY inventario_codigo) i ON p.producto_codigo = i.inventario_codigo
            LEFT JOIN categoria_producto c ON p.producto_categoria_id = c.cat_pro_id";
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

    public function actualizar_producto($data){
        try {
            // Validación de datos
            if(!isset($data['producto_id'])){return json_encode(["mensaje" => "Falta producto_id"]);}
            if(!isset($data['producto_codigo'])){return json_encode(["mensaje" => "Falta producto_codigo"]);}
            if(!isset($data['producto_estado'])){return json_encode(["mensaje" => "Falta producto_estado"]);}
            if(!isset($data['producto_categoria_id']) || empty($data['producto_categoria_id'])){return json_encode(["mensaje" => "Falta producto_categoria_id"]);}
            if(!isset($data['producto_descripcion'])){return json_encode(["mensaje" => "Falta producto_descripcion"]);}
            if(!isset($data['producto_peso'])){return json_encode(["mensaje" => "Falta producto_peso"]);}
            if(!isset($data['producto_precio'])){return json_encode(["mensaje" => "Falta producto_precio"]);}
            if(!isset($data['producto_sku'])){return json_encode(["mensaje" => "Falta producto_sku"]);}
            if(!isset($data['producto_medida'])){return json_encode(["mensaje" => "Falta producto_medida"]);}

            $conectar = parent::db();
            $conectar->beginTransaction();
            $query = "UPDATE producto 
                        SET producto_codigo = '{$data['producto_codigo']}',
                        producto_estado = '{$data['producto_estado']}',
                        producto_medida = '{$data['producto_medida']}',
                        producto_sku = '{$data['producto_sku']}',
                        producto_precio = {$data['producto_precio']},
                        producto_categoria_id = {$data['producto_categoria_id']},
                        producto_peso = {$data['producto_peso']},
                        producto_update = NOW(),
                        producto_descripcion = '{$data['producto_descripcion']}'
                        WHERE producto_id = {$data['producto_id']}
                        ";
            $query = $conectar->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {

                $query_inventario = "UPDATE inventario 
                SET inventario_precio_promedio = {$data['producto_precio']},
                inventario_costo_total =  {$data['producto_precio']} * inventario_stock 
                WHERE inventario_codigo = {$data['producto_codigo']}";
                $query_inventario = $conectar->prepare($query_inventario);
                $query_inventario->execute();

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

    public function crear_producto($data){
        try {
            //Validacion
            if(!isset($data['producto_codigo'])){return json_encode(["mensaje" => "Falta producto_codigo"]);}
            if(!isset($data['producto_categoria_id']) || empty($data['producto_categoria_id'])){return json_encode(["mensaje" => "Falta producto_categoria_id"]);}
            if(!isset($data['producto_descripcion'])){return json_encode(["mensaje" => "Falta producto_descripcion"]);}
            if(!isset($data['producto_peso'])){return json_encode(["mensaje" => "Falta producto_peso"]);}
            if(!isset($data['producto_precio'])){return json_encode(["mensaje" => "Falta producto_precio"]);}
            if(!isset($data['producto_sku'])){return json_encode(["mensaje" => "Falta producto_sku"]);}
            if(!isset($data['producto_medida'])){return json_encode(["mensaje" => "Falta producto_medida"]);}

            $conectar = parent::db();
            $conectar->beginTransaction();

            // Verificar si el producto ya existe
            $queryVerificar = "SELECT COUNT(*) AS count FROM producto WHERE producto_codigo = :producto_codigo OR producto_descripcion = :producto_descripcion OR producto_sku = :producto_sku";
            $queryVerificar = $conectar->prepare($queryVerificar);
            $queryVerificar->execute([
                ':producto_codigo' => $data['producto_codigo'],
                ':producto_descripcion' => $data['producto_descripcion'],
                ':producto_sku' => $data['producto_sku']
            ]);
            $rowCount = $queryVerificar->fetch(PDO::FETCH_ASSOC)['count'];

            if ($rowCount > 0) {
                $conectar->rollback();
                $result = ["mensaje" => "Ya existe un producto con el mismo código, descripción o SKU."];
            } else {
                // Insertar el producto
                $queryInsertar = "INSERT INTO producto (producto_categoria_id, producto_codigo, producto_descripcion, producto_medida, producto_peso, producto_precio, producto_rango_peso, producto_sku, producto_update, producto_estado) 
                                    VALUES (:producto_categoria_id, :producto_codigo, :producto_descripcion, :producto_medida, :producto_peso, :producto_precio, :producto_rango_peso, :producto_sku, NOW(), 1)";
                $queryInsertar = $conectar->prepare($queryInsertar);
                $queryInsertar->execute([
                    ':producto_categoria_id' => $data['producto_categoria_id'],
                    ':producto_codigo' => $data['producto_codigo'],
                    ':producto_descripcion' => $data['producto_descripcion'],
                    ':producto_medida' => $data['producto_medida'],
                    ':producto_peso' => $data['producto_peso'],
                    ':producto_precio' => $data['producto_precio'],
                    ':producto_sku' => $data['producto_sku']
                ]);

                if ($queryInsertar->rowCount() > 0) {
                    $conectar->commit();
                    $result = ["mensaje" => "Producto insertado exitosamente."];
                } else {
                    $conectar->rollback();
                    $result = ["mensaje" => "No se insertó el producto."];
                }
            }

            return json_encode($result);


        } catch(Exception $e){
            $conectar->rollback();
            return json_encode(["error" => $e->getMessage()]);
        }
    }

}

?>