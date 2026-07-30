<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Barbero | Admin - Kadosh</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="app/public/css/global.css">
    <link rel="stylesheet" href="app/public/css/editarProducto.css">
    <link rel="stylesheet" href="app/public/css/admin.css">
</head>

<body>

    <header class="admin-header">
        <h1>Editar Barbero</h1>
        <a href="index.php?controller=admin&action=panel" class="logout-btn">← Volver</a>
    </header>

    <main class="admin-panel">

        <div class="admin-card">

            <?php if (!empty($barbero)): ?>

                <form method="POST" action="index.php?controller=gestionBarbero&action=actualizar">

                    <!-- ID oculto -->
                    <input type="hidden" name="id_usuario" value="<?= $barbero['id_usuario'] ?>">

                    <label>Nombre</label>
                    <input
                        type="text"
                        name="nombre"
                        value="<?= htmlspecialchars($barbero['nombre']) ?>"
                        required>

                    <label>Apellido</label>
                    <input
                        type="text"
                        name="apellido"
                        value="<?= htmlspecialchars($barbero['apellido']) ?>"
                        required>

                    <label>Telefono</label>
                    <input
                        type="text"
                        name="telefono"
                        value="<?= htmlspecialchars($barbero['telefono']) ?>"
                        required>

                    <label>Correo</label>
                    <input
                        type="email"
                        name="correo"
                        value="<?= htmlspecialchars($barbero['correo']) ?>"
                        required>

                    <button type="submit" class="btn-primary">
                        Guardar cambios
                    </button>

                </form>

            <?php else: ?>
                <p>No se encontró el barbero.</p>
            <?php endif; ?>

        </div>

    </main>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>