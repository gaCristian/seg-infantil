<?php
session_start();
require 'conexion.php'; // Asegúrate que usa MySQLi como mostré anteriormente

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    // Consulta preparada con manejo detallado de errores
    $sql = "SELECT id, usuario, contrasena, rol FROM usuarios WHERE usuario = ?";
    $stmt = $conexion->prepare($sql);

    if ($stmt === false) {
        error_log("Error preparando consulta: " . $conexion->error);
        header('Location: login.php?error=db');
        exit();
    }

    // Vincular parámetro y ejecutar
    $stmt->bind_param("s", $usuario);
    if (!$stmt->execute()) {
        error_log("Error ejecutando consulta: " . $stmt->error);
        header('Location: login.php?error=db');
        exit();
    }

    // Obtener resultados
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verificar contraseña hasheada
        if (password_verify($contrasena, $user['contrasena'])) {
            // Autenticación exitosa
            $_SESSION = [
                'user_id' => $user['id'],
                'usuario' => $user['usuario'],
                'rol' => $user['rol'],
                'logged_in' => true
            ];
            
            // Redirección según rol (incluyendo el nuevo rol 'admin')
            $redirecciones = [
                'abogado' => 'panel_abogado.php',
                'psicologo' => 'panel_psicologo.php',
                'trabajador_social' => 'panel_trabajador.php',
            ];
            
            $destino = $redirecciones[$user['rol']] ?? 'index.php';
            header("Location: $destino");
            exit();
        }
    }
    
    // Si llega aquí, la autenticación falló
    header('Location: login.php?error=credenciales');
    exit();
} else {
    // Si acceden directamente al script
    header('Location: login.php');
    exit();
}
?>