<?php

class Beneficiado extends Conectar{

    public function listar_beneficiados(){
        try {
            $conectar = parent::db();
            $query = "SELECT * FROM beneficiado";
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

    public function buscar_beneficiado($data){
        try {
            $conectar = parent::db();
            $query = "SELECT * FROM beneficiado WHERE beneficiado_nombre = '{$data['beneficiado_nombre']}'";
            $query = $conectar->prepare($query);
            $query->execute();
            if ($query->rowCount() > 0) {
                $result = $query->fetch(PDO::FETCH_ASSOC);
            } else{
                $result = ["beneficiado_id" => null];
            }
            return json_encode($result);
        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function crear_beneficiado($data){
        try {
            // Validación de datos
            if (empty($data['beneficiado_nombre']) || empty($data['beneficiado_actividad'])
                || empty($data['beneficiado_periodo']) || empty($data['beneficiado_dia_entrega'])) {
                return json_encode(["error" => "Todos los campos son obligatorios", "recibido" => $data]);
            }
    
            $conectar = parent::db();
            $conectar->beginTransaction();
            $timestamp = time();
    
            // Verificar si el donante ya existe
            $beneficiado_nombre = $data['beneficiado_nombre'];
            $queryVerificar = "SELECT COUNT(*) FROM beneficiado WHERE beneficiado_nombre = :nombre";
            $queryVerificar = $conectar->prepare($queryVerificar);
            $queryVerificar->execute([':nombre' => $beneficiado_nombre]);
            $beneficiadoExistente = $queryVerificar->fetchColumn();
    
            if ($beneficiadoExistente > 0) {
                $conectar->rollback();
                return json_encode(["error" => "Ya existe un beneficiado con el mismo nombre"]);
            }
    
            // Insertar el donante
            $queryInsertar = "INSERT INTO beneficiado (beneficiado_nombre, beneficiado_actividad, beneficiado_periodo, 
            beneficiado_dia_entrega, beneficiado_update,beneficiado_ultima_entrega,beneficiado_telefono,beneficiado_representante,beneficiado_estado)
                VALUES (:nombre, :actividad, :periodo, :dia_entrega, NOW(), :ultima, :telefono, :representante, 1)";
            $queryInsertar = $conectar->prepare($queryInsertar);
            $queryInsertar->execute([
                ':nombre' => $data['beneficiado_nombre'],
                ':actividad' => $data['beneficiado_actividad'],
                ':periodo' => $data['beneficiado_periodo'],
                ':dia_entrega' => $data['beneficiado_dia_entrega'],
                ':ultima' => $data['beneficiado_ultima_entrega'],
                ':telefono' => $data['beneficiado_telefono'],
                ':representante' => $data['beneficiado_representante']
            ]);
    
            if ($queryInsertar->rowCount() > 0) {
                $conectar->commit();
                $result = ["mensaje" => "Beneficiado insertado exitosamente."];
            } else {
                $conectar->rollback();
                $result = ["mensaje" => "No se insertó el Beneficiado."];
            }
    
            return json_encode($result);
    
        } catch(Exception $e){
            $conectar->rollback();
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function actualizar_beneficiado($data){
        try {

            // Validación de datos
            if (empty($data['beneficiado_nombre']) || empty($data['beneficiado_actividad']) || empty($data['beneficiado_id'])
            || empty($data['beneficiado_periodo']) || empty($data['beneficiado_dia_entrega']) 
            || empty($data['beneficiado_estado'])) {
                return json_encode(["mensaje" => "Todos los campos son obligatorios", "recibido" => $data]);
            }
            $telefono = $data['beneficiado_telefono'] ?? '';
            $representante = $data['beneficiado_representante'] ?? '';
            $conectar = parent::db();
            $query = "UPDATE beneficiado 
                        SET beneficiado_nombre = '{$data['beneficiado_nombre']}',
                        beneficiado_actividad = '{$data['beneficiado_actividad']}',
                        beneficiado_periodo = '{$data['beneficiado_periodo']}',
                        beneficiado_dia_entrega = '{$data['beneficiado_dia_entrega']}',
                        beneficiado_ultima_entrega = '{$data['beneficiado_ultima_entrega']}',
                        beneficiado_telefono = '{$telefono}',
                        beneficiado_representante = '{$representante}',
                        beneficiado_update = NOW(),
                        beneficiado_estado = {$data['beneficiado_estado']}
                        WHERE beneficiado_id = {$data['beneficiado_id']}
                        ";
            $query = $conectar->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                $result = ["mensaje" => "Beneficiado actualizado exitosamente."];
            } else {
                $result = ["mensaje" => "No hubo cambios en los datos del Beneficiado."];
            }
            return json_encode($result);
        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }
    }

}

?>