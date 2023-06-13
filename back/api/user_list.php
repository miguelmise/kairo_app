<?php

error_reporting(E_ERROR);//mostrara solo los errores no los warnings

require_once("autoload.php");//cargador de todas las clases

try {
    $usuario = new Usuario();//Creamos objeto tipo usuario.
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $requestData = json_decode(file_get_contents('php://input'), true);

        $users_list = $usuario->listar_usuarios();
        $user_list = json_decode($users_list, true);

        //alguna validacion seria aqui
        $resultado = $user_list;

        echo json_encode($resultado);
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}

?>