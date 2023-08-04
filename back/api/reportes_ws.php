<?php

require_once("autoload.php");

try {

    $requestData = json_decode(file_get_contents('php://input'), true);
    $inventario = new Inventario();
    $reporte = new Reporte();

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        $respuesta = $reporte->obtenerOrdenes();
            echo $respuesta;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

        if(!isset($requestData['reporte'])){
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros.'));
            exit();
        }

        if($requestData['reporte'] == 'donantes'){
            $respuesta = $reporte->reporte_donadores($requestData);
            echo $respuesta;
        }

        if($requestData['reporte'] == 'ordenes'){
            $respuesta = $reporte->buscar_ordenes($requestData);
            echo $respuesta;
        }

        if($requestData['reporte'] == 'beneficiados'){
            $respuesta = $reporte->reporte_beneficiados($requestData);
            echo $respuesta;
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(empty($requestData)){
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros.'));
            exit();
        }
        $respuesta = $inventario->devolverProducto($requestData);
        echo $respuesta;
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.' . $e);
    echo json_encode($response);
    exit();
}

?>
