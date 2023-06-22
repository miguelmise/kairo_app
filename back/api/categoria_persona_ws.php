<?php

error_reporting(E_ERROR);//mostrara solo los errores no los warnings

require_once("autoload.php");//cargador de todas las clases

try {
    $categoria = new Categoria_Persona();
    $requestData = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        if (empty($requestData)) {
            $categorias_list = $categoria->listar_categoria_persona();
            echo $categorias_list;
        }else{
            $categorias_list_beneficiado = $categoria->listar_cat_per_beneficiario($requestData);
            echo $categorias_list_beneficiado;
        }
        
        
    }elseif($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($requestData['beneficiado_id']) && isset($requestData['categoria_persona_id'])) {
            $categorias_creado = $categoria->crear_categ_persona_beneficiario($requestData);
            echo $categorias_creado;
        }elseif(isset($requestData['beneficiado_id'])){
            $categorias_creado = $categoria->crear_categoria_persona($requestData);
            echo $categorias_creado;
        }
        

    }elseif($_SERVER['REQUEST_METHOD'] == 'PUT') {

        $categoria_update = $categoria->actualizar_categoria_persona($requestData);

        echo $categoria_update;
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