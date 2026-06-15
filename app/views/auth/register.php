<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | Kadosh Barber</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="app/public/css/auth.css">
    <link rel="stylesheet" href="app/public/css/global.css">
</head>
<body class="auth-body">

<div class="auth-container">
    <h2>Registro de Cliente</h2>

    <?php if (!empty($error)): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?controller=auth&action=registerCliente">

        <div class="form-group">
            <label>Documento</label>
            <input type="text" name="id_usuario" maxlength="11" required>
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
        
        <p id="mensaje" style="color:red;"></p>

        <button type="submit" class="btn-primary">
            Registrarme
        </button>
    </form>

	<script src="app/public/js/validaciones.js"></script>

    <p class="auth-link">
        ¿Ya tienes cuenta?
        <a href="index.php?controller=auth&action=loginCliente">
            Inicia sesión
        </a>
    </p>

    <a href="index.html" class="back-home">← Volver al inicio</a>
</div>

</body>
</html>