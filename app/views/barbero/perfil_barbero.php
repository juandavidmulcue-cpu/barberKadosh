<?php
// Seguridad básica
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php?controller=auth&action=loginBarbero");
    exit;
}

$barbero = $barbero ?? [];
$nombreBarbero = htmlspecialchars($barbero['nombre'] ?? $_SESSION['nombre'] ?? 'Barbero');
$apellidoBarbero = htmlspecialchars($barbero['apellido'] ?? $_SESSION['apellido'] ?? '');
$telefonoBarbero = htmlspecialchars($barbero['telefono'] ?? $_SESSION['telefono'] ?? 'No disponible');
$correoBarbero = htmlspecialchars($barbero['correo'] ?? $_SESSION['correo'] ?? 'No disponible');
$fotoBarbero = htmlspecialchars($barbero['foto'] ?? $_SESSION['foto'] ?? 'app/public/assets/img/fotobarbero.jpg');

$totalReservas = $totalReservas ?? 0;
$totalResenas = $totalResenas ?? 0;
$promedio = $promedio ?? 0;
$historial = $historial ?? [];
$horarios = $horarios ?? [];
$reservaciones = $reservaciones ?? [];

// Fotos para el carrusel
$fotosCortes = [
    "app/public/assets/img/foto1.png",
    "app/public/assets/img/foto2.png",
    "app/public/assets/img/foto3.png",
    "app/public/assets/img/foto4.png",
    "app/public/assets/img/logo.jpeg"
];

