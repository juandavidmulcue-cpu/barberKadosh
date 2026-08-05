<?php

require_once 'app/config/conexion.php';
require_once 'app/controllers/Controller.php';
require_once 'app/models/ReservacionModel.php';

class ReservacionController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new Reservacion();
    }

    public function index()
    {
        // ID del cliente que inició sesión
        $idCliente = $_SESSION['id'];

        // Datos necesarios para agendar
        $servicios = $this->model->obtenerServicios();
        $barberos = $this->model->obtenerBarberos();

        // Citas del cliente
        $reservaciones = $this->model->obtenerReservacionesCliente($idCliente);

        $vista = 'reservaciones';

        require_once 'app/views/cliente/perfil_cliente.php';
    }
}
