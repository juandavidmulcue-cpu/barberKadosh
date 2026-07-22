<?php
require_once 'app/config/conexion.php';

/**
 * CalificacionModel
 * -------------------------------------------------
 * Acceso a datos de la tabla `calificaciones` (SRP).
 * Cada cita completada admite una única calificación
 * (garantizado también por UNIQUE en la base de datos).
 */
class CalificacionModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    public function crear($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO calificaciones
             (id_cita, id_barbero, id_cliente, puntuacion, comentario)
             VALUES (:id_cita, :id_barbero, :id_cliente, :puntuacion, :comentario)"
        );

        return $stmt->execute([
            ':id_cita'    => $data['id_cita'],
            ':id_barbero' => $data['id_barbero'],
            ':id_cliente' => $data['id_cliente'],
            ':puntuacion' => $data['puntuacion'],
            ':comentario' => $data['comentario']
        ]);
    }

    public function existeParaCita($idCita)
    {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM calificaciones WHERE id_cita = :id LIMIT 1"
        );
        $stmt->execute([':id' => $idCita]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Historial de calificaciones de un barbero.
     */
    public function obtenerPorBarbero($idBarbero)
    {
        $stmt = $this->db->prepare(
            "SELECT cal.puntuacion, cal.comentario, cal.fecha_creacion,
                    c.fecha_cita, u.nombre AS cliente
             FROM calificaciones cal
             INNER JOIN citas c ON cal.id_cita = c.id
             INNER JOIN usuarios u ON cal.id_cliente = u.id_usuario
             WHERE cal.id_barbero = :barbero
             ORDER BY cal.fecha_creacion DESC"
        );
        $stmt->execute([':barbero' => $idBarbero]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Promedio y total de calificaciones del barbero.
     */
    public function resumenBarbero($idBarbero)
    {
        $stmt = $this->db->prepare(
            "SELECT ROUND(AVG(puntuacion), 1) AS promedio, COUNT(*) AS total
             FROM calificaciones
             WHERE id_barbero = :barbero"
        );
        $stmt->execute([':barbero' => $idBarbero]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
