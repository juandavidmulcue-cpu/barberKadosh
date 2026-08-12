<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require './config/setting.php';
require './config/conexion.php';

/* ===============================
   ENVIAR CORREO DE RECUPERACIÓN
================================ */
if (isset($_POST['send'])) {

    if (empty($_POST['correo'])) {
        $_SESSION['response'] = 'Ingrese su correo';
        header("Location: ../index.php?controller=password&action=resetPassword");
        exit;
    }

    $usuario = ConsultaUsuarioPorEmail($_POST['correo']);

    if (!$usuario) {
        $_SESSION['response'] = 'Correo no registrado';
        header("Location: ../index.php?controller=password&action=resetPassword");
        exit;
    }

    $token  = bin2hex(random_bytes(32));
    $expira = time() + 120; // 2 minutos

    updateUser($token, $expira, $usuario->id_usuario);
    EnviarCorreoResetPassword(
        $usuario->correo,
        $usuario->nombre,
        $usuario->id_usuario,
        $token
    );

    $_SESSION['response'] = 'Revisa tu correo';
    header("Location: ../index.php?controller=auth&action=loginCliente");
    exit;
}

/* ===============================
   GUARDAR NUEVA CONTRASEÑA
================================ */
if (isset($_POST['save'])) {

    if ($_POST['password'] !== $_POST['new_password']) {
        $_SESSION['error'] = 'Las contraseñas no coinciden';
        header("Location: ../index.php?controller=password&action=cambiarPassword&id=" . $_POST['id_usuario'] . "&token=" . $_POST['token']);
        exit;
    }

    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    updateUserID($password, $_POST['id_usuario']);

    $_SESSION['response'] = 'Contraseña actualizada';
    header("Location: ../index.php?controller=auth&action=loginCliente");
    exit;
}

/* ===============================
   FUNCIONES
================================ */

function ConsultaUsuarioPorEmail($correo)
{
    $db = new Database();
    $sql = "SELECT * FROM usuarios WHERE correo = :correo LIMIT 1";

    $stmt = $db->conectar()->prepare($sql);
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ);
}

function updateUser($token, $expira, $id)
{
    $db = new Database();
    $sql = "UPDATE usuarios 
            SET token_password = :token,
                expired_session = :expira,
                request_password = '1'
            WHERE id_usuario = :id";

    $stmt = $db->conectar()->prepare($sql);
    $stmt->execute([
        ':token' => $token,
        ':expira' => $expira,
        ':id' => $id
    ]);
}

function updateUserID($password, $id)
{
    $db = new Database();
    $sql = "UPDATE usuarios 
            SET password = :password,
                token_password = NULL,
                expired_session = NULL,
                request_password = '0'
            WHERE id_usuario = :id";

    $stmt = $db->conectar()->prepare($sql);
    $stmt->execute([
        ':password' => $password,
        ':id' => $id
    ]);
}

function EnviarCorreoResetPassword($correo, $nombre, $id, $token)
{

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = USERNAME;
        $mail->Password   = PASSWORD;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom(USERNAME, 'Recupera tu clave');
        $mail->addAddress($correo, $nombre);

        $link = "http://localhost/barberKadosh/app/views/auth/cambiarContraseña.php?id=$id&token=$token";

        $mail->isHTML(true);
        $mail->Subject = 'Recupera tu clave aqui';
        $mail->Body = "
            Hola <b>$nombre</b><br><br>
            Haz clic en el enlace para cambiar tu contraseña y poder acceder correctamente a tu cuenta, y agendar tus proximas citas:<br>
            <a href='$link'>Cambiar contraseña</a><br><br>
            <small>Este enlace expira en 2 minutos</small>
        ";

        $mail->send();
    } catch (Exception $e) {
        die("Error al enviar correo");
    }
}
