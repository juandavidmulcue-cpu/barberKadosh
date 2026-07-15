<?php

require_once 'app/controllers/Controller.php';
require_once 'app/config/conexion.php';
require_once 'app/models/AuthModel.php';

class AuthController extends Controller
{
    private $authModel;

    public function __construct()
    {
        parent::__construct();

        $db = Database::conectar();
        $this->authModel = new AuthModel($db);
    }

    /* =========================
       MÉTODO PRIVADO LOGIN
    ========================== */

    private function login($rol, $vista, $destino)
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $correo = trim($_POST['correo'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($correo === '' || $password === '') {

                $error = "Correo o contraseña vacíos";

            } else {

                $user = $this->authModel->getUserByEmailAndRole($correo, $rol);

                if ($user && password_verify($password, $user['password'])) {

                    $this->loginUser($user, $rol);

                    $this->redirect($destino);
                }

                $error = "Credenciales incorrectas";
            }
        }

        $this->view($vista, [
            'error' => $error
        ]);
    }

    /* =========================
       LOGIN CLIENTE
    ========================== */

    public function loginCliente()
    {
        $this->login(
            'cliente',
            'app/views/auth/login_cliente.php',
            'index.php?controller=cliente&action=perfil'
        );
    }

    /* =========================
       LOGIN BARBERO
    ========================== */

    public function loginBarbero()
    {
        $this->login(
            'barbero',
            'app/views/auth/login_barbero.php',
            'index.php?controller=barbero&action=perfil'
        );
    }

    /* =========================
       LOGIN ADMIN
    ========================== */

    public function loginAdmin()
    {
        $this->login(
            'admin',
            'app/views/auth/login_admin.php',
            'index.php?controller=admin&action=panel'
        );
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

            if (
                empty($id_usuario) ||
                empty($nombre) ||
                empty($apellido) ||
                empty($telefono) ||
                empty($correo) ||
                empty($password)
            ) {

                $error = "Todos los campos son obligatorios";

            } elseif (!ctype_digit($id_usuario)) {

                $error = "Documento solo números";

            } elseif (strlen($id_usuario) < 10 || strlen($id_usuario) > 11) {

                $error = "Documento inválido";

            } elseif (!ctype_digit($telefono)) {

                $error = "Teléfono solo números";

            } elseif (strlen($telefono) != 10) {

                $error = "Teléfono debe tener 10 dígitos";

            } elseif (
                strlen($password) < 8 ||
                !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)
            ) {

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

                if ($this->authModel->registerClient($data)) {

                    $this->redirect("index.php?controller=auth&action=loginCliente");
                }

                $error = "Error al registrar usuario";
            }
        }

        $this->view('app/views/auth/register.php', [
            'error' => $error
        ]);
    }
        /* =========================
       LOGOUT
    ========================== */

    public function logout()
    {
        $this->destroySession();

        $this->redirect("index.html");
    }

    /* =========================
       RESET PASSWORD
    ========================== */

    public function resetPassword()
    {
        $this->view('app/views/auth/reset_password.php');
    }

    /* =========================
       CAMBIAR PASSWORD
    ========================== */

    public function cambiarPassword()
    {
        if (!isset($_GET['id']) || !isset($_GET['token'])) {

            $this->redirect(
                "index.php?controller=auth&action=loginCliente"
            );
        }

        $id = $_GET['id'];
        $token = $_GET['token'];

        $this->view(
            'app/views/auth/cambiarContraseña.php',
            [
                'id' => $id,
                'token' => $token
            ]
        );
    }
}