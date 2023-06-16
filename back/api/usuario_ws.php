<?php

error_reporting(E_ERROR);//mostrara solo los errores no los warnings

require_once("autoload.php");//cargador de todas las clases

try {
    $usuario = new Usuario();//Creamos objeto tipo usuario.
    $requestData = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        
        if (empty($requestData)) {
            // No se recibieron parámetros
            $users_list = $usuario->listar_usuarios();
            echo $users_list;
        }else{
            // Si se recibieron parámetros
            $user_nick = $usuario->buscar_user_nick($requestData);
            echo $user_nick;
        }
        
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $users_created = $usuario->crear_usuario($requestData);
        echo $users_created;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

        $users_update = $usuario->actualizar_usuario($requestData);

        echo $users_update;
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}

?>