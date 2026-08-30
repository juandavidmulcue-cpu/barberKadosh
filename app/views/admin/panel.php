<?php
$panelActivo = $_GET['panel'] ?? 'inicio';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Admin | Kadosh</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS PROPIO -->
    <link rel="stylesheet" href="app/public/css/admin.css">
    <link rel="stylesheet" href="app/public/css/global.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet" />
</head>

<body class="admin-body">

    <header class="admin-header">
        <div class="logo">
            <img src="app/public/assets/img/logo1.jpeg" alt="Kadosh Barber Shop" class="logo-img-circle">
            <span class="logo-text">PANEL ADMINISTRADOR</span>
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline">Cerrar sesión</a>
        </div>
    </header>
    <div class="admin-content-wrapper">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <p class="sidebar-title">Gestión</p>

            <div class="sidebar-link" onclick="showPanel('inicio')">📊 Panel</div>
            <div class="sidebar-link" onclick="showPanel('barberos')">👤 Barberos</div>
            <div class="sidebar-link" onclick="showPanel('clientes')">👤 Clientes</div>
            <div class="sidebar-link" onclick="showPanel('productos')">📦 Productos</div>
            <div class="sidebar-link" onclick="showPanel('servicios')">✂️ Servicios</div>
            <div class="sidebar-link" onclick="showPanel('horarios')">🕒 Horarios</div>

        </aside>

        <main class="main-content">

            <!-- INICIO -->
            <div class="panel" id="panel-inicio">
                <h2>Bienvenido, Señor ADMIN 👋</h2><br>
                <p class="subtext">Panel administrativo de la barbería</p>
                <br>
                <div class="inicio-cards">
                    <div class="card">
                        <h3>Gestión de Barberos</h3>
                        <p>Administra barberos, horarios y disponibilidad.</p>
                    </div>

                    <div class="card">
                        <h3>Gestión de Clientes</h3>
                        <p>Administra clientes y sus datos.</p>
                    </div>

                    <div class="card">
                        <h3>Productos</h3>
                        <p>Controla el inventario y precios.</p>
                    </div>

                    <div class="card">
                        <h3>Citas</h3>
                        <p>Revisa y organiza las citas del día.</p>
                    </div>
                </div>
                <br>
                <p class="footer-text">
                    Usa el menú lateral para comenzar a administrar la barbería 💈
                </p>
            </div>


            <!-- BARBEROS -->
            <div class="panel" id="panel-barberos">

                <h2 class="section-title">👤 Barberos</h2>

                <div class="admin-card">
                    <p>Gestionar barberos</p>
                </div>

                <div class="action-row">
                    <h3>Registrar Barbero</h3>
                    <p>Presiona el botón para añadir a un nuevo integrante al equipo:</p>
                    <br>
                    <br>
                    <a href="index.php?controller=gestionBarbero&action=registrar" class="btn btn-primary">
                        + Registrar Barbero
                    </a>
                </div>

                <br>

                <div class="section-card">

                    <table class="data-table" id="tablaBarberos">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($barberos)): ?>
                                <?php foreach ($barberos as $b): ?>
                                    <tr>
                                        <td><?= $b['id_usuario'] ?></td>
                                        <td><?= $b['nombre'] ?></td>
                                        <td><?= $b['apellido'] ?></td>
                                        <td><?= $b['telefono'] ?></td>
                                        <td><?= $b['correo'] ?></td>
                                        <td>
                                            <?php if ($b['estado'] == 'activo'): ?>
                                                <span style="color:green;font-weight:bold;">🟢 Activo</span>
                                            <?php else: ?>
                                                <span style="color:red;font-weight:bold;">🔴 Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($b['estado'] == 'activo'): ?>

                                                <a class="btn-danger"
                                                    href="index.php?controller=gestionBarbero&action=desactivar&id=<?= $b['id_usuario'] ?>&panel=barberos"
                                                    onclick="return confirm('¿Desea desactivar este barbero?')"
                                                    title="Desactivar">

                                                    <img src="app/public/assets/icons/desactivar.png" alt="Desactivar">
                                                </a>

                                            <?php else: ?>

                                                <a class="btn-success"
                                                    href="index.php?controller=gestionBarbero&action=activar&id=<?= $b['id_usuario'] ?>&panel=barberos"
                                                    onclick="return confirm('¿Desea activar este barbero?')"
                                                    title="Activar">

                                                    <img src="app/public/assets/icons/activar.png" alt="Activar">
                                                </a>

                                            <?php endif; ?>

                                            <a class="icono-editar"
                                                href="index.php?controller=gestionBarbero&action=editar&id_usuario=<?= $b['id_usuario'] ?>"
                                                title="Editar">

                                                <img src="app/public/assets/icons/editar.png" alt="Editar">
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No hay barberos registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>

            <!-- CLIENTES -->
            <div class="panel" id="panel-clientes">

                <h2 class="section-title">👤 Clientes</h2>

                <div class="admin-card">
                    <p>Gestionar clientes</p>
                </div>
                <br>

                <div class="section-card">

                    <table class="data-table" id="tablaClientes">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($clientes)): ?>
                                <?php foreach ($clientes as $c): ?>
                                    <tr>
                                        <td><?= $c['id_usuario'] ?></td>
                                        <td><?= $c['nombre'] ?></td>
                                        <td><?= $c['apellido'] ?></td>
                                        <td><?= $c['telefono'] ?></td>
                                        <td><?= $c['correo'] ?></td>
                                        <td>
                                            <?php if ($c['estado'] == 'activo'): ?>
                                                <span style="color:green;font-weight:bold;">🟢 Activo</span>
                                            <?php else: ?>
                                                <span style="color:red;font-weight:bold;">🔴 Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($c['estado'] == 'activo'): ?>

                                                <a class="btn-danger"
                                                    href="index.php?controller=gestionCliente&action=desactivar&id=<?= $c['id_usuario'] ?>&panel=clientes"
                                                    onclick="return confirm('¿Desea desactivar este cliente?')"
                                                    title="Desactivar">
                                                    <img src="app/public/assets/icons/desactivar.png" alt="Desactivar">
                                                </a>

                                            <?php else: ?>

                                                <a class="btn-success"
                                                    href="index.php?controller=gestionCliente&action=activar&id=<?= $c['id_usuario'] ?>&panel=clientes"
                                                    onclick="return confirm('¿Desea activar este cliente?')"
                                                    title="Activar">
                                                    <img src="app/public/assets/icons/activar.png" alt="Activar">
                                                </a>

                                            <?php endif; ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No hay clientes registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>

            <!-- PRODUCTOS -->
            <div class="panel" id="panel-productos">

                <h2 class="section-title">📦 Productos</h2>

                <div class="action-row">
                    <a href="index.php?controller=gestionProducto&action=listar" class="btn btn-primary">
                        Gestionar Productos
                    </a>
                </div>
                <div class="section-card">

                    <table class="data-table" id="tablaProductos">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Stock</th>
                                <th>Precio</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($productos)): ?>
                                <?php foreach ($productos as $p): ?>
                                    <tr>
                                        <td><?php echo $p['nombre']; ?></td>
                                        <td><?php echo $p['stock']; ?></td>
                                        <td><?php echo $p['precio']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">No hay productos registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>

            </div>

            <!-- SERVICIOS -->
            <div class="panel" id="panel-servicios">

                <h2 class="section-title">✂️ Servicios</h2>

                <div class="admin-card">
                    <p>Administra los servicios que ofrece Kadosh Barber Shop.</p>
                </div>

                <?php if (!empty($_SESSION['mensaje_exito'])): ?>

                    <div class="success-msg">
                        <?= $_SESSION['mensaje_exito']; ?>
                    </div>

                    <?php unset($_SESSION['mensaje_exito']); ?>

                <?php endif; ?>


                <?php if (!empty($_SESSION['mensaje_error'])): ?>

                    <div class="error-msg">
                        <?= $_SESSION['mensaje_error']; ?>
                    </div>

                    <?php unset($_SESSION['mensaje_error']); ?>

                <?php endif; ?>


                <!-- FORMULARIO CREAR / EDITAR -->

                <div class="section-card">

                    <?php if (!empty($servicioEditar)): ?>

                        <h3>Editar servicio</h3>

                        <form
                            action="index.php?controller=servicio&action=editarServicio&id_servicio=<?= urlencode($servicioEditar['id_servicio']); ?>"
                            method="POST">

                        <?php else: ?>

                            <h3>Crear nuevo servicio</h3>

                            <form
                                action="index.php?controller=servicio&action=guardar"
                                method="POST">

                            <?php endif; ?>


                            <div class="form-group">

                                <label>Nombre del servicio</label>

                                <input
                                    type="text"
                                    name="nombre"
                                    maxlength="50"
                                    placeholder="Ej: Corte básico"
                                    value="<?= htmlspecialchars($servicioEditar['nombre'] ?? ''); ?>"
                                    required>

                            </div>

                            <div class="form-group">

                                <label>Descripción</label>

                                <textarea
                                    name="descripcion"
                                    rows="4"
                                    placeholder="Descripción del servicio"><?= htmlspecialchars($servicioEditar['descripcion'] ?? ''); ?></textarea>

                            </div>

                            <div class="form-group">

                                <label>Precio</label>

                                <input
                                    type="number"
                                    name="precio"
                                    min="0"
                                    step="0.01"
                                    placeholder="Ej: 25000"
                                    value="<?= htmlspecialchars($servicioEditar['precio'] ?? ''); ?>"
                                    required>

                            </div>

                            <div class="form-group">
                                <label>Duración</label>
                                <select name="duracion" required>
                                    <?php
                                    $durActual = $servicioEditar['duracion'] ?? '00:30:00';
                                    ?>
                                    <!-- SERVICIOS CORTOS -->
                                    <option value="20" <?= ($durActual == '00:20:00' || $durActual == '20') ? 'selected' : ''; ?>>20 Minutos</option>
                                    <option value="30" <?= ($durActual == '00:30:00' || $durActual == '30') ? 'selected' : ''; ?>>30 Minutos</option>
                                    <option value="40" <?= ($durActual == '00:40:00' || $durActual == '40') ? 'selected' : ''; ?>>40 Minutos</option>
                                    <option value="45" <?= ($durActual == '00:45:00' || $durActual == '45') ? 'selected' : ''; ?>>45 Minutos</option>
                                    <option value="50" <?= ($durActual == '00:50:00' || $durActual == '50') ? 'selected' : ''; ?>>50 Minutos</option>

                                    <!-- SERVICIOS LARGOS (TINTES, TRATAMIENTOS, KERA) -->
                                    <option value="60" <?= ($durActual == '01:00:00' || $durActual == '60') ? 'selected' : ''; ?>>1 Hora (60 Min)</option>
                                    <option value="90" <?= ($durActual == '01:30:00' || $durActual == '90') ? 'selected' : ''; ?>>1 Hora y Media (90 Min)</option>
                                    <option value="120" <?= ($durActual == '02:00:00' || $durActual == '120') ? 'selected' : ''; ?>>2 Horas (120 Min)</option>
                                    <option value="150" <?= ($durActual == '02:30:00' || $durActual == '150') ? 'selected' : ''; ?>>2 Horas y Media (150 Min)</option>
                                    <option value="180" <?= ($durActual == '03:00:00' || $durActual == '180') ? 'selected' : ''; ?>>3 Horas (180 Min)</option>
                                </select>
                            </div>


                            <?php if (!empty($servicioEditar)): ?>

                                <button type="submit" class="btn btn-primary">
                                    💾 Actualizar servicio
                                </button>

                                <a
                                    href="index.php?controller=admin&action=panel&panel=servicios"
                                    class="btn-warning">
                                    Cancelar
                                </a>

                            <?php else: ?>

                                <button type="submit" class="btn btn-primary">
                                    ➕ Crear servicio
                                </button>

                            <?php endif; ?>

                            </form>

                </div>

                <!-- TABLA -->

                <div class="section-card">

                    <table class="data-table" id="tablaServicios">

                        <thead>

                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Duración</th>
                                <th>Acción</th>
                            </tr>

                        </thead>


                        <tbody>

                            <?php if (!empty($servicios)): ?>

                                <?php foreach ($servicios as $s): ?>

                                    <tr>

                                        <td><?= htmlspecialchars($s['id_servicio']); ?></td>

                                        <td><?= htmlspecialchars($s['nombre']); ?></td>

                                        <td><?= htmlspecialchars($s['descripcion']); ?></td>

                                        <td>
                                            $<?= number_format($s['precio'], 2); ?>
                                        </td>

                                        <td><?= htmlspecialchars($s['duracion']); ?></td>

                                        <td>
                                            <a class="icono-editar"
                                                href="index.php?controller=servicio&action=editarServicio&id_servicio=<?= urlencode($s['id_servicio']); ?>"
                                                title="Editar">

                                                <img src="app/public/assets/icons/editar.png" alt="Editar">
                                            </a>
                                            <a class="btn-danger"
                                                href="index.php?controller=servicio&action=eliminar&id_servicio=<?= urlencode($s['id_servicio']); ?>"
                                                onclick="return confirm('¿Está seguro de eliminar este servicio?')"
                                                title="Eliminar">

                                                <img src="app/public/assets/icons/eliminar.png" alt="Eliminar">
                                            </a>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- CDN de FullCalendar v6 -->
            <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

            <div class="panel" id="panel-horarios">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h2 class="section-title" style="margin: 0;">Asignación de Horarios de Barberos</h2>

                    <!-- BOTÓN EXCLUSIVO DEL ADMIN PARA DESCARGAR EL MES -->
                    <button type="button" onclick="abrirModalDescargar()" class="btn btn-primary" style="padding: 8px 15px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px; background-color: #28a745; color: white; border: none;">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                        </svg>
                        Descargar Horarios del Mes
                    </button>
                </div>

                <div class="admin-card">
                    <p>Arrastra un barbero desde la lista hacia cualquier día del calendario para asignarle un turno.</p>
                </div>

                <!-- CONTENEDOR PRINCIPAL: SIDEBAR BARBEROS + CALENDARIO -->
                <div style="display: flex; gap: 20px; align-items: flex-start; margin-top: 15px;">

                    <!-- LISTA DE BARBEROS -->
                    <div id="external-events" style="width: 220px; padding: 15px;">
                        <h4 style="margin-bottom: 12px; font-size: 15px;">Barberos Activos</h4>
                        <?php if (!empty($barberos) && is_array($barberos)): ?>
                            <?php foreach ($barberos as $b): ?>
                                <div class="fc-event-item"
                                    data-id="<?= $b['id_usuario']; ?>"
                                    data-nombre="<?= htmlspecialchars($b['nombre'] . ' ' . $b['apellido']); ?>"
                                    style="padding: 10px; margin-bottom: 8px; border-radius: 8px; cursor: grab; font-weight: bold; text-align: center;">
                                    <?= htmlspecialchars($b['nombre'] . ' ' . $b['apellido']); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- CALENDARIO -->
                    <div style="flex-grow: 1;">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>

            <!-- MODAL PARA ASIGNAR HORAS DE TURNO -->
            <div id="modalHorario" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
                <div style="background: white; width: 350px; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); position: relative;">
                    <h3 id="modalTitulo" style="margin-top: 0; margin-bottom: 15px; font-size: 18px; color: #333;">Asignar Horario</h3>

                    <form action="index.php?controller=horario&action=guardarHorario" method="POST">
                        <input type="hidden" name="id_barbero" id="modal_id_barbero">
                        <input type="hidden" name="fecha_inicio" id="modal_fecha_inicio">
                        <input type="hidden" name="fecha_fin" id="modal_fecha_fin">

                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="display:block; margin-bottom: 5px;">Hora Inicio</label>
                            <input type="time" name="hora_inicio" value="08:00" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>

                        <div class="form-group" style="margin-bottom: 18px;">
                            <label style="display:block; margin-bottom: 5px;">Hora Fin</label>
                            <input type="time" name="hora_fin" value="17:00" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 10px;">
                            <button type="button" onclick="cerrarModalHorario()" class="btn" style="background: #ccc; border:none; padding: 8px 12px; border-radius: 4px; cursor:pointer;">Cancelar</button>
                            <button type="submit" class="btn btn-primary" style="padding: 8px 12px; cursor:pointer;">Guardar Horario</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Descarga de Horarios del Mes -->
            <div id="modalDescargarReporte" class="modal-reporte-overlay">
                <div class="modal-reporte-contenido">
                    <span class="modal-reporte-cerrar" onclick="cerrarModalReporte()">&times;</span>
                    <h3 class="modal-reporte-titulo">Descargar Reporte de Horarios</h3>

                    <form action="index.php?controller=horario&action=exportarHorarios" method="POST" target="_blank">
                        <!-- Selección de Mes -->
                        <div class="form-group-reporte">
                            <label for="filtroMes">Seleccionar Mes:</label>
                            <select name="mes" id="filtroMes" class="select-reporte">
                                <option value="01">Enero</option>
                                <option value="02">Febrero</option>
                                <option value="03">Marzo</option>
                                <option value="04">Abril</option>
                                <option value="05">Mayo</option>
                                <option value="06">Junio</option>
                                <option value="07">Julio</option>
                                <option value="08">Agosto</option>
                                <option value="09">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </div>

                        <!-- Selección de Año -->
                        <div class="form-group-reporte">
                            <label for="filtroAnio">Seleccionar Año:</label>
                            <select name="anio" id="filtroAnio" class="select-reporte">
                                <option value="2026" selected>2026</option>
                                <option value="2025">2025</option>
                            </select>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="modal-reporte-acciones">
                            <button type="button" onclick="descargarCalendarioPDF()" class="btn-reporte btn-pdf">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button type="submit" name="tipo" value="excel" class="btn-reporte btn-excel">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                            <button type="button" onclick="cerrarModalReporte()" class="btn-reporte btn-cancelar">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
    </div>

    </div>

    </main>

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

    <!-- JS PANEL -->
    <script>
        function abrirModalDescargar() {
            const modal = document.getElementById('modalDescargarReporte');
            if (modal) modal.style.display = 'flex';
        }

        function cerrarModalReporte() {
            const modal = document.getElementById('modalDescargarReporte');
            if (modal) modal.style.display = 'none';
        }

        function cerrarModalHorario() {
            const modal = document.getElementById('modalHorario');
            if (modal) modal.style.display = 'none';
        }

        function descargarCalendarioPDF() {
            // ID del contenedor donde renderizas FullCalendar (ej: 'calendar')
            const elemento = document.getElementById('calendar');

            if (!elemento) {
                alert('No se encontró el elemento del calendario');
                return;
            }

            // Configuración para generar el PDF horizontal (landscape)
            const opciones = {
                margin: [10, 10, 10, 10],
                filename: 'Horarios_Kadosh_Calendario.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'landscape'
                }
            };

            // Cerrar el modal antes de capturar la pantalla
            cerrarModalReporte();

            // Generar y descargar el PDF
            html2pdf().set(opciones).from(elemento).save();
        }
    </script>

    <script>
        function showPanel(panel) {

            document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));

            document.getElementById('panel-' + panel).classList.add('active');

            const links = document.querySelectorAll('.sidebar-link');
            const indice = {
                inicio: 0,
                barberos: 1,
                clientes: 2,
                productos: 3,
                servicios: 4,
                horarios: 5,
                reportes: 6
            };

            if (indice[panel] !== undefined) {
                links[indice[panel]].classList.add('active');
            }

            initTablas(panel);
        }

        document.addEventListener('DOMContentLoaded', function() {
            showPanel("<?= $panelActivo ?>");
        });
    </script>
    <!-- LIBRERÍAS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- DataTables INIT -->

    <script>
        let tablaBarberos = null;
        let tablaProductos = null;
        let tablaServicios = null;
        let tablaClientes = null;
        let tablaHorarios = null;


        /* ============================================================
           CONFIGURACIÓN KADOSH
           ============================================================ */

        const coloresKadosh = {
            negro: '#0D0D0D',
            morado: '#3d2857',
            moradoClaro: '#2b1c3d',
            dorado: '#D4AF37',
            blanco: '#FFFFFF'
        };

        /* ============================================================
           BOTONES KADOSH
           ============================================================ */

        function botonesKadosh(nombre, columnas = null) {
            const exportOptions = columnas ? {
                columns: columnas
            } : {
                columns: ':not(:last-child)'
            };

            return [

                /* ========================================================
                   EXCEL
                   ======================================================== */

                {
                    extend: 'excelHtml5',
                    title: 'KADOSH BARBER SHOP',
                    filename: 'Kadosh_' + nombre,
                    messageTop: 'Reporte de ' + nombre,
                    exportOptions: exportOptions
                },

                /* ========================================================
                   PDF
                   ======================================================== */

                {
                    extend: 'pdfHtml5',
                    title: '',
                    filename: 'Kadosh_' + nombre,
                    pageSize: 'A4',
                    orientation: 'landscape',
                    exportOptions: exportOptions,

                    customize: function(doc) {

                        /* ========================================================
                           MÁRGENES
                        ======================================================== */

                        doc.pageMargins = [35, 40, 35, 50];
                        /* ========================================================
                           LOGO
                        ======================================================== */

                        const logoBase64 =
                            'data:image/jpeg;base64,<?= base64_encode(file_get_contents("app/public/assets/img/logo1.jpeg")); ?>';

                        /* ========================================================
                           BUSCAR TABLA ORIGINAL
                        ======================================================== */

                        let tabla = null;
                        for (let i = 0; i < doc.content.length; i++) {
                            if (doc.content[i].table) {
                                tabla = doc.content[i];
                                break;
                            }
                        }


                        /* ========================================================
                           ELIMINAR TÍTULOS AUTOMÁTICOS
                        ======================================================== */

                        doc.content = doc.content.filter(function(item) {

                            return !(
                                item.text &&
                                typeof item.text === 'string' &&
                                (
                                    item.text.includes('Reporte de') ||
                                    item.text === nombre
                                )
                            );
                        });

                        /* ========================================================
                           ENCABEZADO COMPLETO
                        ======================================================== */

                        const encabezado = {

                            stack: [

                                /* ====================================================
                                LOGO
                                ==================================================== */

                                {
                                    image: logoBase64,
                                    width: 70,
                                    height: 70,
                                    alignment: 'center',
                                    margin: [0, 0, 0, 8]
                                },

                                /* ====================================================
                                   KADOSH
                                ==================================================== */

                                {
                                    text: 'KADOSH',
                                    fontSize: 23,
                                    bold: true,
                                    color: coloresKadosh.morado,
                                    alignment: 'center',
                                    margin: [0, 0, 0, 2]
                                },

                                /* ====================================================
                                   BARBER SHOP
                                ==================================================== */

                                {
                                    text: 'BARBER SHOP',
                                    fontSize: 10,
                                    bold: true,
                                    color: coloresKadosh.dorado,
                                    alignment: 'center',
                                    characterSpacing: 3,
                                    margin: [0, 0, 0, 5]
                                },

                                /* ====================================================
                                   REPORTE
                                ==================================================== */

                                {
                                    text: 'Reporte de ' + nombre,
                                    fontSize: 11,
                                    bold: true,
                                    color: coloresKadosh.negro,
                                    alignment: 'center',
                                    margin: [0, 3, 0, 10]
                                },

                                /* ====================================================
                                   LÍNEA DORADA
                                ==================================================== */
                                {
                                    canvas: [{
                                        type: 'line',
                                        x1: 0,
                                        y1: 0,
                                        x2: 525,
                                        y2: 0,
                                        lineWidth: 2,
                                        lineColor: coloresKadosh.dorado
                                    }],
                                    margin: [0, 0, 0, 15]
                                }
                            ],
                            alignment: 'center'
                        };

                        /* ========================================================
                           COLOCAR ENCABEZADO AL PRINCIPIO
                        ======================================================== */

                        doc.content.unshift(encabezado);

                        /* ========================================================
                           CONFIGURAR TABLA
                        ======================================================== */

                        if (tabla) {

                            /* CENTRAR TABLA */

                            tabla.alignment = 'center';
                            tabla.margin = [0, 10, 0, 10];

                            /* ANCHO */

                            tabla.table.widths =
                                Array(tabla.table.body[0].length).fill('*');

                            /* ====================================================
                               BORDES DORADOS
                            ==================================================== */

                            tabla.layout = {

                                hLineWidth: function(i, node) {
                                    return 1;
                                },

                                vLineWidth: function(i, node) {
                                    return 1;
                                },

                                hLineColor: function(i, node) {
                                    return coloresKadosh.dorado;
                                },

                                vLineColor: function(i, node) {
                                    return coloresKadosh.dorado;
                                },

                                paddingLeft: function(i, node) {
                                    return 8;
                                },

                                paddingRight: function(i, node) {
                                    return 8;
                                },

                                paddingTop: function(i, node) {
                                    return 7;
                                },

                                paddingBottom: function(i, node) {
                                    return 7;
                                }
                            };

                            /* ====================================================
                               ENCABEZADO DE TABLA
                            ==================================================== */

                            tabla.table.body[0].forEach(function(cell) {
                                cell.fillColor = coloresKadosh.morado;
                                cell.color = coloresKadosh.blanco;
                                cell.bold = true;
                                cell.alignment = 'center';
                                cell.fontSize = 9;
                            });

                            /* ====================================================
                               CUERPO DE TABLA
                            ==================================================== */

                            for (
                                let fila = 1; fila < tabla.table.body.length; fila++
                            ) {

                                tabla.table.body[fila].forEach(function(cell) {
                                    cell.alignment = 'center';
                                    cell.color = coloresKadosh.negro;
                                    cell.fontSize = 8;
                                });
                            }
                        }

                        /* ========================================================
                           PIE DE PÁGINA
                        ======================================================== */

                        doc.footer = function(currentPage, pageCount) {
                            return {
                                margin: [35, 10, 35, 0],
                                columns: [{
                                        text: 'KADOSH BARBER SHOP',
                                        color: coloresKadosh.morado,
                                        bold: true,
                                        fontSize: 8
                                    },
                                    {
                                        text: 'Página ' +
                                            currentPage +
                                            ' de ' +
                                            pageCount,
                                        alignment: 'right',
                                        color: coloresKadosh.negro,
                                        fontSize: 8
                                    }
                                ]
                            };
                        };
                    }
                },

                /* ========================================================
                   PRINT
                   ======================================================== */

                {
                    extend: 'print',
                    title: '',
                    exportOptions: exportOptions,

                    customize: function(win) {

                        const body = $(win.document.body);

                        /* ------------------------------------------------
                           ESTILO GENERAL
                        ------------------------------------------------ */

                        body.css({
                            'font-family': 'Arial, sans-serif',
                            'background': '#ffffff',
                            'color': coloresKadosh.negro,
                            'padding': '35px',
                            'text-align': 'center'
                        });

                        /* ------------------------------------------------
                           LOGO
                        ------------------------------------------------ */

                        body.prepend(`
                        <div style="
                            text-align:center;
                            margin-bottom:15px;
                        ">
                            <img
                                src="app/public/assets/img/logo1.jpeg"
                                style="
                                    width:90px;
                                    height:90px;
                                    object-fit:cover;
                                    border-radius:50%;
                                    display:block;
                                    margin:0 auto 12px auto;
                                "
                            >
                            <div style="
                                font-size:28px;
                                font-weight:bold;
                                color:#3d2857;
                                letter-spacing:3px;
                            ">
                                KADOSH
                            </div>
                            <div style="
                                font-size:13px;
                                font-weight:bold;
                                color:#D4AF37;
                                letter-spacing:4px;
                                margin-top:4px;
                            ">
                                BARBER SHOP
                            </div>
                            <div style="
                                width:80%;
                                height:3px;
                                background:#D4AF37;
                                margin:15px auto;
                            "></div>
                            <div style="
                                font-size:16px;
                                color:#0D0D0D;
                                font-weight:bold;
                                margin-bottom:20px;
                            ">
                                Reporte de ${nombre}
                            </div>
                        </div>
                    `);

                        /* ------------------------------------------------
                           TABLA
                        ------------------------------------------------ */

                        body.find('table').css({
                            'width': '90%',
                            'margin-left': 'auto',
                            'margin-right': 'auto',
                            'border-collapse': 'collapse',
                            'font-size': '12px',
                            'text-align': 'center'
                        });

                        /* ------------------------------------------------
                           ENCABEZADOS
                        ------------------------------------------------ */

                        body.find('thead th').css({
                            'background-color': '#3d2857',
                            'color': '#ffffff',
                            'border': '1px solid #D4AF37',
                            'padding': '10px',
                            'text-align': 'center',
                            'font-weight': 'bold'
                        });

                        /* ------------------------------------------------
                           CELDAS
                        ------------------------------------------------ */

                        body.find('tbody td').css({
                            'border': '1px solid #D4AF37',
                            'padding': '8px',
                            'text-align': 'center'
                        });

                        /* ------------------------------------------------
                           FILAS ALTERNADAS
                        ------------------------------------------------ */

                        body.find('tbody tr:nth-child(even)').css({
                            'background-color': '#f4eff8'
                        });

                        /* ------------------------------------------------
                           OCULTAR TÍTULO AUTOMÁTICO
                        ------------------------------------------------ */

                        body.find('h1').hide();

                        /* ------------------------------------------------
                           PIE DE PÁGINA
                        ------------------------------------------------ */
                        body.append(`
                        <div style="
                            margin-top:35px;
                            padding-top:12px;
                            border-top:2px solid #D4AF37;
                            font-size:10px;
                            color:#3d2857;
                            font-weight:bold;
                            text-align:center;
                        ">
                            KADOSH BARBER SHOP
                            <br>
                            Reporte administrativo
                        </div>
                    `);
                    }
                }
            ];
        }

        /*INICIALIZAR DATATABLES*/

        function initTablas(panel) {

            /* ========================================================
               BARBEROS
            ======================================================== */

            if (panel === 'barberos' && !tablaBarberos) {
                if (!document.getElementById('tablaBarberos')) return;
                tablaBarberos = $('#tablaBarberos').DataTable({
                    destroy: true,
                    dom: 'Bfrtip',
                    buttons: botonesKadosh(
                        'Barberos',
                        [0, 1, 2, 3, 4]
                    ),
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                    }
                });
            }

            /* ========================================================
               CLIENTES
            ======================================================== */

            if (panel === 'clientes' && !tablaClientes) {
                if (!document.getElementById('tablaClientes')) return;
                tablaClientes = $('#tablaClientes').DataTable({
                    destroy: true,
                    dom: 'Bfrtip',
                    buttons: botonesKadosh(
                        'Clientes',
                        [0, 1, 2, 3, 4]
                    ),
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                    }
                });
            }

            /* ========================================================
               PRODUCTOS
            ======================================================== */

            if (panel === 'productos' && !tablaProductos) {
                if (!document.getElementById('tablaProductos')) return;
                tablaProductos = $('#tablaProductos').DataTable({
                    destroy: true,
                    dom: 'Bfrtip',
                    buttons: botonesKadosh(
                        'Productos',
                        [0, 1, 2]
                    ),
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                    }
                });
            }

            /* ========================================================
               SERVICIOS
            ======================================================== */

            if (panel === 'servicios' && !tablaServicios) {
                if (!document.getElementById('tablaServicios')) return;
                tablaServicios = $('#tablaServicios').DataTable({
                    destroy: true,
                    dom: 'Bfrtip',
                    buttons: botonesKadosh(
                        'Servicios',
                        [0, 1, 2, 3, 4]
                    ),
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                    }
                });
            }

            /* ========================================================
               HORARIOS
            ======================================================== */

            if (panel === 'horarios' && !tablaHorarios) {
                if (!document.getElementById('tablaHorarios')) return;
                tablaHorarios = $('#tablaHorarios').DataTable({
                    destroy: true,
                    dom: 'Bfrtip',
                    buttons: botonesKadosh(
                        'Horarios',
                        [0, 1, 2, 3]
                    ),

                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                    }
                });
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const containerEl = document.getElementById('external-events');
            const calendarEl = document.getElementById('calendar');

            // 1. Convertir elementos de la lista en arrastables
            new FullCalendar.Draggable(containerEl, {
                itemSelector: '.fc-event-item',
                eventData: function(eventEl) {
                    return {
                        title: eventEl.getAttribute('data-nombre'),
                        idBarbero: eventEl.getAttribute('data-id')
                    };
                }
            });

            // 2. Formatear la lista de horarios traída desde el backend PHP
            const eventosBD = [
                <?php if (!empty($horarios)): ?>
                    <?php foreach ($horarios as $h): ?> {
                            id: '<?= $h['id_horario']; ?>',
                            title: '<?= addslashes($h['nombre'] . ' ' . $h['apellido']); ?> (<?= date('g:i A', strtotime($h['hora_inicio'])); ?> - <?= date('g:i A', strtotime($h['hora_fin'])); ?>)',
                            start: '<?= $h['fecha']; ?>',
                            allDay: true,
                            backgroundColor: '#1b4332',
                            borderColor: '#081c15'
                        },
                    <?php endforeach; ?>
                <?php endif; ?>
            ];

            // 3. Inicializar el Calendario
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                height: 'auto',
                contentHeight: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                editable: false,
                droppable: true,
                events: eventosBD,

                // Al soltar a un barbero en un día determinado
                drop: function(info) {
                    const idBarbero = info.draggedEl.getAttribute('data-id');
                    const nombreBarbero = info.draggedEl.getAttribute('data-nombre');

                    // Obtener fecha en formato YYYY-MM-DD
                    const fechaSeleccionada = info.dateStr;

                    abrirModalHorario(idBarbero, nombreBarbero, fechaSeleccionada);
                },

                // Eliminar horario al hacer click en una asignación existente
                eventClick: function(info) {
                    if (confirm(`¿Desea eliminar el horario asignado (${info.event.title})?`)) {
                        window.location.href = `index.php?controller=horario&action=eliminarHorario&id_horario=${info.event.id}`;
                    }
                }
            });

            calendar.render();
        });

        // Funciones Auxiliares para el Modal
        function abrirModalHorario(idBarbero, nombre, fecha) {
            document.getElementById('modalTitulo').innerText = `Asignar a ${nombre} (${fecha})`;
            document.getElementById('modal_id_barbero').value = idBarbero;
            document.getElementById('modal_fecha_inicio').value = fecha;
            document.getElementById('modal_fecha_fin').value = fecha;

            const modal = document.getElementById('modalHorario');
            modal.style.display = 'flex';
        }

        function cerrarModalHorario() {
            document.getElementById('modalHorario').style.display = 'none';
        }
    </script>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>

<?php if (!empty($_SESSION['mensaje_exito'])): ?>
    <div class="success-msg">
        <?= $_SESSION['mensaje_exito']; ?>
    </div>

    <?php unset($_SESSION['mensaje_exito']); ?>
<?php endif; ?>