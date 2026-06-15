<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Admin | Kadosh</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="app/public/css/admin.css">
    <link rel="stylesheet" href="app/public/css/global.css">
</head>

<body>

    <header class="admin-header">
        <h1>Panel Administrador</h1>

    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <p class="sidebar-title">Gestión</p>

        <div class="sidebar-link active" onclick="showPanel('inicio')">📊 Panel</div>
        <div class="sidebar-link" onclick="showPanel('barberos')">👤 Barberos</div>
        <div class="sidebar-link" onclick="showPanel('productos')">📦 Productos</div>
        <div class="sidebar-link" onclick="showPanel('reportes')">📈 Reportes</div>

        <div style="flex:1;"></div>

        <a href="index.php?controller=auth&action=logout">Cerrar sesión</a>

    </aside>


    <main class="main-content">

        <!-- INICIO -->
        <div class="panel active" id="panel-inicio">
            <h2>Bienvenido, ADMIN 👋</h2>
            <p>Panel administrativo activo.</p>
        </div>

        <!-- Barberos -->

        <div class="panel" id="panel-barberos">

            <h2 class="section-title">👤 Barberos</h2>

            <div class="admin-card">
                <p>Gestionar clientes y barberos</p>
            </div>

            <div class="action-row">
                <h3>Registrar Barbero</h3>
                <p>Crear cuentas de barberos</p>
                <a href="index.php?controller=admin&action=registerBarbero"
                    class="btn btn-primary">
                    + Registrar Barbero
                </a>
            </div>

            <br>

            <div class="section-card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($barberos)): ?>
                            <?php foreach ($barberos as $b): ?>
                                <tr>
                                    <td><?php echo $b['id_usuario']; ?></td>
                                    <td><?php echo $b['nombre']; ?></td>
                                    <td><?php echo $b['apellido']; ?></td>
                                    <td><?php echo $b['telefono']; ?></td>
                                    <td><?php echo $b['correo']; ?></td>
                                    <td>
                                        <a class="btn-danger"
                                        href="index.php?controller=admin&action=eliminarBarbero&id=<?= $b['id_usuario'] ?>"
                                        onclick="return confirm('¿Eliminar este barbero?')">
                                        Eliminar
                                    </a>
                                    <a class="btn-warning"
                                    href="index.php?controller=admin&action=editarBarbero&id_usuario=<?= $b['id_usuario'] ?>">
                                    Editar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No hay barberos registrados</td>
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
                <a href="index.php?controller=admin&action=productos"
                    class="btn btn-primary">
                    Gestionar Productos
                </a>
            </div>

            <br>

            <div class="section-card">

                <table class="data-table">
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

        <!-- REPORTES -->

        <div class="panel" id="panel-reportes">
            <h2 class="section-title">📈 Reportes</h2>
            <p>Próximamente reportes dinámicos.</p>
        </div>

    </main>

    <script>
        function showPanel(panel) {
            let panels = document.querySelectorAll('.panel');

            panels.forEach(p => {
                p.classList.remove('active');
            });

            document.getElementById('panel-' + panel).classList.add('active');
        }
    </script>

</body>

</html>