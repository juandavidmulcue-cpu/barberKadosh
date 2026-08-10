<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/GestionPerfilBarberoModel.php';


class BarberoController extends Controller
{
    private $perfilModel;


    public function __construct()
    {
        parent::__construct();

        // SOLO BARBEROS
        $this->requireRole('barbero');

        // Modelo del perfil
        $this->perfilModel = new GestionPerfilBarberoModel();
    }


    /* ===============================
       PERFIL DEL BARBERO
    =============================== */

    public function perfil()
    {
        // Documento del barbero logueado
        $idBarbero = $_SESSION['id'];


        // Datos del barbero
        $barbero = $this->perfilModel->obtenerBarbero(
            $idBarbero
        );


        // Reservaciones
        $reservaciones = $this->perfilModel
            ->obtenerReservacionesBarbero($idBarbero);


        // Horarios
        $horarios = $this->perfilModel
            ->obtenerHorariosBarbero($idBarbero);


        // Reseñas
        $reseñas = $this->perfilModel
            ->obtenerResenasBarbero($idBarbero);


        // Promedio de calificación
        $promedio = $this->perfilModel
            ->obtenerPromedioCalificacion($idBarbero);


        // Total de reservas
        $totalReservas = $this->perfilModel
            ->obtenerTotalReservas($idBarbero);


        // Total de reseñas
        $totalResenas = $this->perfilModel
            ->obtenerTotalResenas($idBarbero);


        // Cargar perfil
        $this->view(
            'app/views/barbero/perfil_barbero.php',
            [
                'barbero' => $barbero,
                'reservaciones' => $reservaciones,
                'horarios' => $horarios,
                'reseñas' => $reseñas,
                'promedio' => $promedio,
                'totalReservas' => $totalReservas,
                'totalResenas' => $totalResenas,
            ]
        );
    }


    /* ===============================
       ACTUALIZAR ESTADO RESERVACIÓN
    =============================== */

    public function actualizarEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }


        $idReservacion = $_POST['id_reservacion'] ?? null;

        $estado = $_POST['estado_reserva'] ?? null;


        // Validar datos
        if (!$idReservacion || !$estado) {
            return;
        }


        // Estados permitidos
        $estadosPermitidos = [
            'Pendiente',
            'Completada',
            'Cancelada'
        ];


        if (!in_array($estado, $estadosPermitidos)) {
            return;
        }


        // Actualizar
        $this->perfilModel->actualizarEstadoReservacion(
            $idReservacion,
            $estado
        );


        // Regresar al perfil
        $this->redirect(
            'index.php?controller=barbero&action=perfil'
        );
    }
}