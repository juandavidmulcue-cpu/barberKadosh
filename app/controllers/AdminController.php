<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/AdminModel.php';
require_once 'app/models/GestionProductoModel.php';

class AdminController extends Controller
{
    private $usuarioModel;
    private $productoModel;

    public function __construct()
    {
        parent::__construct();

        // Solo administradores
        $this->requireRole('admin');

        $this->usuarioModel = new Usuario();
        $this->productoModel = new GestionProductoModel();
    }

    /* =========================
       PANEL DEL ADMINISTRADOR
    ========================== */

    public function panel()
    {
        $barberos = $this->usuarioModel->obtenerBarberos();
        $clientes = $this->usuarioModel->obtenerClientes();
        $productos = $this->productoModel->obtenerProductos();

        $panelActivo = $_GET['panel'] ?? 'panel-inicio';

        $this->view('app/views/admin/panel.php', [
            'barberos'     => $barberos,
            'clientes'     => $clientes,
            'productos'    => $productos,
            'panelActivo'  => $panelActivo
        ]);
    }
}
