<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Abogado</title>
    <link rel="stylesheet" href="estilos-prof.css">
    <link rel="stylesheet" href="abogado.php">
</head>
<body>
    <h1>Bienvenido, Abogado <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
    <p><a href="logout.php" style="color:rgb(255, 255, 255);">Cerrar sesión</a></p>
    
    <h2>Casos asignados</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Situación</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($denuncia = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?php echo $denuncia['id']; ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($denuncia['fecha_registro'])); ?></td>
                <td><?php echo ucfirst(str_replace('_', ' ', $denuncia['situacion'])); ?></td>
                <td><?php echo substr($denuncia['descripcion'], 0, 50) . '...'; ?></td>
                <td><?php echo ucfirst($denuncia['estado']); ?></td>
                <td>
                    <a href="ver_denuncia.php?id=<?php echo $denuncia['id']; ?>" class="btn btn-primary">Ver detalles</a>
                    <?php if ($denuncia['usuario_asignado'] == $_SESSION['id_usuario']): ?>
                        <a href="seguimiento.php?id=<?php echo $denuncia['id']; ?>" class="btn btn-success">Agregar seguimiento</a>
                    <?php elseif ($denuncia['usuario_asignado'] === null): ?>
                        <a href="asignar_denuncia.php?id=<?php echo $denuncia['id']; ?>&usuario=<?php echo $_SESSION['id_usuario']; ?>" class="btn btn-success">Tomar caso</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<?php $conexion->close(); ?>
