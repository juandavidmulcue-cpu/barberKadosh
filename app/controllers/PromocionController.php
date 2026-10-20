<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/PromocionModel.php';

class PromocionController extends Controller
{
    private $promocionModel;

    public function __construct()
    {
        parent::__construct();

        // Solo administradores
        $this->requireRole('admin');

        $this->promocionModel = new PromocionModel();
    }

    /* =========================
       GUARDAR PROMOCIÓN
    ========================== */
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id_promocion = $_POST['id_promocion'] ?? null;
            $nombre = $_POST['nombre'] ?? '';
            $descuento = $_POST['descuento_porcentaje'] ?? 0;
            $fecha_inicio = $_POST['fecha_inicio'] ?? '';
            $fecha_fin = $_POST['fecha_fin'] ?? '';
            $tipo = $_POST['tipo_aplicacion'] ?? '';
            $items_seleccionados = $_POST['items'] ?? [];

            if ($id_promocion) {
                $this->promocionModel->actualizar($id_promocion, $nombre, $descuento, $fecha_inicio, $fecha_fin);
            } else {
                $id_promocion = $this->promocionModel->crear($nombre, $descuento, $fecha_inicio, $fecha_fin);
            }

            if ($id_promocion) {
                $this->promocionModel->limpiarRelaciones($id_promocion);

                if ($tipo === 'servicio') {
                    $this->promocionModel->asociarServicios($id_promocion, $items_seleccionados);
                } else if ($tipo === 'producto') {
                    $this->promocionModel->asociarProductos($id_promocion, $items_seleccionados);
                }
            }
        }

        $this->redirect("index.php?controller=admin&action=panel&panel=promociones");
    }

    public function cambiarEstado()
    {
        $id = $_GET['id'] ?? null;

        if ($id !== null) {
            $this->promocionModel->cambiarEstado($id);
        }

        $this->redirect("index.php?controller=admin&action=panel&panel=promociones");
    }

    // En tu PromocionController.php

    public function editar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id_promocion         = isset($_POST['id_promocion']) ? intval($_POST['id_promocion']) : 0;
            $nombre               = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $descuento_porcentaje = isset($_POST['descuento_porcentaje']) ? floatval($_POST['descuento_porcentaje']) : 0;
            $fecha_inicio         = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
            $fecha_fin            = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';
            $estado               = isset($_POST['estado']) ? intval($_POST['estado']) : 1;

            if ($id_promocion > 0 && !empty($nombre)) {
                // Llamamos al método del modelo de promociones
                $resultado = $this->promocionModel->actualizarPromocion($id_promocion, $nombre, $descuento_porcentaje, $fecha_inicio, $fecha_fin, $estado);

                if ($resultado) {
                    $_SESSION['mensaje'] = "Promoción actualizada con éxito.";
                } else {
                    $_SESSION['error'] = "No se pudo actualizar la promoción.";
                }
            } else {
                $_SESSION['error'] = "Datos no válidos.";
            }

            // Redirige de vuelta al panel
            header("Location: index.php?controller=promocion&action=index");
            exit();
        }
    }
}
