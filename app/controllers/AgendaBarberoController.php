<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/CitaModel.php';

/**
 * AgendaBarberoController
 * -------------------------------------------------
 * Responsabilidad única: agenda del barbero
 * (ver sus citas y marcarlas como completadas).
 * Extraído de CitaController para separar el flujo
 * del cliente del flujo del barbero.
 */
class AgendaBarberoController extends Controller
{
    private $citaModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireRole('barbero');
        $this->citaModel = new CitaModel();
    }

    public function citas()
    {
        $this->view('app/views/barbero/citas.php', [
            'citas' => $this->citaModel->obtenerPorBarbero($_SESSION['id'])
        ]);
    }

    public function completar()
    {
        if (isset($_GET['id'])) {
            $this->citaModel->completarDeBarbero($_GET['id'], $_SESSION['id']);
        }
        $this->redirect("index.php?controller=agendaBarbero&action=citas");
    }
}
