<?php
require_once 'app/models/AdminModel.php';

class AdminController
{
    private $adminModel;

    public function __construct()
    {
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            header("Location: index.php");
            exit;
        }

        $this->adminModel = new AdminModel();
    }

    /* =========================
       PANEL
       ========================= */

    public function panel()
    {
        $barberos = $this->adminModel->obtenerBarberos();
        $clientes = $this->adminModel->obtenerClientes();
        $productos = $this->adminModel->obtenerProductos();
        require 'app/views/admin/panel.php';
    }

    /* =========================
       BARBEROS
       ========================= */

    public function barberos()
    {
        $buscar = $_GET['buscar'] ?? '';
        $orden  = $_GET['orden'] ?? 'az';

        $barberos = $this->adminModel->obtenerBarberos($buscar, $orden);
        require 'app/views/admin/panel.php';
    }

    public function clientes()
    {
        $buscar = $_GET['buscar'] ?? '';
        $orden  = $_GET['orden'] ?? 'az';

        $clientes = $this->adminModel->obtenerClientes($buscar, $orden);
        require 'app/views/admin/panel.php';
    }

    public function registerBarbero()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                ':id' => $_POST['id_usuario'],
                ':nombre' => $_POST['nombre'],
                ':apellido' => $_POST['apellido'],
                ':telefono' => $_POST['telefono'],
                ':correo' => $_POST['correo'],
                ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
            ];

            $this->adminModel->registrarBarbero($data);
            header("Location: index.php?controller=admin&action=panel");
        }

        require 'app/views/admin/register_barbero.php';
    }

    public function eliminarBarbero()
    {
        $this->adminModel->eliminarBarbero($_GET['id']);
        header("Location: index.php?controller=admin&action=panel");
    }

    public function eliminarCliente()
    {
        $this->adminModel->eliminarCliente($_GET['id']);
        header("Location: index.php?controller=admin&action=panel");
    }

    /* =========================
       PRODUCTOS
       ========================= */

    public function productos()
    {
        $productos = $this->adminModel->obtenerProductos();
        require 'app/views/admin/productos.php';
    }

    public function guardarProducto()
    {
        $this->adminModel->guardarProducto([
            ':nombre' => $_POST['nombre'],
            ':precio' => $_POST['precio'],
            ':stock' => $_POST['stock']
        ]);

        header("Location: index.php?controller=admin&action=productos");
    }

    public function eliminarProducto()
    {
        $this->adminModel->eliminarProducto($_GET['id']);
        header("Location: index.php?controller=admin&action=productos");
    }

    /* =========================
       CITAS
       ========================= */

    public function citas()
    {
        $citas = $this->adminModel->obtenerCitas();
        require 'app/views/admin/citas.php';
    }

    public function cancelarCita()
    {
        $this->adminModel->cancelarCita($_GET['id']);
        header("Location: index.php?controller=admin&action=citas");
    }

    public function editarBarbero()
    {
        if (!isset($_GET['id_usuario']) || empty($_GET['id_usuario'])) {
            header("Location: index.php?controller=admin&action=panel");
            exit;
        }

        $id = $_GET['id_usuario'];

        $barbero = $this->adminModel->getUsuarioById($id);

        if (!$barbero) {
            header("Location: index.php?controller=admin&action=panel");
            exit;
        }

        require "app/views/admin/editarBarbero.php";
    }
}
