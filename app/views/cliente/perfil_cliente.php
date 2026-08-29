<?php

// Seguridad básica
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php?controller=auth&action=loginCliente");
    exit;
}

$nombre = $_SESSION['nombre'] ?? 'Cliente';
$apellido = $_SESSION['apellido'] ?? '';
$rol    = $_SESSION['nombre_rol'] ?? 'cliente';
$servicios = $servicios ?? [];
$barberos = $barberos ?? [];
$historial = $historial ?? [];

// Rutas de fotos para el carrusel
$fotosCortes = [
    ["src" => "app/public/assets/img/foto1.png", "alt" => "Corte clásico"],
    ["src" => "app/public/assets/img/foto2.png", "alt" => "Fade moderno"],
    ["src" => "app/public/assets/img/foto3.png", "alt" => "Barba profesional"],
    ["src" => "app/public/assets/img/foto4.png", "alt" => "Estilo premium"]
];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Perfil Cliente - Kadosh Barber</title>
    <link rel="stylesheet" href="app/public/css/perfilCliente.css">
    <link rel="stylesheet" href="app/public/css/index.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
</head>

<body>

    <div class="barra">
        HOLA, <?php echo htmlspecialchars($nombre . ' ' . $apellido); ?>! 💈
    </div>

    <div class="perfil">

        <!-- Usuario -->
        <div class="card info-usuario">
            <img src="app/public/assets/img/logo1.jpeg" class="avatar">

            <h2><?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></h2>
            <p class="rol"><?php echo strtoupper(htmlspecialchars($rol)); ?></p>

            <div class="acciones">
                <a href="index.php?controller=gestionCita&action=agendarCita" class="btn btn-primary"><button type="button">AGENDAR</button></a>
                <a href="index.php?controller=password&action=resetPassword" class="btn"><button type="button" class="btn-outline">Cambiar contraseña</button></a>
            </div>

            <a href="index.php?controller=auth&action=logout">Cerrar sesión</a>
        </div>


        <!-- CARRUSEL -->
        <section class="carousel-section">
            <h2 class="carousel-title">Nuestro Estilo</h2>

            <div class="carousel">
                <button class="carousel-btn prev" onclick="moverCarruselCliente(-1)">&#10094;</button>

                <div class="carousel-track-container">
                    <div class="carousel-track" id="carouselTrackCliente">
                        <?php foreach ($fotosCortes as $foto): ?>
                            <div class="carousel-item">
                                <img src="<?php echo htmlspecialchars($foto['src']); ?>" alt="<?php echo htmlspecialchars($foto['alt']); ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button class="carousel-btn next" onclick="moverCarruselCliente(1)">&#10095;</button>
            </div>
        </section>

        <!-- Dirección -->
        <div class="card direccion">
            <h3>DIRECCIÓN</h3>
            <p>
                Cra 99 #999-99<br>
                2do piso – Soacha
            </p>

            <a href="#">Ver en el mapa</a>

            <button
                type="button"
                class="btn-historial"
                onclick="abrirModalHistorial()">
                📋 Ver historial
            </button>
        </div>
    </div>

    <div class="panel" id="panel-reservas">
        <h2 class="section-title">Mis Reservas</h2>
        <br>
        <div class="section-card">
            <table class="data-table" id="tablaCitas">
                <thead>
                    <tr>
                        <th>Barbero</th>
                        <th>Servicio</th>
                        <th>Producto</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($citas)): ?>
                        <?php foreach ($citas as $c): ?>
                            <?php
                            // Formatear el valor total recibido desde la consulta SQL
                            $total = isset($c['total']) ? '$' . number_format($c['total'], 0, ',', '.') : '$0';
                            $producto = !empty($c['producto']) ? $c['producto'] : 'Ninguno';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($c['barbero']) ?></td>
                                <td><?= htmlspecialchars($c['servicio']) ?></td>
                                <td><?= htmlspecialchars($producto) ?></td>
                                <td><?= htmlspecialchars($c['fecha_cita']) ?></td>
                                <td><?= htmlspecialchars($c['hora_cita']) ?></td>
                                <td><strong><?= htmlspecialchars($total) ?></strong></td>
                                <td>
                                    <?php if ($c['estado'] == 'Pendiente'): ?>
                                        <span style="color:orange;font-weight:bold;">
                                            🟡 Pendiente
                                        </span>
                                    <?php elseif ($c['estado'] == 'Cancelada'): ?>
                                        <span style="color:red;font-weight:bold;">
                                            🔴 Cancelada
                                        </span>
                                    <?php elseif ($c['estado'] == 'Completada'): ?>
                                        <span style="color:green;font-weight:bold;">
                                            🟢 Completada
                                        </span>
                                    <?php else: ?>
                                        <?= htmlspecialchars($c['estado']) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($c['estado'] == 'Pendiente'): ?>

                                        <!-- CANCELAR -->
                                        <a class="cancelar-cita"
                                            href="index.php?controller=gestionCita&action=cancelar&id=<?= htmlspecialchars($c['id_reservacion']) ?>"
                                            onclick="return confirm('¿Desea cancelar esta reserva?')"
                                            title="Cancelar">
                                            <img src="app/public/assets/icons/cancelarcita.png"
                                                alt="Cancelar">
                                        </a>

                                        <!-- EDITAR -->
                                        <a href="#"
                                            class="btn-editar-cita"
                                            onclick="abrirModalEditar(
                                            '<?= htmlspecialchars($c['id_reservacion']) ?>',
                                            '<?= htmlspecialchars($c['id_barbero']) ?>',
                                            '<?= htmlspecialchars($c['id_servicio']) ?>',
                                            '<?= htmlspecialchars($c['fecha_cita']) ?>',
                                            '<?= htmlspecialchars($c['hora_cita']) ?>'
                                            ); return false;"
                                            title="Editar">
                                            <img src="app/public/assets/icons/editar.png" alt="Editar">
                                        </a>

                                        <!-- FINALIZAR -->
                                        <button type="button" class="btn-finalizar" onclick="abrirModalFinalizar('<?= htmlspecialchars($c['id_reservacion']) ?>','<?= htmlspecialchars($c['id_barbero']) ?>')"
                                            title="Finalizar">
                                            <img src="app/public/assets/icons/finalizarReservacion.png" alt="Finalizar">
                                        </button>

                                    <?php elseif ($c['estado'] == 'Cancelada'): ?>

                                        <span style="color:gray;">
                                            Sin acciones
                                        </span>

                                    <?php elseif ($c['estado'] == 'Completada'): ?>

                                        <span style="color:green;">
                                            ✓ Finalizada
                                        </span>

                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align:center;">No tienes reservas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalEditarCita" class="modal-editar-cita">

        <div class="contenido-modal">

            <button type="button" class="cerrar-modal" onclick="cerrarModalEditar()">
                &times;
            </button>

            <h2>Actualizar reservación</h2>
            <div id="horariosEditar">
                <p>Seleccione servicio, barbero y fecha.</p>
            </div>
            <br>
            <p>
                <strong>Reserva:</strong>
                <span id="idReservaEditar"></span>
            </p>

            <form id="formEditarCita" method="POST" action="index.php?controller=gestionCita&action=actualizarCita">

                <input type="hidden" name="id_reservacion" id="idReservacionEditar">
                <!-- SERVICIO -->

                <label for="servicioEditar">Servicio:</label>

                <select name="servicio" id="servicioEditar" required>
                    <option value="">Seleccione un servicio</option>

                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?= htmlspecialchars($servicio['id_servicio']) ?>">
                            <?= htmlspecialchars($servicio['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <br>

                <!-- BARBERO -->

                <label for="barberoEditar">Barbero:</label>

                <select name="barbero" id="barberoEditar" required>

                    <option value="">Seleccione un barbero:</option>

                    <?php foreach ($barberos as $barbero): ?>

                        <option value="<?= htmlspecialchars($barbero['id_usuario']) ?>">
                            <?= htmlspecialchars(
                                $barbero['nombre'] . ' ' . $barbero['apellido']
                            ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <br>
                <!-- FECHA -->

                <label for="fechaEditar">Fecha:</label>

                <input type="date" name="fecha" id="fechaEditar" required>
                <!-- HORARIOS -->
                <br>
                <label>Hora disponible:</label>
                <input type="hidden" name="hora" id="horaEditar" required>
                <br>
                <button type="submit">Guardar cambios</button>
            </form>
        </div>
    </div>

    <!-- =========================================
     MODAL HISTORIAL DE RESERVAS
========================================= -->

    <div id="modalHistorial" class="modal-historial">

        <div class="contenido-modal-historial">

            <!-- CERRAR -->
            <button
                type="button"
                class="cerrar-modal-historial"
                onclick="cerrarModalHistorial()">
                &times;
            </button>
            <h2>Historial de reservas 📋</h2>
            <p class="subtitulo-historial">
                Aquí puedes consultar tus reservas anteriores.
            </p>
            <div class="tabla-historial">
                <table>
                    <!-- TABLA EN EL MODAL HISTORIAL -->
                    <thead>
                        <tr>
                            <th>Barbero</th>
                            <th>Servicio</th>
                            <th>Producto</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acción</th> <!-- Columna añadida -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($historial)): ?>
                            <?php if (!empty($historial)): ?>
                                <?php foreach ($historial as $h): ?>
                                    <?php
                                    // Formateamos el total que calculó directamente la consulta SQL
                                    $totalFormateado = '$' . number_format($h['total'], 0, ',', '.');
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($h['barbero']) ?></td>
                                        <td><?= htmlspecialchars($h['servicio']) ?></td>
                                        <td><?= htmlspecialchars($h['producto']) ?></td>
                                        <td><?= htmlspecialchars($h['fecha_cita']) ?></td>
                                        <td><?= htmlspecialchars($h['hora_cita']) ?></td>
                                        <td><strong><?= htmlspecialchars($totalFormateado) ?></strong></td>
                                        <td>
                                            <?php if ($h['estado'] == 'Completada'): ?>
                                                <span class="estado-completada">🟢 Completada</span>
                                            <?php else: ?>
                                                <span class="estado-cancelada">🔴 Cancelada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button"
                                                class="btn-descargar-pdf"
                                                onclick="generarPDFReserva(
                                                '<?= htmlspecialchars($h['barbero'], ENT_QUOTES) ?>', 
                                                '<?= htmlspecialchars($h['servicio'], ENT_QUOTES) ?>', 
                                                '<?= htmlspecialchars($h['producto'], ENT_QUOTES) ?>', 
                                                '<?= htmlspecialchars($h['fecha_cita'], ENT_QUOTES) ?>', 
                                                '<?= htmlspecialchars($h['hora_cita'], ENT_QUOTES) ?>', 
                                                '<?= htmlspecialchars($totalFormateado, ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($h['estado'], ENT_QUOTES) ?>')"
                                                title="Descargar Comprobante PDF">

                                                <img src="app/public/assets/icons/descargar.png" alt="Descargar">
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="sin-historial">No tienes reservas en tu historial.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- =========================================
     MODAL FINALIZAR CITA
========================================= -->

    <div id="modalFinalizar" class="modal-finalizar">

        <div class="contenido-modal-finalizar">

            <button type="button" class="cerrar-modal-finalizar" onclick="cerrarModalFinalizar()">
                &times;
            </button>

            <h2>¡Cita finalizada! 🎉</h2>
            <p>¿Te gustaría calificar nuestro servicio?</p>

            <div class="botones-finalizar">
                <button type="button" onclick="mostrarModalResena()">Sí</button>
                <form method="POST" action="index.php?controller=gestionCita&action=finalizar">
                    <input type="hidden" name="id_reservacion" id="idReservaFinalizar">
                    <button type="submit"> No </button>
                </form>

            </div>

        </div>

    </div>

    <!-- =========================================
     MODAL RESEÑA
========================================= -->

    <div id="modalResena" class="modal-resena">

        <div class="contenido-modal-resena">

            <button type="button" class="cerrar-modal-resena" onclick="cerrarModalResena()">
                &times;
            </button>

            <h2>Califica nuestro servicio</h2>

            <p>¿Cómo fue tu experiencia?</p>

            <form method="POST" action="index.php?controller=gestionCita&action=guardarResena">

                <input type="hidden" name="id_reservacion" id="idReservaResena">
                <input type="hidden" name="id_barbero" id="idBarberoResena">
                <label>Calificación</label>

                <select name="calificacion" required>

                    <option value="">Selecciona una calificación</option>

                    <option value="5">
                        ⭐⭐⭐⭐⭐ Excelente
                    </option>

                    <option value="4">
                        ⭐⭐⭐⭐ Muy bueno
                    </option>

                    <option value="3">
                        ⭐⭐⭐ Bueno
                    </option>

                    <option value="2">
                        ⭐⭐ Regular
                    </option>

                    <option value="1">
                        ⭐ Malo
                    </option>

                </select>


                <label>Comentario</label>

                <textarea name="comentario" placeholder="Cuéntanos tu experiencia..." rows="4"></textarea>

                <button type="submit">Publicar reseña</button>

            </form>

        </div>

    </div>


    <footer class="footer">

        <div class="footer-container">

            <div class="footer-section">
                <h4>Enlaces</h4>

                <a href="index.php">Inicio</a>
                <a href="#">Servicios</a>
                <a href="#">Contacto</a>
                <a href="#">Política de Privacidad</a>
            </div>

            <div class="footer-section">
                <h4>Contacto</h4>

                <p>📍 Bogotá - Colombia</p>
                <p>📞 +57 300 359 3276</p>
                <p>✉️ kadosh1234@gmail.com</p>
            </div>

            <div class="footer-section">
                <h4>Desarrollado por:</h4>

                <p>Daniela Yara, Laura Buitrago, Juan Acuña, Juan Mulcue, Jose Cuastumal</p>

                <br>

                <p><strong>SENA - ADSO</strong></p>
                <p>Ficha: 3171693</p>
            </div>

        </div>

        <div class="footer-bottom">
            <p>
                © 2026 <strong>KADOSH Barber Shop</strong>. Todos los derechos reservados.
                | Versión 1.0
            </p>
        </div>

    </footer>

    <script>
        // Paleta de colores corporativos
        const coloresKadosh = {
            morado: '#4A154B',
            dorado: '#D4AF37',
            negro: '#1A1A1A'
        };

        // Carga previa de la imagen en Base64 para el reporte
        const logoBase64 = 'data:image/jpeg;base64,<?= base64_encode(file_get_contents("app/public/assets/img/logo1.jpeg")); ?>';
        const nombreCliente = '<?= htmlspecialchars($nombre . " " . $apellido, ENT_QUOTES); ?>';

        function generarPDFReserva(barbero, servicio, producto, fecha, hora, total, estado) {
            // Si la variable llega vacía o indefinida, mostramos 'Ninguno'
            const productoTexto = (producto && producto.trim() !== '') ? producto : 'Ninguno';

            const docDefinition = {
                pageSize: 'A4',
                pageOrientation: 'landscape',
                pageMargins: [35, 40, 35, 50],
                content: [
                    /* LOGO KADOSH */
                    {
                        image: logoBase64,
                        width: 70,
                        height: 70,
                        alignment: 'center',
                        margin: [0, 0, 0, 8]
                    },
                    {
                        text: 'KADOSH',
                        fontSize: 23,
                        bold: true,
                        color: coloresKadosh.morado,
                        alignment: 'center',
                        margin: [0, 0, 0, 2]
                    },
                    {
                        text: 'BARBER SHOP',
                        fontSize: 10,
                        bold: true,
                        color: coloresKadosh.dorado,
                        alignment: 'center',
                        characterSpacing: 3,
                        margin: [0, 0, 0, 5]
                    },
                    {
                        text: 'Comprobante de Reserva - ' + nombreCliente,
                        fontSize: 11,
                        bold: true,
                        color: coloresKadosh.negro,
                        alignment: 'center',
                        margin: [0, 3, 0, 10]
                    },
                    {
                        canvas: [{
                            type: 'line',
                            x1: 0,
                            y1: 0,
                            x2: 770,
                            y2: 0,
                            lineWidth: 2,
                            lineColor: coloresKadosh.dorado
                        }],
                        margin: [0, 0, 0, 15]
                    },

                    /* TABLA DE DETALLES */
                    {
                        alignment: 'center',
                        margin: [0, 10, 0, 10],
                        table: {
                            widths: ['*', '*', '*', '*', '*', '*'],
                            body: [
                                [{
                                        text: 'Barbero',
                                        bold: true,
                                        color: '#ffffff',
                                        fillColor: coloresKadosh.morado,
                                        alignment: 'center'
                                    },
                                    {
                                        text: 'Servicio',
                                        bold: true,
                                        color: '#ffffff',
                                        fillColor: coloresKadosh.morado,
                                        alignment: 'center'
                                    },
                                    {
                                        text: 'Producto',
                                        bold: true,
                                        color: '#ffffff',
                                        fillColor: coloresKadosh.morado,
                                        alignment: 'center'
                                    },
                                    {
                                        text: 'Fecha',
                                        bold: true,
                                        color: '#ffffff',
                                        fillColor: coloresKadosh.morado,
                                        alignment: 'center'
                                    },
                                    {
                                        text: 'Hora',
                                        bold: true,
                                        color: '#ffffff',
                                        fillColor: coloresKadosh.morado,
                                        alignment: 'center'
                                    },
                                    {
                                        text: 'Estado',
                                        bold: true,
                                        color: '#ffffff',
                                        fillColor: coloresKadosh.morado,
                                        alignment: 'center'
                                    }
                                ],
                                [{
                                        text: barbero,
                                        alignment: 'center'
                                    },
                                    {
                                        text: servicio,
                                        alignment: 'center'
                                    },
                                    {
                                        text: productoTexto,
                                        alignment: 'center'
                                    }, // Se muestra el producto o "Ninguno"
                                    {
                                        text: fecha,
                                        alignment: 'center'
                                    },
                                    {
                                        text: hora,
                                        alignment: 'center'
                                    },
                                    {
                                        text: estado,
                                        alignment: 'center'
                                    }
                                ],
                                /* TOTAL DE LA COMPRA (SUMA DE SERVICIO + PRODUCTO SI LO HAY) */
                                [{
                                        text: 'TOTAL A PAGAR',
                                        colSpan: 5,
                                        bold: true,
                                        alignment: 'right',
                                        fillColor: '#F5F5F5'
                                    },
                                    {}, {}, {}, {},
                                    {
                                        text: total,
                                        bold: true,
                                        color: coloresKadosh.morado,
                                        alignment: 'center',
                                        fillColor: '#F5F5F5'
                                    }
                                ]
                            ]
                        },
                        layout: {
                            hLineWidth: () => 1,
                            vLineWidth: () => 1,
                            hLineColor: () => coloresKadosh.dorado,
                            vLineColor: () => coloresKadosh.dorado,
                            paddingLeft: () => 8,
                            paddingRight: () => 8,
                            paddingTop: () => 7,
                            paddingBottom: () => 7
                        }
                    }
                ]
            };

            pdfMake.createPdf(docDefinition).download('Kadosh_' + nombreCliente.replace(/\s+/g, '_') + '_Reserva.pdf');
        }

        function abrirModalFinalizar(idReserva, idBarbero) {
            document.getElementById('idReservaFinalizar').value = idReserva;

            document.getElementById('idReservaResena').value = idReserva;

            document.getElementById('idBarberoResena').value = idBarbero;


            document.getElementById('modalFinalizar')
                .classList.add('activo');
        }


        function cerrarModalFinalizar() {
            document.getElementById('modalFinalizar')
                .classList.remove('activo');
        }


        function mostrarModalResena() {
            document.getElementById('modalFinalizar')
                .classList.remove('activo');


            document.getElementById('modalResena')
                .classList.add('activo');
        }


        function cerrarModalResena() {
            document.getElementById('modalResena')
                .classList.remove('activo');
        }
    </script>
    <script>
        function abrirModalHistorial() {
            document.getElementById('modalHistorial')
                .classList.add('activo');
        }

        function cerrarModalHistorial() {
            document.getElementById('modalHistorial')
                .classList.remove('activo');
        }
    </script>
    <script>
        function abrirModalEditar(
            idReserva,
            idBarbero,
            idServicio,
            fecha,
            hora
        ) {

            // Mostrar datos de la reserva
            document.getElementById('idReservaEditar').textContent = idReserva;

            document.getElementById('idReservacionEditar').value = idReserva;

            // Seleccionar servicio actual
            document.getElementById('servicioEditar').value = idServicio;

            // Seleccionar barbero actual
            document.getElementById('barberoEditar').value = idBarbero;

            // Seleccionar fecha actual
            document.getElementById('fechaEditar').value = fecha;

            // Guardar hora actual
            document.getElementById('horaEditar').value = hora;

            // Mostrar modal
            document.getElementById('modalEditarCita')
                .classList.add('activo');
        }


        function cerrarModalEditar() {

            document.getElementById('modalEditarCita')
                .classList.remove('activo');

        }

        /* LÓGICA DE MOVIMIENTO DEL CARRUSEL */
        let posCliente = 0;

        function moverCarruselCliente(direccion) {
            const track = document.getElementById('carouselTrackCliente');
            const items = track.querySelectorAll('.carousel-item');
            const total = items.length;

            // Determina la cantidad visible según el ancho de pantalla
            const visibles = window.innerWidth <= 600 ? 1 : 2;
            const maxPos = total - visibles;

            posCliente += direccion;

            if (posCliente < 0) {
                posCliente = maxPos > 0 ? maxPos : 0;
            } else if (posCliente > maxPos) {
                posCliente = 0;
            }

            const desplazamiento = -(posCliente * (100 / visibles));
            track.style.transform = `translateX(${desplazamiento}%)`;
        }
    </script>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>