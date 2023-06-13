<?php

class Conectar {
    protected $conexion;
    
    protected function db() {
        $password = "admin";
        $usuario = "admin";
        $nombreBaseDeDatos = "kairo";
        $rutaServidor = "192.168.227.123";
        $puerto = "3306";

        try {
            $conectar = $this->conexion = new PDO("mysql:host=$rutaServidor;port=$puerto;dbname=$nombreBaseDeDatos", $usuario, $password);
            return $conectar;
        } catch(PDOException $e) {
            throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    public function cerrarConexion() {
        $this->conexion = null;
    }
}

?>
