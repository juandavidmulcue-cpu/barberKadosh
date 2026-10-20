<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login Cliente | Kadosh</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="app/public/css/auth.css">
    <link rel="stylesheet" href="app/public/css/global.css">
</head>

<body class="auth-body">

    <div class="auth-container">

        <!-- 🔝 BOTÓN SUPERIOR -->
        <div class="login-switch">
            <a href="index.php?controller=auth&action=loginBarbero" class="switch-btn">
                Barbero
            </a>
        </div>

        <h2>Iniciar Sesión</h2>
        <p class="login-sub">Clientes</p>

        <?php if (isset($error)): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?controller=auth&action=loginCliente">

            <div class="form-group">
                <label>Correo</label>
                <input type="correo" name="correo" required>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn-primary">Entrar</button>

            <?php
            if (isset($_GET['message'])) {

            ?>
                <div class="alert alert-primary" role="alert">
                    <?php
                    switch ($_GET['message']) {
                        case 'ok':
                            echo 'Por favor, revisa tu correo';
                            break;

                        case 'success_password':
                            echo 'Inicia sesión con tu nueva contraseña';
                            break;

                        default:
                            echo 'Algo salió mal, intenta de nuevo';
                            break;
                    }
                    ?>

                </div>
            <?php
            }
            ?>
        </form>

        <p class="auth-link">
            ¿No tienes cuenta?
            <a href="index.php?controller=auth&action=register">Regístrate</a>
        </p>
        <p class="auth-link">
            <a href="index.php?controller=password&action=resetPassword">¿Olvidaste la contraseña?</a>
        </p>
        <a href="index.php?controller=auth&action=logout" class="back-home">← Volver al inicio</a>

    </div>
    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>
</body>

</html>