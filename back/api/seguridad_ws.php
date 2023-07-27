<?php

require_once("autoload.php");//cargador de todas las clases

try {
    $usuario = new Usuario();//Creamos objeto tipo usuario.
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        //[1] Administrador [2] Planificador [3] Inventario [4] Reportador [5] Invitado
        $accesos[1] = array('Inicio',"Usuarios","Proveedores",'Beneficiados','Productos','Inventario','Reglas','Planificador','Categoria','Ordenes');
        $accesos[2] = array('Inicio',"Proveedores",'Beneficiados','Productos','Inventario','Reglas','Planificador','Categoria','Ordenes');
        $accesos[3] = array('Inicio',"Proveedores",'Beneficiados','Productos','Inventario','Ordenes');
        $accesos[4] = array('Inicio','Ordenes');
        $accesos[5] = array('Inicio');

        //$requestData = json_decode(file_get_contents('php://input'), true);

        
        
        if(isset($_REQUEST['token'])){
            $requestData = $_REQUEST['token'];
        }else{
            echo json_encode(array('error' => 'Sin Parametros.'));
            die();
        }

        //$decoded_token = base64_decode($requestData['token']);
        $data_token = base64_decode($requestData);

        $decodedToken = json_decode($data_token, true);

        

        if (isset($decodedToken['exp']) && $decodedToken['exp'] >= time()) {
            $resultado['mensaje'] = '¡Bienvenido!.';
            $resultado['acceso'] = 1;
            $resultado['paginas'] = $accesos[$decodedToken['rol']];
            // Aumentar el tiempo de expiración en 3600 segundos
            $decodedToken['exp'] += 3600;
            $resultado['token'] = base64_encode(json_encode($decodedToken));
        }else{
            $resultado['mensaje'] = 'Sesión inválida o expirada.';
            $resultado['acceso'] = 0;
        }

        //print_r($decodedToken);
        echo json_encode($resultado);

    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}

?>