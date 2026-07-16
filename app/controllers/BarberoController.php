 <?php
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
            $this->redirect("index.php?controller=cita&action=citasBarbero");
        }
    }
