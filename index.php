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

// Controlador y acción por defecto
$controller = $_GET['controller'] ?? 'auth';
$action     = $_GET['action'] ?? 'loginCliente';

// Nombre del controlador
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = "app/controllers/$controllerName.php";

// Validar controlador
if (!file_exists($controllerFile)) {
    die("Error: el controlador '$controllerName' no existe.");
}

// Cargar controlador
require_once $controllerFile;

// Crear instancia
$controllerObj = new $controllerName();

// Validar acción
if (!method_exists($controllerObj, $action)) {
    die("Error: la acción '$action' no existe en $controllerName.");
}

// Ejecutar acción
$controllerObj->$action();