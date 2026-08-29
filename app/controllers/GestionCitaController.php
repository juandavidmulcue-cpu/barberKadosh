<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/GestionCitaModel.php';
require_once 'app/models/ServicioModel.php';
require_once 'app/models/BarberoModel.php';
require_once 'app/models/ClienteProductoModel.php';

class GestionCitaController extends Controller
{
    private $citaModel;
    private $servicioModel;
    private $barberoModel;
    private $clienteProductoModel;

    public function __construct()
    {
        parent::__construct();

        $this->requireLogin();
        $this->requireRole('cliente');

        $this->citaModel = new GestionCitaModel();
        $this->servicioModel = new ServicioModel();
        $this->barberoModel = new BarberoModel();
        $this->clienteProductoModel = new ClienteProductoModel();
    }

    /* =========================================
       PERFIL DEL CLIENTE - MOSTRAR RESERVAS
    ========================================= */

    public function misCitas()
    {
        $citas = $this->citaModel->obtenerReservasCliente(
            $_SESSION['id']
        );

        $historial = $this->citaModel->obtenerHistorialCliente(
            $_SESSION['id']
        );

        $servicios = $this->servicioModel->obtenerServicios();

        $barberos = $this->barberoModel->obtenerBarberosDisponibles();

        $this->view('app/views/cliente/perfil_cliente.php', [
            'citas' => $citas,
            'historial' => $historial,
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            $this->redirect(
                "index.php?controller=gestionCita&action=agendarCita"
            );

            return;
        }

        // ==========================================
        // 1. CREAR LA RESERVACIÓN
        // ==========================================

        $idReservacion = $this->citaModel->crearReserva(
            $_SESSION['id'],
            $_POST['barbero'],
            $_POST['servicio'],
            $_POST['fecha'],
            $_POST['hora']
        );


        // ==========================================
        // 2. VERIFICAR SI LA RESERVACIÓN SE CREÓ
        // ==========================================

        if ($idReservacion === false) {

            $_SESSION['error_cita'] =
                '❌ Esta hora no está disponible. Selecciona otra hora.';

            $this->redirect(
                "index.php?controller=gestionCita&action=agendarCita"
            );

            return;
        }


        // ==========================================
        // 3. OBTENER PRODUCTO SELECCIONADO
        // ==========================================

        $productosJson = $_POST['productos_seleccionados'] ?? null;
        $productosIds = [];

        if(!empty($productosJson)){
            $productosIds = json_decode($productosJson, true);
        }

        // ==========================================
        // 4. SI HAY PRODUCTO, GUARDARLO
        // ==========================================

        $erroresProductos = false;

        if (is_array($productosIds) && !empty($productosIds)) {
            foreach ($productosIds as $idProducto) {
                // Inserta cada producto en detalle_reservacion mediante el modelo existente
                $productoGuardado = $this->clienteProductoModel->agregarProducto(
                    $idReservacion,
                    $idProducto,
                    1
                );

                if (!$productoGuardado) {
                    $erroresProductos = true;
                }
            }

            if ($erroresProductos) {
                $_SESSION['error_cita'] = '⚠️ La cita fue creada, pero algunos productos no pudieron guardarse.';
            }
        }


        // ==========================================
        // 5. MENSAJE DE ÉXITO
        // ==========================================

        if (!isset($_SESSION['error_cita'])) {
            if (!empty($productosIds)) {
                $_SESSION['mensaje_cita'] = '✅ ¡Cita agendada y productos agregados correctamente!';
            } else {
                $_SESSION['mensaje_cita'] = '✅ ¡Cita agendada correctamente!';
            }
        }


        // ==========================================
        // 6. VOLVER AL PERFIL
        // ==========================================

        $this->redirect(
            "index.php?controller=gestionCita&action=misCitas"
        );

        return;
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

        $historial = $this->citaModel->obtenerHistorialCliente(
            $_SESSION['id']
        );

        // Cargar el perfil con las reservas y la cita a editar
        $this->view(
            'app/views/cliente/perfil_cliente.php',
            [
                'citas' => $citas,
                'historial' => $historial,
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

    /* =========================================
   FINALIZAR RESERVA
========================================= */

    public function finalizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            $this->redirect(
                "index.php?controller=gestionCita&action=misCitas"
            );

            return;
        }

        $idReserva = $_POST['id_reservacion'] ?? null;

        if (!$idReserva) {

            $this->redirect(
                "index.php?controller=gestionCita&action=misCitas"
            );

            return;
        }


        // Finalizar solamente si pertenece al cliente
        $resultado = $this->citaModel->finalizarReserva(
            $idReserva,
            $_SESSION['id']
        );


        if ($resultado) {

            $_SESSION['mensaje_cita'] =
                '✅ La cita ha sido finalizada correctamente.';
        }


        // Volver al perfil
        $this->redirect(
            "index.php?controller=gestionCita&action=misCitas"
        );
    }

    /* =========================================
   GUARDAR RESEÑA Y CALIFICACIÓN
========================================= */

    public function guardarResena()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(
                "index.php?controller=gestionCita&action=misCitas"
            );
            return;
        }

        $idReservacion = $_POST['id_reservacion'] ?? null;
        $calificacion = $_POST['calificacion'] ?? null;
        $comentario = trim($_POST['comentario'] ?? '');

        // Validar datos
        if (!$idReservacion || !$calificacion) {

            $_SESSION['error_cita'] =
                '❌ Debes seleccionar una calificación.';

            $this->redirect(
                "index.php?controller=gestionCita&action=misCitas"
            );

            return;
        }

        // La calificación debe estar entre 1 y 5
        if ($calificacion < 1 || $calificacion > 5) {

            $_SESSION['error_cita'] =
                '❌ La calificación debe estar entre 1 y 5.';

            $this->redirect(
                "index.php?controller=gestionCita&action=misCitas"
            );

            return;
        }

        // Guardar reseña
        $resultado = $this->citaModel->guardarResena(
            $idReservacion,
            $_SESSION['id'],
            $calificacion,
            $comentario
        );

        if ($resultado) {

            $_SESSION['mensaje_cita'] =
                '✅ ¡Gracias por calificar nuestro servicio!';
        } else {

            $_SESSION['error_cita'] =
                '❌ No fue posible guardar la reseña.';
        }

        $this->redirect(
            "index.php?controller=gestionCita&action=misCitas"
        );
    }
}
