<?php

class Controller
{
    public function __construct()
    {
        $this->startSession();
    }

    /**
     * Inicia la sesión si aún no existe.
     */
    protected function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Carga una vista.
     */
    protected function view($ruta, $data = [])
    {
        if (!empty($data)) {
            extract($data);
        }

        require $ruta;
    }

    /**
     * Redirecciona.
     */
    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Verifica que el usuario haya iniciado sesión.
     */
    protected function requireLogin()
    {
        if (!isset($_SESSION['id'])) {
            $this->redirect("index.php");
        }
    }

    /**
     * Verifica el rol del usuario.
     */
    protected function requireRole($rol)
    {
        $this->requireLogin();

        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $rol) {
            $this->redirect("index.php");
        }
    }

    /* =========================
       LOGOUT
    ========================== */
    public function destroySession()

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


    /**
     * Guarda datos del usuario en sesión.
     */
    protected function loginUser($user, $rol)
    {
        $_SESSION['id'] = $user['id_usuario'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $rol;
    }
}
