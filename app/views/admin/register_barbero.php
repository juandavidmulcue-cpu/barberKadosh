<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro | Kadosh Barber</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="public/css/global.css">
    <link rel="stylesheet" href="public/css/auth.css">
</head>

<body class="auth-body">

    <div class="auth-container">
        <h2>Registro de Barbero</h2>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?controller=admin&action=registerBarbero">

            <div class="form-group">
                <label>Documento de identidad</label>
                <input type="text" name="id_usuario" required>
            </div>

            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" required>
            </div>

            <div class="form-group">
                <label>Apellido</label>
                <input type="text" name="apellido" required>
            </div>

            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" required>
            </div>

            <div class="form-group">
                <label>Correo electrónico</label>
                <input type="email" name="correo" required>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn-primary">
                Registrar al barbero </button>
        </form>

        <script src="app/public/js/validaciones.js"></script>
        
        <a href="index.php?controller=admin&action=panel" class="back-home">← Volver</a>
    </div>

</body>

</html>