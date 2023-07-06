<?php
require_once("autoload.php");

try {

    $categoria_producto = NEW Categoria_Producto();
    
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        
        $categoria_list = $categoria_producto->listar_categorias();
        echo $categoria_list;
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor: '.$e);
    echo json_encode($response);
    exit();
}

?>