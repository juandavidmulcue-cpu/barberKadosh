<?php

require_once 'app/config/conexion.php';

class GestionProductoModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    public function obtenerProductos()
    {
        $stmt = $this->db->prepare("SELECT * FROM productos");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function guardarProducto($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO productos (nombre, precio, stock)
            VALUES (:nombre, :precio, :stock)
        ");

        return $stmt->execute($data);
    }

    public function eliminar($id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM productos
            WHERE id_producto = :id
        ");

        return $stmt->execute([
            ':id' => $id
        ]);
    }

    public function obtenerProductoPorId($id)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM productos
            WHERE id_producto = :id
        ");

        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarProducto($data)
    {
        $stmt = $this->db->prepare("
            UPDATE productos
            SET nombre = :nombre,
                precio = :precio,
                stock = :stock
            WHERE id_producto = :id
        ");

        return $stmt->execute($data);
    }
}