// Mapear los horarios de trabajo del barbero a FullCalendar
$eventosCalendario = [];
foreach ($horarios as $h) {
    $eventosCalendario[] = [
        'id'    => $h['id_horario'] ?? null,
        'start' => ($h['fecha'] ?? '') . 'T' . ($h['hora_inicio'] ?? ''),
        'end'   => ($h['fecha'] ?? '') . 'T' . ($h['hora_fin'] ?? ''),
        'color' => '#3d2857'
    ];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Barbero - Kadosh Barber</title>
    <link rel="stylesheet" href="app/public/css/perfilBarbero.css">

    <!-- Librería FullCalendar 6 -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/es.global.min.js"></script>
</head>

<body>

    <!-- =========================
         HEADER PRINCIPAL
    ========================== -->
    <header class="header-barberia">
        <div class="contenedor-header">
            <div class="logo">
                <img src="app/public/assets/img/logo1.jpeg" alt="Logo Kadosh Barber" class="logo-img">
                <h2>KADOSH <span>BARBER</span></h2>
            </div>
            <nav class="nav-barberia">
                <a href="index.php?controller=auth&action=logout" class="btn-logout">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL -->
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
                <img src="<?php echo $fotoBarbero; ?>" class="avatar" alt="Foto del barbero">
            </div>

            <div class="informacion">
                <div class="mensaje-bienvenida">
                    <h2>¡Hola, Barber <?php echo $nombreBarbero; ?>!</h2>
                    <p class="subtitulo-motivador">Bienvenido de nuevo a un día más de trabajo. Cada corte es una obra de arte, ¡a dar la mejor actitud hoy!</p>
                </div>

                <hr class="divisor-perfil">

                <div class="detalles-contacto">
                    <p><strong>Nombre completo:</strong> <?php echo $nombreBarbero . ' ' . $apellidoBarbero; ?></p>
                    <p><strong>Teléfono:</strong> <?php echo $telefonoBarbero; ?></p>
                    <p><strong>Correo:</strong> <?php echo $correoBarbero; ?></p>
                </div>

                <button type="button" class="btn-editar-perfil" onclick="abrirModalEditarPerfil()">
                    ✏️ Editar Perfil
                </button>
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
             HORARIOS DE TRABAJO (CALENDARIO COMPACTO)
        ========================== -->
        <section class="card" id="horarios">
            <h2>Mis Horarios de Trabajo</h2>
            <div class="calendar-wrapper">
                <div id="calendarBarbero"></div>
            </div>
        </section>

        <!-- =========================
             RESERVACIONES
        ========================== -->
        <section class="card" id="reservas">
            <h2>Reservaciones Activas</h2>
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
             HISTORIAL DE RESERVACIONES
        ========================== -->
        <section class="card" id="historial" style="display:none;">
            <h2>Historial de Citas</h2>

            <?php if (!empty($historial)) { ?>
                <div class="buscador-contenedor">
                    <input
                        type="text"
                        id="inputBusquedaHistorial"
                        placeholder="🔍 Buscar por cliente, servicio, fecha o estado..."
                        onkeyup="filtrarYPaginarHistorial()">
                </div>

                <div class="tablaResponsive">
                    <table id="tablaHistorial">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Servicio</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyHistorial">
                            <?php foreach ($historial as $h) { ?>
                                <tr class="fila-historial">
                                    <td><?php echo htmlspecialchars($h['cliente'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($h['servicio'] ?? ''); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($h['fecha_cita'] ?? '')); ?></td>
                                    <td><?php echo date('h:i A', strtotime($h['hora_cita'] ?? '')); ?></td>
                                    <td>
                                        <?php if (($h['estado'] ?? '') === 'Completada') { ?>
                                            <span class="estado completada">🟢 Completada</span>
                                        <?php } elseif (($h['estado'] ?? '') === 'Cancelada') { ?>
                                            <span class="estado cancelada">🔴 Cancelada</span>
                                        <?php } else { ?>
                                            <span class="estado"><?php echo htmlspecialchars($h['estado'] ?? ''); ?></span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="paginacion-contenedor" id="paginacionHistorial">
                    <button id="btnPrevPágina" onclick="cambiarPagina(-1)" class="btn-paginacion">&#10094; Anterior</button>
                    <span id="infoPágina" class="info-pagina">Página 1 de 1</span>
                    <button id="btnNextPágina" onclick="cambiarPagina(1)" class="btn-paginacion">Siguiente &#10095;</button>
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

    <!-- =========================
     MODAL EDITAR PERFIL BARBERO
========================== -->
    <div id="modalEditarPerfilBarbero" class="modal">
        <div class="modal-contenido">
            <span class="cerrar-modal" onclick="cerrarModalEditarPerfil()">&times;</span>
            <h2>Editar Perfil</h2>

            <form action="index.php?controller=barbero&action=actualizarPerfil" method="POST" enctype="multipart/form-data">

                <!-- AVATAR Y SUBIDA DE FOTO -->
                <div class="modal-foto-container">
                    <img id="previewFotoBarbero" src="<?php echo $fotoBarbero; ?>" alt="Foto de perfil" class="avatar-preview">
                    <label for="fotoBarberoInput" class="btn-cambiar-foto">
                        📷 Cambiar Foto
                    </label>
                    <input type="file" id="fotoBarberoInput" name="foto_perfil" accept="image/*" onchange="previsualizarFoto(event)">
                </div>

                <div class="form-grupo">
                    <label for="nombreBarberoInput">Nombre</label>
                    <input type="text" id="nombreBarberoInput" name="nombre" value="<?php echo $nombreBarbero; ?>" required>
                </div>

                <div class="form-grupo">
                    <label for="apellidoBarberoInput">Apellido</label>
                    <input type="text" id="apellidoBarberoInput" name="apellido" value="<?php echo $apellidoBarbero; ?>" required>
                </div>

                <div class="form-grupo">
                    <label for="telefonoBarberoInput">Teléfono</label>
                    <input type="text" id="telefonoBarberoInput" name="telefono" value="<?php echo $telefonoBarbero; ?>" required>
                </div>

                <div class="form-grupo">
                    <label for="correoBarberoInput">Correo Electrónico</label>
                    <input type="email" id="correoBarberoInput" name="correo" value="<?php echo $correoBarbero; ?>" required>
                </div>

                <div class="modal-acciones">
                    <button type="button" class="btn-cancelar" onclick="cerrarModalEditarPerfil()">Cancelar</button>
                    <button type="submit" class="btn-guardar">Guardar Cambios</button>
                </div>
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
                © 2026 <strong>KADOSH Barber Shop</strong>. Todos los derechos reservados. | Versión 1.0
            </p>
        </div>
    </footer>

    <!-- SCRIPT MANEJO DE MODAL -->
    <script>
        function abrirModalEditarPerfil() {
            document.getElementById('modalEditarPerfilBarbero').style.display = 'flex';
        }

        function cerrarModalEditarPerfil() {
            document.getElementById('modalEditarPerfilBarbero').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('modalEditarPerfilBarbero');
            if (event.target === modal) {
                cerrarModalEditarPerfil();
            }
        }
    </script>

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

        document.addEventListener('DOMContentLoaded', function() {
            mostrarSeccion("reservas");

            const calendarEl = document.getElementById('calendarBarbero');
            if (calendarEl) {
                const eventosPHP = <?php echo json_encode($eventosCalendario); ?>;

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: 'es',
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    aspectRatio: 1.85,
                    displayEventEnd: true, // <-- FORZAR A MOSTRAR LA HORA DE FIN
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek'
                    },
                    events: eventosPHP,
                    // Configuración del formato de la hora (Inicio - Fin)
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        meridiem: false, // Cámbialo a 'short' si quieres ver am/pm (ej: 10:00a - 06:00p)
                        hour12: false // Formato 24 horas (ej: 10:00 - 18:00)
                    }
                });

                calendar.render();
            }
        });

        function filtrarHistorial() {
            const input = document.getElementById("inputBusquedaHistorial");
            const filtro = input.value.toLowerCase();
            const tabla = document.getElementById("tablaHistorial");

            if (!tabla) return;

            const filas = tabla.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

            for (let i = 0; i < filas.length; i++) {
                const textoFila = filas[i].textContent || filas[i].innerText;
                if (textoFila.toLowerCase().indexOf(filtro) > -1) {
                    filas[i].style.display = "";
                } else {
                    filas[i].style.display = "none";
                }
            }
        }

        // Variables globales para paginación
        let paginaActual = 1;
        const filasPorPagina = 5;
        let filasFiltradas = [];

        function inicializarPaginacion() {
            const tbody = document.getElementById("tbodyHistorial");
            if (!tbody) return;

            // Obtener todas las filas originales
            filasFiltradas = Array.from(tbody.getElementsByClassName("fila-historial"));
            paginaActual = 1;
            mostrarPaginaActual();
        }

        function filtrarYPaginarHistorial() {
            const input = document.getElementById("inputBusquedaHistorial");
            const filtro = input.value.toLowerCase();
            const tbody = document.getElementById("tbodyHistorial");
            if (!tbody) return;

            const todasLasFilas = Array.from(tbody.getElementsByClassName("fila-historial"));

            // Filtrar filas según la búsqueda
            filasFiltradas = todasLasFilas.filter(fila => {
                const textoFila = fila.textContent.toLowerCase();
                return textoFila.indexOf(filtro) > -1;
            });

            // Reiniciar a la primera página tras una búsqueda
            paginaActual = 1;
            mostrarPaginaActual();
        }

        function mostrarPaginaActual() {
            const tbody = document.getElementById("tbodyHistorial");
            if (!tbody) return;

            const todasLasFilas = Array.from(tbody.getElementsByClassName("fila-historial"));

            // Ocultar absolutamente todas las filas
            todasLasFilas.forEach(fila => fila.style.display = "none");

            const totalPaginas = Math.ceil(filasFiltradas.length / filasPorPagina) || 1;
            if (paginaActual > totalPaginas) paginaActual = totalPaginas;

            const inicio = (paginaActual - 1) * filasPorPagina;
            const fin = inicio + filasPorPagina;

            // Mostrar solo las 5 filas correspondientes a la página actual
            const filasAMostrar = filasFiltradas.slice(inicio, fin);
            filasAMostrar.forEach(fila => fila.style.display = "");

            // Actualizar texto e interfaz de controles
            const infoPagina = document.getElementById("infoPágina");
            const btnPrev = document.getElementById("btnPrevPágina");
            const btnNext = document.getElementById("btnNextPágina");

            if (infoPagina) {
                infoPagina.textContent = `Página ${paginaActual} de ${totalPaginas}`;
            }

            if (btnPrev) btnPrev.disabled = (paginaActual === 1);
            if (btnNext) btnNext.disabled = (paginaActual === totalPaginas || filasFiltradas.length === 0);
        }

        function cambiarPagina(direccion) {
            paginaActual += direccion;
            mostrarPaginaActual();
        }

        // Inicializar la paginación al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            inicializarPaginacion();
        });

        function abrirModalEditarPerfil() {
            document.getElementById('modalEditarPerfilBarbero').style.display = 'flex';
        }

        function cerrarModalEditarPerfil() {
            document.getElementById('modalEditarPerfilBarbero').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('modalEditarPerfilBarbero');
            if (event.target === modal) {
                cerrarModalEditarPerfil();
            }
        }
    </script>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>