<?php
// procesar_login.php
session_start();

// Incluir el archivo de conexión
require_once 'conexion.php'; // Asegúrate de poner el nombre correcto del archivo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
 
    // Consulta preparada para mayor seguridad
    $stmt = $conexion->prepare("SELECT id, nombre, contrasena, rol FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verificar contraseña (asumiendo que está hasheada)
        if (password_verify($contrasena, $user['contrasena'])) {
            // Credenciales válidas
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol'];
            
            // Redirección según rol
            switch ($user['rol']) {
                case 'abogado':
                    header('Location: panel_abogado.php');
                    break;
                case 'psicologo':
                    header('Location: panel_psicologo.php');
                    break;
                case 'trabajador_social':
                    header('Location: panel_trabajador.php');
                    break;
                default:
                    header('Location: index.php');
            }
            exit();
        } else {
            // Contraseña incorrecta
            header('Location: login.html?error=credenciales');
            exit();
        }
    } else {
        // Usuario no encontrado
        header('Location: login.html?error=credenciales');
        exit();
    }
} else {
    // Intento de acceso directo
    header('Location: login.html');
    exit();
}
?>