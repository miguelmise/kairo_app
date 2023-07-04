<?php

require_once("autoload.php");

try {

    $producto = new Producto();
    
    $producto_list = $producto->listar_productos();
    echo $producto_list;

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}

?>