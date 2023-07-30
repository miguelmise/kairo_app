<?php


require_once("autoload.php");//cargador de todas las clases

try {
    
    $usuario = new Usuario();//Creamos objeto tipo usuario.

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $requestData = json_decode(file_get_contents('php://input'), true);

        if (!isset($requestData['username']) || !isset($requestData['password'])) {
            http_response_code(400); // Bad Request
            $response = array('error' => 'Por favor, ingrese el usuario y la contraseña.');
            echo json_encode($response);
            exit();
        }

        $username = $requestData['username'];
        $password = $requestData['password'];


        $user_data = $usuario->login_usuario($username, $password);
        $user_data = json_decode($user_data, true); 

        $resultado = array();

        $rol = $user_data['user_rol'];

        if($rol != null){
            if($user_data['user_estado'] == 1){
                $resultado['username'] = $user_data['user_nick'];
                $resultado['rol'] = $rol;
                $resultado['nombres'] = $user_data['user_nombres'];
                $resultado['exp'] = time() + 6600;
            }else{
                $resultado = array( "error" => "Usuario se encuentra inactivado");
            }
             
        }else{
            $resultado = $user_data;
        }

        echo json_encode(array("token" => base64_encode(json_encode($resultado))));

        exit();
        
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}




?>