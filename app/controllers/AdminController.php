<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/AdminModel.php';
require_once 'app/models/GestionProductoModel.php';
require_once 'app/models/ServicioModel.php';
require_once 'app/models/HorarioModel.php';
require_once 'app/models/PromocionModel.php';

class AdminController extends Controller
{
    private $usuarioModel;
    private $productoModel;
    private $servicioModel;
    private $horarioModel;
    private $promocionModel;

    public function __construct()
    {
        parent::__construct();

        // Solo administradores
        $this->requireRole('admin');

        $this->usuarioModel  = new Usuario();
        $this->productoModel = new GestionProductoModel();
        $this->servicioModel = new ServicioModel();
        $this->horarioModel  = new HorarioModel(Database::conectar());
        $this->promocionModel = new PromocionModel();
    }

    /* =========================
       PANEL DEL ADMINISTRADOR
    ========================== */

    public function panel()
    {
        $barberos    = $this->usuarioModel->obtenerBarberos();
        $clientes    = $this->usuarioModel->obtenerClientes();
        $productos   = $this->productoModel->obtenerProductos();
        $servicios   = $this->servicioModel->obtenerServicios();
        $horarios    = $this->horarioModel->obtenerHorarios();
        $promociones = $this->promocionModel->obtenerTodas(); // 4. Consulta de las promociones

        $servicioEditar = null;

        if (!empty($_GET['editar_servicio'])) {
            $id_servicio    = $_GET['editar_servicio'];
            $servicioEditar = $this->servicioModel->obtenerServicioPorId($id_servicio);
        }

        $panelActivo = $_GET['panel'] ?? 'inicio';

        $this->view('app/views/admin/panel.php', [
            'barberos'       => $barberos,
            'clientes'       => $clientes,
            'productos'      => $productos,
            'servicios'      => $servicios,
            'horarios'       => $horarios,
            'promociones'    => $promociones, // 5. Se pasa la variable a la vista
            'servicioEditar' => $servicioEditar,
            'panelActivo'    => $panelActivo
        ]);
    }
}