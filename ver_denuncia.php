<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require 'conexion.php';
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener datos de la denuncia
$id_denuncia = $_GET['id'] ?? 0;
$query = "SELECT d.*, u.usuario as asignado_a 
          FROM denuncias d 
          LEFT JOIN usuarios u ON d.usuario_asignado = u.id 
          WHERE d.id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $id_denuncia);
$stmt->execute();
$resultado = $stmt->get_result();
$denuncia = $resultado->fetch_assoc();

if (!$denuncia) {
    $_SESSION['error'] = "Denuncia no encontrada";
    header("Location: " . ($_SESSION['rol'] == 'trabajador_social' ? 'panel_trabajador.php' : 'panel_' . $_SESSION['rol'] . '.php'));
    exit;
}

// Obtener evidencias
$query_evidencias = "SELECT * FROM denuncia_evidencia WHERE denuncia_id = ?";
$stmt_evidencias = $conexion->prepare($query_evidencias);
$stmt_evidencias->bind_param("i", $id_denuncia);
$stmt_evidencias->execute();
$evidencias = $stmt_evidencias->get_result();

// Obtener seguimientos
$query_seguimientos = "SELECT s.*, u.usuario 
                       FROM seguimiento_denuncias s 
                       JOIN usuarios u ON s.usuario_id = u.id 
                       WHERE s.denuncia_id = ? 
                       ORDER BY s.fecha DESC";
