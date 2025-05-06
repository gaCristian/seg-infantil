<?php
require 'conexion.php';

// Configuración para subida de archivos
$directorioEvidencias = 'uploads/denuncias/';
if (!file_exists($directorioEvidencias)) {
    if (!mkdir($directorioEvidencias, 0777, true)) {
        error_log("Error al crear directorio de evidencias");
        header('Location: denunciar.php?status=error&message=' . urlencode('Error interno del sistema'));
        exit();
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar campos requeridos
    $requiredFields = ['fecha', 'situacion', 'descripcion', 'edad_infante'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            header('Location: denunciar.php?status=error&message=' . urlencode("El campo $field es requerido"));
            exit();
        }
    }

    // Validar y formatear fecha
    try {
        $date = new DateTime($_POST['fecha']);
        $fecha = $date->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        header('Location: denunciar.php?status=error&message=' . urlencode('Formato de fecha inválido'));
        exit();
    }

    // Sanitizar y validar datos
    $anonimo = isset($_POST['anonimo']) ? 1 : 0;
    $nombre = $anonimo ? null : $conexion->real_escape_string(trim($_POST['nombre']));
    $apellido_p = $anonimo ? null : $conexion->real_escape_string(trim($_POST['apellido_p']));
    $apellido_m = $anonimo ? null : $conexion->real_escape_string(trim($_POST['apellido_m']));
    $telefono = $anonimo ? null : $conexion->real_escape_string(trim($_POST['telefono']));
    $latitud = isset($_POST['latitud']) ? floatval($_POST['latitud']) : null;
    $longitud = isset($_POST['longitud']) ? floatval($_POST['longitud']) : null;
    $relacion = $conexion->real_escape_string(trim($_POST['relacion']));
    $situacion = $conexion->real_escape_string(trim($_POST['situacion']));
    $otra_situacion = ($situacion === 'otro' && !empty($_POST['otra_situacion'])) 
        ? $conexion->real_escape_string(trim($_POST['otra_situacion'])) 
        : null;
    $descripcion = $conexion->real_escape_string(trim($_POST['descripcion']));
    $edad_infante = $conexion->real_escape_string(trim($_POST['edad_infante']));
    $ocurre_ahora = $conexion->real_escape_string(trim($_POST['ocurre_ahora']));
    $tiempo_ocurrencia = ($ocurre_ahora === 'no' && !empty($_POST['tiempo_ocurrencia'])) 
        ? $conexion->real_escape_string(trim($_POST['tiempo_ocurrencia'])) 
        : null;
    $primera_vez = $conexion->real_escape_string(trim($_POST['primera_vez']));
    $frecuencia = ($primera_vez === 'no' && !empty($_POST['frecuencia'])) 
        ? $conexion->real_escape_string(trim($_POST['frecuencia'])) 
        : null;
    $confirmacion = isset($_POST['confirmacion']) ? 1 : 0;

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Insertar denuncia en la base de datos
        $stmt = $conexion->prepare("INSERT INTO denuncias (
            fecha, es_anonima, nombre, apellido_p, apellido_m, telefono,
            latitud, longitud, relacion, situacion, otra_situacion, descripcion,
            edad_infante, ocurre_ahora, tiempo_ocurrencia, primera_vez, frecuencia,
            confirmacion
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $conexion->error);
        }

        $stmt->bind_param(
            "sisssssdsssssssssi",
            $fecha, $anonimo, $nombre, $apellido_p, $apellido_m, $telefono,
            $latitud, $longitud, $relacion, $situacion, $otra_situacion, $descripcion,
            $edad_infante, $ocurre_ahora, $tiempo_ocurrencia, $primera_vez, $frecuencia,
            $confirmacion
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        $denuncia_id = $conexion->insert_id;

        // Procesar evidencias si existen
        if (!empty($_FILES['evidencia']['name'][0])) {
            $evidencias = $_FILES['evidencia'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'application/pdf'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB
            
            for ($i = 0; $i < count($evidencias['name']); $i++) {
                if ($evidencias['error'][$i] === UPLOAD_ERR_OK) {
                    // Validar tipo y tamaño de archivo
                    if (!in_array($evidencias['type'][$i], $allowedTypes) || $evidencias['size'][$i] > $maxFileSize) {
                        continue; // Saltar archivo no válido
                    }

                    // Generar nombre seguro para el archivo
                    $extension = pathinfo($evidencias['name'][$i], PATHINFO_EXTENSION);
                    $nombreArchivo = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . strtolower($extension);
                    $rutaCompleta = $directorioEvidencias . $nombreArchivo;
                    
                    if (move_uploaded_file($evidencias['tmp_name'][$i], $rutaCompleta)) {
                        // Determinar tipo de archivo
                        $tipo = 'otro';
                        if (strpos($evidencias['type'][$i], 'image/') === 0) {
                            $tipo = 'imagen';
                        } elseif (strpos($evidencias['type'][$i], 'video/') === 0) {
                            $tipo = 'video';
                        } elseif ($extension === 'pdf') {
                            $tipo = 'documento';
                        }
                        
                        // Insertar en la tabla de evidencias
                        $stmtEvidencia = $conexion->prepare("INSERT INTO denuncia_evidencia (denuncia_id, archivo_path, tipo) VALUES (?, ?, ?)");
                        if (!$stmtEvidencia) {
                            throw new Exception("Error al preparar consulta de evidencia: " . $conexion->error);
                        }
                        
                        $stmtEvidencia->bind_param("iss", $denuncia_id, $rutaCompleta, $tipo);
                        if (!$stmtEvidencia->execute()) {
                            throw new Exception("Error al guardar evidencia: " . $stmtEvidencia->error);
                        }
                        $stmtEvidencia->close();
                    }
                }
            }
        }

        // Confirmar transacción
        $conexion->commit();
        
        // Redireccionar con mensaje de éxito
        header('Location: denunciar.php?status=success&message=' . urlencode('¡Denuncia registrada exitosamente! Se ha creado el caso #' . $denuncia_id));
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conexion->rollback();
        error_log("Error en denuncia: " . $e->getMessage());
        header('Location: denunciar.php?status=error&message=' . urlencode('Error al procesar la denuncia: ' . $e->getMessage()));
    } finally {
        // Cerrar conexiones
        if (isset($stmt)) $stmt->close();
        $conexion->close();
    }
    exit();
}

// Si no es POST, redirigir
header('Location: denunciar.php');
?>