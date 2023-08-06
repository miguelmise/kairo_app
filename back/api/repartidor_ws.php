<?php

require_once("autoload.php");

try {
    
    $planificador = new Planificador();
    $requestData = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(empty($requestData)){
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros.'));
            exit();
        }
        $orden_respuesta = $planificador->repartir_producto($requestData);
        echo $orden_respuesta;
    }


} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor: '.$e);
    echo json_encode($response);
    exit();
}

?>