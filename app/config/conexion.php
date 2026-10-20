<?php

/**
 * --------------------------------------------------
 * CONFIGURACIÓN GENERAL DEL SISTEMA
 * KADOSH BARBER SHOP
 * --------------------------------------------------
 */

class Database
{
    private static $host = "localhost";
    private static $db   = "barberia kadosh";
    private static $user = "root";
    private static $pass = "";
    private static $charset = "utf8mb4";

    public static function conectar()
    {
        try {
            $dsn = "mysql:host=" . self::$host .
                ";dbname=" . self::$db .
                ";charset=" . self::$charset;

            $pdo = new PDO($dsn, self::$user, self::$pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $e) {

            http_response_code(500);

            $codigoError = 500;

            require_once __DIR__ . '/../views/errors/error.php';

            exit;
        }
    }
}


/* =============================
   CONFIGURACIÓN DEL SISTEMA
   ============================= */

// Ruta base del proyecto
define('BASE_URL', 'http://localhost/barberkadosh/');

// Zona horaria
date_default_timezone_set('America/Bogota');

// Modo desarrollo (true = muestra errores)
define('DEBUG', true);
