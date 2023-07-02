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
    }elseif($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (!empty($_REQUEST['beneficiado_id']) && isset($_REQUEST['beneficiado_id'])){
            echo json_encode(["Bien" => "Bien."]);
            die();

        }else{
            http_response_code(404);
            echo json_encode(["Error" => "No se recibieron parámetros."]);
            die();
        }

        /*if (!empty($requestData)) {
            $categorias_creado = $categoria->crear_categ_persona_beneficiario($requestData);
            echo $categorias_creado;
        }else{
            http_response_code(404);
            echo json_encode(["Error" => "No se recibieron parámetros."]);
            die();
        }*/

    }elseif($_SERVER['REQUEST_METHOD'] == 'PUT') {
        echo json_encode(["Prueba" => "put."]);
        die();
    } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        // Código para manejar solicitudes DELETE
    } else {
        http_response_code(404);
        echo json_encode(["Error" => "Solicitud incorrecta o no encontrada."]);
    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor: ' . $e->getMessage());
    echo json_encode($response);
    exit();
}


?>