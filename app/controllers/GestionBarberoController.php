<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/AdminModel.php';

class GestionBarberoController extends Controller
{
    private $usuarioModel;

    public function __construct()
    {
        parent::__construct();

        // Solo administradores
        $this->requireRole('admin');

        $this->usuarioModel = new Usuario();
    }

    /* =========================
       LISTAR BARBEROS
    ========================== */

    public function listar()
    {
        $buscar = $_GET['buscar'] ?? '';
        $orden  = $_GET['orden'] ?? 'az';

        $barberos = $this->usuarioModel->obtenerBarberos($buscar, $orden);

        $this->view('app/views/admin/barberos.php', [
            'barberos' => $barberos
        ]);
    }

    /* =========================
       REGISTRAR BARBERO
    ========================== */

    public function registrar()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                ':id'        => $_POST['id_usuario'],
                ':nombre'    => $_POST['nombre'],
                ':apellido'  => $_POST['apellido'],
                ':telefono'  => $_POST['telefono'],
                ':correo'    => $_POST['correo'],
                ':password'  => password_hash($_POST['password'], PASSWORD_DEFAULT)
            ];

            $ok = $this->usuarioModel->registrarBarbero($data);

            if ($ok) {

                $_SESSION['mensaje_exito'] = "Barbero registrado correctamente.";

                $this->redirect("index.php?controller=admin&action=panel");
            }

            $error = "No se pudo registrar el barbero.";
        }

        $this->view('app/views/admin/register_barbero.php', [
            'error' => $error
        ]);
    }

    /* =========================
       EDITAR BARBERO
    ========================== */

    public function editar()
    {
        if (!isset($_GET['id_usuario']) || empty($_GET['id_usuario'])) {
            $this->redirect("index.php?controller=admin&action=panel");
        }

        $id = $_GET['id_usuario'];

        $barbero = $this->usuarioModel->getUsuarioById($id);

        if (!$barbero) {
            $this->redirect("index.php?controller=admin&action=panel");
        }

        $this->view('app/views/admin/editarBarbero.php', [
            'barbero' => $barbero
        ]);
    }

    /* =========================
       ELIMINAR BARBERO
    ========================== */

    public function desactivar()
    {
        if (isset($_GET['id'])) {
            $this->usuarioModel->inactivar($_GET['id']);
        }

        $this->redirect("index.php?controller=admin&action=panel");
    }

    public function activar()
    {
        if (isset($_GET['id'])) {

            $id = $_GET['id'];

            $this->usuarioModel->activar($id);
        }

        $this->redirect("index.php?controller=admin&action=panel");
    }
}
