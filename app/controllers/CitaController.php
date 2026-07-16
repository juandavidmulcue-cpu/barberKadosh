<?php

require_once 'app/config/conexion.php';

class CitaController extends Controller
{
    private $citaModel;

    public function __construct()
    {
        parent::__construct();

        $this->requireLogin();

        $this->citaModel = new CitaModel();
    }

    /* =====================================================
       CLIENTE: VER SUS CITAS
       ===================================================== */
    public function misCitas()
    {
        $this->requireRole('cliente');

        $citas = $this->citaModel->obtenerPorCliente($_SESSION['id']);

        $this->view('app/views/cliente/mis_citas.php', [
            'citas' => $citas
        ]);
    }

    /* =====================================================
       CLIENTE: AGENDAR CITA
       ===================================================== */
    public function agendarCita()
    {
        $this->requireRole('cliente');

        // Obtener barberos disponibles
        $barberos = $this->citaModel->obtenerBarberosDisponibles();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->citaModel->crear(
                $_SESSION['id'],
                $_POST['barbero_id'],
                $_POST['fecha'],
                $_POST['hora']
            );

            $this->redirect("index.php?controller=cita&action=misCitas");
        }

        $this->view('app/views/cliente/agendar.php', [
            'barberos' => $barberos
        ]);
    }

    /* =====================================================
       CLIENTE: CANCELAR CITA
       ===================================================== */
    public function cancelar()
    {
        $this->requireRole('cliente');

        $this->citaModel->cancelarDeCliente($_GET['id'], $_SESSION['id']);

        $this->redirect("index.php?controller=cita&action=misCitas");
    }

    /* =====================================================
       BARBERO: VER SUS CITAS
       ===================================================== */
    public function citasBarbero()
    {
        $this->requireRole('barbero');

        $citas = $this->citaModel->obtenerPorBarbero($_SESSION['id']);

        $this->view('app/views/barbero/citas.php', [
            'citas' => $citas
        ]);
    }
}

