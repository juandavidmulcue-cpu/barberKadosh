<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Producto | Admin - Kadosh</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="app/public/css/global.css">
    <link rel="stylesheet" href="app/public/css/admin.css">
    <link rel="stylesheet" href="app/public/css/editarProducto.css">
</head>

<body>

    <header class="admin-header">
        <h1>Editar Producto</h1>
        <a href="index.php?controller=gestionProducto&action=listar" class="logout-btn">← Volver</a>
    </header>

    <main class="admin-panel">

        <div class="admin-card">

            <?php if (!empty($producto)): ?>

                <form method="POST" action="index.php?controller=gestionProducto&action=actualizarProducto">

                    <!-- ID oculto -->
                    <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">

                    <label>Nombre</label>
                    <input
                        type="text"
                        name="nombre"
                        value="<?= htmlspecialchars($producto['nombre']) ?>"
                        required>

                    <label>Precio</label>
                    <input
                        type="number"
                        name="precio"
                        step="0.01"
                        value="<?= $producto['precio'] ?>"
                        required>

                    <label>Stock</label>
                    <input
                        type="number"
                        name="stock"
                        value="<?= $producto['stock'] ?>"
                        required>

                    <button type="submit" class="btn-primary">
                        Guardar cambios
                    </button>

                </form>

            <?php else: ?>
                <p>No se encontró el producto.</p>
            <?php endif; ?>

        </div>

    </main>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>