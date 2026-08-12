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

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
</head>

<body class="admin-body">

    <header class="admin-header">
        <div class="logo">
            <img src="app/public/assets/img/logo1.jpeg" alt="Kadosh Barber Shop" class="logo-img-circle">
            <span class="logo-text">PANEL ADMINISTRADOR</span>
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline">Cerrar sesión</a>
        </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <p class="sidebar-title">Gestión</p>

        <div class="sidebar-link" onclick="showPanel('inicio')">📊 Panel</div>
        <div class="sidebar-link" onclick="showPanel('barberos')">👤 Barberos</div>
        <div class="sidebar-link" onclick="showPanel('clientes')">👤 Clientes</div>
        <div class="sidebar-link" onclick="showPanel('productos')">📦 Productos</div>
        <div class="sidebar-link" onclick="showPanel('servicios')">✂️ Servicios</div>
        <div class="sidebar-link" onclick="showPanel('horarios')">🕒 Horarios</div>
        <div class="sidebar-link" onclick="showPanel('reportes')">📈 Reportes</div>

        <div style="flex:1;"></div>

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

                            <input
                                type="time"
                                name="duracion"
                                value="<?= htmlspecialchars($servicioEditar['duracion'] ?? ''); ?>"
                                required>

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
                                            href="index.php?controller=servicio&action=eliminarServicio&id_servicio=<?= urlencode($s['id_servicio']); ?>"
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

        <!-- HORARIOS -->
        <div class="panel" id="panel-horarios">

            <h2 class="section-title">🕒 Horarios de Barberos</h2>

            <div class="admin-card">
                <p>Asigna los horarios de trabajo de cada barbero.</p>
            </div>

            <div class="section-card">
                <form action="index.php?controller=horario&action=guardarHorario" method="POST">
                    <div class="form-group">
                        <label>Barbero</label>
                        <select name="id_barbero" required>
                            <option value="">Seleccione...</option>
                            <?php if (!empty($barberos) && is_array($barberos)): ?>
                                <?php foreach ($barberos as $b): ?>
                                    <option value="<?= $b['id_usuario']; ?>">
                                        <?= $b['nombre'] . ' ' . $b['apellido']; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Desde (Fecha Inicio)</label>
                        <input type="date" name="fecha_inicio" min="<?= date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Hasta (Fecha Fin)</label>
                        <input type="date" name="fecha_fin" min="<?= date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Hora Inicio</label>
                        <input type="time" name="hora_inicio" required>
                    </div>

                    <div class="form-group">
                        <label>Hora Fin</label>
                        <input type="time" name="hora_fin" required>
                    </div>

                    <br>
                    <button type="submit" class="btn btn-primary">Guardar Horario</button>
                </form>
            </div>

            <!-- TABLA DE HORARIOS -->
            <table class="data-table" id="tablaHorarios">
                <thead>
                    <tr>
                        <th>Barbero</th>
                        <th>Fecha</th>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($horarios)): ?>
                        <?php foreach ($horarios as $h): ?>
                            <tr>
                                <td><?= $h['nombre'] . " " . $h['apellido']; ?></td>
                                <td><?= date('d/m/Y', strtotime($h['fecha'])); ?></td>
                                <td><?= date('g:i A', strtotime($h['hora_inicio'])); ?></td>
                                <td><?= date('g:i A', strtotime($h['hora_fin'])); ?></td>
                                <td>
                                    <a class="btn-danger"
                                        href="index.php?controller=horario&action=eliminarHorario&id_horario=<?= urlencode($h['id_horario']); ?>"
                                        onclick="return confirm('¿Desea eliminar este horario?')"
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

        </div>

        <!-- REPORTES -->
        <div class="panel" id="panel-reportes">
            <h2 class="section-title">📈 Reportes</h2>
            <p>Próximamente reportes dinámicos.</p>
        </div>

    </main>

    <!-- JS PANEL -->
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

        function initTablas(panel) {

            /* =========================
               BARBEROS
               ========================= */
            if (panel === 'barberos' && !tablaBarberos) {

                if (!document.getElementById('tablaBarberos')) return;

                tablaBarberos = $('#tablaBarberos').DataTable({
                    destroy: true,
                    dom: 'Bfrtip',
                    buttons: [{
                            extend: 'excelHtml5',
                            title: 'Kadosh - Barberos',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            title: 'Kadosh - Barberos',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Kadosh - Barberos',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        }
                    ],
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                    }
                });
            }

            /* =========================
            CLIENTES
            ========================= */
            if (panel === 'clientes' && !tablaClientes) {

                if (!document.getElementById('tablaClientes')) return;

                tablaClientes = $('#tablaClientes').DataTable({
                    destroy: true,
                    dom: 'Bfrtip',
                    buttons: [{
                            extend: 'excelHtml5',
                            title: 'Kadosh - Clientes',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            title: 'Kadosh - Clientes',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Kadosh - Clientes',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        }
                    ],
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                    }
                });
            }

            /* =========================
               PRODUCTOS
               ========================= */
            if (panel === 'productos' && !tablaProductos) {

                if (!document.getElementById('tablaProductos')) return;

                tablaProductos = $('#tablaProductos').DataTable({
                    destroy: true,
                    dom: 'Bfrtip',
                    buttons: [{
                            extend: 'excelHtml5',
                            title: 'Kadosh - Productos'
                        },
                        {
                            extend: 'pdfHtml5',
                            title: 'Kadosh - Productos',
                            pageSize: 'A4'
                        },
                        {
                            extend: 'print',
                            title: 'Kadosh - Productos'
                        }
                    ],
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                    }
                });
            }
            /* =========================
               SERVICIOS
            ========================= */

            if (panel === 'servicios' && !tablaServicios) {

                if (!document.getElementById('tablaServicios')) return;

                tablaServicios = $('#tablaServicios').DataTable({

                    destroy: true,

                    dom: 'Bfrtip',

                    buttons: [

                        {
                            extend: 'excelHtml5',
                            title: 'Kadosh - Servicios'
                        },

                        {
                            extend: 'pdfHtml5',
                            title: 'Kadosh - Servicios',
                            pageSize: 'A4'
                        },

                        {
                            extend: 'print',
                            title: 'Kadosh - Servicios'
                        }

                    ],

                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                    }

                });

            }

            /* =========================
   HORARIOS
========================= */

            if (panel === 'horarios' && !tablaHorarios) {

                if (!document.getElementById('tablaHorarios')) return;

                tablaHorarios = $('#tablaHorarios').DataTable({

                    destroy: true,

                    dom: 'Bfrtip',

                    buttons: [

                        {
                            extend: 'excelHtml5',
                            title: 'Kadosh - Horarios'
                        },

                        {
                            extend: 'pdfHtml5',
                            title: 'Kadosh - Horarios'
                        },

                        {
                            extend: 'print',
                            title: 'Kadosh - Horarios'
                        }

                    ],

                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                    }

                });

            }
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