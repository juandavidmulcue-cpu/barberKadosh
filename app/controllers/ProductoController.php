<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/ProductoModel.php';

class ProductoController extends Controller
{
    private $productoModel;

    public function __construct()
    {
        parent::__construct();

        $this->requireRole('admin');

        $this->productoModel = new Producto();
    }

    /* =========================
       LISTAR PRODUCTOS
    ========================== */

    public function index()
    {
        $productos = $this->productoModel->obtenerProductos();

        $this->view('app/views/admin/productos.php', [
            'productos' => $productos
        ]);
    }

    /* =========================
       CREAR PRODUCTO
    ========================== */

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'id' => $_POST['id_producto'],
                'nombre' => htmlspecialchars(trim($_POST['nombre'])),
                'precio' => $_POST['precio'],
                'stock' => $_POST['stock']
            ];

            $this->productoModel->crearProducto($data);
        }

        $this->redirect("index.php?controller=producto&action=index");
    }

    /* =========================
       ELIMINAR PRODUCTO
    ========================== */

    public function eliminarProducto()
    {
        if (isset($_GET['id_producto'])) {
            $this->productoModel->eliminarProducto($_GET['id_producto']);
        }

        $this->redirect("index.php?controller=producto&action=index");
    }
}