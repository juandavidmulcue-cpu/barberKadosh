<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Recuperar contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="app/public/css/auth.css">
  <link rel="stylesheet" href="app/public/css/global.css">
</head>

<body class="auth-body">

  <div class="auth-container">
    <h2>Recuperar contraseña</h2>
    <p class="login-sub">¿Cómo deseas recibir el código?</p>

    <a href="index.php?controller=auth&action=resetPassword" class="btn-primary">
      Correo electrónico
    </a>

    <a href="index.php?controller=auth&action=codeSent&type=sms" class="btn-primary" style="margin-top:10px;">
      SMS
    </a>

    <a href="index.php?controller=auth&action=loginCliente" class="back-home">
      ← Volver
    </a>
  </div>
  <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
  <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>