$stmt_seguimientos = $conexion->prepare($query_seguimientos);
$stmt_seguimientos->bind_param("i", $id_denuncia);
$stmt_seguimientos->execute();
$seguimientos = $stmt_seguimientos->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de Denuncia #<?php echo $denuncia['id']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #2c3e50; }
        .info-section { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .info-row { display: flex; margin-bottom: 10px; }
        .info-label { font-weight: bold; width: 200px; }
        .evidencias { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 20px; }
        .evidencia { width: 200px; border: 1px solid #ddd; padding: 10px; }
        .seguimiento { background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
        .seguimiento-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 3px; }
        .btn-primary { background-color: #3498db; color: white; }
        .btn-back { background-color: #95a5a6; color: white; }
    </style>
</head>
<body>
    <a href="<?php echo 'panel_' . $_SESSION['rol'] . '.php'; ?>" class="btn btn-back">← Volver al panel</a>
    <h1>Denuncia #<?php echo $denuncia['id']; ?></h1>
    
    <div class="info-section">
        <h2>Información básica</h2>
        <div class="info-row">
            <div class="info-label">Fecha:</div>
            <div><?php echo date('d/m/Y H:i', strtotime($denuncia['fecha_registro'])); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Estado:</div>
            <div><?php echo ucfirst($denuncia['estado']); ?></div>
        </div>
        <?php if ($denuncia['asignado_a']): ?>
        <div class="info-row">
            <div class="info-label">Asignado a:</div>
            <div><?php echo $denuncia['asignado_a']; ?></div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="info-section">
        <h2>Datos del denunciante</h2>
        <div class="info-row">
            <div class="info-label">Tipo de denuncia:</div>
            <div><?php echo $denuncia['es_anonima'] ? 'Anónima' : 'Identificada'; ?></div>
        </div>
        <?php if (!$denuncia['es_anonima']): ?>
        <div class="info-row">
            <div class="info-label">Nombre:</div>
            <div><?php echo htmlspecialchars($denuncia['nombre'] . ' ' . $denuncia['apellido_p'] . ' ' . $denuncia['apellido_m']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Teléfono:</div>
            <div><?php echo htmlspecialchars($denuncia['telefono']); ?></div>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <div class="info-label">Relación:</div>
            <div><?php echo ucfirst(str_replace('_', ' ', $denuncia['relacion'])); ?></div>
        </div>
    </div>
    
    <div class="info-section">
        <h2>Detalles del caso</h2>
        <div class="info-row">
            <div class="info-label">Situación:</div>
            <div><?php echo ucfirst(str_replace('_', ' ', $denuncia['situacion'])); 
                 echo $denuncia['otra_situacion'] ? ' (' . htmlspecialchars($denuncia['otra_situacion']) . ')' : ''; ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Edad del infante:</div>
            <div><?php echo str_replace('-', ' a ', $denuncia['edad_infante']) . ' años'; ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">¿Ocurre ahora?:</div>
            <div><?php echo $denuncia['ocurre_ahora'] == 'si' ? 'Sí' : 'No'; ?></div>
        </div>
        <?php if ($denuncia['ocurre_ahora'] == 'no'): ?>
        <div class="info-row">
            <div class="info-label">Tiempo de ocurrencia:</div>
            <div><?php echo ucfirst(str_replace('_', ' ', $denuncia['tiempo_ocurrencia'])); ?></div>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <div class="info-label">¿Primera vez?:</div>
            <div><?php echo $denuncia['primera_vez'] == 'si' ? 'Sí' : 'No'; ?></div>
        </div>
        <?php if ($denuncia['primera_vez'] == 'no'): ?>
        <div class="info-row">
            <div class="info-label">Frecuencia:</div>
            <div><?php 
                if ($denuncia['frecuencia'] == '2') echo '2 veces';
                elseif ($denuncia['frecuencia'] == '3') echo '3 veces';
                elseif ($denuncia['frecuencia'] == '4') echo '4 veces';
                elseif ($denuncia['frecuencia'] == '5') echo '5 veces';
                elseif ($denuncia['frecuencia'] == 'no_seguro') echo 'No estoy seguro';
                elseif ($denuncia['frecuencia'] == 'muy_frecuente') echo 'Muy frecuente';
            ?></div>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <div class="info-label">Ubicación:</div>
            <div>
                <?php if ($denuncia['latitud'] && $denuncia['longitud']): ?>
                    <a href="https://maps.google.com/?q=<?php echo $denuncia['latitud']; ?>,<?php echo $denuncia['longitud']; ?>" target="_blank">
                        Ver en mapa
                    </a>
                <?php else: ?>
                    No especificada
                <?php endif; ?>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Descripción:</div>
            <div><?php echo nl2br(htmlspecialchars($denuncia['descripcion'])); ?></div>
        </div>
    </div>
    
    <?php if ($evidencias->num_rows > 0): ?>
    <div class="info-section">
        <h2>Evidencias</h2>
        <div class="evidencias">
            <?php while ($evidencia = $evidencias->fetch_assoc()): ?>
            <div class="evidencia">
                <strong><?php echo ucfirst($evidencia['tipo']); ?></strong><br>
                <a href="<?php echo htmlspecialchars($evidencia['archivo_path']); ?>" target="_blank">Ver archivo</a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="info-section">
        <h2>Seguimientos</h2>
        <?php if ($seguimientos->num_rows > 0): ?>
            <?php while ($seguimiento = $seguimientos->fetch_assoc()): ?>
            <div class="seguimiento">
                <div class="seguimiento-header">
                    <strong><?php echo htmlspecialchars($seguimiento['usuario']); ?></strong>
                    <span><?php echo date('d/m/Y H:i', strtotime($seguimiento['fecha'])); ?></span>
                </div>
                <p><strong>Acción:</strong> <?php echo htmlspecialchars($seguimiento['accion']); ?></p>
                <?php if ($seguimiento['observaciones']): ?>
                <p><strong>Observaciones:</strong> <?php echo nl2br(htmlspecialchars($seguimiento['observaciones'])); ?></p>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay seguimientos registrados.</p>
        <?php endif; ?>
        
        <?php if ($denuncia['usuario_asignado'] == $_SESSION['id_usuario'] || $_SESSION['rol'] == 'trabajador_social'): ?>
        <h3>Agregar nuevo seguimiento</h3>
        <form action="agregar_seguimiento.php" method="post">
            <input type="hidden" name="denuncia_id" value="<?php echo $denuncia['id']; ?>">
            <div style="margin-bottom: 10px;">
                <label for="accion">Acción realizada:</label><br>
                <input type="text" id="accion" name="accion" required style="width: 100%; padding: 8px;">
            </div>
            <div style="margin-bottom: 10px;">
                <label for="observaciones">Observaciones:</label><br>
                <textarea id="observaciones" name="observaciones" style="width: 100%; padding: 8px; height: 100px;"></textarea>
            </div>
            <div style="margin-bottom: 10px;">
                <label for="cambiar_estado">Cambiar estado:</label>
                <select id="cambiar_estado" name="nuevo_estado">
                    <option value="">-- Mantener estado actual --</option>
                    <option value="nueva">Nueva</option>
                    <option value="asignada">Asignada</option>
                    <option value="investigacion">En investigación</option>
                    <option value="resuelta">Resuelta</option>
                    <option value="archivada">Archivada</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar seguimiento</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conexion->close(); ?>