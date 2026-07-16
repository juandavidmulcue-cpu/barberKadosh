<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/GestionProductoModel.php';

class GestionProductoController extends Controller
{
    private $productoModel;

    public function __construct()
    {
        parent::__construct();

        // Solo administradores
        $this->requireRole('admin');

        $this->productoModel = new ProductoModel();
    }

    /* =========================
       LISTAR PRODUCTOS
    ========================== */

    public function listar()
    {
        $productos = $this->productoModel->obtenerProductos();

        $this->view('app/views/admin/productos.php', [
            'productos' => $productos
        ]);
    }

    /* =========================
       GUARDAR PRODUCTO
    ========================== */

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->productoModel->guardarProducto([
                ':nombre' => $_POST['nombre'],
                ':precio' => $_POST['precio'],
                ':stock'  => $_POST['stock']
            ]);
        }

        $this->redirect("index.php?controller=admin&action=panel");
    }

    /* =========================
       ELIMINAR PRODUCTO
    ========================== */

    public function eliminar()
    {
        if (isset($_GET['id'])) {
            $this->productoModel->eliminarProducto($_GET['id']);
        }

        $this->redirect("index.php?controller=admin&action=panel");
    }

    public function editarProducto()
    {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->redirect("index.php?controller=admin&action=panel");
        }

        $id = $_GET['id'];

        $producto = $this->productoModel->obtenerProductoPorId($id);

        if (!$producto) {
            $this->redirect("index.php?controller=admin&action=panel");
        }

        $this->view("app/views/admin/editarProducto.php", [
            'producto' => $producto
        ]);
    }

    /* =========================
   ACTUALIZAR PRODUCTO
========================= */

    public function actualizarProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                ':id'      => $_POST['id_producto'],
                ':nombre'  => $_POST['nombre'],
                ':precio'  => $_POST['precio'],
                ':stock'   => $_POST['stock']
            ];

            $this->productoModel->actualizarProducto($data);
        }

        $this->redirect("index.php?controller=admin&action=panel");
    }
}
