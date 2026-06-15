<?php

require_once 'app/config/conexion.php';

class AdminController
{
    private $db;

    public function __construct()
    {

        // 🔐 SOLO ADMIN
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            header("Location: index.html");
            exit;
        }

        $this->db = Database::conectar();
    }

    /* =====================================================
       PANEL PRINCIPAL ADMIN
       ===================================================== */
    public function panel()
    {
        // 🔹 BARBEROS (rol = 2)
        $stmt = $this->db->prepare(
            "SELECT id_usuario, nombre, apellido, telefono, correo
         FROM usuarios
         WHERE id_rol = 2"
        );
        $stmt->execute();
        $barberos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$barberos) {
            $barberos = [];
        }

        // 🔹 PRODUCTOS (ya que también los usas en el panel)
        $stmt = $this->db->prepare("SELECT * FROM productos");
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$productos) {
            $productos = [];
        }

        require 'app/views/admin/panel.php';
    }

    /* =====================================================
       USUARIOS (CLIENTES Y BARBEROS)
       ===================================================== */
    public function usuarios()
    {
        $stmt = $this->db->query(
            "SELECT u.id_usuario, u.nombre, u.correo, r.nombre_rol AS roles
             FROM usuarios u
             INNER JOIN roles r ON u.id_rol = r.id_rol"
        );

        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require 'app/views/admin/usuarios.php';
    }

    /* =====================================================
       REGISTRAR BARBERO (ADMIN)
       ===================================================== */

    public function registerBarbero()
    {
        // 🔐 Solo admin
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            header("Location: index.php");
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id_usuario = trim($_POST['id_usuario']);
            $nombre     = trim($_POST['nombre']);
            $apellido   = trim($_POST['apellido']);
            $telefono   = trim($_POST['telefono']);
            $correo      = trim($_POST['correo']);
            $password   = $_POST['password'];

            if (
                empty($id_usuario) || empty($nombre) || empty($apellido) ||
                empty($telefono) || empty($correo) || empty($password)
            ) {
                $error = "Todos los campos son obligatorios";
            } else {

                // 🔒 FORZAR ROL BARBERO (id = 2)
                $rol_id = 2;

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $this->db->prepare(
                    "INSERT INTO usuarios 
                (id_usuario, id_rol, nombre, apellido, telefono, correo, password)
                VALUES 
                (:id, :rol, :nombre, :apellido, :telefono, :correo, :password)"
                );

                $stmt->execute([
                    ':id'       => $id_usuario,
                    ':rol'      => $rol_id,
                    ':nombre'   => $nombre,
                    ':apellido' => $apellido,
                    ':telefono' => $telefono,
                    ':correo'    => $correo,
                    ':password' => $passwordHash
                ]);

                header("Location: index.php?controller=admin&action=panel");
                exit;
            }
        }

        require 'app/views/admin/register_barbero.php';
    }

    /* =====================================================
       PRODUCTOS
       ===================================================== */
    public function productos()
    {
        // Consulta segura
        $stmt = $this->db->prepare("SELECT * FROM productos");
        $stmt->execute();

        // 🔑 SIEMPRE devolver un array
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Por seguridad, si no hay resultados
        if (!$productos) {
            $productos = [];
        }

        require 'app/views/admin/productos.php';
    }

    public function guardarProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $stmt = $this->db->prepare(
                "INSERT INTO productos (nombre, precio, stock)
                 VALUES ( :nombre, :precio, :stock)"
            );

            $stmt->execute([
                ':nombre' => htmlspecialchars($_POST['nombre']),
                ':precio' => $_POST['precio'],
                ':stock' => $_POST['stock']
            ]);

            header("Location: index.php?controller=admin&action=productos");
            exit;
        }
    }

    public function eliminarProducto()
    {
        $stmt = $this->db->prepare(
            "DELETE FROM productos WHERE `productos`.`id_producto` = :id"
        );

        $stmt->execute([':id' => $_GET['id']]);

        header("Location: index.php?controller=admin&action=productos");
        exit;
    }

    public function actualizarProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $stmt = $this->db->prepare(
                "UPDATE productos 
             SET nombre = :nombre, 
                 precio = :precio, 
                 stock = :stock
             WHERE id_producto = :id"
            );

            $stmt->execute([
                ':nombre' => htmlspecialchars($_POST['nombre']),
                ':precio' => $_POST['precio'],
                ':stock' => $_POST['stock'],
                ':id' => $_POST['id_producto'] // 👈 ESTE FALTABA
            ]);

            header("Location: index.php?controller=admin&action=productos");
            exit;
        }
    }

    public function editarProducto()
    {
        if (!isset($_GET['id_producto'])) {
            header("Location: index.php?controller=admin&action=productos");
            exit;
        }

        $id = $_GET['id_producto'];

        $stmt = $this->db->prepare("SELECT * FROM productos WHERE id_producto = :id");
        $stmt->execute([':id' => $id]);

        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        require "app/views/admin/editarProducto.php";
    }

    /* =====================================================
       CITAS (VER TODAS)
       ===================================================== */
    public function citas()
    {
        $stmt = $this->db->query(
            "SELECT c.id, c.fecha, c.hora, c.estado,
                    u.nombre AS cliente,
                    b.nombre AS barbero
             FROM citas c
             INNER JOIN usuarios u ON c.cliente_id = u.id
             INNER JOIN usuarios b ON c.barbero_id = b.id
             ORDER BY c.fecha, c.hora"
        );

        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require 'app/views/admin/citas.php';
    }

    public function cancelarCita()
    {
        $stmt = $this->db->prepare(
            "UPDATE citas SET estado = 'cancelada' WHERE id = :id"
        );

        $stmt->execute([':id' => $_GET['id']]);

        header("Location: index.php?controller=admin&action=citas");
        exit;
    }

    public function eliminarBarbero()
    {
        $stmt = $this->db->prepare(
            "DELETE FROM usuarios WHERE `usuarios`.`id_usuario` = :id"
        );

        $stmt->execute([':id' => $_GET['id']]);

        header("Location: index.php?controller=admin&action=panel");
        exit;
    }

    public function actualizarBarbero()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $stmt = $this->db->prepare(
                "UPDATE usuarios 
             SET nombre = :nombre, 
                 apellido = :apellido, 
                 telefono = :telefono, 
                 correo = :correo
             WHERE id_usuario = :id"
            );

            $stmt->execute([
                ':nombre' => htmlspecialchars($_POST['nombre']),
                ':apellido' => htmlspecialchars($_POST['apellido']),
                ':telefono' => htmlspecialchars($_POST['telefono']),
                ':correo' => htmlspecialchars($_POST['correo']),
                ':id' => $_POST['id_usuario']
            ]);

            header("Location: index.php?controller=admin&action=panel");
            exit;
        }
    }

    public function editarBarbero()
    {
        if (!isset($_GET['id_usuario'])) {
            header("Location: index.php?controller=admin&action=panel");
            exit;
        }

        $id = $_GET['id_usuario'];

        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
        $stmt->execute([':id' => $id]);

        $barbero = $stmt->fetch(PDO::FETCH_ASSOC);

        require "app/views/admin/editarBarbero.php";
    }
}
