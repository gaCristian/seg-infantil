<?php require 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Adopcion | Protección Infantil IXTLAHUACA</title>
    <link rel="stylesheet" href="estilo-adopcion.css">
</head>
<body>
    <header class="header-formulario">
        <div class="container">
            <h1>Formulario de Adopcion</h1>
            <a href="index.php" class="btn-regresar">Regresar</a>
        </div>
    </header>
    
    <main>
        <div class="contenedor-formulario">
            <form action="procesar-adopcion.php" method="POST" enctype="multipart/form-data">
                <h2>Datos Personales</h2>
                
                <div class="form-group">
                    <label for="nombre_completo" class="campo-requerido">Nombre completo</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="campo-requerido">Correo electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="telefono" class="campo-requerido">Número de teléfono</label>
                    <input type="tel" id="telefono" name="telefono" required>
                </div>
                
                <div class="form-group">
                    <label for="direccion" class="campo-requerido">Dirección completa</label>
                    <textarea id="direccion" name="direccion" rows="3" required></textarea>
                </div>
                
                <h2>Documentación Requerida</h2>
                
                <div class="requisito-documento">
                    <div class="form-group">
                        <label for="identificacion" class="campo-requerido">1. Identificación oficial vigente con fotografía (INE, pasaporte, cédula profesional)</label>
                        <input type="file" id="identificacion" name="identificacion" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>
                
                <div class="requisito-documento">
                    <div class="form-group">
                        <label for="acta_nacimiento" class="campo-requerido">2. Acta de nacimiento</label>
                        <input type="file" id="acta_nacimiento" name="acta_nacimiento" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>
                
                <div class="requisito-documento">
                    <div class="form-group">
                        <label for="comprobante_domicilio" class="campo-requerido">3. Comprobante de domicilio (no mayor a 3 meses)</label>
                        <input type="file" id="comprobante_domicilio" name="comprobante_domicilio" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>
                
                <div class="requisito-documento">
                    <div class="form-group">
                        <label for="documento_extranjero">4. Documento que acredite estancia legal en el país (solo para extranjeros)</label>
                        <input type="file" id="documento_extranjero" name="documento_extranjero" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>
                
                <div class="requisito-documento">
                    <div class="form-group">
                        <label for="certificado_deudores" class="campo-requerido">5. Certificado del Registro de Deudores Alimentarios Morosos</label>
                        <input type="file" id="certificado_deudores" name="certificado_deudores" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>
                
                <div class="requisito-documento">
                    <div class="form-group">
                        <label for="comprobante_ingresos" class="campo-requerido">6. Comprobante de ingresos (últimos 3 meses)</label>
                        <input type="file" id="comprobante_ingresos" name="comprobante_ingresos" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">Enviar Solicitud</button>
            </form>
        </div>
    </main>
    
</body>
</html>