<?php

require_once 'app/controllers/Controller.php';
require_once 'app/config/conexion.php';
require_once 'app/models/AuthModel.php';

/**
 * AuthController
 * -------------------------------------------------
 * Responsabilidad única: autenticación (inicio y cierre
 * de sesión). El registro y la recuperación de contraseña
 * se movieron a RegistroController y PasswordController.
 */
class AuthController extends Controller
{
    private $authModel;

    public function __construct()
    {
        parent::__construct();
        $this->authModel = new AuthModel(Database::conectar());
    }

    /**
     * Lógica común de inicio de sesión (DRY).
     */
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

                if ($user) {

                    if (password_verify($password, $user['password'])) {

                        // Verificar si el usuario está inactivo
                        if ($user['estado'] === 'inactivo') {

                            $error = "Lo sentimos, tu cuenta se encuentra inactiva. Comunícate con el administrador.";

                        } else {

                            $this->loginUser($user, $rol);
                            $this->redirect($destino);
                            exit;
                        }

                    } else {

                        $error = "Credenciales incorrectas.";

                    }

                } else {

                    $error = "Credenciales incorrectas.";

                }
            }
        }

        $this->view($vista, ['error' => $error]);
    }

    public function loginCliente()
    {
        $this->login(
            'cliente',
            'app/views/auth/login_cliente.php',
            'index.php?controller=cliente&action=perfil'
        );
    }

    public function loginBarbero()
    {
        $this->login(
            'barbero',
            'app/views/auth/login_barbero.php',
            'index.php?controller=barbero&action=perfil'
        );
    }

    public function loginAdmin()
    {
        $this->login(
            'admin',
            'app/views/auth/login_admin.php',
            'index.php?controller=admin&action=panel'
        );
    }

    public function logout()
    {
        $this->destroySession();
        $this->redirect("index.html");
    }
}