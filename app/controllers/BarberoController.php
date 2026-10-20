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


        // Reservaciones pendientes del barbero
        $reservaciones = $this->perfilModel
            ->obtenerReservacionesBarbero($idBarbero);

        // Reservaciones completadas del barbero
        $historial = $this->perfilModel
            ->obtenerHistorialBarbero($idBarbero);

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
                'historial' => $historial,
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

    public function actualizarPerfil()
    {
        $idUsuario = $_SESSION['id'] ?? $_SESSION['id_usuario'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $idUsuario) {
            $nombre   = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $telefono = trim($_POST['telefono']);
            $correo   = trim($_POST['correo']);

            $rutaFotoDB = null;

            // Procesar la foto de perfil
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath   = $_FILES['foto']['tmp_name'];
                $fileName      = $_FILES['foto']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($fileExtension, $extensionesPermitidas)) {
                    $nuevoNombreFoto  = "barbero_" . $idUsuario . "_" . time() . "." . $fileExtension;
                    $directorioSubida = "app/public/uploads/perfiles/";

                    if (!is_dir($directorioSubida)) {
                        mkdir($directorioSubida, 0755, true);
                    }

                    $destinoFinal = $directorioSubida . $nuevoNombreFoto;

                    if (move_uploaded_file($fileTmpPath, $destinoFinal)) {
                        $rutaFotoDB = $destinoFinal;
                    }
                }
            }

            // Actualizar datos en la base de datos
            $resultado = $this->perfilModel->actualizarDatosPerfil($idUsuario, $nombre, $apellido, $telefono, $correo, $rutaFotoDB);
            if ($resultado) {
                // Actualizar variables de sesión
                $_SESSION['nombre']   = $nombre;
                $_SESSION['apellido'] = $apellido;
                $_SESSION['telefono'] = $telefono;
                $_SESSION['correo']   = $correo;

                if ($rutaFotoDB !== null) {
                    $_SESSION['foto'] = $rutaFotoDB;
                }

                $this->redirect("index.php?controller=barbero&action=perfil&status=perfil_actualizado");
                exit();
            } else {
                $this->redirect("index.php?controller=barbero&action=perfil&status=error_perfil");
                exit();
            }
        }
    }
}
