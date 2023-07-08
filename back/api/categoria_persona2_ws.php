<?php

require_once("autoload.php");

try {

    $categoria = new Categoria_Persona();
    $requestData = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        
        $categorias_list = $categoria->listar_categoria_persona();
            echo $categorias_list;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(empty($requestData)){
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros.'));
            exit();
        }
        $categorias_creado = $categoria->crear_categoria_persona($requestData);
        echo $categorias_creado;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        
        if(empty($requestData)){
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros.'));
            exit();
        }
        $categoria_update = $categoria->actualizar_categoria_persona($requestData);
        echo $categoria_update;
    }
    

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}

?>