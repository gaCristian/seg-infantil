<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['rol'], ['abogado', 'psicologo', 'trabajador_social'])) {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require 'conexion.php';
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Validar que el usuario tenga permiso para agregar seguimiento
$denuncia_id = $_POST['denuncia_id'] ?? 0;
$query = "SELECT usuario_asignado FROM denuncias WHERE id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $denuncia_id);
$stmt->execute();
$resultado = $stmt->get_result();
$denuncia = $resultado->fetch_assoc();

if (!$denuncia || ($denuncia['usuario_asignado'] != $_SESSION['id_usuario'] && $_SESSION['rol'] != 'trabajador_social')) {
    $_SESSION['error'] = "No tienes permiso para agregar seguimiento a esta denuncia";
    header("Location: panel_" . $_SESSION['rol'] . ".php");
    exit;
}

// Insertar seguimiento
$accion = trim($_POST['accion']);
$observaciones = trim($_POST['observaciones'] ?? '');

$query = "INSERT INTO seguimiento_denuncias (denuncia_id, usuario_id, accion, observaciones) 
          VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($query);
$stmt->bind_param("iiss", $denuncia_id, $_SESSION['id_usuario'], $accion, $observaciones);
$stmt->execute();

// Actualizar estado si se especificó
if (!empty($_POST['nuevo_estado'])) {
    $query = "UPDATE denuncias SET estado = ? WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("si", $_POST['nuevo_estado'], $denuncia_id);
    $stmt->execute();
}

$_SESSION['exito'] = "Seguimiento agregado correctamente";
header("Location: ver_denuncia.php?id=" . $denuncia_id);
exit;
?>