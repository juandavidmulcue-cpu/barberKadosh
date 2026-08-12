<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/AdminModel.php';
require_once 'app/models/GestionProductoModel.php';
require_once 'app/models/ServicioModel.php';
require_once 'app/models/HorarioModel.php';

class AdminController extends Controller
{
    private $usuarioModel;
    private $productoModel;
    private $servicioModel;
    private $horarioModel;

    public function __construct()
    {
        parent::__construct();

        // Solo administradores
        $this->requireRole('admin');

        $this->usuarioModel = new Usuario();
        $this->productoModel = new GestionProductoModel();
        $this->servicioModel = new ServicioModel();
        $this->horarioModel = new HorarioModel(Database::conectar());
    }

    /* =========================
       PANEL DEL ADMINISTRADOR
    ========================== */

    public function panel()
    {
        $barberos = $this->usuarioModel->obtenerBarberos();
        $clientes = $this->usuarioModel->obtenerClientes();
        $productos = $this->productoModel->obtenerProductos();
        $servicios = $this->servicioModel->obtenerServicios();
        $horarios = $this->horarioModel->obtenerHorarios();

        $servicioEditar = null;

        if (!empty($_GET['editar_servicio'])) {
            $id_servicio = $_GET['editar_servicio'];
            $servicioEditar = $this->servicioModel->obtenerServicioPorId($id_servicio);
        }

        $panelActivo = $_GET['panel'] ?? 'inicio';

        $this->view('app/views/admin/panel.php', [
            'barberos'     => $barberos,
            'clientes'     => $clientes,
            'productos'    => $productos,
            'servicios'    => $servicios,
            'horarios'     => $horarios,
            'servicioEditar' => $servicioEditar,
            'panelActivo'  => $panelActivo
        ]);
    }
}
