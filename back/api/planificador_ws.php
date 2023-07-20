<?php

require_once("autoload.php");

try {
    
    $planificador = new Planificador();
    $requestData = json_decode(file_get_contents('php://input'), true);
    $parametro = $_REQUEST['parametro'];

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        if(!isset($_REQUEST['parametro'])){
            http_response_code(404);
            echo json_encode(["Error" => "No se recibieron parámetros."]);
            die();
        }
        
        if ($parametro === 'productos') {
            $lista = $planificador->listar_productos_incompletos();
            echo $lista;
        }

        if ($parametro === 'beneficiados') {
            $lista = $planificador->listar_beneficiados();
            echo $lista;
        }

        if ($parametro === 'existencias') {
            $lista = $planificador->listarExistencias();
            echo $lista;
        }
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor: '.$e);
    echo json_encode($response);
    exit();
}

?>