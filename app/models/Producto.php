<?php

require_once 'Database.php';

class Producto
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /* ===============================
       LISTAR PRODUCTOS
       =============================== */
    public function listar()
    {
        $stmt = $this->db->query("SELECT * FROM productos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===============================
       CREAR PRODUCTO
       =============================== */
    public function crear($nombre, $precio, $stock)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO productos (nombre, precio, stock)
             VALUES (:n, :p, :s)"
        );

        return $stmt->execute([
            ':n' => $nombre,
            ':p' => $precio,
            ':s' => $stock,
        ]);
    }

    /* ===============================
       ELIMINAR PRODUCTO
       =============================== */
    public function eliminar($id)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM productos WHERE id_producto = :id"
        );

        return $stmt->execute([':id' => $id]);
    }
}