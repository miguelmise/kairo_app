<?php

require_once("autoload.php");

try {

    $requestData = json_decode(file_get_contents('php://input'), true);
    $inventario = new Inventario();
    $reporte = new Reporte();

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