<?php

require_once 'app/config/conexion.php';

class ClienteProductoModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /* =========================
       PRODUCTOS DISPONIBLES
       PARA EL CLIENTE
    ========================== */

    public function obtenerProductosDisponibles()
    {
        $stmt = $this->db->prepare("
            SELECT id_producto, nombre, precio
            FROM productos
            WHERE stock > 0
            ORDER BY nombre ASC
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* =========================
       AGREGAR PRODUCTO A RESERVACIÓN
    ========================== */

    public function agregarProducto($idReservacion, $idProducto, $cantidad = 1)
    {
        $stmt = $this->db->prepare("
            INSERT INTO detalle_reservacion
            (id_reservacion, id_producto, cantidad)
            VALUES
            (:id_reservacion, :id_producto, :cantidad)
        ");

        return $stmt->execute([
            ':id_reservacion' => $idReservacion,
            ':id_producto' => $idProducto,
            ':cantidad' => $cantidad
        ]);
    }
}