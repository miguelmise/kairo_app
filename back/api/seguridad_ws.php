<?php

require_once("autoload.php");//cargador de todas las clases

try {
    $usuario = new Usuario();//Creamos objeto tipo usuario.
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $requestData = json_decode(file_get_contents('php://input'), true);

        $decoded_token = base64_decode($requestData['token']);

        if (verifyToken($decoded_token)) {
            $resultado['mensaje'] = '¡Bienvenido!.';
            $resultado['acceso'] = 1;
            $resultado['paginas'] = null;
            // Aumentar el tiempo de expiración en 3600 segundos
            $decodedToken = json_decode($decoded_token, true);
            $decodedToken['exp'] += 3600;
            // Codificar nuevamente el token modificado
            $resultado['token'] = base64_encode(json_encode($decodedToken));
        } else {
            $resultado['mensaje'] = 'Sesión inválida o expirada.';
            $resultado['acceso'] = 0;
        }
        
        echo json_encode($resultado);
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response = array('error' => 'Se produjo un error en el servidor.');
    echo json_encode($response);
    exit();
}

function verifyToken($token) {
    // Verificar si el token es válido (esto depende de tu implementación)
    $decodedToken = json_decode($token, true);

    if ($decodedToken && isset($decodedToken['exp']) && $decodedToken['exp'] >= time()) {
        return true;
    }

    return false;
}


?>
