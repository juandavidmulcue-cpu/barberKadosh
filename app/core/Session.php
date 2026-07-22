<?php

/**
 * Clase Session
 * -------------------------------------------------
 * Responsabilidad única (SRP): gestionar el ciclo de
 * vida de la sesión PHP y los datos del usuario logueado.
 * Antes esta lógica vivía mezclada en la superclase Controller.
 */
class Session
{
    /**
     * Inicia la sesión si aún no existe.
     */
    public static function iniciar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Guarda los datos del usuario autenticado en la sesión.
     */
    public static function guardarUsuario($user, $rol)
    {
        $_SESSION['id'] = $user['id_usuario'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $rol;
    }

    /**
     * Indica si hay un usuario con sesión iniciada.
     */
    public static function estaLogueado()
    {
        return isset($_SESSION['id']);
    }

    /**
     * Devuelve el rol del usuario actual (o null).
     */
    public static function rol()
    {
        return $_SESSION['rol'] ?? null;
    }

    /**
     * Devuelve el id del usuario actual (o null).
     */
    public static function idUsuario()
    {
        return $_SESSION['id'] ?? null;
    }

    /**
     * Destruye por completo la sesión actual
     * (variables, cookie y sesión en el servidor).
     */
    public static function destruir()
    {
        self::iniciar();

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        session_write_close();
    }
}
