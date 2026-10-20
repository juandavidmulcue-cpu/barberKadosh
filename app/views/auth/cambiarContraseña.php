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
  <style>
    /* ====== CONTENEDOR GENERAL ====== */
    .auth-body {
      min-height: 100vh;
      margin: 0;
      background: radial-gradient(circle at top,
          #2b0a3d,
          #0b0610 60%,
          #000000);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont;
      color: #f5f5f5;
    }

    /* ====== WRAPPER ====== */
    .auth-wrapper {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* ====== CARD ====== */
    .card {
      width: 100%;
      max-width: 420px;
      background: linear-gradient(160deg,
          #140018,
          #0b0610);
      border-radius: 18px;
      box-shadow:
        0 20px 40px rgba(0, 0, 0, 0.8),
        0 0 0 1px rgba(212, 175, 55, 0.08);
      overflow: hidden;
      animation: fadeIn 0.6s ease-out;
    }

    /* ====== HEADER ====== */
    .card-header {
      padding: 24px;
      text-align: center;
      border-bottom: 1px solid rgba(212, 175, 55, 0.15);
    }

    .card-header p {
      margin: 0;
      font-size: 22px;
      font-weight: 600;
      letter-spacing: 0.5px;
      color: #d4af37;
      /* dorado */
    }

    /* ====== BODY ====== */
    .card-body {
      padding: 28px;
    }

    /* Mensajes de error */
    .card-body h2 {
      margin: 0 0 16px;
      padding: 12px;
      background: rgba(255, 0, 0, 0.08);
      border: 1px solid rgba(255, 0, 0, 0.25);
      border-radius: 10px;
      font-size: 14px;
      color: #ffb3b3;
      text-align: center;
    }

    /* ====== LABELS ====== */
    .form-label {
      display: block;
      margin-bottom: 6px;
      font-size: 14px;
      color: #cbb7e2;
    }

    /* ====== INPUTS ====== */
    .form-control {
      width: 100%;
      padding: 12px 14px;
      border-radius: 10px;
      border: 1px solid rgba(212, 175, 55, 0.25);
      background: rgba(0, 0, 0, 0.6);
      color: #ffffff;
      outline: none;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    .form-control::placeholder {
      color: #888;
    }

    .form-control:focus {
      border-color: #d4af37;
      box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.25);
      background: rgba(0, 0, 0, 0.75);
    }

    /* ====== FOOTER ====== */
    .card-footer {
      padding: 22px;
      display: flex;
      gap: 12px;
      justify-content: space-between;
      border-top: 1px solid rgba(212, 175, 55, 0.15);
    }

    /* ====== BOTONES ====== */
    .btn {
      flex: 1;
      padding: 12px;
      border-radius: 10px;
      text-decoration: none;
      text-align: center;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
    }

    /* Botón principal (Guardar) */
    .btn-primary {
      background: linear-gradient(135deg,
          #d4af37,
          #b9962e);
      color: #1a1a1a;
    }

    .btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(212, 175, 55, 0.4);
    }

    /* Botón cancelar */
    .btn-danger {
      background: transparent;
      color: #d4af37;
      border: 1px solid rgba(212, 175, 55, 0.5);
    }

    .btn-danger:hover {
      background: rgba(212, 175, 55, 0.1);
      box-shadow: inset 0 0 0 1px rgba(212, 175, 55, 0.4);
    }

    /* ====== ANIMACIÓN ====== */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(12px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>


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
          <?php unset($_SESSION['error']);
          endif; ?>

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
<script src="app/public/js/validaciones.js"></script>

</html>