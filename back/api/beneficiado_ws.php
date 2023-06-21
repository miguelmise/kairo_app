<?php

error_reporting(E_ERROR);//mostrara solo los errores no los warnings

require_once("autoload.php");//cargador de todas las clases

try {
    $beneficiado = new Beneficiado();
    $requestData = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        if (empty($requestData)) {
            // No se recibieron parámetros, llamar a listar_donantes
            $beneficiados_list = $beneficiado->listar_beneficiados();
            echo $beneficiados_list;
        }else {
            // Se recibieron parámetros, llamar a buscar_donante
            $beneficiado_data = $beneficiado->buscar_beneficiado($requestData);
            echo $beneficiado_data;
        }
    }elseif($_SERVER['REQUEST_METHOD'] == 'POST') {

        $beneficiado_creado = $beneficiado->crear_beneficiado($requestData);

        echo $beneficiado_creado;
    }elseif($_SERVER['REQUEST_METHOD'] == 'PUT') {

        $beneficiado_update = $beneficiado->actualizar_beneficiado($requestData);

        echo $beneficiado_update;
    }else{
        http_response_code(404);
        echo json_encode(["Error"=> "Solicitud Incorrecta o No se encontró."]);
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}


?>