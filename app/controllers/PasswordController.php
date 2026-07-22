<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/TokenPasswordModel.php';

/**
 * PasswordController
 * -------------------------------------------------
 * Responsabilidad única: flujo de recuperación de
 * contraseña (mostrar formulario de reset y de cambio).
 * La validación del token se delega en TokenPasswordModel,
 * respetando la separación de responsabilidades MVC.
 * El envío de correo se procesa en logicamail.php.
 */
class PasswordController extends Controller
{
    private $tokenModel;

    public function __construct()
    {
        parent::__construct();
        $this->tokenModel = new TokenPasswordModel();
    }

    public function resetPassword()
    {
        $this->view('app/views/auth/reset_password.php');
    }

    public function cambiarPassword()
    {
        $id    = $_GET['id'] ?? null;
        $token = $_GET['token'] ?? null;

        // Token inválido o expirado -> volver al login
        $usuario = ($id && $token) ? $this->tokenModel->validar($id, $token) : null;

        if (!$usuario) {
            $this->redirect("index.php?controller=auth&action=loginCliente");
        }

        $this->view('app/views/auth/cambiarContrasena.php', [
            'id'    => $id,
            'token' => $token
        ]);
    }
}