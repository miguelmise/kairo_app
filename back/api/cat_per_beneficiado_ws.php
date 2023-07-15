<?php

error_reporting(E_ERROR); // Mostrará solo los errores, no las advertencias
require_once("autoload.php"); // Cargador de todas las clases

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('content-type: application/json; charset=utf-8');


try {
    $categoria = new Categoria_Persona();
    $requestData = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (!empty($_REQUEST['beneficiado_id']) && isset($_REQUEST['beneficiado_id'])) {
            $categorias_list_beneficiado = $categoria->listar_cat_per_beneficiario(array("beneficiado_id" => $_REQUEST['beneficiado_id']));
            echo $categorias_list_beneficiado;
        } else {
            http_response_code(404);
            echo json_encode(["Error" => "No se recibieron parámetros."]);
            die();
        }
    }
    
    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (empty($requestData)) {
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros para crear.'));
            exit();
        }

        $categoria_creada = $categoria->crear_categ_persona_beneficiario($requestData);
        echo $categoria_creada;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        
        if (empty($requestData)) {
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros para crear.'));
            exit();
        }

        $categoria_actualizado = $categoria->actualizar_categ_persona_beneficiario($requestData);
        echo $categoria_actualizado;
        
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor: ' . $e->getMessage());
    echo json_encode($response);
    exit();
}


?>