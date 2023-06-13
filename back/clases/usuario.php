<?php

class Usuario extends Conectar{

    public function login_usuario($usuario,$clave){
        try{
            $pass = md5($clave);
            $conectar = parent::db();
            $query="SELECT user_nombres, user_nick,user_rol,user_estado FROM usuario 
            WHERE user_nick = '$usuario' AND user_clave = '$pass'";

            $query = $conectar->prepare($query);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result == null || !$result) {
                // El usuario no existe en la base de datos
                $result = array( "error" => "Error en Usuario o Clave");
            }
            return json_encode($result);
        }catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function listar_usuarios(){

        try {
            $conectar = parent::db();
            $query = "SELECT user_id,user_nick,user_nombres,user_correo,user_rol,user_estado FROM usuario";
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

    public function buscar_user_nick($data){

        try {
            $conectar = parent::db();
            $query="SELECT user_nombres, user_nick FROM usuario 
            WHERE user_nick = '{$data['user_nick']}'";
            $query = $conectar->prepare($query);
            $query->execute();
            if ($query->rowCount() > 0) {
                $result = $query->fetch(PDO::FETCH_ASSOC);
                $result['existe'] = 1;
            } else {
                $result = ["existe" => 0];
            }

            return json_encode($result);

        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function actualizar_usuario($data){

        try {
            $conectar = parent::db();

            $query_clave = "";
            if($data['user_clave'] != ""){
                $clave = md5($data['user_clave']);
                $query_clave = ",user_clave = '{$clave}' ";
            }
            
            $query = "UPDATE usuario 
                    SET user_nombres = '{$data['user_nombres']}', 
                    user_correo = '{$data['user_correo']}', 
                    user_rol = {$data['user_rol']}, 
                    user_estado = {$data['user_estado']} {$query_clave} 
                    WHERE user_id = {$data['user_id']}";
            $query = $conectar->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                $result = ["mensaje" => "Usuario actualizado exitosamente."];
            } else {
                $result = ["mensaje" => "No hubo cambios en los datos del usuario."];
            }

            return json_encode($result);
        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }

    }

    public function crear_usuario($data){
        try {

            // Validación de datos
            if (empty($data['user_nick']) || empty($data['user_nombres']) || empty($data['user_correo']) || empty($data['user_rol']) || empty($data['user_clave'])) {
                return json_encode(["error" => "Todos los campos son obligatorios",
                                    "recibido" => $data]);
            }

            $conectar = parent::db();
            $conectar->beginTransaction();
            $clave = md5($data['user_clave']);
            $timestamp = time();

            $query = "INSERT INTO usuario (user_nick,user_nombres,user_correo,user_rol,user_estado,user_clave,user_token)
            VALUES('{$data['user_nick']}',
                    '{$data['user_nombres']}',
                    '{$data['user_correo']}',
                    {$data['user_rol']},
                    1,
                    '{$clave}',
                    '{$timestamp}')";

            $query = $conectar->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                $conectar->commit();
                $result = ["mensaje" => "Usuario insertado exitosamente."];
            } else {
                $conectar->rollback();
                $result = ["mensaje" => "No se inserto el Usuario."];
            }

            return json_encode($result);
        } catch(Exception $e){
            $conectar->rollback();
            return json_encode(["error" => $e->getMessage()]);
        }
    }
}

?>