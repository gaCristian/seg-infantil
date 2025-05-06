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

$id_denuncia = $_GET['id'] ?? 0;
$query = "SELECT d.* FROM denuncias d WHERE d.id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $id_denuncia);
$stmt->execute();
$resultado = $stmt->get_result();
$denuncia = $resultado->fetch_assoc();

if (!$denuncia || ($denuncia['usuario_asignado'] != $_SESSION['id_usuario'] && $_SESSION['rol'] != 'trabajador_social')) {
    $_SESSION['error'] = "No tienes permiso para acceder a esta denuncia";
    header("Location: panel_" . $_SESSION['rol'] . ".php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Seguimiento a Denuncia #<?php echo $denuncia['id']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #2c3e50; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea, select { width: 100%; padding: 8px; }
        textarea { height: 100px; }
        .btn { padding: 8px 15px; text-decoration: none; border-radius: 3px; border: none; cursor: pointer; }
        .btn-primary { background-color: #3498db; color: white; }
        .btn-back { background-color: #95a5a6; color: white; }
    </style>
</head>
<body>
    <a href="ver_denuncia.php?id=<?php echo $denuncia['id']; ?>" class="btn btn-back">← Volver a la denuncia</a>
    <h1>Agregar Seguimiento - Denuncia #<?php echo $denuncia['id']; ?></h1>
    
    <form action="agregar_seguimiento.php" method="post">
        <input type="hidden" name="denuncia_id" value="<?php echo $denuncia['id']; ?>">
        
        <div class="form-group">
            <label for="accion">Acción realizada:</label>
            <input type="text" id="accion" name="accion" required>
        </div>
        
        <div class="form-group">
            <label for="observaciones">Observaciones:</label>
            <textarea id="observaciones" name="observaciones"></textarea>
        </div>
        
        <div class="form-group">
            <label for="nuevo_estado">Cambiar estado:</label>
            <select id="nuevo_estado" name="nuevo_estado">
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
</body>
</html>
<?php $conexion->close(); ?>