<?php

error_reporting(E_ERROR);//mostrara solo los errores no los warnings

require_once("autoload.php");//cargador de todas las clases

try {
    $beneficiado = new Beneficiado();
    $requestData = json_decode(file_get_contents('php://input'), true);

    $beneficiado_update = $beneficiado->actualizar_beneficiado($requestData);

    echo $beneficiado_update;
    
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}