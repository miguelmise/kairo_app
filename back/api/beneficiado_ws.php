<?php

error_reporting(E_ERROR);

require_once("autoload.php");

try {
    $beneficiado = new Beneficiado();
    $requestData = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        if (empty($requestData)) {
            // No se recibieron parámetros
            $beneficiados_list = $beneficiado->listar_beneficiados();
            echo $beneficiados_list;
        }else {
            // Se recibieron parámetros
            $beneficiado_data = $beneficiado->buscar_beneficiado($requestData);
            echo $beneficiado_data;
        }
    }
    
    /*if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $beneficiado_creado = $beneficiado->crear_beneficiado($requestData);

        echo $beneficiado_creado;
    }
    
    if($_SERVER['REQUEST_METHOD'] == 'PUT') {

        $beneficiado_update = $beneficiado->actualizar_beneficiado($requestData);

        echo $beneficiado_update;
    }else{
        http_response_code(404);
        echo json_encode(["Error"=> "Solicitud Incorrecta o No se encontró."]);
    }*/

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}


?>