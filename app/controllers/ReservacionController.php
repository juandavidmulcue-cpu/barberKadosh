<?php

require_once 'app/config/conexion.php';
require_once 'app/controllers/Controller.php';
require_once 'app/models/ReservacionModel.php';

class ReservacionController extends Controller
{
    private $model;

    public function __construct()
    {
        // Asegurar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->model = new Reservacion();
    }

    public function index()
    {
        // 1. Obtener el ID del cliente probando las llaves habituales de $_SESSION
        $idCliente = $_SESSION['id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['usuario']['id_usuario'] ?? null;

        // Si no existe ID en la sesión, redirigir al login
        if (!$idCliente) {
            header('Location: index.php?action=login');
            exit();
        }

        // 2. Cargar datos desde la base de datos
        $servicios = $this->model->obtenerServicios();
        $barberos = $this->model->obtenerBarberos();

        // 3. Consultar las reservaciones de este cliente específico
        $citas = $this->model->obtenerReservacionesCliente($idCliente);

        $vista = 'reservaciones';

        // 4. Cargar la vista
        require_once 'app/views/cliente/perfil_cliente.php';
    }
}
