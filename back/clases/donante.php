<?php

class Donante extends Conectar{

    public function listar_donantes(){
        try {
            $conectar = parent::db();
            $query = "SELECT * FROM donante WHERE donante_estado = 1";
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