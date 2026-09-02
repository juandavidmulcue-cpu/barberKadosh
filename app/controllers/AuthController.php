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

        // Inicializar contador de intentos
        if (!isset($_SESSION['login_intentos'])) {
            $_SESSION['login_intentos'] = 0;
        }

        // Inicializar tiempo de bloqueo
        if (!isset($_SESSION['login_bloqueado_hasta'])) {
            $_SESSION['login_bloqueado_hasta'] = 0;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verificar si el usuario está temporalmente bloqueado
            if (time() < $_SESSION['login_bloqueado_hasta']) {

                http_response_code(429);
                $codigoError = 429;
                require_once __DIR__ . '/../views/errors/error.php';
                exit;
            }

            $correo = trim($_POST['correo'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($correo === '' || $password === '') {
                $error = "Correo o contraseña vacíos";
            } else {

                $user = $this->authModel->getUserByEmailAndRole($correo, $rol);

                if ($user) {

                    // En AuthController.php dentro de private function login(...)

                    if (password_verify($password, $user['password'])) {
                        if ($user['estado'] === 'inactivo') {
                            $error = "Lo sentimos, tu cuenta se encuentra inactiva. Comunícate con el administrador.";
                        } else {
                            $_SESSION['login_intentos'] = 0;
                            $_SESSION['login_bloqueado_hasta'] = 0;

                            // Ejecutar el login base
                            $this->loginUser($user, $rol);

                            // Guardar explícitamente todos los campos requeridos por la vista y el modal:
                            $_SESSION['id']          = $user['id_usuario'];
                            $_SESSION['nombre']      = $user['nombre'];
                            $_SESSION['apellido']    = $user['apellido'];
                            $_SESSION['telefono']    = $user['telefono'];
                            $_SESSION['correo']      = $user['correo'];
                            $_SESSION['foto'] = $user['foto'] ?? '';
                            $_SESSION['nombre_rol']  = $rol;

                            $this->redirect($destino);
                            exit;
                        }
                    } else {

                        // Contraseña incorrecta
                        $_SESSION['login_intentos']++;
                        $error = "Credenciales incorrectas.";
                    }
                } else {

                    // Usuario inexistente o correo incorrecto
                    $_SESSION['login_intentos']++;
                    $error = "Credenciales incorrectas.";
                }

                // Si llega a 5 intentos fallidos
                if ($_SESSION['login_intentos'] >= 5) {

                    $_SESSION['login_bloqueado_hasta'] = time() + 5;
                    http_response_code(429);
                    $codigoError = 429;

                    require_once __DIR__ . '/../views/errors/error.php';
                    exit;
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

        // Evitamos el header("Location: ...") y usamos replace
        echo '<script>window.location.replace("index.html");</script>';
        exit;
    }
}
