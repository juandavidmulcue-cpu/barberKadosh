<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro | Kadosh Barber</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="app/public/css/global.css">
    <link rel="stylesheet" href="app/public/css/index.css">
    <link rel="stylesheet" href="app/public/css/auth.css">
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

            <div id="mensaje" class="error-msg" style="color: #ff9999; background: rgba(214, 40, 40, 0.15); border: 1px solid #d62828; padding: 12px; border-radius: 8px; margin-bottom: 15px; display: none;"></div>

            <button type="submit" class="btn-primary">
                Registrar al barbero </button>
        </form>

        <script src="app/public/js/validaciones.js"></script>
        
        <a href="index.php?controller=admin&action=panel&panel=barberos" class="logout-btn">← Volver</a>
    </div>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>
    
</body>

</html>