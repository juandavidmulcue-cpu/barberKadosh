<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login Barbero | Kadosh</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="app/public/css/auth.css">
    <link rel="stylesheet" href="app/public/css/global.css">
</head>

<body class="auth-body">

    <div class="auth-container">

        <!-- 🔝 BOTÓN SUPERIOR -->
        <div class="login-switch">
            <a href="index.php?controller=auth&action=loginCliente" class="switch-btn">
                Cliente
            </a>
        </div>

        <h2>Acceso Barbero</h2>

        <?php if (isset($error)): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?controller=auth&action=loginBarbero">

            <div class="form-group">
                <label>Correo</label>
                <input type="email" name="correo" placeholder="Ingresa tu correo" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>

                <div class="campo-password">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Ingresa tu contraseña"
                        required>

                    <button
                        type="button"
                        class="btn-ojo"
                        id="btnOjo"
                        onclick="alternarPassword()"
                        aria-label="Mostrar contraseña"
                        title="Mostrar contraseña">
                        <svg
                            id="iconoOjo"
                            xmlns="http://www.w3.org/2000/svg"
                            width="22"
                            height="22"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
            </div>
            <a href="index.php?controller=password&action=resetPassword">¿Olvidaste la contraseña?</a>
            </p>
            <a href="index.php?controller=auth&action=loginCliente" class="back-home">← Volver al inicio</a>
            <button type="submit" class="btn-primary">Ingresar</button>
        </form>

    </div>

    <script>
        function alternarPassword() {
            const campo = document.getElementById('password');
            const boton = document.getElementById('btnOjo');
            const icono = document.getElementById('iconoOjo');

            if (!campo || !boton || !icono) {
                return;
            }

            if (campo.type === 'password') {
                // Mostrar contraseña
                campo.type = 'text';

                // Cambiar al ojo tachado
                icono.innerHTML = `
            <path d="M3 3l18 18"/>
            <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
            <path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a15.5 15.5 0 0 1-4 4.8"/>
            <path d="M6.2 6.2C3.5 8 2 12 2 12s3.5 7 10 7c1 0 2-.2 2.9-.5"/>
        `;

                boton.setAttribute('aria-label', 'Ocultar contraseña');
                boton.setAttribute('title', 'Ocultar contraseña');

            } else {
                // Ocultar contraseña
                campo.type = 'password';

                // Restaurar el ojo normal
                icono.innerHTML = `
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
            <circle cx="12" cy="12" r="3"/>
        `;

                boton.setAttribute('aria-label', 'Mostrar contraseña');
                boton.setAttribute('title', 'Mostrar contraseña');
            }
        }
    </script>
    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>