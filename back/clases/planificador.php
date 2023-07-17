<?php

class Planificador extends Conectar{

    public function listar_productos_incompletos(){

        try {
            $conectar = parent::db();
            $query = "SELECT producto_id, producto_codigo, producto_peso, producto_sku, producto_categoria_id FROM producto 
                WHERE producto_categoria_id = 26 OR producto_peso = 0 AND producto_estado = 1";

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

}

?>