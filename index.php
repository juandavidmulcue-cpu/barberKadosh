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

/* ===============================
   ROUTER MVC
================================ */

// Controlador y acción desde la URL
$controller = $_GET['controller'] ?? null;
$action     = $_GET['action'] ?? null;

// 1. Si no se especifica NI controlador NI acción (estamos en la raíz), cargar home.php
if ($controller === null && $action === null) {
    require_once 'home.php';
    exit;
}

// 2. Si viene uno de los dos pero falta el otro, es un error 404
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