<?php

class Categoria_Persona extends Conectar{

    public function listar_categoria_persona(){
        try {
            $conectar = parent::db();
            $query = "SELECT * FROM categoria_persona";
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

    public function listar_cat_per_beneficiario($data){
        try {
            $conectar = parent::db();

            if (!isset($data['beneficiado_id']) || empty($data['beneficiado_id'])) {
                throw new Exception("El campo beneficiado_id no está presente o está vacío.");
            }

            
            $query="SELECT p.*,b.cat_persona_beneficiado_id,b.beneficiado_id,b.cat_persona_beneficiado_cantidad 
            FROM categoria_persona p
            LEFT JOIN cat_persona_beneficiado b ON b.categoria_persona_id = p.categoria_persona_id AND b.beneficiado_id = {$data['beneficiado_id']}";
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

    public function crear_categ_persona_beneficiario($data){
        try {
            if (!isset($data['beneficiado_id']) || empty($data['beneficiado_id'])
                || empty($data['categoria_persona_id']) || empty($data['cat_persona_beneficiado_cantidad'])) {
                throw new Exception("Faltan campos son obligatorios.");
            }

            $conectar = parent::db();
            $conectar->beginTransaction();
            $timestamp = time();

            // Insertar
            $queryInsertar = "INSERT INTO cat_persona_beneficiado (beneficiado_id, 
            categoria_persona_id, cat_persona_beneficiado_cantidad)
                VALUES ({$data['beneficiado_id']}, {$data['categoria_persona_id']}, {$data['cat_persona_beneficiado_cantidad']})";
            $queryInsertar = $conectar->prepare($queryInsertar);
            $queryInsertar->execute();
    
            if ($queryInsertar->rowCount() > 0) {
                $conectar->commit();
                $result = ["mensaje" => "Categoria Persona insertado exitosamente."];
            } else {
                $conectar->rollback();
                $result = ["mensaje" => "No se insertó el Categoria Persona."];
            }
    
            return json_encode($result);

        } catch(Exception $e){
            //$conectar->rollback();
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function crear_categoria_persona($data){
        try {
            // Validación de datos
            if (empty($data['categoria_persona_nombre']) || empty($data['categoria_persona_descripcion'])) {
                return json_encode(["error" => "Todos los campos son obligatorios", "recibido" => $data]);
            }
    
            $conectar = parent::db();
            $conectar->beginTransaction();
            $timestamp = time();
    
            // Verificar si el donante ya existe
            $categoria_persona_nombre = $data['categoria_persona_nombre'];
            $queryVerificar = "SELECT COUNT(*) FROM categoria_persona WHERE categoria_persona_nombre = :nombre";
            $queryVerificar = $conectar->prepare($queryVerificar);
            $queryVerificar->execute([':nombre' => $categoria_persona_nombre]);
            $categoriaExistente = $queryVerificar->fetchColumn();
    
            if ($categoriaExistente > 0) {
                $conectar->rollback();
                return json_encode(["error" => "Ya existe una categoria de persona con el mismo nombre"]);
            }
    
            // Insertar
            $queryInsertar = "INSERT INTO categoria_persona (categoria_persona_nombre, 
            categoria_persona_descripcion, categoria_persona_update,categoria_persona_estado)
                VALUES (:nombre, :descripcion, NOW(), 1)";
            $queryInsertar = $conectar->prepare($queryInsertar);
            $queryInsertar->execute([
                ':nombre' => $data['categoria_persona_nombre'],
                ':descripcion' => $data['categoria_persona_descripcion']
            ]);
    
            if ($queryInsertar->rowCount() > 0) {
                $conectar->commit();
                $result = ["mensaje" => "Categoria Persona insertado exitosamente."];
            } else {
                $conectar->rollback();
                $result = ["mensaje" => "No se insertó el Categoria Persona."];
            }
    
            return json_encode($result);
    
        } catch(Exception $e){
            $conectar->rollback();
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function actualizar_categ_persona_beneficiario($data){//revisar
        try {
            if (empty($data['cat_persona_beneficiado_id']) || empty($data['cat_persona_beneficiado_cantidad'])) {
                throw new Exception("Faltan campos obligatorios.");
            }
    
            $conectar = parent::db();
            $conectar->beginTransaction();
    
            // Actualizar la categoría de persona beneficiado
            $queryActualizar = "UPDATE cat_persona_beneficiado 
                SET cat_persona_beneficiado_cantidad = {$data['cat_persona_beneficiado_cantidad']}
                WHERE cat_persona_beneficiado_id = {$data['cat_persona_beneficiado_id']}";
            $queryActualizar = $conectar->prepare($queryActualizar);
            $queryActualizar->execute();
    
            if ($queryActualizar->rowCount() > 0) {
                $conectar->commit();
                $result = ["mensaje" => "Categoría Persona actualizada exitosamente."];
            } else {
                $conectar->rollback();
                $result = ["mensaje" => "No se actualizó la Categoría Persona."];
            }
    
            return json_encode($result);
    
        } catch(Exception $e){
            $conectar->rollback();
            return json_encode(["error" => $e->getMessage()]);
        }
    }
    

    public function actualizar_categoria_persona($data){
        try {

            // Validación de datos
            $requiredFields = ['categoria_persona_id', 'categoria_persona_nombre', 'categoria_persona_descripcion', 'categoria_persona_estado'];
            $missingFields = array_diff($requiredFields, array_keys($data));
            if (!empty($missingFields)) {
                return json_encode(["mensaje" => "Todos los campos son obligatorios, faltan:".json_encode($missingFields)]);
            }

            $descripcion = $data['categoria_persona_descripcion'] ?? '';
            $conectar = parent::db();
            $query = "UPDATE categoria_persona 
                        SET categoria_persona_nombre = '{$data['categoria_persona_nombre']}',
                        categoria_persona_descripcion = '{$descripcion}',
                        categoria_persona_update = NOW(),
                        categoria_persona_estado = {$data['categoria_persona_estado']}
                        WHERE categoria_persona_id = {$data['categoria_persona_id']}
                        ";
            $query = $conectar->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                $result = ["mensaje" => "Categoria actualizado exitosamente."];
            } else {
                $result = ["mensaje" => "No hubo cambios en los datos del Categoria."];
            }
            return json_encode($result);
        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }
    }

}

?>