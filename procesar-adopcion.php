<?php
require 'conexion.php';

// Configuración para subida de archivos
$directorioUploads = 'uploads/adopciones/';
if (!file_exists($directorioUploads)) {
    mkdir($directorioUploads, 0777, true);
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar datos
    $nombre_completo = $conexion->real_escape_string($_POST['nombre_completo']);
    $email = $conexion->real_escape_string($_POST['email']);
    $telefono = $conexion->real_escape_string($_POST['telefono']);
    $direccion = $conexion->real_escape_string($_POST['direccion']);

    // Función para subir archivos
    function subirArchivo($fileInput, $directorio, $nombreCampo) {
        global $conexion;
        if (isset($_FILES[$fileInput])) {
            $archivo = $_FILES[$fileInput];
            $nombreArchivo = uniqid() . '_' . basename($archivo['name']);
            $rutaCompleta = $directorio . $nombreArchivo;
            
            if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
                return $rutaCompleta;
            } else {
                die("Error al subir el archivo de $nombreCampo");
            }
        }
        return null;
    }

    // Subir todos los archivos requeridos
    $identificacion_path = subirArchivo('identificacion', $directorioUploads, 'identificación');
    $acta_nacimiento_path = subirArchivo('acta_nacimiento', $directorioUploads, 'acta de nacimiento');
    $comprobante_domicilio_path = subirArchivo('comprobante_domicilio', $directorioUploads, 'comprobante de domicilio');
    $certificado_deudores_path = subirArchivo('certificado_deudores', $directorioUploads, 'certificado de deudores');
    $comprobante_ingresos_path = subirArchivo('comprobante_ingresos', $directorioUploads, 'comprobante de ingresos');
    
    // Archivo opcional para extranjeros
    $documento_extranjero_path = isset($_FILES['documento_extranjero']) && $_FILES['documento_extranjero']['error'] === UPLOAD_ERR_OK 
        ? subirArchivo('documento_extranjero', $directorioUploads, 'documento de extranjero') 
        : null;

    // Insertar en la base de datos
    $stmt = $conexion->prepare("INSERT INTO adopciones (
        nombre_completo, email, telefono, direccion, 
        identificacion_path, acta_nacimiento_path, comprobante_domicilio_path,
        documento_extranjero_path, certificado_deudores_path, comprobante_ingresos_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "ssssssssss", 
        $nombre_completo, $email, $telefono, $direccion,
        $identificacion_path, $acta_nacimiento_path, $comprobante_domicilio_path,
        $documento_extranjero_path, $certificado_deudores_path, $comprobante_ingresos_path
    );

    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header('Location: adopcion.php?status=success&message=' . urlencode('Adopcion registrada exitosamente! Espere a que algun trabajador social se ponga en contacto con usted' . $denuncia_id));
    } else {
        // Redirigir con mensaje de error
        header('Location: adopcion.php?status=error&message=' . urlencode($stmt->error));
    }

    $stmt->close();
    $conexion->close();
    exit();
}

// Si no es POST, redirigir
header('Location: adopcion.php');
?>