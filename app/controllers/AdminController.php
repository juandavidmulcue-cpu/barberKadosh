<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/AdminModel.php';

class AdminController extends Controller
{
    private $adminModel;

    public function __construct()
    {
        parent::__construct();

        $this->requireRole('admin');

        $this->adminModel = new AdminModel();
    }

    /* =========================
       PANEL
    ========================== */

    public function panel()
    {
        $barberos = $this->adminModel->obtenerBarberos();
        $clientes = $this->adminModel->obtenerClientes();
        $productos = $this->adminModel->obtenerProductos();

        $this->view('app/views/admin/panel.php', [
            'barberos' => $barberos,
            'clientes' => $clientes,
            'productos' => $productos
        ]);
    }

    /* =========================
       BARBEROS
    ========================== */

    public function barberos()
    {
        $buscar = $_GET['buscar'] ?? '';
        $orden  = $_GET['orden'] ?? 'az';

        $barberos = $this->adminModel->obtenerBarberos($buscar, $orden);

        $this->view('app/views/admin/panel.php', [
            'barberos' => $barberos
        ]);
    }

    /* =========================
       CLIENTES
    ========================== */

    public function clientes()
    {
        $buscar = $_GET['buscar'] ?? '';
        $orden  = $_GET['orden'] ?? 'az';

        $clientes = $this->adminModel->obtenerClientes($buscar, $orden);

        $this->view('app/views/admin/panel.php', [
            'clientes' => $clientes
        ]);
    }

    /* =========================
       REGISTRAR BARBERO
    ========================== */

    public function registerBarbero()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                ':id' => $_POST['id_usuario'],
                ':nombre' => $_POST['nombre'],
                ':apellido' => $_POST['apellido'],
                ':telefono' => $_POST['telefono'],
                ':correo' => $_POST['correo'],
                ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
            ];

            $ok = $this->adminModel->registrarBarbero($data);

            if ($ok) {

                $_SESSION['mensaje_exito'] = "Registrado correctamente";

                $this->redirect("index.php?controller=admin&action=panel");
            }

            $error = "No se pudo registrar (revisa duplicados o la base de datos)";
        }

        $this->view('app/views/admin/register_barbero.php', [
            'error' => $error
        ]);
    }

    /* =========================
       ELIMINAR
    ========================== */

    public function eliminarBarbero()
    {
        if (isset($_GET['id'])) {
            $this->adminModel->eliminarBarbero($_GET['id']);
        }

        $this->redirect("index.php?controller=admin&action=panel");
    }

    public function eliminarCliente()
    {
        if (isset($_GET['id'])) {
            $this->adminModel->eliminarCliente($_GET['id']);
        }

        $this->redirect("index.php?controller=admin&action=panel");
    }

    /* =========================
       PRODUCTOS
    ========================== */

    public function productos()
    {
        $productos = $this->adminModel->obtenerProductos();

        $this->view('app/views/admin/productos.php', [
            'productos' => $productos
        ]);
    }

    public function guardarProducto()
    {
        $this->adminModel->guardarProducto([
            ':nombre' => $_POST['nombre'],
            ':precio' => $_POST['precio'],
            ':stock' => $_POST['stock']
        ]);

        $this->redirect("index.php?controller=admin&action=productos");
    }

        public function eliminarProducto()
    {
        if (isset($_GET['id'])) {
            $this->adminModel->eliminarProducto($_GET['id']);
        }

        $this->redirect("index.php?controller=admin&action=productos");
    }

    /* =========================
       CITAS
    ========================== */

    public function citas()
    {
        $citas = $this->adminModel->obtenerCitas();

        $this->view('app/views/admin/citas.php', [
            'citas' => $citas
        ]);
    }

    public function cancelarCita()
    {
        if (isset($_GET['id'])) {
            $this->adminModel->cancelarCita($_GET['id']);
        }

        $this->redirect("index.php?controller=admin&action=citas");
    }

    /* =========================
       EDITAR BARBERO
    ========================== */

    public function editarBarbero()
    {
        if (!isset($_GET['id_usuario']) || empty($_GET['id_usuario'])) {
            $this->redirect("index.php?controller=admin&action=panel");
        }

        $id = $_GET['id_usuario'];

        $barbero = $this->adminModel->getUsuarioById($id);

        if (!$barbero) {
            $this->redirect("index.php?controller=admin&action=panel");
        }

        $this->view("app/views/admin/editarBarbero.php", [
            'barbero' => $barbero
        ]);
    }
}