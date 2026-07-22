<?php
require_once 'app/config/conexion.php';

/**
 * CitaModel
 * -------------------------------------------------
 * Acceso a datos de la tabla `citas` (SRP).
 * Reescrito para coincidir con el esquema real de la
 * base de datos (fecha_cita, hora_cita, id_cliente,
 * id_barbero, id_servicio, estado). Las consultas
 * anteriores usaban columnas inexistentes y fallaban.
 */
class CitaModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /* =====================================================
       CREACIÓN
       ===================================================== */

    /**
     * Crea una cita. nombre_cliente/email_cliente se
     * guardan denormalizados porque el sistema de
     * recordatorios por correo los utiliza.
     */
    public function crear($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO citas
             (id_cliente, id_barbero, id_servicio, nombre_cliente, email_cliente, fecha_cita, hora_cita, estado)
             VALUES
             (:id_cliente, :id_barbero, :id_servicio, :nombre_cliente, :email_cliente, :fecha_cita, :hora_cita, 'pendiente')"
        );

        return $stmt->execute([
            ':id_cliente'     => $data['id_cliente'],
            ':id_barbero'     => $data['id_barbero'],
            ':id_servicio'    => $data['id_servicio'],
            ':nombre_cliente' => $data['nombre_cliente'],
            ':email_cliente'  => $data['email_cliente'],
            ':fecha_cita'     => $data['fecha_cita'],
            ':hora_cita'      => $data['hora_cita']
        ]);
    }

    /* =====================================================
       CONSULTAS
       ===================================================== */

    public function obtenerPorId($id)
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, s.nombre AS servicio, b.nombre AS barbero
             FROM citas c
             LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
             LEFT JOIN usuarios b ON c.id_barbero = b.id_usuario
             WHERE c.id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPorCliente($idCliente)
    {
        $stmt = $this->db->prepare(
            "SELECT c.id, c.fecha_cita, c.hora_cita, c.estado,
                    s.nombre AS servicio, s.precio,
                    b.nombre AS barbero, b.apellido AS barbero_apellido,
                    cal.puntuacion
             FROM citas c
             LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
             LEFT JOIN usuarios b ON c.id_barbero = b.id_usuario
             LEFT JOIN calificaciones cal ON cal.id_cita = c.id
             WHERE c.id_cliente = :cliente
             ORDER BY c.fecha_cita DESC, c.hora_cita DESC"
        );
        $stmt->execute([':cliente' => $idCliente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerPorBarbero($idBarbero)
    {
        $stmt = $this->db->prepare(
            "SELECT c.id, c.fecha_cita, c.hora_cita, c.estado, c.nombre_cliente,
                    s.nombre AS servicio,
                    cal.puntuacion, cal.comentario
             FROM citas c
             LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
             LEFT JOIN calificaciones cal ON cal.id_cita = c.id
             WHERE c.id_barbero = :barbero
             ORDER BY c.fecha_cita DESC, c.hora_cita ASC"
        );
        $stmt->execute([':barbero' => $idBarbero]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerTodas()
    {
        $stmt = $this->db->query(
            "SELECT c.id, c.fecha_cita, c.hora_cita, c.estado, c.nombre_cliente,
                    s.nombre AS servicio,
                    b.nombre AS barbero
             FROM citas c
             LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
             LEFT JOIN usuarios b ON c.id_barbero = b.id_usuario
             ORDER BY c.fecha_cita DESC, c.hora_cita ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /* =====================================================
       DISPONIBILIDAD (usadas por DisponibilidadService)
       ===================================================== */

    /**
     * Horas ya reservadas (citas activas) de un barbero en una fecha.
     */
    public function horasOcupadas($idBarbero, $fecha)
    {
        $stmt = $this->db->prepare(
            "SELECT hora_cita
             FROM citas
             WHERE id_barbero = :barbero
               AND fecha_cita = :fecha
               AND estado <> 'cancelada'"
        );
        $stmt->execute([':barbero' => $idBarbero, ':fecha' => $fecha]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /**
     * ¿Existe una cita activa del barbero en esa fecha y hora?
     */
    public function existeConflicto($idBarbero, $fecha, $hora)
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*)
             FROM citas
             WHERE id_barbero = :barbero
               AND fecha_cita = :fecha
               AND hora_cita = :hora
               AND estado <> 'cancelada'"
        );
        $stmt->execute([
            ':barbero' => $idBarbero,
            ':fecha' => $fecha,
            ':hora' => $hora . ':00'
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Conteo de citas activas por día en un mes (para el calendario).
     * Devuelve ['YYYY-MM-DD' => n, ...]
     */
    public function conteoCitasActivasPorDia($idBarbero, $anio, $mes)
    {
        $stmt = $this->db->prepare(
            "SELECT fecha_cita, COUNT(*) AS n
             FROM citas
             WHERE id_barbero = :barbero
               AND YEAR(fecha_cita) = :anio
               AND MONTH(fecha_cita) = :mes
               AND estado <> 'cancelada'
             GROUP BY fecha_cita"
        );
        $stmt->execute([':barbero' => $idBarbero, ':anio' => $anio, ':mes' => $mes]);

        $resultado = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $resultado[$fila['fecha_cita']] = (int) $fila['n'];
        }
        return $resultado;
    }

    /* =====================================================
       CAMBIOS DE ESTADO
       ===================================================== */

    /**
     * El cliente cancela una cita propia (solo si sigue activa).
     */
    public function cancelarDeCliente($idCita, $idCliente)
    {
        $stmt = $this->db->prepare(
            "UPDATE citas
             SET estado = 'cancelada'
             WHERE id = :id
               AND id_cliente = :cliente
               AND estado IN ('pendiente', 'confirmada')"
        );
        $stmt->execute([':id' => $idCita, ':cliente' => $idCliente]);
        return $stmt->rowCount() > 0;
    }

    /**
     * El admin cancela cualquier cita activa.
     */
    public function cancelarCita($id)
    {
        $stmt = $this->db->prepare(
            "UPDATE citas
             SET estado = 'cancelada'
             WHERE id = :id AND estado IN ('pendiente', 'confirmada')"
        );
        return $stmt->execute([':id' => $id]);
    }

    /**
     * El barbero marca su cita como completada
     * (habilita la calificación del cliente).
     */
    public function completarDeBarbero($idCita, $idBarbero)
    {
        $stmt = $this->db->prepare(
            "UPDATE citas
             SET estado = 'completada'
             WHERE id = :id
               AND id_barbero = :barbero
               AND estado IN ('pendiente', 'confirmada')"
        );
        $stmt->execute([':id' => $idCita, ':barbero' => $idBarbero]);
        return $stmt->rowCount() > 0;
    }
}
