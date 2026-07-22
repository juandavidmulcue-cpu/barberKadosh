<?php

require_once 'app/controllers/Controller.php';

/**
 * BarberoController
 * -------------------------------------------------
 * Refactor: existían DOS versiones de esta clase
 * (una aquí sin seguridad, otra dentro de
 * ClienteController.php). Se consolidaron en una sola
 * con la verificación de rol correspondiente.
 */
class BarberoController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // 🔐 SOLO BARBEROS
        $this->requireRole('barbero');
    }

    /* ===============================
       PERFIL DEL BARBERO
       =============================== */
    public function perfil()
    {
        $this->view('app/views/barbero/perfil_barbero.php');
    }

    /* ===============================
       VER SUS CITAS
       =============================== */
    public function citas()
    {
        $this->redirect("index.php?controller=agendaBarbero&action=citas");
    }
}