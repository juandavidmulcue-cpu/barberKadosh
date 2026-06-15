<?php

require_once 'Database.php';

class Cita
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /* ===============================
       CREAR CITA
       =============================== */
    public function crear($cliente, $barbero, $fecha, $hora)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO citas (cliente_id, barbero_id, fecha, hora, estado)
             VALUES (:c, :b, :f, :h, 'activa')"
        );

        return $stmt->execute([
            ':c' => $cliente,
            ':b' => $barbero,
            ':f' => $fecha,
            ':h' => $hora
        ]);
    }

    /* ===============================
       CITAS POR CLIENTE
       =============================== */
    public function obtenerPorCliente($cliente)
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.nombre AS barbero
             FROM citas c
             INNER JOIN usuarios u ON c.barbero_id = u.id
             WHERE c.cliente_id = :id"
        );

        $stmt->execute([':id' => $cliente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===============================
       CITAS POR BARBERO
       =============================== */
    public function obtenerPorBarbero($barbero)
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.nombre AS cliente
             FROM citas c
             INNER JOIN usuarios u ON c.cliente_id = u.id
             WHERE c.barbero_id = :id"
        );

        $stmt->execute([':id' => $barbero]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===============================
       TODAS LAS CITAS (ADMIN)
       =============================== */
    public function listar()
    {
        $stmt = $this->db->query(
            "SELECT c.id, c.fecha, c.hora, c.estado,
                    u.nombre AS cliente,
                    b.nombre AS barbero
             FROM citas c
             INNER JOIN usuarios u ON c.cliente_id = u.id
             INNER JOIN usuarios b ON c.barbero_id = b.id"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===============================
       CANCELAR CITA
       =============================== */
    public function cancelar($id)
    {
        $stmt = $this->db->prepare(
            "UPDATE citas SET estado = 'cancelada' WHERE id = :id"
        );

        return $stmt->execute([':id' => $id]);
    }
}