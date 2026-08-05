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
        $citas = $this->citaModel->obtenerReservasCliente(
            $_SESSION['id']
        );

        $servicios = $this->servicioModel->obtenerServicios();

        $barberos = $this->barberoModel->obtenerBarberosDisponibles();

        $this->view('app/views/cliente/perfil_cliente.php', [
            'citas' => $citas,
            'servicios' => $servicios,
            'barberos' => $barberos
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

    public function obtenerHorario()
    {
        header('Content-Type: application/json');

        if (!isset($_GET['barbero']) || !isset($_GET['fecha'])) {

            echo json_encode([
                'horario' => null,
                'ocupadas' => [],
                'error' => 'Faltan datos'
            ]);

            return;
        }

        $idServicio = $_GET['servicio'];
        $idBarbero = $_GET['barbero'];
        $fecha = $_GET['fecha'];

        $duracion = $this->citaModel->obtenerDuracionServicio($idServicio);

        // Obtener horario laboral del barbero
        $horario = $this->citaModel->obtenerHorarioBarbero(
            $idBarbero,
            $fecha
        );

        // Obtener horas que ya tienen una reserva
        $ocupadas = $this->citaModel->obtenerHorasOcupadas(
            $idBarbero,
            $fecha
        );

        echo json_encode([
            'horario' => $horario,
            'ocupadas' => $ocupadas,
            'duracion' => $duracion
        ]);
    }

    /* =========================================
       GUARDAR RESERVA
    ========================================= */


    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $resultado = $this->citaModel->crearReserva(
                $_SESSION['id'],
                $_POST['barbero'],
                $_POST['servicio'],
                $_POST['fecha'],
                $_POST['hora']
            );

            // Si la hora ya está ocupada
            if ($resultado === false) {

                $_SESSION['error_cita'] =
                    '❌ Esta hora no está disponible. Selecciona otra hora.';

                $this->redirect(
                    "index.php?controller=gestionCita&action=agendarCita"
                );

                return;
            }

            // Si la reserva se guardó correctamente
            $_SESSION['mensaje_cita'] =
                '✅ ¡Cita agendada correctamente!';

            $this->redirect(
                "index.php?controller=gestionCita&action=misCitas"
            );

            return;
        }

        $this->redirect(
            "index.php?controller=gestionCita&action=agendarCita"
        );
    }


    public function editarCita()
    {
        if (!isset($_GET['id'])) {

            $this->redirect(
                "index.php?controller=gestionCita&action=misCitas"
            );

            return;
        }

        $idReservacion = $_GET['id'];

        $cita = $this->citaModel->obtenerReservaPorId(
            $idReservacion
        );

        if (!$cita) {

            $_SESSION['error_cita'] =
                '❌ No se encontró la cita que deseas editar.';

            $this->redirect(
                "index.php?controller=gestionCita&action=misCitas"
            );

            return;
        }

        // Verificar que la cita pertenece al cliente
        if ($cita['id_cliente'] != $_SESSION['id']) {

            $_SESSION['error_cita'] =
                '❌ No tienes permiso para editar esta cita.';

            $this->redirect(
                "index.php?controller=gestionCita&action=misCitas"
            );

            return;
        }

        // Obtener todas las reservas del cliente
        $citas = $this->citaModel->obtenerReservasCliente(
            $_SESSION['id']
        );

        // Cargar el perfil con las reservas y la cita a editar
        $this->view(
            'app/views/cliente/perfil_cliente.php',
            [
                'citas' => $citas,
                'citaEditar' => $cita
            ]
        );
    }

    public function actualizarCita()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $resultado = $this->citaModel->actualizarReserva(
                $_POST['id_reservacion'],
                $_SESSION['id'],
                $_POST['barbero'],
                $_POST['servicio'],
                $_POST['fecha'],
                $_POST['hora']
            );

            // Si la hora ya está ocupada
            if ($resultado === false) {

                $_SESSION['error_cita'] =
                    '❌ Esta hora no está disponible. Selecciona otra hora.';

                $this->redirect(
                    "index.php?controller=gestionCita&action=editarCita&id=" . $_POST['id_reservacion']
                );

                return;
            }

            // Si la reserva se actualizó correctamente
            $_SESSION['mensaje_cita'] =
                '✅ ¡Cita actualizada correctamente!';

            $this->redirect(
                "index.php?controller=gestionCita&action=misCitas"
            );

            return;
        }

        $this->redirect(
            "index.php?controller=gestionCita&action=misCitas"
        );
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
