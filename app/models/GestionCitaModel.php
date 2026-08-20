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
                r.id_barbero,
                r.id_servicio,
                CONCAT(b.nombre, ' ', b.apellido) AS barbero,
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
            AND r.estado IN ('Pendiente')
            ORDER BY r.fecha_cita DESC, r.hora_cita DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':cliente' => $idCliente
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerHistorialCliente($idCliente)
    {
        $sql = "SELECT
            r.id_reservacion,
            r.id_barbero,
            r.id_servicio,
            CONCAT(b.nombre, ' ', b.apellido) AS barbero,
            s.nombre AS servicio,
            s.precio AS precio_servicio,
            COALESCE(p.nombre, 'Ninguno') AS producto,
            COALESCE(p.precio, 0) AS precio_producto,
            COALESCE(dr.cantidad, 0) AS cantidad_producto,
            (s.precio + (COALESCE(p.precio, 0) * COALESCE(dr.cantidad, 0))) AS total,
            r.fecha_cita,
            r.hora_cita,
            r.estado
        FROM reservacion r
        INNER JOIN usuarios b
            ON r.id_barbero = b.id_usuario
        INNER JOIN servicios s
            ON r.id_servicio = s.id_servicio
        LEFT JOIN detalle_reservacion dr 
            ON r.id_reservacion = dr.id_reservacion
        LEFT JOIN productos p 
            ON dr.id_producto = p.id_producto
        WHERE r.id_cliente = :cliente
        AND r.estado IN ('Completada', 'Cancelada')
        ORDER BY r.fecha_cita DESC, r.hora_cita DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':cliente' => $idCliente
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerReservaPorId($idReservacion)
    {
        $sql = "SELECT
                r.id_reservacion,
                r.id_cliente,
                r.id_barbero,
                r.id_servicio,
                r.fecha_cita,
                r.hora_cita,
                r.estado,
                s.nombre AS servicio,
                s.duracion,
                CONCAT(b.nombre, ' ', b.apellido) AS barbero
            FROM reservacion r

            INNER JOIN servicios s
                ON r.id_servicio = s.id_servicio

            INNER JOIN usuarios b
                ON r.id_barbero = b.id_usuario

            WHERE r.id_reservacion = :id
            LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $idReservacion
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Crear una reserva
    public function crearReserva($cliente, $barbero, $servicio, $fecha, $hora)
    {
        // ==========================================
        // 1. Verificar si la hora ya está ocupada
        // ==========================================

        $sql = "SELECT COUNT(*)
            FROM reservacion
            WHERE id_barbero = :barbero
            AND fecha_cita = :fecha
            AND hora_cita = :hora
            AND estado != 'Cancelada'";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':barbero' => $barbero,
            ':fecha'   => $fecha,
            ':hora'    => $hora
        ]);

        $ocupada = $stmt->fetchColumn();

        if ($ocupada > 0) {
            return false;
        }


        // ==========================================
        // 2. Buscar la última reservación
        // ==========================================

        $sql = "SELECT id_reservacion
            FROM reservacion
            WHERE id_reservacion LIKE 'RESER%'
            ORDER BY id_reservacion DESC
            LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $ultimo = $stmt->fetch(PDO::FETCH_ASSOC);


        // ==========================================
        // 3. Generar el siguiente ID
        // ==========================================

        if ($ultimo) {

            $numero = intval(
                substr($ultimo['id_reservacion'], 5)
            );

            $numero++;
        } else {

            $numero = 1;
        }


        // RESER001, RESER002, RESER003...
        $idReservacion =
            'RESER' .
            str_pad($numero, 3, '0', STR_PAD_LEFT);


        // ==========================================
        // 4. Insertar la reservación
        // ==========================================

        $sql = "INSERT INTO reservacion
            (
                id_reservacion,
                id_cliente,
                id_barbero,
                id_servicio,
                fecha_cita,
                hora_cita,
                estado
            )
            VALUES
            (
                :id_reservacion,
                :cliente,
                :barbero,
                :servicio,
                :fecha,
                :hora,
                'Pendiente'
            )";

        $stmt = $this->db->prepare($sql);

        $resultado = $stmt->execute([
            ':id_reservacion' => $idReservacion,
            ':cliente'        => $cliente,
            ':barbero'        => $barbero,
            ':servicio'       => $servicio,
            ':fecha'          => $fecha,
            ':hora'           => $hora
        ]);

        if ($resultado) {
            return $idReservacion;
        }

        return false;
    }


    public function verificarDisponibilidad($barbero, $fecha, $hora)
    {
        $sql = "SELECT COUNT(*) 
            FROM reservacion
            WHERE id_barbero = :barbero
            AND fecha_cita = :fecha
            AND hora_cita = :hora
            AND estado != 'Cancelada'";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':barbero' => $barbero,
            ':fecha'   => $fecha,
            ':hora'    => $hora
        ]);

        return $stmt->fetchColumn() > 0;
    }



    public function obtenerHorarioBarbero($idBarbero, $fecha)
    {
        $sql = "SELECT hora_inicio, hora_fin
            FROM horarios
            WHERE id_barbero = :barbero
            AND fecha = :fecha
            LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':barbero' => $idBarbero,
            ':fecha'   => $fecha
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDuracionServicio($idServicio)
    {
        $sql = "SELECT duracion
            FROM servicios
            WHERE id_servicio = :servicio
            LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':servicio' => $idServicio
        ]);

        return $stmt->fetchColumn();
    }

    public function obtenerHorasOcupadas($idBarbero, $fecha)
    {
        $sql = "SELECT
                r.hora_cita,
                s.duracion
            FROM reservacion r
            INNER JOIN servicios s
                ON r.id_servicio = s.id_servicio
            WHERE r.id_barbero = :barbero
            AND r.fecha_cita = :fecha
            AND r.estado != 'Cancelada'
            ORDER BY r.hora_cita ASC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':barbero' => $idBarbero,
            ':fecha'   => $fecha
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function actualizarReserva(
        $idReservacion,
        $idCliente,
        $barbero,
        $servicio,
        $fecha,
        $hora
    ) {
        $sql = "UPDATE reservacion
            SET
                id_barbero = :barbero,
                id_servicio = :servicio,
                fecha_cita = :fecha,
                hora_cita = :hora
            WHERE id_reservacion = :id_reservacion
            AND id_cliente = :cliente";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':barbero'        => $barbero,
            ':servicio'       => $servicio,
            ':fecha'          => $fecha,
            ':hora'           => $hora,
            ':id_reservacion' => $idReservacion,
            ':cliente'        => $idCliente
        ]);
    }

    /* =========================================
   FINALIZAR RESERVA
========================================= */

    public function finalizarReserva($idReserva, $idCliente)
    {
        $sql = "UPDATE reservacion
            SET estado = 'Completada'
            WHERE id_reservacion = :id
            AND id_cliente = :cliente
            AND estado = 'Pendiente'";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $idReserva,
            ':cliente' => $idCliente
        ]);
    }

    /* =========================================
   GUARDAR RESEÑA
========================================= */

    public function guardarResena(
        $idReservacion,
        $idCliente,
        $calificacion,
        $comentario
    ) {
        try {

            $sql = "SELECT
                    id_reservacion,
                    id_barbero,
                    estado
                FROM reservacion
                WHERE id_reservacion = :reservacion
                AND id_cliente = :cliente
                LIMIT 1";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':reservacion' => $idReservacion,
                ':cliente' => $idCliente
            ]);

            $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$reserva) {
                return false;
            }

            if ($reserva['estado'] === 'Cancelada') {
                return false;
            }

            $sql = "SELECT COUNT(*)
                FROM resenas
                WHERE id_reservacion = :reservacion";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':reservacion' => $idReservacion
            ]);

            if ($stmt->fetchColumn() > 0) {
                return false;
            }

            $sql = "SELECT id_resena
                FROM resenas
                WHERE id_resena LIKE 'RESEN%'
                ORDER BY id_resena DESC
                LIMIT 1";

            $stmt = $this->db->prepare($sql);

            $stmt->execute();

            $ultima = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($ultima) {
                $numero = intval(
                    substr($ultima['id_resena'], 5)
                );
                $numero++;
            } else {
                $numero = 1;
            }

            $idResena =
                'RESEN' .
                str_pad($numero, 3, '0', STR_PAD_LEFT);

            $this->db->beginTransaction();

            $sql = "UPDATE reservacion
                SET estado = 'Completada'
                WHERE id_reservacion = :id
                AND id_cliente = :cliente
                AND estado = 'Pendiente'";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':id' => $idReservacion,
                ':cliente' => $idCliente
            ]);

            $sql = "INSERT INTO resenas
                (
                    id_resena,
                    id_reservacion,
                    id_cliente,
                    id_barbero,
                    calificacion,
                    comentario,
                    fecha
                )
                VALUES
                (
                    :id_resena,
                    :reservacion,
                    :cliente,
                    :barbero,
                    :calificacion,
                    :comentario,
                    NOW()
                )";

            $stmt = $this->db->prepare($sql);

            $resultado = $stmt->execute([
                ':id_resena' => $idResena,
                ':reservacion' => $idReservacion,
                ':cliente' => $idCliente,
                ':barbero' => $reserva['id_barbero'],
                ':calificacion' => $calificacion,
                ':comentario' => $comentario
            ]);
            if ($resultado) {

                $this->db->commit();

                return true;
            }

            $this->db->rollBack();

            return false;
        } catch (PDOException $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }
}
