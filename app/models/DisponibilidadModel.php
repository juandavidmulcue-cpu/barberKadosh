<?php
require_once 'app/config/conexion.php';

/**
 * DisponibilidadModel
 * -------------------------------------------------
 * Acceso a datos de la tabla `dias_no_laborales` (SRP).
 * Un registro con id_barbero NULL aplica a toda la barbería.
 */
class DisponibilidadModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /**
     * ¿La fecha es no laboral para el barbero
     * (o para toda la barbería)?
     */
    public function esDiaNoLaboral($idBarbero, $fecha)
    {
        $stmt = $this->db->prepare(
            "SELECT 1
             FROM dias_no_laborales
             WHERE fecha = :fecha
               AND (id_barbero IS NULL OR id_barbero = :barbero)
             LIMIT 1"
        );
        $stmt->execute([':fecha' => $fecha, ':barbero' => $idBarbero]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Fechas no laborales de un mes para un barbero
     * (incluye las globales). Devuelve ['YYYY-MM-DD', ...]
     */
    public function diasNoLaboralesDelMes($idBarbero, $anio, $mes)
    {
        $stmt = $this->db->prepare(
            "SELECT fecha
             FROM dias_no_laborales
             WHERE YEAR(fecha) = :anio
               AND MONTH(fecha) = :mes
               AND (id_barbero IS NULL OR id_barbero = :barbero)"
        );
        $stmt->execute([':anio' => $anio, ':mes' => $mes, ':barbero' => $idBarbero]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /**
     * Listado completo (administración).
     */
    public function listar()
    {
        $stmt = $this->db->query(
            "SELECT d.id_dia, d.fecha, d.motivo,
                    u.nombre AS barbero
             FROM dias_no_laborales d
             LEFT JOIN usuarios u ON d.id_barbero = u.id_usuario
             ORDER BY d.fecha ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function agregar($idBarbero, $fecha, $motivo)
    {
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO dias_no_laborales (id_barbero, fecha, motivo)
             VALUES (:barbero, :fecha, :motivo)"
        );
        return $stmt->execute([
            ':barbero' => $idBarbero ?: null,
            ':fecha'   => $fecha,
            ':motivo'  => $motivo
        ]);
    }

    public function eliminar($idDia)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM dias_no_laborales WHERE id_dia = :id"
        );
        return $stmt->execute([':id' => $idDia]);
    }
}
