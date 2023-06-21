<?php

class Donante extends Conectar{

    public function listar_donantes(){
        try {
            $conectar = parent::db();
            $query = "SELECT * FROM donante";
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

    public function buscar_donante($data){
        try {
            $conectar = parent::db();
            $query = "SELECT * FROM donante WHERE donante_nombre = '{$data['donante_nombre']}'";
            $query = $conectar->prepare($query);
            $query->execute();
            if ($query->rowCount() > 0) {
                $result = $query->fetch(PDO::FETCH_ASSOC);
            } else{
                $result = ["donante_id" => null];
            }
            return json_encode($result);
        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function crear_donante($data){
        try {
            // Validación de datos
            if (empty($data['donante_nombre']) || empty($data['donante_tipo']) 
                || empty($data['donante_descripcion'])) {
                return json_encode(["error" => "Todos los campos son obligatorios", "recibido" => $data]);
            }
    
            $conectar = parent::db();
            $conectar->beginTransaction();
            $timestamp = time();
    
            // Verificar si el donante ya existe
            $donanteNombre = $data['donante_nombre'];
            $queryVerificar = "SELECT COUNT(*) FROM donante WHERE donante_nombre = :nombre";
            $queryVerificar = $conectar->prepare($queryVerificar);
            $queryVerificar->execute([':nombre' => $donanteNombre]);
            $donanteExistente = $queryVerificar->fetchColumn();
    
            if ($donanteExistente > 0) {
                $conectar->rollback();
                return json_encode(["error" => "Ya existe un donante con el mismo nombre"]);
            }
    
            // Insertar el donante
            $queryInsertar = "INSERT INTO donante (donante_nombre, donante_tipo, donante_descripcion, donante_fecha_update, donante_estado)
                VALUES (:nombre, :tipo, :descripcion, NOW(), 1)";
            $queryInsertar = $conectar->prepare($queryInsertar);
            $queryInsertar->execute([
                ':nombre' => $data['donante_nombre'],
                ':tipo' => $data['donante_tipo'],
                ':descripcion' => $data['donante_descripcion']
            ]);
    
            if ($queryInsertar->rowCount() > 0) {
                $conectar->commit();
                $result = ["mensaje" => "Donante insertado exitosamente."];
            } else {
                $conectar->rollback();
                $result = ["mensaje" => "No se insertó el Donante."];
            }
    
            return json_encode($result);
    
        } catch(Exception $e){
            $conectar->rollback();
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function actualizar_donante($data){
        try {

            // Validación de datos
            if (empty($data['donante_nombre']) || empty($data['donante_tipo']) || empty($data['donante_id'])
                || empty($data['donante_estado'])) {
                return json_encode(["mensaje" => "Todos los campos son obligatorios", "recibido" => $data]);
            }

            $conectar = parent::db();
            $query = "UPDATE donante 
                        SET donante_nombre = '{$data['donante_nombre']}',
                        donante_tipo = '{$data['donante_tipo']}',
                        donante_descripcion = '{$data['donante_descripcion']}',
                        donante_fecha_update = NOW(),
                        donante_estado = {$data['donante_estado']}
                        WHERE donante_id = {$data['donante_id']}
                        ";
            $query = $conectar->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                $result = ["mensaje" => "Donante actualizado exitosamente."];
            } else {
                $result = ["mensaje" => "No hubo cambios en los datos del Donante."];
            }
            return json_encode($result);
        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }
    }
    

}

?>