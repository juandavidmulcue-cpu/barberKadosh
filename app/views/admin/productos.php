<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Productos | Admin - Kadosh</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
<link rel="stylesheet" href="public/css/global.css">
<link rel="stylesheet" href="public/css/admin.css">
<link rel="stylesheet" href="public/css/datatable.css">
<link rel="stylesheet" href="public/css/producto/productos.css">

<!-- DataTables -->
<link rel="stylesheet" href="/barberKadosh/app/public/css/producto/productos.css">

<body>

    <a href="index.php?controller=admin&action=panel" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i>
    Volver
</a>

    <main class="admin-panel">

        <!-- FORMULARIO CREAR PRODUCTO -->
        <div class="admin-card">
            <h3>Agregar Producto</h3>

            <form method="POST" action="index.php?controller=admin&action=guardarProducto">
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
   href="index.php?controller=producto&action=eliminarProducto&id_producto=<?= $p['id_producto'] ?>"
   onclick="return confirm('¿Eliminar este producto?')">
    Eliminar
</a>

<a class="btn-warning"
   href="index.php?controller=admin&action=editarProducto&id_producto=<?= $p['id_producto'] ?>">
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
        },

        paging: false,
        info: false,
        lengthChange: false,
        ordering: false,
        searching: true

    });

});
</script>

</body>

</html>