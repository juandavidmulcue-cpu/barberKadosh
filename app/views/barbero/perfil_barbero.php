<?php
// Seguridad básica
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php?controller=auth&action=loginBarbero");
    exit;
}

$barbero = $barbero ?? [];
$nombreBarbero = htmlspecialchars($barbero['nombre'] ?? 'Barbero');
$totalReservas = $totalReservas ?? 0;
$totalResenas = $totalResenas ?? 0;
$promedio = $promedio ?? 0;
$historial = $historial ?? [];

// Ejemplos de fotos para el carrusel
$fotosCortes = [
    "app/public/assets/img/foto1.png",
    "app/public/assets/img/foto2.png",
    "app/public/assets/img/foto3.png",
    "app/public/assets/img/foto4.png",
    "app/public/assets/img/logo.jpeg"
];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Barbero - Kadosh Barber</title>
    <link rel="stylesheet" href="app/public/css/perfilBarbero.css">
</head>

<body>

    <!-- =========================
         HEADER PRINCIPAL
    ========================== -->
    <header class="header-barberia">
        <div class="contenedor-header">
            <div class="logo">
                <!-- Imagen del logo agregada -->
                <img src="app/public/assets/img/logo1.jpeg" alt="Logo Kadosh Barber" class="logo-img">
                <h2>KADOSH <span>BARBER</span></h2>
            </div>
            <nav class="nav-barberia">
                <a href="index.php?controller=auth&action=logout" class="btn-logout">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL MAS AMPLIO -->
    <div class="contenedorPerfil">

        <!-- =========================
             CARRUSEL DE TRABAJOS
        ========================== -->
        <section class="carrusel-seccion card">
            <h2>Galería de Cortes y Estilos</h2>
            <div class="carrusel-contenedor">
                <button class="carrusel-btn prev" onclick="moverCarrusel(-1)">&#10094;</button>

                <div class="carrusel-track-container">
                    <div class="carrusel-track" id="carruselTrack">
                        <?php foreach ($fotosCortes as $index => $foto): ?>
                            <div class="carrusel-slide">
                                <img src="<?php echo htmlspecialchars($foto); ?>" alt="Corte <?php echo $index + 1; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button class="carrusel-btn next" onclick="moverCarrusel(1)">&#10095;</button>
            </div>
        </section>

        <!-- =========================
     TARJETA DE PERFIL
