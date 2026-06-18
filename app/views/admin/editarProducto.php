<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>editarProducto | Admin - Kadosh</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <!-- CSS -->
<link rel="stylesheet" href="public/css/global.css">
<link rel="stylesheet" href="public/css/admin.css">
<link rel="stylesheet" href="/barberKadosh/app/public/css/producto/editarProducto.css">

<body>

<header class="admin-header">
    <h1>Editar Producto</h1>
    <a href="index.php?controller=admin&action=productos" class="logout-btn">← Volver</a>
</header>

<main class="admin-panel">

    <div class="admin-card">

        <?php if (!empty($producto)): ?>

            <form method="POST" action="index.php?controller=admin&action=actualizarProducto">

                <!-- ID oculto -->
                <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">

                <label>Nombre</label>
                <input
                    type="text"
                    name="nombre"
                    value="<?= htmlspecialchars($producto['nombre']) ?>"
                    required
                >

                <label>Precio</label>
                <input
                    type="number"
                    name="precio"
                    step="0.01"
                    value="<?= $producto['precio'] ?>"
                    required
                >

                <label>Stock</label>
                <input
                    type="number"
                    name="stock"
                    value="<?= $producto['stock'] ?>"
                    required
                >

                <button type="submit" class="btn-primary">
                    Guardar cambios
                </button>

            </form>

        <?php else: ?>
            <p>No se encontró el producto.</p>
        <?php endif; ?>

    </div>

</main>

</body>
</html>