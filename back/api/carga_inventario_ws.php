<?php
require_once("autoload.php");

try {
    $requestData = json_decode(file_get_contents('php://input'), true);
    $inventario = new Inventario();

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        
        
            $inventario_list = $inventario->listarInventario();
            echo $inventario_list;
        
    }

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        
        if(empty($requestData)){
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibio parámetros.'));
            exit();
        }
        $inventario_update = $inventario->actualizar_producto_inventario($requestData);
        echo $inventario_update;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (empty($requestData)) {
            http_response_code(402);
            echo json_encode(array('error' => 'No se recibieron parámetros.'));
            exit();
        }

        $headers = $requestData[1]; // Obtener los encabezados
        array_shift($requestData); // Eliminar el primer elemento del array

        $dataExcel = array();
        foreach ($requestData as $item) {
            $newItem = array();
            foreach ($headers as $index => $header) {
                $newItem[$header] = $item[$index];
            }

            $dataExcel[] = $newItem;
        }

        array_shift($dataExcel); // Eliminar el primer elemento del array

        $carga_inventario = $inventario->cargar_inventario($dataExcel);

        echo $carga_inventario;
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.' . $e);
    echo json_encode($response);
    exit();
}
?>
