<?php

require_once 'app/config/conexion.php';

class ProductoModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /* =========================
       OBTENER TODOS LOS PRODUCTOS
    ========================== */
    public function obtenerProductos()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM productos
            ORDER BY nombre ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       OBTENER PRODUCTO POR ID
    ========================== */
    public function obtenerProductoPorId($id)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM productos
            WHERE id_producto = :id
            LIMIT 1
        ");

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       CREAR PRODUCTO
    ========================== */
    public function crearProducto($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO productos
            (id_producto, nombre, precio, stock)
            VALUES
            (:id, :nombre, :precio, :stock)
        ");

        return $stmt->execute([
            ':id' => $data['id'],
            ':nombre' => $data['nombre'],
            ':precio' => $data['precio'],
            ':stock' => $data['stock']
        ]);
    }

    /* =========================
       ACTUALIZAR PRODUCTO
    ========================== */
    public function actualizarProducto($data)
    {
        $stmt = $this->db->prepare("
            UPDATE productos
            SET nombre = :nombre,
                precio = :precio,
                stock = :stock
            WHERE id_producto = :id
        ");

        return $stmt->execute([
            ':id' => $data['id'],
            ':nombre' => $data['nombre'],
            ':precio' => $data['precio'],
            ':stock' => $data['stock']
        ]);
    }

    /* =========================
       ELIMINAR PRODUCTO
    ========================== */
    public function eliminarProducto($id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM productos
            WHERE id_producto = :id
        ");

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}