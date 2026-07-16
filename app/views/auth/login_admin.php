<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login Admin | Kadosh</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="app/public/css/auth.css">
    <link rel="stylesheet" href="app/public/css/global.css">
</head>

<body class="auth-body">

    <div class="auth-container">

        <h2>Acceso Administrador</h2>

        <?php if (isset($error)): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?controller=auth&action=loginAdmin">

            <div class="form-group">
                <label>Correo</label>
                <input type="email" name="correo" required>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn-primary">Ingresar</button>
        </form>

        <a href="index.php?controller=auth&action=logout" class="back-home">← Volver al inicio</a>

    </div>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>
    
</body>

</html>