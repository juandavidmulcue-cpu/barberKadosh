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

        $this->productoModel = new GestionProductoModel();
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

        $this->redirect("index.php?controller=gestionProducto&action=listar");
    }

    /* =========================
       ELIMINAR PRODUCTO
    ========================== */

        public function eliminarProducto()
        {
            if (isset($_GET['id_producto'])) {
                $this->productoModel->eliminar($_GET['id_producto']);
            }

            $this->redirect("index.php?controller=gestionProducto&action=listar");
        }

    public function editarProducto()
    {
        if (!isset($_GET['id_producto']) || empty($_GET['id_producto'])) {
            $this->redirect("index.php?controller=gestionProducto&action=actualizarProducto");
        }

        $id = $_GET['id_producto'];

        $producto = $this->productoModel->obtenerProductoPorId($id);

        if (!$producto) {
            $this->redirect("index.php?controller=gestionProducto&action=editarProducto&id_producto=" . $id);
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

        $this->redirect("index.php?controller=gestionProducto&action=listar");
    }
}
