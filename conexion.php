<?php
// conexion.php - Versión MySQLi
$host = 'localhost';
$usuario = 'root';
$contrasena = '1234';
$basedatos = 'denuncias_ciudadanas';

$conexion = new mysqli($host, $usuario, $contrasena, $basedatos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Configurar charset
$conexion->set_charset("utf8");
?>