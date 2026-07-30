<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/AdminModel.php';

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

    public function desactivar()
    {
        if (isset($_GET['id'])) {
            $this->usuarioModel->inactivar($_GET['id']);
        }

        $panel = $_GET['panel'] ?? 'panel-clientes';

        $this->redirect("index.php?controller=admin&action=panel&panel=$panel");
    }

    public function activar()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->usuarioModel->activar($id);
        }

        $panel = $_GET['panel'] ?? 'panel-clientes';

        $this->redirect("index.php?controller=admin&action=panel&panel=$panel");
    }
}
