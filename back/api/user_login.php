<?php

error_reporting(E_ERROR);//mostrara solo los errores no los warnings

require_once("autoload.php");//cargador de todas las clases

try {
    
    $usuario = new Usuario();//Creamos objeto tipo usuario.

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $requestData = json_decode(file_get_contents('php://input'), true);

        if (!isset($requestData['username']) || !isset($requestData['password'])) {
            http_response_code(400); // Bad Request
            $response = array('error' => 'Por favor, ingrese el usuario y la contraseña.');
            echo json_encode($response);
            exit();
        }

        $username = $requestData['username'];
        $password = $requestData['password'];


        $user_data = $usuario->login_usuario($username, $password);
        $user_data = json_decode($user_data, true); // Decodificar la respuesta JSON como array asociativo
        //ejemplo user_data = {"user_nombres":"Administrador","user_nick":"admin","user_rol":1}
        //ejemplo user_data en caso de error  = {"error":"Usuario no encontrado"}

        $resultado = array();

        $rol = $user_data['user_rol'];

        if($rol != null){
            if($user_data['user_estado'] == 1){
                $resultado['username'] = $user_data['user_nick'];
                $resultado['rol'] = $rol;
                $resultado['nombres'] = $user_data['user_nombres'];
                $resultado['exp'] = time() + 3600;
            }else{
                $resultado = array( "error" => "Usuario se encuentra inactivado");
            }
             
        }else{
            //http_response_code(400); // Bad Request
            $resultado = $user_data;
        }

        //echo json_encode($resultado);
        echo json_encode(array("token" => base64_encode(json_encode($resultado))));

        exit();
        
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}




?>