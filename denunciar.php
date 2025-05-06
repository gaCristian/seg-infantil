<?php require 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Denuncia | Protección Infantil IXTLAHUACA</title>
    <link rel="stylesheet" href="estilo-denuncia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Reemplaza TU_API_KEY con una clave válida de Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_ACTUAL_API_KEY&libraries=places&language=es&region=MX" defer></script>
    <style>
        #mapa {
            width: 100%;
            height: 300px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <header class="header-formulario">
        <div class="container">
            <a href="index.php" class="btn-regresar">← Regresar</a>
            <h1>Denuncia ciudadana</h1>
            <div class="fecha-automatica">
                <span id="fecha-actual"></span>
                <span id="hora-actual"></span>
            </div>
        </div>
    </header>

    <main>
        <div class="contenedor-formulario">
            <form action="procesar-denuncia.php" method="POST">
                <input type="hidden" id="fecha" name="fecha">
                <input type="hidden" id="latitud" name="latitud">
                <input type="hidden" id="longitud" name="longitud">

                <div class="seccion-formulario">
                    <h2>Ubicación de los hechos (opcional)</h2>
                    <p>Puedes hacer clic en el mapa para señalar la ubicación.</p>
                    <div id="mapa"></div>
                </div>

                <div class="seccion-formulario">
                    <h2>Ingresa los siguientes datos</h2>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="anonimo" name="anonimo"> Prefiero realizar la denuncia de forma anónima
                        </label>
                    </div>

                    <div id="datos-personales">
                        <div class="form-group">
                            <label for="nombre" class="campo-requerido">Nombre(s)</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>

                        <div class="form-group">
                            <label for="apellido_p" class="campo-requerido">Apellido paterno</label>
                            <input type="text" id="apellido_p" name="apellido_p" required>
                        </div>

                        <div class="form-group">
                            <label for="apellido_m" class="campo-requerido">Apellido materno</label>
                            <input type="text" id="apellido_m" name="apellido_m" required>
                        </div>

                        <div class="form-group">
                            <label for="telefono" class="campo-requerido">Teléfono de contacto</label>
                            <input type="tel" id="telefono" name="telefono" required>
                        </div>
                    </div>
                </div>

                <div class="seccion-formulario">
                    <h2>Detalles de la denuncia</h2>

                    <div class="form-group">
                        <label class="campo-requerido">¿Cuál es tu relación con el caso?</label>
                        <div class="opciones-radio">
                            <label>
                                <input type="radio" name="relacion" value="victima" required> Soy la víctima
                            </label>
                            <label>
                                <input type="radio" name="relacion" value="testigo"> Soy testigo
                            </label>
                            <label>
                                <input type="radio" name="relacion" value="familiar"> Soy familiar de la víctima
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="situacion" class="campo-requerido">Situación que se presenta</label>
                        <select id="situacion" name="situacion" required>
                            <option value="">Selecciona una opción</option>
                            <option value="abuso_fisico">Abuso físico</option>
                            <option value="abuso_emocional">Abuso emocional/psicológico</option>
                            <option value="abuso_sexual">Abuso sexual</option>
                            <option value="negligencia">Negligencia o abandono</option>
                            <option value="explotacion">Explotación infantil</option>
                            <option value="violencia_domestica">Violencia doméstica</option>
                            <option value="acoso">Acoso escolar (bullying)</option>
                            <option value="maltrato">Maltrato infantil</option>
                            <option value="trabajo_infantil">Trabajo infantil</option>
                            <option value="otro">Otra situación (describir)</option>
                        </select>
                    </div>

                    <div class="form-group" id="otra-situacion-container" style="display:none;">
                        <label for="otra_situacion" class="campo-requerido">Describe la situación</label>
                        <input type="text" id="otra_situacion" name="otra_situacion">
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="campo-requerido">Descripción detallada de la situación</label>
                        <textarea id="descripcion" name="descripcion" rows="5" required></textarea>
                        <small class="texto-ayuda">Proporciona todos los detalles que consideres relevantes: lugar de los hechos, personas involucradas, características físicas, horarios, etc.</small>
                    </div>

                    <div class="form-group">
                        <label for="edad_infante" class="campo-requerido">Edad aproximada del infante</label>
                        <select id="edad_infante" name="edad_infante" required>
                            <option value="">Selecciona un rango de edad</option>
                            <option value="0-3">0 a 3 años</option>
                            <option value="4-6">4 a 6 años</option>
                            <option value="7-12">7 a 12 años</option>
                            <option value="13-17">13 a 17 años</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="campo-requerido">¿La situación está ocurriendo ahora mismo?</label>
                        <div class="opciones-radio">
                            <label>
                                <input type="radio" name="ocurre_ahora" value="si" required> Sí
                            </label>
                            <label>
                                <input type="radio" name="ocurre_ahora" value="no"> No
                            </label>
                        </div>
                    </div>

                    <div class="form-group" id="tiempo-ocurrencia-container" style="display:none;">
                        <label for="tiempo_ocurrencia" class="campo-requerido">¿Cuándo ocurrió la situación?</label>
                        <select id="tiempo_ocurrencia" name="tiempo_ocurrencia">
                            <option value="">Selecciona una opción</option>
                            <option value="hoy">Durante el día de hoy</option>
                            <option value="menos_3_dias">Hace menos de 3 días</option>
                            <option value="esta_semana">Esta semana</option>
                            <option value="este_mes">Durante este mes</option>
                            <option value="mas_1_mes">Hace más de un mes</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="campo-requerido">¿Es la primera vez que ocurre esta situación?</label>
                        <div class="opciones-radio">
                            <label>
                                <input type="radio" name="primera_vez" value="si" required> Sí
                            </label>
                            <label>
                                <input type="radio" name="primera_vez" value="no"> No
                            </label>
                        </div>
                    </div>

                    <div class="form-group" id="frecuencia-container" style="display:none;">
                        <label for="frecuencia" class="campo-requerido">¿Con qué frecuencia ha ocurrido?</label>
                        <select id="frecuencia" name="frecuencia">
                            <option value="">Selecciona una opción</option>
                            <option value="2">2 veces</option>
                            <option value="3">3 veces</option>
                            <option value="4">4 veces</option>
                            <option value="5">5 o más veces</option>
                            <option value="no_seguro">No estoy seguro</option>
                            <option value="muy_frecuente">Es muy frecuente</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="evidencia">¿Tienes alguna evidencia? (fotos, videos, documentos)</label>
                        <input type="file" id="evidencia" name="evidencia[]" multiple accept="image/*,video/*,.pdf,.doc,.docx">
                        <small class="texto-ayuda">Puedes subir hasta 5 archivos (fotos, videos o documentos)</small>
                    </div>
                </div>

                <div class="form-group confirmacion">
                    <label>
                        <input type="checkbox" name="confirmacion" required>
                        Confirmo que la información proporcionada es verídica
                    </label>
                </div>

                <div class="acciones-formulario">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Enviar Denuncia
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Actualizar fecha y hora en tiempo real
        function actualizarFechaHora() {
            const ahora = new Date();
            const opcionesFecha = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const opcionesHora = { hour: '2-digit', minute: '2-digit', second: '2-digit' };

            document.getElementById('fecha-actual').textContent = ahora.toLocaleDateString('es-MX', opcionesFecha);
            document.getElementById('hora-actual').textContent = ahora.toLocaleTimeString('es-MX', opcionesHora);
            document.getElementById('fecha').value = ahora.toISOString();

            setTimeout(actualizarFechaHora, 1000);
        }

        // Iniciar el reloj
        actualizarFechaHora();

        // Mostrar/ocultar campos según selecciones
        document.getElementById('anonimo').addEventListener('change', function() {
            const datosPersonales = document.getElementById('datos-personales');
            datosPersonales.style.display = this.checked ? 'none' : 'block';

            // Hacer campos no requeridos si es anónimo
            const inputs = datosPersonales.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.required = !this.checked;
            });
        });

        document.getElementById('situacion').addEventListener('change', function() {
            document.getElementById('otra-situacion-container').style.display =
                this.value === 'otro' ? 'block' : 'none';
        });

        document.querySelectorAll('input[name="ocurre_ahora"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const container = document.getElementById('tiempo-ocurrencia-container');
                container.style.display = this.value === 'no' ? 'block' : 'none';
                container.querySelector('select').required = this.value === 'no';
            });
        });

        document.querySelectorAll('input[name="primera_vez"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const container = document.getElementById('frecuencia-container');
                container.style.display = this.value === 'no' ? 'block' : 'none';
                container.querySelector('select').required = this.value === 'no';
            });
        });

        // Validar archivos antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            const archivos = document.getElementById('evidencia').files;
            if (archivos.length > 5) {
                alert("Solo puedes subir un máximo de 5 archivos como evidencia.");
                e.preventDefault();
            }

            // Validar tamaño de archivos (ejemplo: máximo 5MB cada uno)
            for (let i = 0; i < archivos.length; i++) {
                if (archivos[i].size > 5 * 1024 * 1024) {
                    alert(`El archivo ${archivos[i].name} es demasiado grande (máximo 5MB)`);
                    e.preventDefault();
                    break;
                }
            }
        });

        let map;
        let marker;

        function initMap() {
            const defaultLatLng = { lat: 19.4326, lng: -99.1332 }; // Centro por defecto: Ciudad de México

            map = new google.maps.Map(document.getElementById('mapa'), {
                center: defaultLatLng,
                zoom: 10
            });

            marker = new google.maps.Marker({
                position: defaultLatLng,
                map: map,
                title: 'Ubicación seleccionada',
                draggable: true // Permite mover el marcador
            });

            // Al cargar el mapa, intenta obtener la ubicación del usuario
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const userLatLng = { lat: position.coords.latitude, lng: position.coords.longitude };
                        map.setCenter(userLatLng);
                        marker.setPosition(userLatLng);
                        document.getElementById('latitud').value = userLatLng.lat;
                        document.getElementById('longitud').value = userLatLng.lng;
                    },
                    function(error) {
                        console.log("Error al obtener la ubicación:", error);
                        // Si hay un error, se usa la ubicación por defecto
                    }
                );
            }

            // Evento al arrastrar y soltar el marcador
            marker.addListener('dragend', function(event) {
                document.getElementById('latitud').value = event.latLng.lat();
                document.getElementById('longitud').value = event.latLng.lng();
            });

            // Evento al hacer clic en el mapa
            map.addListener('click', function(event) {
                marker.setPosition(event.latLng);
                document.getElementById('latitud').value = event.latLng.lat();
                document.getElementById('longitud').value = event.latLng.lng();
            });
        }

        // Llama a la función initMap cuando la página se cargue
        window.onload = initMap;
    </script>
</body>
</html>
