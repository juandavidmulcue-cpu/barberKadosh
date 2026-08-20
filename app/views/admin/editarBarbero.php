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
        <a href="index.php?controller=admin&action=panel&panel=barberos" class="logout-btn">← Volver</a>
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
    
    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>