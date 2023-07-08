<?php
require_once("autoload.php");

try {

    $categoria_producto = NEW Categoria_Producto();
    $requestData = json_decode(file_get_contents('php://input'), true);
    
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        
        $categoria_list = $categoria_producto->listar_categorias();
        echo $categoria_list;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(empty($requestData)){
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros.'));
            exit();
        }
        $categorias_creado = $categoria_producto->crear_categoria_producto($requestData);
        echo $categorias_creado;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        
        if(empty($requestData)){
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros.'));
            exit();
        }
        $categoria_update = $categoria_producto->actualizar_categoria_producto($requestData);
        echo $categoria_update;
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor: '.$e);
    echo json_encode($response);
    exit();
}

?>