<?php

require_once("autoload.php");

try {

    $producto = new Producto();
    $requestData = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        
        $producto_list = $producto->listar_productos();
        echo $producto_list;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(empty($requestData)){
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros.'));
            exit();
        }
        $producto_nuevo = $producto->crear_producto($requestData);
        echo $producto_nuevo;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        
        if(empty($requestData)){
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros.'));
            exit();
        }
        $producto_update = $producto->actualizar_producto($requestData);
        echo $producto_update;
    }
    

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}

?>