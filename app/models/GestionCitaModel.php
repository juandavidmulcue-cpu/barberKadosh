<?php

require_once 'app/config/conexion.php';

class CitaModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    public function obtenerCitas()
    {
        $stmt = $this->db->query("
            SELECT c.id,
                   c.fecha,
                   c.hora,
                   c.estado,
                   u.nombre AS cliente,
                   b.nombre AS barbero
            FROM citas c
            INNER JOIN usuarios u
                ON c.cliente_id = u.id
            INNER JOIN usuarios b
                ON c.barbero_id = b.id
            ORDER BY c.fecha, c.hora
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function cancelarCita($id)
    {
        $stmt = $this->db->prepare("
            UPDATE citas
            SET estado = 'cancelada'
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}