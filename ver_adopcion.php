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

$id_adopcion = $_GET['id'] ?? 0;
$query = "SELECT a.*, u.usuario as revisado_por 
          FROM adopciones a 
          LEFT JOIN usuarios u ON a.usuario_revision = u.id 
          WHERE a.id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $id_adopcion);
$stmt->execute();
$resultado = $stmt->get_result();
$adopcion = $resultado->fetch_assoc();

if (!$adopcion) {
    $_SESSION['error'] = "Solicitud de adopción no encontrada";
    header("Location: panel_trabajador.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Adopción #<?php echo $adopcion['id']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #2c3e50; }
        .info-section { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .info-row { display: flex; margin-bottom: 10px; }
        .info-label { font-weight: bold; width: 250px; }
        .documentos { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 20px; }
        .documento { width: 200px; border: 1px solid #ddd; padding: 10px; }
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 3px; }
        .btn-primary { background-color: #3498db; color: white; }
        .btn-back { background-color: #95a5a6; color: white; }
    </style>
</head>
<body>
    <a href="panel_trabajador.php" class="btn btn-back">← Volver al panel</a>
    <h1>Solicitud de Adopción #<?php echo $adopcion['id']; ?></h1>
    
    <div class="info-section">
        <h2>Información básica</h2>
        <div class="info-row">
            <div class="info-label">Estado:</div>
            <div><?php echo ucfirst($adopcion['estado']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Fecha de solicitud:</div>
            <div><?php echo date('d/m/Y H:i', strtotime($adopcion['fecha_solicitud'])); ?></div>
        </div>
        <?php if ($adopcion['usuario_revision']): ?>
        <div class="info-row">
            <div class="info-label">Revisado por:</div>
            <div><?php echo $adopcion['revisado_por']; ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Fecha de revisión:</div>
            <div><?php echo date('d/m/Y H:i', strtotime($adopcion['fecha_revision'])); ?></div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="info-section">
        <h2>Datos del solicitante</h2>
        <div class="info-row">
            <div class="info-label">Nombre completo:</div>
            <div><?php echo htmlspecialchars($adopcion['nombre_completo']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div><?php echo htmlspecialchars($adopcion['email']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Teléfono:</div>
            <div><?php echo htmlspecialchars($adopcion['telefono']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Dirección:</div>
            <div><?php echo htmlspecialchars($adopcion['direccion']); ?></div>
        </div>
    </div>
    
    <div class="info-section">
        <h2>Documentos adjuntos</h2>
        <div class="documentos">
            <div class="documento">
                <strong>Identificación oficial</strong><br>
                <a href="<?php echo htmlspecialchars($adopcion['identificacion_path']); ?>" target="_blank">Ver documento</a>
            </div>
            <div class="documento">
                <strong>Acta de nacimiento</strong><br>
                <a href="<?php echo htmlspecialchars($adopcion['acta_nacimiento_path']); ?>" target="_blank">Ver documento</a>
            </div>
            <div class="documento">
                <strong>Comprobante de domicilio</strong><br>
                <a href="<?php echo htmlspecialchars($adopcion['comprobante_domicilio_path']); ?>" target="_blank">Ver documento</a>
            </div>
            <?php if ($adopcion['documento_extranjero_path']): ?>
            <div class="documento">
                <strong>Documento de estancia en el país</strong><br>
                <a href="<?php echo htmlspecialchars($adopcion['documento_extranjero_path']); ?>" target="_blank">Ver documento</a>
            </div>
            <?php endif; ?>
            <div class="documento">
                <strong>Certificado de no deudores alimenticios</strong><br>
                <a href="<?php echo htmlspecialchars($adopcion['certificado_deudores_path']); ?>" target="_blank">Ver documento</a>
            </div>
            <div class="documento">
                <strong>Comprobante de ingresos</strong><br>
                <a href="<?php echo htmlspecialchars($adopcion['comprobante_ingresos_path']); ?>" target="_blank">Ver documento</a>
            </div>
        </div>
    </div>
    
    <?php if ($adopcion['notas']): ?>
    <div class="info-section">
        <h2>Notas adicionales</h2>
        <p><?php echo nl2br(htmlspecialchars($adopcion['notas'])); ?></p>
    </div>
    <?php endif; ?>
    
    <div class="info-section">
        <h2>Cambiar estado</h2>
        <form action="cambiar_estado_adopcion.php" method="post">
            <input type="hidden" name="adopcion_id" value="<?php echo $adopcion['id']; ?>">
            <select name="nuevo_estado">
                <option value="pendiente" <?php echo $adopcion['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                <option value="revision" <?php echo $adopcion['estado'] == 'revision' ? 'selected' : ''; ?>>En revisión</option>
                <option value="aprobado" <?php echo $adopcion['estado'] == 'aprobado' ? 'selected' : ''; ?>>Aprobado</option>
                <option value="rechazado" <?php echo $adopcion['estado'] == 'rechazado' ? 'selected' : ''; ?>>Rechazado</option>
            </select>
            <button type="submit" class="btn btn-primary">Actualizar estado</button>
        </form>
    </div>
</body>
</html>
<?php $conexion->close(); ?>