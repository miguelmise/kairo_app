<?php
/**Autocargador de clases */
require_once("../clases/db.php");
require_once ("../clases/usuario.php");
require_once ("../clases/donante.php");
require_once ("../clases/beneficiado.php");
require_once ("../clases/producto.php");
require_once ("../clases/categoria_persona.php");
require_once ("../clases/categoria_producto.php");


header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('content-type: application/json; charset=utf-8');

?>