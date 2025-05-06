<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'psicologo') {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require 'conexion.php';
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
} 

// Obtener denuncias asignadas al psicólogo o que requieren atención psicológica
$query = "SELECT d.* FROM denuncias d 
          WHERE (d.usuario_asignado = ? OR 
                (d.usuario_asignado IS NULL AND d.situacion IN ('abuso_emocional', 'negligencia', 'acoso', 'maltrato')))
          AND d.estado != 'resuelta' AND d.estado != 'archivada'
          ORDER BY d.fecha_registro DESC";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$resultado = $stmt->get_result();
?>
