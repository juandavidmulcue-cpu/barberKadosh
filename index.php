<?php
/* ===============================
   CONFIGURACIÓN DE SESIÓN
   (UNA SOLA VEZ EN TODO EL SISTEMA)
================================ */
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', 0);
ini_set('session.cookie_httponly', 1);

session_start();

if (!isset($_SESSION['id_usuario'])) {
    $controller = 'auth';
    $action = 'loginCliente';
}

/* ===============================
   ROUTER MVC
================================ */

// Controlador y acción
$controller = $_GET['controller'] ?? null;
$action     = $_GET['action'] ?? null;

// Si no se especifica controlador o acción
if ($controller === null || $action === null) {

    http_response_code(404);

    $codigoError = 404;

    require_once 'app/views/errors/error.php';

    exit;
}

// Nombre del controlador
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = "app/controllers/$controllerName.php";

// Validar controlador
if (!file_exists($controllerFile)) {
    http_response_code(404);
    $codigoError = 404;

    require_once 'app/views/errors/error.php';
    exit;
}

// Cargar controlador
require_once $controllerFile;

// Crear instancia
$controllerObj = new $controllerName();

// Validar acción
if (!method_exists($controllerObj, $action)) {

    http_response_code(404);
    $codigoError = 404;

    require_once 'app/views/errors/error.php';
    exit;
}

// Ejecutar acción
$controllerObj->$action();
