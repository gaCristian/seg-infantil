<?php
require 'conexion.php';

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Usuarios a registrar (puedes modificar esta lista)
$usuarios = [
    ['usuario' => 'abogado1', 'contrasena' => 'clave123', 'rol' => 'abogado'],
    ['usuario' => 'psicologo1', 'contrasena' => 'clave123', 'rol' => 'psicologo'],
    ['usuario' => 'trabajador1', 'contrasena' => 'clave123', 'rol' => 'trabajador_social']
];

// Insertar usuarios
foreach ($usuarios as $u) {
    $usuario = $u['usuario'];
    $hash = password_hash($u['contrasena'], PASSWORD_DEFAULT);
    $rol = $u['rol'];

    // Verifica si ya existe
    $check = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $check->bind_param("s", $usuario);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        // Insertar nuevo usuario
        $stmt = $conexion->prepare("INSERT INTO usuarios (usuario, contrasena, rol) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $usuario, $hash, $rol);
        if ($stmt->execute()) {
            echo "✅ Usuario '$usuario' registrado como '$rol'.<br>";
        } else {
            echo "❌ Error al registrar '$usuario': " . $stmt->error . "<br>";
        }
        $stmt->close();
    } else {
        echo "⚠️ Usuario '$usuario' ya existe.<br>";
    }
    $check->close();
}

$conexion->close();
