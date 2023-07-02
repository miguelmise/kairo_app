<?php

error_reporting(E_ERROR);//mostrara solo los errores no los warnings

require_once("autoload.php");//cargador de todas las clases

try {
    $categoria = new Categoria_Persona();
    $requestData = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        if (!empty($_REQUEST['beneficiado_id']) && isset($_REQUEST['beneficiado_id'])) {
            $categorias_list_beneficiado = $categoria->listar_cat_per_beneficiario(array("beneficiado_id"=>$_REQUEST['beneficiado_id']));
            echo $categorias_list_beneficiado;
        }else{
            http_response_code(404);
            echo json_encode(["Error"=> "No se recibio parámetros."]);die();
        }
        
        
    }elseif($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_REQUEST['beneficiado_id']) && isset($_REQUEST['categoria_persona_id']) && isset($_REQUEST['cat_persona_beneficiado_cantidad'])) {
            $categorias_creado = $categoria->crear_categ_persona_beneficiario(array("beneficiado_id"=>$_REQUEST['beneficiado_id'],
                                                                                    "categoria_persona_id"=>$_REQUEST['categoria_persona_id'],
                                                                                    "cat_persona_beneficiado_cantidad"=>$_REQUEST['cat_persona_beneficiado_cantidad']));
            echo $categorias_creado;
        }
        

    }elseif($_SERVER['REQUEST_METHOD'] == 'PUT') {

        
    }elseif($_SERVER['REQUEST_METHOD'] == 'DELETE') {

        
    }else{
        http_response_code(404);
        echo json_encode(["Error"=> "Solicitud Incorrecta o No se encontró."]);
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}

?>