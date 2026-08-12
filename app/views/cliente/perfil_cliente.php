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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Perfil Cliente - Kadosh Barber</title>
    <link rel="stylesheet" href="app/public/css/perfilCliente.css">
    <link rel="stylesheet" href="app/public/css/index.css">
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
                <a href="index.php?controller=gestionCita&action=agendarCita" class="btn btn-primary"><button>AGENDAR</button></a>
                <a href="index.php?controller=auth&action=resetPassword" class="btn"><button class="btn-outline">Cambiar contraseña</button></a>
            </div>

            <a href="index.php?controller=auth&action=logout">Cerrar sesión</a>
        </div>


        <!-- CARRUSEL -->
        <section class="carousel-section">
            <h2 class="carousel-title">Nuestro Estilo</h2>

            <div class="carousel">
                <div class="carousel-track">
                    <div class="carousel-item">
                        <img src="app/public/assets/img/foto1.png" alt="Corte clásico">
                    </div>
                    <div class="carousel-item">
                        <img src="app/public/assets/img/foto2.png" alt="Fade moderno">
                    </div>
                    <div class="carousel-item">
                        <img src="app/public/assets/img/foto3.png" alt="Barba profesional">
                    </div>
                    <div class="carousel-item">
                        <img src="app/public/assets/img/foto4.png" alt="Estilo premium">
                    </div>
                </div>
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
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($citas)): ?>

                        <?php foreach ($citas as $c): ?>

                            <tr>

                                <td><?= htmlspecialchars($c['barbero']) ?></td>

                                <td><?= htmlspecialchars($c['servicio']) ?></td>

                                <td><?= htmlspecialchars($c['fecha_cita']) ?></td>

                                <td><?= htmlspecialchars($c['hora_cita']) ?></td>

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
                                        <button
                                            type="button"
                                            class="btn-finalizar"
                                            onclick="abrirModalFinalizar('<?= htmlspecialchars($c['id_reservacion']) ?>','<?= htmlspecialchars($c['id_barbero']) ?>')"
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
                            <td colspan="6" style="text-align:center;">
                                No tienes reservas registradas.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

    <div id="modalEditarCita" class="modal-editar-cita">

        <div class="contenido-modal">

            <button type="button"
                class="cerrar-modal"
                onclick="cerrarModalEditar()">
                &times;
            </button>

            <h2>Actualizar reservación</h2>
            <div id="horariosEditar">
                <p>
                    Seleccione servicio, barbero y fecha.
                </p>
            </div>
            <br>
            <p>
                <strong>Reserva:</strong>
                <span id="idReservaEditar"></span>
            </p>

            <form id="formEditarCita"
                method="POST"
                action="index.php?controller=gestionCita&action=actualizarCita">

                <input type="hidden"
                    name="id_reservacion"
                    id="idReservacionEditar">


                <!-- SERVICIO -->

                <label for="servicioEditar">
                    Servicio:
                </label>

                <select name="servicio"
                    id="servicioEditar"
                    required>

                    <option value="">
                        Seleccione un servicio
                    </option>

                    <?php foreach ($servicios as $servicio): ?>

                        <option value="<?= htmlspecialchars($servicio['id_servicio']) ?>">
                            <?= htmlspecialchars($servicio['nombre']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <br>
                <!-- BARBERO -->

                <label for="barberoEditar">
                    Barbero:
                </label>

                <select name="barbero"
                    id="barberoEditar"
                    required>

                    <option value="">
                        Seleccione un barbero:
                    </option>

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

                <label for="fechaEditar">
                    Fecha:
                </label>

                <input type="date"
                    name="fecha"
                    id="fechaEditar"
                    required>


                <!-- HORARIOS -->
                <br>
                <label>
                    Hora disponible:
                </label>


                <input type="hidden"
                    name="hora"
                    id="horaEditar"
                    required>

                <br>
                <button type="submit">
                    Guardar cambios
                </button>

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

                    <thead>
                        <tr>
                            <th>Barbero</th>
                            <th>Servicio</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (!empty($historial)): ?>

                            <?php foreach ($historial as $h): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($h['barbero']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($h['servicio']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($h['fecha_cita']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($h['hora_cita']) ?>
                                    </td>

                                    <td>

                                        <?php if ($h['estado'] == 'Completada'): ?>

                                            <span class="estado-completada">
                                                🟢 Completada
                                            </span>

                                        <?php elseif ($h['estado'] == 'Cancelada'): ?>

                                            <span class="estado-cancelada">
                                                🔴 Cancelada
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="5" class="sin-historial">
                                    No tienes reservas en tu historial.
                                </td>
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

            <button
                type="button"
                class="cerrar-modal-finalizar"
                onclick="cerrarModalFinalizar()">

                &times;

            </button>


            <h2>¡Cita finalizada! 🎉</h2>

            <p>
                ¿Te gustaría calificar nuestro servicio?
            </p>


            <div class="botones-finalizar">

                <!-- SI -->
                <button
                    type="button"
                    onclick="mostrarModalResena()">

                    ⭐ Sí

                </button>


                <!-- NO -->
                <form
                    method="POST"
                    action="index.php?controller=gestionCita&action=finalizar">

                    <input
                        type="hidden"
                        name="id_reservacion"
                        id="idReservaFinalizar">

                    <button type="submit">

                        No

                    </button>

                </form>

            </div>

        </div>

    </div>

    <!-- =========================================
     MODAL RESEÑA
========================================= -->

    <div id="modalResena" class="modal-resena">

        <div class="contenido-modal-resena">

            <button
                type="button"
                class="cerrar-modal-resena"
                onclick="cerrarModalResena()">

                &times;

            </button>


            <h2>Califica nuestro servicio ⭐</h2>

            <p>
                ¿Cómo fue tu experiencia?
            </p>


            <form
                method="POST"
                action="index.php?controller=gestionCita&action=guardarResena">


                <input
                    type="hidden"
                    name="id_reservacion"
                    id="idReservaResena">


                <input
                    type="hidden"
                    name="id_barbero"
                    id="idBarberoResena">


                <label>
                    Calificación
                </label>


                <select
                    name="calificacion"
                    required>

                    <option value="">
                        Selecciona una calificación
                    </option>

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


                <label>
                    Comentario
                </label>


                <textarea
                    name="comentario"
                    placeholder="Cuéntanos tu experiencia..."
                    rows="4"></textarea>


                <button type="submit">

                    Publicar reseña

                </button>

            </form>

        </div>

    </div>


    <script>
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
    </script>
</body>