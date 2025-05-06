<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'trabajador_social') {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require 'conexion.php';
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$denuncia_id = $_POST['denuncia_id'] ?? 0;
$profesional_id = $_POST['profesional_id'] ?? 0;

// Validar que el profesional exista y sea abogado o psicólogo
$query = "SELECT id, rol FROM usuarios WHERE id = ? AND rol IN ('abogado', 'psicologo')";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $profesional_id);
$stmt->execute();
$resultado = $stmt->get_result();
$profesional = $resultado->fetch_assoc();

if (!$profesional) {
    $_SESSION['error'] = "Profesional no válido";
    header("Location: panel_trabajador.php?id=" . $denuncia_id);
    exit;
}

// Asignar denuncia
$query = "UPDATE denuncias SET usuario_asignado = ?, estado = 'asignada' WHERE id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("ii", $profesional_id, $denuncia_id);
$stmt->execute();

// Registrar seguimiento automático
$accion = "Denuncia asignada a " . $profesional['rol'] . " (ID: " . $profesional['id'] . ")";
$query_seg = "INSERT INTO seguimiento_denuncias (denuncia_id, usuario_id, accion) 
              VALUES (?, ?, ?)";
$stmt_seg = $conexion->prepare($query_seg);
$stmt_seg->bind_param("iis", $denuncia_id, $_SESSION['id_usuario'], $accion);
$stmt_seg->execute();

$_SESSION['exito'] = "Denuncia asignada correctamente al profesional";
header("Location: ver_denuncia.php?id=" . $denuncia_id);
exit;
?>