<?php

class Conectar {
    protected $conexion;
    
    protected function db() {
        $password = "root";
        $usuario = "root";
        $nombreBaseDeDatos = "kairo";
        $rutaServidor = "localhost";
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
