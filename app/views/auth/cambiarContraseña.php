<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';

if (!isset($_GET['id'], $_GET['token'])) {
    header("Location: login.php");
    exit;
}

$id     = $_GET['id'];
$token  = $_GET['token'];
$tiempo = time();

$db  = new Database();
$pdo = $db->conectar();

$sql = "SELECT id_usuario 
        FROM usuarios 
        WHERE id_usuario = :id
          AND token_password = :token
          AND expired_session > :tiempo
          AND request_password = '1'
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->bindParam(':token', $token);
$stmt->bindParam(':tiempo', $tiempo);
$stmt->execute();

$usuario = $stmt->fetch(PDO::FETCH_OBJ);

if (!$usuario) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="barberKadosh/app/public/css/cambiarContrasena.css">
</head>

<body class="auth-body">

  <div class="auth-wrapper">

    <div class="card">

      <div class="card-header">
        <p>Cambiar Contraseña</p>
      </div>

      <form action="../../logicamail.php" method="POST">

        <div class="card-body">

          <?php if (isset($_SESSION['error'])): ?>
            <h2><?= $_SESSION['error']; ?></h2>
          <?php unset($_SESSION['error']); endif; ?>

          <label class="form-label">Nueva contraseña</label>
          <input type="password" name="password" class="form-control" required>

          <label class="form-label" style="margin-top:14px;">Confirmar contraseña</label>
          <input type="password" name="new_password" class="form-control" required>

          <input type="hidden" name="id_usuario" value="<?= $usuario->id_usuario ?>">

        </div>

        <div class="card-footer">
          <button class="btn btn-primary" name="save">Guardar</button>
          <a href="/barberKadosh/index.php?controller=auth&action=loginCliente"
             class="btn btn-danger">Cancelar</a>
        </div>

      </form>

    </div>

  </div>

</body>
</html>