<?php

require_once 'app/config/conexion.php';
require_once 'app/controllers/Controller.php';
require_once 'app/models/ServicioModel.php';

class ServicioController extends Controller
{
    private $servicioModel;

    public function __construct()
    {
        parent::__construct();

        // Solo administradores
        $this->requireRole('admin');

        $this->servicioModel = new ServicioModel();
    }

    // Función aux para convertir minutos a formato HH:MM:SS de MySQL
    private function formatearDuracion($minutosEntrada)
    {
        $minutosTotales = intval($minutosEntrada);
        $horas = floor($minutosTotales / 60);
        $minutos = $minutosTotales % 60;

        return sprintf('%02d:%02d:00', $horas, $minutos);
    }

    // Mostrar servicios
    public function index()
    {
        $servicios = $this->servicioModel->obtenerServicios();

        require 'app/views/admin/panel.php';
    }

    // Guardar servicio
    public function guardar()
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = $_POST['precio'] ?? '';
        $duracionMinutos = $_POST['duracion'] ?? '';

        if (
            empty($nombre) ||
            empty($precio) ||
            empty($duracionMinutos)
        ) {
            die('Todos los campos obligatorios deben estar completos.');
        }

        // Convertir minutos recibidos a HH:MM:SS
        $duracionFormateada = $this->formatearDuracion($duracionMinutos);

        $resultado = $this->servicioModel->crearServicio(
            $nombre,
            $descripcion,
            $precio,
            $duracionFormateada
        );

        if ($resultado) {
            $_SESSION['mensaje_exito'] = 'Servicio creado correctamente.';
            header("Location: index.php?controller=admin&action=panel&panel=servicios");
            exit;
        } else {
            echo "No se pudo crear el servicio.";
        }
    }

    // Eliminar servicio
    public function eliminar()
    {
        $id_servicio = $_GET['id_servicio'] ?? null;

        if (!$id_servicio) {
            die('ID de servicio no válido.');
        }

        $resultado = $this->servicioModel->eliminarServicio(
            $id_servicio
        );

        if ($resultado) {
            header("Location: index.php?controller=admin&action=panel&panel=servicios");
            exit;
        } else {
            echo "No se pudo eliminar el servicio.";
        }
    }

    /* =========================
       EDITAR SERVICIO
    ========================== */

    public function editarServicio()
    {
        $id_servicio = $_GET['id_servicio'] ?? '';

        if (empty($id_servicio)) {
            header("Location: index.php?controller=admin&action=panel&panel=servicios");
            exit;
        }

        // Buscar servicio
        $servicio = $this->servicioModel->obtenerServicioPorId($id_servicio);

        if (!$servicio) {
            $_SESSION['mensaje_error'] = 'El servicio no existe.';
            header("Location: index.php?controller=admin&action=panel&panel=servicios");
            exit;
        }

        // Si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio = $_POST['precio'] ?? '';
            $duracionMinutos = $_POST['duracion'] ?? '';

            if (
                empty($nombre) ||
                empty($precio) ||
                empty($duracionMinutos)
            ) {
                $_SESSION['mensaje_error'] = 'Complete todos los campos obligatorios.';
            } else {

                // Convertir minutos recibidos a HH:MM:SS
                $duracionFormateada = $this->formatearDuracion($duracionMinutos);

                $resultado = $this->servicioModel->actualizarServicio(
                    $id_servicio,
                    $nombre,
                    $descripcion,
                    $precio,
                    $duracionFormateada
                );

                if ($resultado) {
                    $_SESSION['mensaje_exito'] = 'Servicio actualizado correctamente.';
                } else {
                    $_SESSION['mensaje_error'] = 'No se pudo actualizar el servicio.';
                }
            }

            header("Location: index.php?controller=admin&action=panel&panel=servicios");
            exit;
        }

        // Volver al panel indicando qué servicio se va a editar
        header(
            "Location: index.php?controller=admin&action=panel&panel=servicios&editar_servicio="
                . urlencode($id_servicio)
        );

        exit;
    }
}
