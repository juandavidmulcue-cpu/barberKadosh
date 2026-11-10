<?php

/**
 * --------------------------------------------------
 * CONFIGURACIÓN GENERAL DEL SISTEMA
 * KADOSH BARBER SHOP
 * --------------------------------------------------
 */

class Database
{
    private static $host = "kadosh-mysql-juandavidmulcue-24ce.f.aivencloud.com";
    private static $port = "24141"; // <--- Reemplaza con tu puerto de Aiven (ej. 23456)
    private static $db   = "barberia_kadosh";
    private static $user = "avnadmin";
    private static $pass = "hola"; // <--- Tu contraseña real
    private static $charset = "utf8mb4";

    public static function conectar()
    {
        try {
            $dsn = "mysql:host=" . self::$host .
                ";port=" . self::$port .
                ";dbname=" . self::$db .
                ";charset=" . self::$charset;

            // Opciones obligatorias para que Aiven acepte la conexión segura con SSL
            $options = [
                PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE       => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT               => true,
                PDO::MYSQL_ATTR_SSL_CA             => __DIR__ . '/ca.pem', // Ruta al certificado ca.pem
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ];

            $pdo = new PDO($dsn, self::$user, self::$pass, $options);

            return $pdo;
        } catch (PDOException $e) {
            http_response_code(500);
            $codigoError = 500;

            // Si estás depurando, puedes descomentar la siguiente línea temporalmente para ver el error exacto:
            // echo "Error PDO: " . $e->getMessage();

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
