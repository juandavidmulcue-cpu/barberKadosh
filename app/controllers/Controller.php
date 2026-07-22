<?php

require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../core/View.php';
require_once __DIR__ . '/../core/Auth.php';

/**
 * Clase base Controller
 * -------------------------------------------------
 * Refactor SRP: antes esta superclase concentraba
 * sesión + vistas + redirección + autorización + logout.
 * Ahora cada responsabilidad vive en su propia clase
 * (Session, View, Auth) y Controller solo DELEGA.
 *
 * Los métodos protegidos conservan la misma firma,
 * por lo que los controladores hijos siguen funcionando
 * exactamente igual que antes.
 */
class Controller
{
    public function __construct()
    {
        Session::iniciar();
    }

    /**
     * Carga una vista (delegado a View).
     */
    protected function view($ruta, $data = [])
    {
        View::render($ruta, $data);
    }

    /**
     * Redirecciona a una URL.
     */
    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Verifica que el usuario haya iniciado sesión (delegado a Auth).
     */
    protected function requireLogin()
    {
        Auth::requireLogin();
    }

    /**
     * Verifica el rol del usuario (delegado a Auth).
     */
    protected function requireRole($rol)
    {
        Auth::requireRole($rol);
    }

    /**
     * Guarda datos del usuario en sesión (delegado a Session).
     */
    protected function loginUser($user, $rol)
    {
        Session::guardarUsuario($user, $rol);
    }

    /**
     * Cierra la sesión (delegado a Session).
     * Se mantiene pública y con la redirección original
     * para conservar el comportamiento existente.
     */

    /* =========================
       LOGOUT
    ========================== */
    public function destroySession()

    {
        Session::destruir();

        header("Location: index.html");
        exit;
    }
}
