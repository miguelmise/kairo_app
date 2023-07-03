<?php

error_reporting(E_ERROR);//mostrara solo los errores no los warnings

require_once("autoload.php");//cargador de todas las clases

try {
    $categoria = new Categoria_Persona();
    $requestData = json_decode(file_get_contents('php://input'), true);

    $categoria_actualizado = $categoria->actualizar_categ_persona_beneficiario($requestData);

    echo $categoria_actualizado;
    
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}