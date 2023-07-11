<?php

require_once("autoload.php");

try {
    //code...
    $porcion = new Porciones();
    //$requestData = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        
        if (!empty($_REQUEST['id']) || isset($_REQUEST['id'])) {
            $porcion_list = $porcion->listar_por_categoria($_REQUEST['id']);
            echo $porcion_list;
        } else {
            http_response_code(404);
            echo json_encode(["Error" => "No se recibieron parámetros."]);
            die();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        
        if (!isset($_REQUEST['id']) || !isset($_REQUEST['cantidad'])) {
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros.', 'data' => $_REQUEST['cantidad']));
            exit();
        }
        $porcion_update = $porcion->actualizarCantidad($_REQUEST['id'],$_REQUEST['cantidad']);
        echo $porcion_update;
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.'.$e);
    echo json_encode($response);
    exit();
}

?>