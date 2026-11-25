<?php

require_once 'app/config/conexion.php';
require_once 'app/models/ClienteModel.php';
require_once 'app/controllers/Controller.php';

class ClienteController extends Controller
{
    private $db;
    private $clienteModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 🔐 SOLO CLIENTES
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
            header("Location: home.php");
            exit;
        }

        $this->db = Database::conectar();

        // Instanciamos el modelo pasando la conexión PDO
        $this->clienteModel = new ClienteModel($this->db);
    }

    /* ===============================
       PERFIL DEL CLIENTE
       =============================== */
    public function perfil()
    {
        header("Location: index.php?controller=gestionCita&action=misCitas");
        exit;
    }
    /* ===============================
       IR A AGENDAR CITA
       =============================== */
    public function agendar()
    {
        header("Location: index.php?controller=cita&action=agendar");
        exit;
    }


    public function actualizarPerfil()
    {
        $idUsuario = $_SESSION['id'] ?? $_SESSION['id_usuario'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$idUsuario) {
            $this->redirect('index.php?controller=gestionCita&action=misCitas');
            exit();
        }

        $nombre   = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $correo   = trim($_POST['correo'] ?? '');

        $rutaFotoDB = null;

        // Procesar foto únicamente si se seleccionó una.
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {

            if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                $this->redirect('index.php?controller=gestionCita&action=misCitas&status=error_foto');
                exit();
            }

            $fileTmpPath = $_FILES['foto']['tmp_name'];
            $tamanoArchivo = $_FILES['foto']['size'];

            // Máximo 5 MB.
            if ($tamanoArchivo > 5 * 1024 * 1024) {
                $this->redirect('index.php?controller=gestionCita&action=misCitas&status=error_foto_tamano');
                exit();
            }

            // Validar que realmente sea una imagen.
            $infoImagen = getimagesize($fileTmpPath);

            $extensionesMime = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if ($infoImagen === false || !isset($extensionesMime[$infoImagen['mime']])) {
                $this->redirect('index.php?controller=gestionCita&action=misCitas&status=error_foto');
                exit();
            }

            $extension = $extensionesMime[$infoImagen['mime']];

            // Carpeta física donde se guardan las imágenes.
            $directorioSubida = dirname(__DIR__, 2) . '/app/public/uploads/perfiles/';

            if (!is_dir($directorioSubida)) {
                mkdir($directorioSubida, 0755, true);
            }

            // Nombre único para evitar sobrescribir otras fotos.
            $nuevoNombreFoto = 'usuario_' . $idUsuario . '_' . bin2hex(random_bytes(8)) . '.' . $extension;

            $destinoFinal = $directorioSubida . $nuevoNombreFoto;

            if (!move_uploaded_file($fileTmpPath, $destinoFinal)) {
                $this->redirect('index.php?controller=gestionCita&action=misCitas&status=error_foto');
                exit();
            }

            // Esta es la ruta que se guarda en la base de datos.
            $rutaFotoDB = 'app/public/uploads/perfiles/' . $nuevoNombreFoto;
        }

        // Actualizar información en la base de datos.
        $resultado = $this->clienteModel->actualizarDatosPerfil(
            $idUsuario,
            $nombre,
            $apellido,
            $telefono,
            $correo,
            $rutaFotoDB
        );

        if ($resultado) {
            $_SESSION['nombre']   = $nombre;
            $_SESSION['apellido'] = $apellido;
            $_SESSION['telefono'] = $telefono;
            $_SESSION['correo']   = $correo;

            if ($rutaFotoDB !== null) {
                $_SESSION['foto'] = $rutaFotoDB;
            }

            $this->redirect('index.php?controller=gestionCita&action=misCitas&status=perfil_actualizado');
            exit();
        }

        $this->redirect('index.php?controller=gestionCita&action=misCitas&status=error_perfil');
        exit();
    }
}
