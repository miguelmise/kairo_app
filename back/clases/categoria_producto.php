<?php

class Categoria_Producto extends Conectar{

    public function listar_categorias(){
        try {
            $conectar = parent::db();
            $query = "SELECT * FROM categoria_producto;";
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

    public function crear_categoria_producto($data){
        try {
            
            // Validación de datos
            if (empty($data['cat_pro_nombre']) || empty($data['cat_pro_descripcion'])) {
                return json_encode(["error" => "Todos los campos son obligatorios", "recibido" => $data]);
            }
    
            $conectar = parent::db();
            $conectar->beginTransaction();
            $timestamp = time();
    
            // Verificar si el donante ya existe
            $categoria_producto_nombre = $data['cat_pro_nombre'];
            $queryVerificar = "SELECT COUNT(*) FROM categoria_producto WHERE cat_pro_nombre = :nombre";
            $queryVerificar = $conectar->prepare($queryVerificar);
            $queryVerificar->execute([':nombre' => $categoria_producto_nombre]);
            $categoriaExistente = $queryVerificar->fetchColumn();
    
            if ($categoriaExistente > 0) {
                $conectar->rollback();
                return json_encode(["error" => "Ya existe una categoria de producto con el mismo nombre"]);
            }
    
            // Insertar
            $queryInsertar = "INSERT INTO categoria_producto (cat_pro_nombre,cat_pro_main_categoria, 
            cat_pro_descripcion, cat_pro_update,cat_pro_estado)
                VALUES (:nombre,'1', :descripcion, NOW(), :estado)";
            $queryInsertar = $conectar->prepare($queryInsertar);
            $queryInsertar->execute([
                ':nombre' => $data['cat_pro_nombre'],
                ':descripcion' => $data['cat_pro_descripcion'],
                ':estado' => $data['cat_pro_estado']
            ]);
    
            if ($queryInsertar->rowCount() > 0) {
                $conectar->commit();
                $result = ["mensaje" => "Categoria producto insertado exitosamente."];
            } else {
                $conectar->rollback();
                $result = ["mensaje" => "No se insertó el Categoria producto."];
            }
    
            return json_encode($result);
    
        } catch(Exception $e){
            $conectar->rollback();
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function actualizar_categoria_producto($data){
        try {

            // Validación de datos
            $requiredFields = ['cat_pro_id', 'cat_pro_nombre', 'cat_pro_descripcion', 'cat_pro_estado'];
            $missingFields = array_diff($requiredFields, array_keys($data));
            if (!empty($missingFields)) {
                return json_encode(["mensaje" => "Todos los campos son obligatorios, faltan:".json_encode($missingFields)]);
            }

            $descripcion = $data['cat_pro_descripcion'] ?? '';
            $conectar = parent::db();
            $query = "UPDATE categoria_producto 
            SET cat_pro_nombre = '{$data['cat_pro_nombre']}',
            cat_pro_descripcion = '{$descripcion}',
            cat_pro_update = NOW(),
            cat_pro_estado = {$data['cat_pro_estado']}
            WHERE cat_pro_id = {$data['cat_pro_id']}";
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