<?php

require_once 'app/config/conexion.php';
require_once 'app/controllers/Controller.php';
require_once 'app/models/HorarioModel.php';

class HorarioController extends Controller
{
    private $model;

    public function __construct()
    {
        // Llamamos al método estático de la clase Database en conexion.php
        $db = Database::conectar();

        // Le pasamos la conexión al modelo
        $this->model = new HorarioModel($db);
    }

    // Muestra la vista con la lista de barberos y los horarios registrados
    public function index()
    {
        // Carga la información requerida
        $barberos = $this->model->obtenerBarberos();
        $horarios = $this->model->obtenerHorarios();

        // Define la pestaña/sección activa en el panel
        $vista = 'horarios';

        // Carga la plantilla principal del panel administrativo
        require_once 'app/views/admin/panel.php'; // Ajusta la ruta exacta según tus carpetas
    }

    // Procesa el formulario y guarda cada día en el rango ingresado
    public function guardarHorario()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_barbero   = $_POST['id_barbero'] ?? null;
            $fecha_inicio = $_POST['fecha_inicio'] ?? null;
            $fecha_fin    = $_POST['fecha_fin'] ?? null;
            $hora_inicio  = $_POST['hora_inicio'] ?? null;
            $hora_fin     = $_POST['hora_fin'] ?? null;

            if ($id_barbero && $fecha_inicio && $fecha_fin && $hora_inicio && $hora_fin) {

                // Recorremos el rango de fechas día por día
                $inicio = new DateTime($fecha_inicio);
                $fin    = new DateTime($fecha_fin);
                $fin->modify('+1 day'); // Incluye el último día del rango

                $intervalo = new DateInterval('P1D');
                $rango     = new DatePeriod($inicio, $intervalo, $fin);

                foreach ($rango as $fechaObj) {
                    $fechaFormateada = $fechaObj->format('Y-m-d');
                    $this->model->insertarHorario($id_barbero, $fechaFormateada, $hora_inicio, $hora_fin);
                }
            }
        }

        // Redireccionar a la vista de horarios
        header("Location: index.php?controller=horario&action=index&panel=horarios");
        exit;
    }

    // Eliminar un horario
    public function eliminarHorario()
    {
        $id_horario = $_GET['id_horario'] ?? null;

        if (!$id_horario) {
            die('ID de horario no válido');
        }

        $resultado = $this->model->eliminarHorario($id_horario);

        if ($resultado) {

            header("Location: index.php?controller=horario&action=index&panel=horarios");
            exit;
        }

        echo "No se pudo eliminar el horario.";
    }
}
