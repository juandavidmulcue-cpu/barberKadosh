<?php
require_once 'app/config/conexion.php';

class CitaModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /* =========================
       ADMIN
       ========================= */

    public function obtenerCitas()
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function cancelarCita($id)
    {
        $stmt = $this->db->prepare(
            "UPDATE citas SET estado='cancelada' WHERE id=:id"
        );
        return $stmt->execute([':id' => $id]);
    }

    /* =========================
       CLIENTE
       ========================= */

    public function obtenerPorCliente($idCliente)
    {
        $stmt = $this->db->prepare(
            "SELECT c.id, c.fecha, c.hora, c.estado,
                    u.nombre AS barbero
             FROM citas c
             INNER JOIN usuarios u ON c.barbero_id = u.id
             WHERE c.cliente_id = :cliente"
        );

        $stmt->execute([':cliente' => $idCliente]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($idCliente, $idBarbero, $fecha, $hora)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO citas (cliente_id, barbero_id, fecha, hora, estado)
             VALUES (:cliente, :barbero, :fecha, :hora, 'activa')"
        );

        return $stmt->execute([
            ':cliente' => $idCliente,
            ':barbero' => $idBarbero,
            ':fecha'   => $fecha,
            ':hora'    => $hora
        ]);
    }

    public function cancelarDeCliente($idCita, $idCliente)
    {
        $stmt = $this->db->prepare(
            "UPDATE citas
             SET estado = 'cancelada'
             WHERE id = :id AND cliente_id = :cliente"
        );

        return $stmt->execute([
            ':id'      => $idCita,
            ':cliente' => $idCliente
        ]);
    }

    /* =========================
       BARBERO
       ========================= */

    public function obtenerPorBarbero($idBarbero)
    {
        $stmt = $this->db->prepare(
            "SELECT c.id, c.fecha, c.hora, c.estado,
                    u.nombre AS cliente
             FROM citas c
             INNER JOIN usuarios u ON c.cliente_id = u.id
             WHERE c.barbero_id = :barbero"
        );

        $stmt->execute([':barbero' => $idBarbero]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista de barberos disponibles para agendar.
     */
    public function obtenerBarberosDisponibles()
    {
        return $this->db->query(
            "SELECT id, nombre FROM usuarios WHERE rol_id = 2"
        )->fetchAll(PDO::FETCH_ASSOC);
    }
}
