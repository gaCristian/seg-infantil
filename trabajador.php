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

// Configuración de paginación
$por_pagina = 10;

// Paginación para denuncias
$pagina_denuncias = isset($_GET['pagina_denuncias']) ? max(1, (int)$_GET['pagina_denuncias']) : 1;
$inicio_denuncias = ($pagina_denuncias - 1) * $por_pagina;

// Obtener denuncias con paginación
$query_denuncias = "SELECT SQL_CALC_FOUND_ROWS d.id, d.fecha_registro, d.situacion, 
                   LEFT(d.descripcion, 50) as descripcion_corta, d.estado, 
                   u.usuario as asignado_a 
                   FROM denuncias d 
                   LEFT JOIN usuarios u ON d.usuario_asignado = u.id
                   ORDER BY d.fecha_registro DESC
                   LIMIT $inicio_denuncias, $por_pagina";
$denuncias_result = $conexion->query($query_denuncias);
$denuncias = $denuncias_result->fetch_all(MYSQLI_ASSOC);

$total_denuncias = $conexion->query("SELECT FOUND_ROWS() as total")->fetch_assoc()['total'];
$paginas_denuncias = ceil($total_denuncias / $por_pagina);

// Paginación para adopciones
$pagina_adopciones = isset($_GET['pagina_adopciones']) ? max(1, (int)$_GET['pagina_adopciones']) : 1;
$inicio_adopciones = ($pagina_adopciones - 1) * $por_pagina;

// Obtener solicitudes de adopción con paginación
$query_adopciones = "SELECT SQL_CALC_FOUND_ROWS id, nombre_completo, email, telefono, estado, fecha_solicitud
                    FROM adopciones 
                    ORDER BY fecha_solicitud DESC
                    LIMIT $inicio_adopciones, $por_pagina";
$adopciones_result = $conexion->query($query_adopciones);
$adopciones = $adopciones_result->fetch_all(MYSQLI_ASSOC);

$total_adopciones = $conexion->query("SELECT FOUND_ROWS() as total")->fetch_assoc()['total'];
$paginas_adopciones = ceil($total_adopciones / $por_pagina);

// Obtener lista de abogados y psicólogos para asignación (una sola vez)
$query_profesionales = "SELECT id, usuario, rol FROM usuarios WHERE rol IN ('abogado', 'psicologo') ORDER BY rol, usuario";
$profesionales_result = $conexion->query($query_profesionales);
$profesionales = $profesionales_result->fetch_all(MYSQLI_ASSOC);
?>
