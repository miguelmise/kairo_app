<?php

error_reporting(E_ERROR);//mostrara solo los errores no los warnings

require_once("autoload.php");//cargador de todas las clases

try {
    $usuario = new Usuario();//Creamos objeto tipo usuario.
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $requestData = json_decode(file_get_contents('php://input'), true);

        $user_nick = $usuario->buscar_user_nick($requestData);
        $user_nick = json_decode($user_nick, true);

        //alguna validacion seria aqui
        $resultado = $user_nick;

        echo json_encode($resultado);
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}

?>