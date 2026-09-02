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
            header("Location: index.html");
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
        $idUsuario = $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;

        if (!$idUsuario) {
            $this->redirect('index.php?controller=auth&action=login');
            exit;
        }

        $cliente = $this->clienteModel->obtenerPorId($idUsuario);
        $barberos = $this->clienteModel->obtenerBarberosDisponiblesCincoDias();

        // Cargamos la vista y le enviamos los datos
        $this->view('app/views/cliente/perfil_cliente.php', [
            'cliente'  => $cliente,
            'barberos' => $barberos
        ]);
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
        // Validar la sesión con 'id' o 'id_usuario'
        $idUsuario = $_SESSION['id'] ?? $_SESSION['id_usuario'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $idUsuario) {
            $nombre   = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $telefono = trim($_POST['telefono']);
            $correo   = trim($_POST['correo']);

            $rutaFotoDB = null;

            // Procesar subida de foto si se seleccionó un archivo
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath   = $_FILES['foto']['tmp_name'];
                $fileName      = $_FILES['foto']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($fileExtension, $extensionesPermitidas)) {
                    $nuevoNombreFoto  = "usuario_" . $idUsuario . "_" . time() . "." . $fileExtension;
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

            // Actualizar en base de datos usando el modelo instanciado
            $resultado = $this->clienteModel->actualizarDatosPerfil($idUsuario, $nombre, $apellido, $telefono, $correo, $rutaFotoDB);

            if ($resultado) {
                // Actualizar la sesión para reflejar los datos en tiempo real
                $_SESSION['nombre']   = $nombre;
                $_SESSION['apellido'] = $apellido;
                $_SESSION['telefono'] = $telefono;
                $_SESSION['correo']   = $correo;

                if ($rutaFotoDB !== null) {
                    $_SESSION['foto'] = $rutaFotoDB;
                }

                // Redirección con el método heredado
                $this->redirect("index.php?controller=gestionCita&action=misCitas&status=perfil_actualizado");
                exit();
            } else {
                $this->redirect("index.php?controller=gestionCita&action=misCitas&status=error_perfil");
                exit();
            }
        }
    }
}
