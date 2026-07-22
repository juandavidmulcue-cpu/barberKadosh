<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/CitaModel.php';
require_once 'app/models/CalificacionModel.php';

/**
 * CalificacionController
 * -------------------------------------------------
 * Permite al cliente calificar una cita ya completada.
 * Reglas: la cita debe pertenecer al cliente, estar en
 * estado 'completada' y no haber sido calificada antes.
 */
class CalificacionController extends Controller
{
    private $citaModel;
    private $calificacionModel;

    public function __construct()
    {
        parent::__construct();

        $this->requireRole('cliente');

        $this->citaModel = new CitaModel();
        $this->calificacionModel = new CalificacionModel();
    }

    /* =====================================================
       MOSTRAR FORMULARIO / GUARDAR CALIFICACIÓN
       ===================================================== */
    public function calificar()
    {
        $idCita = $_GET['id'] ?? $_POST['id_cita'] ?? null;

        if (!$idCita) {
            $this->redirect("index.php?controller=cita&action=misCitas");
        }

        $cita = $this->citaModel->obtenerPorId($idCita);

        // La cita debe existir, ser del cliente y estar completada
        if (
            !$cita ||
            $cita['id_cliente'] !== $_SESSION['id'] ||
            $cita['estado'] !== 'completada'
        ) {
            $_SESSION['flash_error'] = "No puedes calificar esta cita.";
            $this->redirect("index.php?controller=cita&action=misCitas");
        }

        // Evitar doble calificación
        if ($this->calificacionModel->existeParaCita($idCita)) {
            $_SESSION['flash_error'] = "Esta cita ya fue calificada.";
            $this->redirect("index.php?controller=cita&action=misCitas");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->guardar($cita);
            return;
        }

        $this->view('app/views/cliente/calificar.php', [
            'cita' => $cita,
            'error' => $_SESSION['flash_error'] ?? null
        ]);
        unset($_SESSION['flash_error']);
    }

    private function guardar($cita)
    {
        $puntuacion = (int) ($_POST['puntuacion'] ?? 0);
        $comentario = trim($_POST['comentario'] ?? '');

        if ($puntuacion < 1 || $puntuacion > 5) {
            $_SESSION['flash_error'] = "La puntuación debe estar entre 1 y 5.";
            $this->redirect("index.php?controller=calificacion&action=calificar&id=" . $cita['id']);
        }

        $this->calificacionModel->crear([
            'id_cita'    => $cita['id'],
            'id_barbero' => $cita['id_barbero'],
            'id_cliente' => $_SESSION['id'],
            'puntuacion' => $puntuacion,
            'comentario' => $comentario !== '' ? $comentario : null
        ]);

        $_SESSION['flash_exito'] = "¡Gracias por tu calificación!";
        $this->redirect("index.php?controller=cita&action=misCitas");
    }
}
