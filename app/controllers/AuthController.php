<?php

require_once 'app/config/conexion.php';
require_once 'app/models/AuthModel.php';

class AuthController
{
    private $authModel;

    public function __construct()
    {
        $db = Database::conectar();
        $this->authModel = new AuthModel($db);

        // session NO aquí obligatoria, pero puedes manejarla global si quieres
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /* =========================
       LOGIN CLIENTE
    ========================== */
    public function loginCliente()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $correo = trim($_POST['correo'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($correo === '' || $password === '') {
                $error = "Correo o contraseña vacíos";
            } else {

                $user = $this->authModel->getUserByEmailAndRole($correo, 'cliente');

                if ($user && password_verify($password, $user['password'])) {

                    $_SESSION['id'] = $user['id_usuario'];
                    $_SESSION['nombre'] = $user['nombre'];
                    $_SESSION['rol'] = 'cliente';

                    header("Location: index.php?controller=cliente&action=perfil");
                    exit;
                }

                $error = "Credenciales incorrectas";
            }
        }

        require 'app/views/auth/login_cliente.php';
    }

    /* =========================
       LOGIN BARBERO
    ========================== */
    public function loginBarbero()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $correo = trim($_POST['correo'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($correo === '' || $password === '') {
                $error = "Correo o contraseña vacíos";
            } else {

                $user = $this->authModel->getUserByEmailAndRole($correo, 'barbero');

                if ($user && password_verify($password, $user['password'])) {

                    $_SESSION['id'] = $user['id_usuario'];
                    $_SESSION['nombre'] = $user['nombre'];
                    $_SESSION['rol'] = 'barbero';

                    header("Location: index.php?controller=barbero&action=perfil");
                    exit;
                }

                $error = "Credenciales incorrectas";
            }
        }

        require 'app/views/auth/login_barbero.php';
    }

    /* =========================
       LOGIN ADMIN
    ========================== */
    public function loginAdmin()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $correo = trim($_POST['correo'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($correo === '' || $password === '') {
                $error = "Correo o contraseña vacíos";
            } else {

                $user = $this->authModel->getUserByEmailAndRole($correo, 'admin');

                if ($user && password_verify($password, $user['password'])) {

                    $_SESSION['id'] = $user['id_usuario'];
                    $_SESSION['nombre'] = $user['nombre'];
                    $_SESSION['rol'] = 'admin';

                    header("Location: index.php?controller=admin&action=panel");
                    exit;
                }

                $error = "Credenciales incorrectas";
            }
        }

        require 'app/views/auth/login_admin.php';
    }

    /* =========================
       REGISTRO CLIENTE
    ========================== */
    public function registerCliente()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id_usuario = trim($_POST['id_usuario']);
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $telefono = trim($_POST['telefono']);
            $correo = trim($_POST['correo']);
            $password = $_POST['password'];

            // VALIDACIONES
            if (
                empty($id_usuario) || empty($nombre) || empty($apellido) ||
                empty($telefono) || empty($correo) || empty($password)
            ) {
                $error = "Todos los campos son obligatorios";
            } elseif (!ctype_digit($id_usuario)) {
                $error = "Documento solo números";
            } elseif (strlen($id_usuario) < 10 || strlen($id_usuario) > 11) {
                $error = "Documento inválido";
            } elseif (!ctype_digit($telefono)) {
                $error = "Teléfono solo números";
            } elseif (strlen($telefono) !== 10) {
                $error = "Teléfono debe tener 10 dígitos";
            } elseif (strlen($password) < 8 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
                $error = "Contraseña débil";
            } else {

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $data = [
                    ':id' => $id_usuario,
                    ':rol' => 3,
                    ':nombre' => $nombre,
                    ':apellido' => $apellido,
                    ':telefono' => $telefono,
                    ':correo' => $correo,
                    ':password' => $passwordHash
                ];

                $ok = $this->authModel->registerClient($data);

                if ($ok) {
                    header("Location: index.php?controller=auth&action=loginCliente");
                    exit;
                } else {
                    $error = "Error al registrar usuario";
                }
            }
        }

        require 'app/views/auth/register.php';
    }

    /* =========================
       LOGOUT
    ========================== */
    public function logout()

    {

        session_start();
        // destruir variables
        $_SESSION = [];

        // destruir cookie de sesión

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 420, '/');
        }
        // destruir sesión
        session_destroy();

        // asegurar que no se reutilice

        session_write_close();

        header("Location: index.html");

        exit;
    }

    public function resetPassword()
    {
        require 'app/views/auth/reset_password.php';
    }

    public function cambiarPassword()
    {
        if (!isset($_GET['id'], $_GET['token'])) {
            header("Location: index.php?controller=auth&action=loginCliente");
            exit;
        }

        $id = $_GET['id'];
        $token = $_GET['token'];

        require 'app/views/auth/cambiarContraseña.php';
    }
}
