<?php

require_once("autoload.php");

try {
    
    $planificador = new Planificador();
    $requestData = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        
        //if (!empty($requestData)) {
            $lista_productos = $planificador->listar_productos_incompletos();
            echo $lista_productos;
        //}
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor: '.$e);
    echo json_encode($response);
    exit();
}

?>