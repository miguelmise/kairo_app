<?php

class Porciones extends Conectar{

    public function listar_por_categoria($data){
        try {
            $conectar = parent::db();
            $query = "SELECT p.porciones_id, porciones_persona_id, porciones_producto_id, porciones_cantidad, c.cat_pro_nombre 
            FROM porciones p
            LEFT JOIN categoria_producto c ON p.porciones_producto_id = c.cat_pro_id
            WHERE p.porciones_persona_id = {$data}";

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

    public function actualizarCantidad($id,$cantidad){
        try {
            // Validación de datos
            if($cantidad<0){return json_encode(["error" => "Cantidad no puede ser Negativa"]);}
            if(!is_numeric($cantidad)){return json_encode(["error" => "Cantidad debe ser un número"]);}

            $conectar = parent::db();
            $query="UPDATE porciones SET porciones_cantidad = {$cantidad} , porciones_update = NOW() WHERE porciones_id = {$id}";
            $query = $conectar->prepare($query);
            $query->execute();
            if ($query->rowCount() > 0) {
                $result = ["mensaje" => "Actualizado exitosamente."];
            } else {
                $result = ["mensaje" => "No hubo cambios."];
            }

            return json_encode($result);

        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function crearNueva($id_persona, $id_producto, $cantidad){
        try {
            // Validación
            if ($cantidad < 0) {
                return json_encode(["error" => "Cantidad no puede ser negativa"]);
            }
            if (!is_numeric($cantidad)) {
                return json_encode(["error" => "Cantidad debe ser un número"]);
            }

            $conectar = parent::db();
            $conectar->beginTransaction();

            // Verificar si el producto ya existe
            $queryVerificar = "SELECT COUNT(*) AS count FROM porciones 
                                WHERE porciones_persona_id = :id_persona AND porciones_producto_id = :id_producto";
            $queryVerificar = $conectar->prepare($queryVerificar);
            $queryVerificar->bindParam(':id_persona', $id_persona);
            $queryVerificar->bindParam(':id_producto', $id_producto);
            $queryVerificar->execute();
            $rowCount = $queryVerificar->fetch(PDO::FETCH_ASSOC)['count'];

            if ($rowCount > 0) {
                $conectar->rollback();
                $result = ["mensaje" => "Ya existe."];
            } else {
                // Insertar el producto
                $queryInsertar = "INSERT INTO porciones (porciones_persona_id, porciones_producto_id, porciones_cantidad, porciones_update) 
                                    VALUES (:id_persona, :id_producto, :cantidad, NOW())";
                $queryInsertar = $conectar->prepare($queryInsertar);
                $queryInsertar->bindParam(':id_persona', $id_persona);
                $queryInsertar->bindParam(':id_producto', $id_producto);
                $queryInsertar->bindParam(':cantidad', $cantidad);
                $queryInsertar->execute();

                if ($queryInsertar->rowCount() > 0) {
                    $conectar->commit();
                    $result = ["mensaje" => "Agregado exitosamente."];
                } else {
                    $conectar->rollback();
                    $result = ["error" => "No se insertó."];
                }
            }

            return json_encode($result);
        } catch (Exception $e) {
            $conectar->rollback();
            return json_encode(["error" => $e->getMessage()]);
        }
    }

}

?>