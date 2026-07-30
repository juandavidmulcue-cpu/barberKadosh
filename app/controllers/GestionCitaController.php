<?php

require_once 'app/controllers/Controller.php';

require_once 'app/models/GestionCitaModel.php';
require_once 'app/models/ServicioModel.php';
require_once 'app/models/BarberoModel.php';

class GestionCitaController extends Controller
{
    private $citaModel;
    private $servicioModel;
    private $barberoModel;

    public function __construct()
    {
        parent::__construct();

        $this->requireLogin();
        $this->requireRole('cliente');

        $this->citaModel = new GestionCitaModel();
        $this->servicioModel = new ServicioModel();
        $this->barberoModel = new BarberoModel();
    }

    /* =========================================
       PERFIL DEL CLIENTE - MOSTRAR RESERVAS
    ========================================= */

    public function misCitas()
    {
        $citas = $this->citaModel->obtenerReservasCliente($_SESSION['id']);

        $this->view('app/views/cliente/perfilCliente.php', [
            'citas' => $citas
        ]);
    }

    /* =========================================
       FORMULARIO AGENDAR CITA
    ========================================= */

    public function agendarCita()
    {
        $servicios = $this->servicioModel->obtenerServicios();

        $barberos = $this->barberoModel->obtenerBarberosDisponibles();

        $this->view('app/views/cliente/agendar.php', [
            'servicios' => $servicios,
            'barberos'  => $barberos,
            'idServicio' => '',
            'idBarbero' => '',
            'fecha' => '',
            'slots' => [],
            'error' => ''
        ]);
    }

    /* =========================================
       GUARDAR RESERVA
    ========================================= */

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $this->citaModel->crearReserva(

                $_SESSION['id'],

                $_POST['barbero'],

                $_POST['servicio'],

                $_POST['fecha'],

                $_POST['hora']
            );
        }

        $this->redirect("index.php?controller=gestionCita&action=misCitas");
    }

    /* =========================================
       CANCELAR RESERVA
    ========================================= */

    public function cancelar()
    {
        if (isset($_GET['id'])) {

            $this->citaModel->cancelarReserva(
                $_GET['id'],
                $_SESSION['id']
            );
        }

        $this->redirect("index.php?controller=gestionCita&action=misCitas");
    }
}