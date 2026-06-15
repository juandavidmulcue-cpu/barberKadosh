<?php
require_once 'app/config/conexion.php';

class ProductoController
{
    private $db;

    public function __construct()
    {

        // Solo admin
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $this->db = Database::conectar();
    }

    /* =========================
       LISTAR PRODUCTOS
       ========================= */
    public function index()
    {
        $stmt = $this->db->query("SELECT * FROM productos");
        $productos = $stmt->fetchAll();

        require 'app/views/admin/productos.php';
    }

    /* =========================
       CREAR PRODUCTO
       ========================= */
    public function crear()
    {
        $stmt = $this->db->prepare(
                "INSERT INTO productos (id_producto, nombre, precio, stock)
                 VALUES (:id, :nombre, :precio, :stock)"
        );

        $stmt->execute([
                ':id' => $_POST['id_producto'],
                ':nombre' => htmlspecialchars($_POST['nombre']),
                ':precio' => $_POST['precio'],
                ':stock' => $_POST['stock']
        ]);
            header("Location: index.php?controller=producto&action=index");
            exit;
    }

    /* =========================
       ELIMINAR PRODUCTO
       ========================= */
    public function eliminarProducto()
    {
        $stmt = $this->db->prepare(
            "DELETE FROM productos WHERE `productos`.`id_producto` = :id"
        );

        $stmt->execute([':id' => $_GET['id_producto']]);

        header("Location: index.php?controller=producto&action=index");
        exit;
    }
}