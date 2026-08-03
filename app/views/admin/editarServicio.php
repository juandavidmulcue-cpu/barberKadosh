<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Servicio | Admin - Kadosh</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="app/public/css/global.css">
    <link rel="stylesheet" href="app/public/css/editarProducto.css">
    <link rel="stylesheet" href="app/public/css/admin.css">
</head>

<body>

    <header class="admin-header">
        <h1>Editar Servicio</h1>
        <a href="index.php?controller=admin&action=panel" class="logout-btn">← Volver</a>
    </header>

    <main class="admin-panel">

        <div class="admin-card">

            <?php if (!empty($servicio)): ?>

                <form method="POST" action="index.php?controller=servicio&action=actualizarServicio">

                    <!-- ID oculto -->
                    <input type="hidden" name="id_servicio" value="<?= $servicio['id_servicio'] ?>">

                    <label>Nombre</label>
                    <input
                        type="text"
                        name="nombre"
                        value="<?= htmlspecialchars($servicio['nombre']) ?>"
                        required>

                    <label>Descripción</label>
                    <input
                        type="text"
                        name="descripcion"
                        value="<?= htmlspecialchars($servicio['descripcion']) ?>"
                        required>

                    <label>Precio</label>
                    <input
                        type="number"
                        name="precio"
                        value="<?= htmlspecialchars($servicio['precio']) ?>"
                        step="0.01"
                        min="0"
                        required>

                    <label>Duración</label>
                    <input
                        type="text"
                        name="duracion"
                        value="<?= htmlspecialchars($servicio['duracion']) ?>"
                        required>

                    <button type="submit" class="btn-primary">
                        Guardar cambios
                    </button>

                </form>

            <?php else: ?>
                <p>No se encontró el servicio.</p>
            <?php endif; ?>

        </div>

    </main>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>