<?php

require_once("autoload.php");//cargador de todas las clases

try {
    $donante = new Donante();
    $requestData = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        if (empty($requestData)) {
            // No se recibieron parámetros, llamar a listar_donantes
            $donantes_list = $donante->listar_donantes();
            echo $donantes_list;
        }else {
            // Se recibieron parámetros, llamar a buscar_donante
            $donante_data = $donante->buscar_donante($requestData);
            echo $donante_data;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $donante_creado = $donante->crear_donante($requestData);

        echo $donante_creado;
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

        $donante_update = $donante->actualizar_donante($requestData);

        echo $donante_update;
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}

?>