========================== -->
        <section class="perfil">
            <div class="imagen">
                <img src="app/public/assets/img/fotobarbero.jpg" class="avatar" alt="Foto del barbero">
            </div>

            <div class="informacion">
                <!-- Saludo motivador dinámico integrado -->
                <div class="mensaje-bienvenida">
                    <h2>¡Hola, Barber <?php echo $nombreBarbero; ?></h2>
                    <p class="subtitulo-motivador">Bienvenido de nuevo a un día más de trabajo. Cada corte es una obra de arte, ¡a dar la mejor actitud hoy!</p>
                </div>

                <hr class="divisor-perfil">

                <div class="detalles-contacto">
                    <p><strong>Nombre completo:</strong> <?php echo htmlspecialchars(($barbero['nombre'] ?? 'Barbero') . ' ' . ($barbero['apellido'] ?? '')); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($barbero['telefono'] ?? 'No disponible'); ?></p>
                    <p><strong>Correo:</strong> <?php echo htmlspecialchars($barbero['correo'] ?? 'No disponible'); ?></p>
                </div>

            </div>

            <!-- ESTADÍSTICAS -->
            <div class="estadisticas">
                <button class="stat activo" id="btnReservas" onclick="mostrarSeccion('reservas')">
                    <h3><?php echo $totalReservas; ?></h3>
                    <span>Reservas</span>
                </button>

                <button class="stat" id="btnHistorial" onclick="mostrarSeccion('historial')">
                    <h3><?php echo count($historial); ?></h3>
                    <span>Historial</span>
                </button>

                <button class="stat" id="btnResenas" onclick="mostrarSeccion('resenas')">
                    <h3><?php echo $totalResenas; ?></h3>
                    <span>Reseñas</span>
                </button>

                <div class="stat">
                    <h3><?php echo number_format((float)$promedio, 1); ?> / 5</h3>
                    <div class="estrellasPromedio">
                        <?php
                        $promedioRedondeado = round((float)$promedio);
                        for ($i = 1; $i <= 5; $i++) {
                            echo ($i <= $promedioRedondeado) ? '<span>⭐</span>' : '<span>☆</span>';
                        }
                        ?>
                    </div>
                    <span>Calificación</span>
                </div>
            </div>
        </section>

        <!-- =========================
             HORARIOS DE TRABAJO
        ========================== -->
        <section class="card" id="horarios">
            <h2>Horarios de Trabajo</h2>
            <div class="horarios">
                <?php if (!empty($horarios)) { ?>
                    <?php foreach ($horarios as $hora) { ?>
                        <div class="horario">
                            <span>🕒</span>
                            <p>
                                <strong><?php echo date('d/m/Y', strtotime($hora['fecha'])); ?></strong><br>
                                <?php echo date('h:i A', strtotime($hora['hora_inicio'])); ?> -
                                <?php echo date('h:i A', strtotime($hora['hora_fin'])); ?>
                            </p>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p class="sinDatos">No tienes horarios registrados.</p>
                <?php } ?>
            </div>
        </section>

        <!-- =========================
             RESERVACIONES
        ========================== -->
        <section class="card" id="reservas">
            <h2>Reservaciones</h2>
            <?php if (!empty($reservaciones)) { ?>
                <div class="tablaResponsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservaciones as $r) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($r['cliente']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($r['fecha_cita'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($r['hora_cita'])); ?></td>
                                    <td>
                                        <?php if ($r['estado'] === 'Pendiente') { ?>
                                            <span class="estado pendiente">🟡 Pendiente</span>
                                        <?php } elseif ($r['estado'] === 'Cancelada') { ?>
                                            <span class="estado cancelada">🔴 Cancelada</span>
                                        <?php } elseif ($r['estado'] === 'Completada') { ?>
                                            <span class="estado completada">🟢 Completada</span>
                                        <?php } else { ?>
                                            <span class="estado"><?php echo htmlspecialchars($r['estado']); ?></span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="sinDatos">
                    <p>No tienes reservaciones actualmente.</p>
                </div>
            <?php } ?>
        </section>

        <!-- =========================
             HISTORIAL
        ========================== -->
        <section class="card" id="historial" style="display:none;">
            <h2>Historial de Reservaciones</h2>
            <?php if (!empty($historial)) { ?>
                <div class="tablaResponsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Servicio</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $h) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($h['cliente']); ?></td>
                                    <td><?php echo htmlspecialchars($h['servicio']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($h['fecha_cita'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($h['hora_cita'])); ?></td>
                                    <td>
                                        <?php if ($h['estado'] === 'Completada') { ?>
                                            <span class="estado completada">🟢 Completada</span>
                                        <?php } elseif ($h['estado'] === 'Cancelada') { ?>
                                            <span class="estado cancelada">🔴 Cancelada</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="sinDatos">
                    <p>No tienes reservaciones en tu historial.</p>
                </div>
            <?php } ?>
        </section>

        <!-- =========================
             RESEÑAS
        ========================== -->
        <section class="card" id="resenas" style="display:none;">
            <h2>Reseñas de Clientes</h2>
            <?php if (!empty($reseñas)) { ?>
                <div class="contenedorResenas">
                    <?php foreach ($reseñas as $r) { ?>
                        <div class="review">
                            <h3><?php echo htmlspecialchars($r['usuario']); ?></h3>
                            <div class="estrellas">
                                <?php
                                $calificacion = (int)$r['calificacion'];
                                for ($i = 1; $i <= 5; $i++) {
                                    echo ($i <= $calificacion) ? '<span>⭐</span>' : '<span>☆</span>';
                                }
                                ?>
                            </div>
                            <p><?php echo htmlspecialchars($r['comentario']); ?></p>
                            <?php if (!empty($r['fecha'])) { ?>
                                <small><?php echo date('d/m/Y', strtotime($r['fecha'])); ?></small>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="sinDatos">
                    <p>Este barbero todavía no tiene reseñas.</p>
                </div>
            <?php } ?>
        </section>

    </div>

    <!-- JS Secciones Tab -->
    <script>
        function mostrarSeccion(opcion) {
            const reservas = document.getElementById("reservas");
            const historial = document.getElementById("historial");
            const resenas = document.getElementById("resenas");

            const btnReservas = document.getElementById("btnReservas");
            const btnHistorial = document.getElementById("btnHistorial");
            const btnResenas = document.getElementById("btnResenas");

            reservas.style.display = "none";
            historial.style.display = "none";
            resenas.style.display = "none";

            btnReservas.classList.remove("activo");
            btnHistorial.classList.remove("activo");
            btnResenas.classList.remove("activo");

            if (opcion === "reservas") {
                reservas.style.display = "block";
                btnReservas.classList.add("activo");
            }
            if (opcion === "historial") {
                historial.style.display = "block";
                btnHistorial.classList.add("activo");
            }
            if (opcion === "resenas") {
                resenas.style.display = "block";
                btnResenas.classList.add("activo");
            }
        }

        let posicionActual = 0;

        function moverCarrusel(direccion) {
            const track = document.getElementById('carruselTrack');
            const slides = document.querySelectorAll('.carrusel-slide');
            const totalSlides = slides.length;

            const visibles = window.innerWidth <= 768 ? 1 : (window.innerWidth <= 1024 ? 2 : 4);
            const maxPosicion = totalSlides - visibles;

            posicionActual += direccion;

            if (posicionActual < 0) {
                posicionActual = maxPosicion > 0 ? maxPosicion : 0;
            } else if (posicionActual > maxPosicion) {
                posicionActual = 0;
            }

            const porcentaje = -(posicionActual * (100 / visibles));
            track.style.transform = `translateX(${porcentaje}%)`;
        }

        window.onload = function() {
            mostrarSeccion("reservas");
        };
    </script>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>