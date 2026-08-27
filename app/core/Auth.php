<?php

require_once __DIR__ . '/Session.php';

/**
 * Clase Auth
 * -------------------------------------------------
 * Responsabilidad única (SRP): verificar autenticación
 * y autorización por rol. Si el usuario no cumple,
 * se le redirige fuera de la zona protegida.
 */
class Auth
{
    /**
     * Exige que el usuario haya iniciado sesión.
     */
    public static function requireLogin($destino = "index.php")
    {
        if (!Session::estaLogueado()) {
            header("Location: {$destino}");
            exit;
        }
    }

    /**
     * Exige que el usuario tenga un rol específico.
     */
    public static function requireRole($rol, $destino = "index.php")
    {
        self::requireLogin($destino);

        if (Session::rol() !== $rol) {

            http_response_code(403);
            $codigoError = 403;
            require_once __DIR__ . '/../views/errors/error.php';
            exit;
        }
    }
}
