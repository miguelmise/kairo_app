<?php

error_reporting(E_ERROR);//mostrara solo los errores no los warning
require_once("autoload.php");//cargador de todas las clases

try {
    $donante = new Donante();
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $requestData = json_decode(file_get_contents('php://input'), true);

        $donantes_list = $donante->listar_donantes();
        
        echo $donantes_list;

    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}

?>