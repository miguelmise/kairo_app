<?php

class Producto extends Conectar{

    public function listar_productos(){
        try {
            $conectar = parent::db();
            $query = "SELECT * FROM producto;";
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