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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$idUsuario) {
            $this->redirect('index.php?controller=barbero&action=perfil');
            exit();
        }

        $nombre   = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $correo   = trim($_POST['correo'] ?? '');

        $rutaFotoDB = null;

        // Procesar la foto únicamente si se seleccionó un archivo.
        if (
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            // Comprobar si PHP recibió correctamente el archivo.
            if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                error_log(
                    'Error al subir foto del barbero. Código: ' .
                        $_FILES['foto']['error']
                );

                $this->redirect(
                    'index.php?controller=barbero&action=perfil&status=error_foto'
                );
                exit();
            }

            $archivoTemporal = $_FILES['foto']['tmp_name'];
            $tamanoArchivo = $_FILES['foto']['size'];

            // Tamaño máximo: 5 MB.
            if ($tamanoArchivo > 5 * 1024 * 1024) {
                $this->redirect(
                    'index.php?controller=barbero&action=perfil&status=error_foto_tamano'
                );
                exit();
            }

            // Validar que el archivo sea una imagen real.
            $infoImagen = getimagesize($archivoTemporal);

            if ($infoImagen === false) {
                $this->redirect(
                    'index.php?controller=barbero&action=perfil&status=error_foto'
                );
                exit();
            }

            $extensionesMime = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            $mime = $infoImagen['mime'];

            if (!isset($extensionesMime[$mime])) {
                $this->redirect(
                    'index.php?controller=barbero&action=perfil&status=error_foto'
                );
                exit();
            }

            $extension = $extensionesMime[$mime];

            /*
         * Ruta física de la carpeta de imágenes.
         * __DIR__ apunta a app/controllers.
         * dirname(__DIR__, 2) apunta a la raíz del proyecto.
         */
            $directorioSubida = dirname(__DIR__, 2)
                . '/app/public/uploads/perfiles/';

            if (
                !is_dir($directorioSubida) &&
                !mkdir($directorioSubida, 0755, true) &&
                !is_dir($directorioSubida)
            ) {
                error_log(
                    'No se pudo crear la carpeta de fotos: ' .
                        $directorioSubida
                );

                $this->redirect(
                    'index.php?controller=barbero&action=perfil&status=error_foto'
                );
                exit();
            }

            // Nombre único para evitar sobrescribir otras imágenes.
            $nuevoNombreFoto = 'barbero_' . $idUsuario . '_'
                . bin2hex(random_bytes(8)) . '.' . $extension;

            $destinoFinal = $directorioSubida . $nuevoNombreFoto;

            if (!move_uploaded_file($archivoTemporal, $destinoFinal)) {
                error_log(
                    'No se pudo mover la foto a: ' . $destinoFinal
                );

                $this->redirect(
                    'index.php?controller=barbero&action=perfil&status=error_foto'
                );
                exit();
            }

            // Ruta relativa que se almacena en la base de datos.
            $rutaFotoDB = 'app/public/uploads/perfiles/' . $nuevoNombreFoto;
        }

        // Actualizar los datos y, si corresponde, la foto.
        $resultado = $this->perfilModel->actualizarDatosPerfil(
            $idUsuario,
            $nombre,
            $apellido,
            $telefono,
            $correo,
            $rutaFotoDB
        );

        if ($resultado) {
            $_SESSION['nombre'] = $nombre;
            $_SESSION['apellido'] = $apellido;
            $_SESSION['telefono'] = $telefono;
            $_SESSION['correo'] = $correo;

            if ($rutaFotoDB !== null) {
                $_SESSION['foto'] = $rutaFotoDB;
            }

            $this->redirect(
                'index.php?controller=barbero&action=perfil&status=perfil_actualizado'
            );
            exit();
        }

        $this->redirect(
            'index.php?controller=barbero&action=perfil&status=error_perfil'
        );
        exit();
    }
}
