<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/UsuarioModel.php';

class GestionClienteController extends Controller
{
    private $usuarioModel;

    public function __construct()
    {
        parent::__construct();

        // Solo administradores
        $this->requireRole('admin');

        $this->usuarioModel = new Usuario();
    }

    /* =========================
       LISTAR CLIENTES
    ========================== */

    public function listar()
    {
        $buscar = $_GET['buscar'] ?? '';
        $orden  = $_GET['orden'] ?? 'az';

        $clientes = $this->usuarioModel->obtenerClientes($buscar, $orden);

        $this->view('app/views/admin/clientes.php', [
            'clientes' => $clientes
        ]);
    }

    /* =========================
       ELIMINAR CLIENTE
    ========================== */

    public function eliminar()
    {
        if (isset($_GET['id'])) {
            $this->usuarioModel->eliminarCliente($_GET['id']);
        }

        $this->redirect("index.php?controller=admin&action=panel");
    }
}