<?php

require_once 'app/config/conexion.php';

class Reservacion
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /*=========================
        BARBEROS
    =========================*/

    public function obtenerBarberos()
    {
        $sql = "SELECT id_usuario,nombre,apellido
                FROM usuarios
                WHERE id_rol=2
                AND estado='activo'
                ORDER BY nombre";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*=========================
        SERVICIOS
    =========================*/

    public function obtenerServicios()
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM servicios
            ORDER BY nombre
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*=========================
        HORARIOS
    =========================*/

    public function obtenerHorarios()
    {
        $sql = "SELECT
                    h.*,
                    u.nombre,
                    u.apellido
                FROM horarios h
                INNER JOIN usuarios u
                    ON h.id_barbero=u.id_usuario
                ORDER BY fecha,hora_inicio";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardarHorario($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO horarios
            (id_horario,id_barbero,fecha,hora_inicio,hora_fin)
            VALUES
            (:id_horario,:id_barbero,:fecha,:hora_inicio,:hora_fin)
        ");

        return $stmt->execute($data);
    }

    /*=========================
        RESERVACIONES
    =========================*/

    public function guardarReservacion($data)
    {
        $stmt=$this->db->prepare("
            INSERT INTO reservacion
            (id_reservacion,
            id_cliente,
            id_barbero,
            id_servicio,
            fecha_cita,
            hora_cita,
            estado)

            VALUES
            (:id_reservacion,
            :id_cliente,
            :id_barbero,
            :id_servicio,
            :fecha_cita,
            :hora_cita,
            'Pendiente')
        ");

        return $stmt->execute($data);
    }

}