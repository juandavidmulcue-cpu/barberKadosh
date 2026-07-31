<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Productos | Admin - Kadosh</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="app/public/css/global.css">
    <link rel="stylesheet" href="app/public/css/productos.css">
    <link rel="stylesheet" href="app/public/css/datatable.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>

<body>

    <header class="admin-header">
        <h1>Gestión de Productos</h1>
        <a href="index.php?controller=admin&action=panel&panel=productos" class="logout-btn">← Volver</a>
    </header>

    <main class="admin-panel">

        <!-- FORMULARIO CREAR PRODUCTO -->
        <div class="admin-card">
            <h3>Agregar Producto</h3>

            <form method="POST" action="index.php?controller=gestionProducto&action=guardar">
                <input type="text" name="nombre" placeholder="Nombre del producto" required>
                <input type="number" name="precio" placeholder="Precio" required step="0.01">
                <input type="number" name="stock" placeholder="stock" required step="1">
                <button type="submit" class="btn-primary">Agregar Producto</button>
            </form>
        </div>

        <!-- TABLA DE PRODUCTOS -->
        <div class="admin-card" style="grid-column: 1 / -1;">
            <table id="tablaProductos" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($productos) && is_array($productos)): ?>
                        <?php foreach ($productos as $p): ?>
                            <tr>
                                <td><?= $p['id_producto'] ?></td>
                                <td><?= htmlspecialchars($p['nombre']) ?></td>
                                <td>$<?= number_format($p['precio'], 2) ?></td>
                                <td>
                                    <a class="btn-danger"
                                        href="index.php?controller=gestionProducto&action=eliminarProducto&id_producto=<?= $p['id_producto'] ?>"
                                        onclick="return confirm('¿Eliminar este producto?')">
                                        Eliminar
                                    </a>
                                    <a class="btn-warning" href="index.php?controller=gestionProducto&action=editarProducto&id_producto=<?= $p['id_producto'] ?>">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">
                                No hay productos registrados
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tablaProductos').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                }
            });
        });
    </script>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>