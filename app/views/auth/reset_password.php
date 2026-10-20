<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reset Password | Kadosh</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSS ABSOLUTO -->
  <link rel="stylesheet" href="app/public/css/resetPassword.css">
  <link rel="stylesheet" href="app/public/css/global.css">
</head>

<body class="auth-body">

  <div class="auth-card">

    <!-- LOGO -->
    <img src="app/public/assets/img/logo1.jpeg" class="auth-logo" alt="Kadosh">

    <h2>Recuperar contraseña</h2>
    <p class="auth-sub">Ingresa tu correo</p>

    <form action="/barberKadosh/app/logicamail.php" method="POST">

      <div class="form-group">
        <label>Correo electrónico</label>
        <input type="email" name="correo" required>
      </div>

      <?php if (isset($_SESSION['response'])): ?>
        <div class="auth-msg">
          <?= $_SESSION['response']; ?>
        </div>
        <?php unset($_SESSION['response']); ?>
      <?php endif; ?>

      <button class="btn-gold" name="send">Enviar enlace</button>

      <a href="/barberKadosh/index.php?controller=auth&action=loginCliente"
        class="btn btn-danger">Cancelar</a>

    </form>
  </div>

  <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
  <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>