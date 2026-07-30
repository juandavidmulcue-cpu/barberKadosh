<?php

require_once 'app/config/conexion.php';

class GestionCitaModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    // Obtener las reservas del cliente
    public function obtenerReservasCliente($idCliente)
    {
        $sql = "SELECT
                    r.id_reservacion,
                    CONCAT(b.nombre,' ',b.apellido) AS barbero,
                    s.nombre AS servicio,
                    r.fecha_cita,
                    r.hora_cita,
                    r.estado
                FROM reservacion r
                INNER JOIN usuarios b
                    ON r.id_barbero = b.id_usuario
                INNER JOIN servicios s
                    ON r.id_servicio = s.id_servicio
                WHERE r.id_cliente = :cliente
                ORDER BY r.fecha_cita DESC, r.hora_cita DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cliente' => $idCliente
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear una reserva
    public function crearReserva($cliente, $barbero, $servicio, $fecha, $hora)
    {
        $sql = "INSERT INTO reservacion
                (id_cliente,id_barbero,id_servicio,fecha_cita,hora_cita,estado)
                VALUES
                (:cliente,:barbero,:servicio,:fecha,:hora,'Pendiente')";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':cliente'  => $cliente,
            ':barbero'  => $barbero,
            ':servicio' => $servicio,
            ':fecha'    => $fecha,
            ':hora'     => $hora
        ]);
    }

    // Cancelar reserva
    public function cancelarReserva($idReserva, $idCliente)
    {
        $sql = "UPDATE reservacion
                SET estado='Cancelada'
                WHERE id_reservacion=:id
                AND id_cliente=:cliente";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $idReserva,
            ':cliente' => $idCliente
        ]);
    }
}