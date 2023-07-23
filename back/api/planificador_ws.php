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

        if ($parametro === 'ordenes') {
            $lista = $planificador->listar_ordenes();
            echo $lista;
        }

        if (is_numeric($parametro)) {
            $lista = $planificador->mostrar_orden($parametro);
            echo $lista;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

        if(isset($_REQUEST['parametro'])){
            $respuesta = $planificador->aceptar_orden($_REQUEST['parametro']);
            echo $respuesta;
        }

        if(!isset($_REQUEST['parametro'])){
            $respuesta = $planificador->rechazar_orden();
            echo $respuesta;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(empty($requestData)){
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros.'));
            exit();
        }
        $orden_respuesta = $planificador->generarOrden($requestData);
        echo $orden_respuesta;
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor: '.$e);
    echo json_encode($response);
    exit();
}

?>