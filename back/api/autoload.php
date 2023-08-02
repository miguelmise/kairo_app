<?php

// Habilitar la visualización de errores
ini_set('display_errors', 1);
error_reporting(E_ERROR);

/**Autocargador de clases */
require_once("../clases/db.php");
require_once ("../clases/usuario.php");
require_once ("../clases/donante.php");
require_once ("../clases/beneficiado.php");
require_once ("../clases/producto.php");
require_once ("../clases/categoria_persona.php");
require_once ("../clases/categoria_producto.php");
require_once ("../clases/porciones.php");
require_once ("../clases/inventario.php");
require_once ("../clases/planificador.php");
require_once ("../clases/reportes.php");


header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('content-type: application/json; charset=utf-8');

?>