<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/ClienteProductoModel.php';

class ClienteProductoController extends Controller
{
    private $productoModel;

    public function __construct()
    {
        $this->productoModel = new ClienteProductoModel();
    }

    public function obtenerProductosDisponibles()
    {
        $productos = $this->productoModel->obtenerProductosDisponibles();

        header('Content-Type: application/json');
        echo json_encode($productos);
        exit;
    }

    public function agregarProducto()
    {
        $idReservacion = $_POST['id_reservacion'] ?? null;
        $idProducto = $_POST['id_producto'] ?? null;
        $cantidad = $_POST['cantidad'] ?? 1;

        if (!$idReservacion || !$idProducto) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Datos incompletos'
            ]);
            exit;
        }

        $resultado = $this->productoModel->agregarProducto(
            $idReservacion,
            $idProducto,
            $cantidad
        );

        echo json_encode([
            'success' => $resultado
        ]);
        exit;
    }
}