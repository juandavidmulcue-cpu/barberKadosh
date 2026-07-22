<?php

require_once 'app/controllers/Controller.php';
require_once 'app/config/conexion.php';
require_once 'app/models/AuthModel.php';
require_once 'app/services/ValidadorRegistro.php';

/**
 * RegistroController
 * -------------------------------------------------
 * Responsabilidad única: registro de nuevos clientes.
 * La validación se delega en ValidadorRegistro (SRP).
 */
class RegistroController extends Controller
{
    private $authModel;
    private $validador;

    public function __construct()
    {
        parent::__construct();
        $this->authModel = new AuthModel(Database::conectar());
        $this->validador = new ValidadorRegistro();
    }

    public function registerCliente()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id_usuario' => trim($_POST['id_usuario'] ?? ''),
                'nombre'     => trim($_POST['nombre'] ?? ''),
                'apellido'   => trim($_POST['apellido'] ?? ''),
                'telefono'   => trim($_POST['telefono'] ?? ''),
                'correo'     => trim($_POST['correo'] ?? ''),
                'password'   => $_POST['password'] ?? ''
            ];

            $error = $this->validador->validar($datos);

            if ($error === null) {
                $ok = $this->authModel->registerClient([
                    ':id'       => $datos['id_usuario'],
                    ':rol'      => 3,
                    ':nombre'   => $datos['nombre'],
                    ':apellido' => $datos['apellido'],
                    ':telefono' => $datos['telefono'],
                    ':correo'   => $datos['correo'],
                    ':password' => password_hash($datos['password'], PASSWORD_DEFAULT)
                ]);

                if ($ok) {
                    $this->redirect("index.php?controller=auth&action=loginCliente");
                }
                $error = "Error al registrar usuario";
            }
        }

        $this->view('app/views/auth/register.php', ['error' => $error]);
    }
}
