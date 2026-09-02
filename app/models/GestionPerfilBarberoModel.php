<?php

require_once 'app/config/conexion.php';

class GestionPerfilBarberoModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }


    /* =========================
       DATOS DEL BARBERO
    ========================== */

    public function obtenerBarbero($idBarbero)
    {
        $sql = "SELECT
                    id_usuario,
                    nombre,
                    apellido,
                    telefono,
                    correo
                FROM usuarios
                WHERE id_usuario = ?
                AND id_rol = 2";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $idBarbero
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /* =========================
       RESERVACIONES DEL BARBERO
    ========================== */

    public function obtenerReservacionesBarbero($idBarbero)
    {
        $sql = "SELECT
                r.id_reservacion,
                r.id_cliente,
                CONCAT(c.nombre, ' ', c.apellido) AS cliente,
                s.nombre AS servicio,
                r.fecha_cita,
                r.hora_cita,
                r.estado
            FROM reservacion r
            INNER JOIN usuarios c
                ON r.id_cliente = c.id_usuario
            INNER JOIN servicios s
                ON r.id_servicio = s.id_servicio
            WHERE r.id_barbero = :barbero
            AND r.estado IN ('Pendiente')
            ORDER BY r.fecha_cita DESC, r.hora_cita DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':barbero' => $idBarbero
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerHistorialBarbero($idBarbero)
    {
        $sql = "SELECT
                r.id_reservacion,
                r.id_cliente,
                CONCAT(c.nombre, ' ', c.apellido) AS cliente,
                s.nombre AS servicio,
                r.fecha_cita,
                r.hora_cita,
                r.estado
            FROM reservacion r
            INNER JOIN usuarios c
                ON r.id_cliente = c.id_usuario
            INNER JOIN servicios s
                ON r.id_servicio = s.id_servicio
            WHERE r.id_barbero = :barbero
            AND r.estado IN ('Completada', 'Cancelada')
            ORDER BY r.fecha_cita DESC, r.hora_cita DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':barbero' => $idBarbero
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       HORARIOS DEL BARBERO
    ========================== */

    public function obtenerHorariosBarbero($idBarbero)
    {
        $sql = "SELECT
                    id_horario,
                    fecha,
                    hora_inicio,
                    hora_fin

                FROM horarios

                WHERE id_barbero = ?

                ORDER BY
                    fecha ASC,
                    hora_inicio ASC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $idBarbero
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* =========================
       RESEÑAS DEL BARBERO
    ========================== */

    public function obtenerResenasBarbero($idBarbero)
    {
        $sql = "SELECT
                    r.id_resena,
                    CONCAT(
                        u.nombre,
                        ' ',
                        u.apellido
                    ) AS usuario,

                    r.calificacion,
                    r.comentario,
                    r.fecha
                FROM resenas r

                INNER JOIN usuarios u
                    ON r.id_cliente = u.id_usuario
                WHERE r.id_barbero = ?
                ORDER BY r.fecha DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $idBarbero
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* =========================
       PROMEDIO DE CALIFICACIÓN
    ========================== */

    public function obtenerPromedioCalificacion($idBarbero)
    {
        $sql = "SELECT
                    ROUND(
                        AVG(calificacion),
                        1
                    ) AS promedio

                FROM resenas

                WHERE id_barbero = ?";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $idBarbero
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['promedio'] ?? 0;
    }


    /* =========================
       TOTAL DE RESERVACIONES
    ========================== */

    public function obtenerTotalReservas($idBarbero)
    {
        $sql = "SELECT
                    COUNT(*) AS total

                FROM reservacion

                WHERE id_barbero = ?";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $idBarbero
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['total'] ?? 0;
    }


    /* =========================
       TOTAL DE RESEÑAS
    ========================== */

    public function obtenerTotalResenas($idBarbero)
    {
        $sql = "SELECT
                    COUNT(*) AS total

                FROM resenas

                WHERE id_barbero = ?";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $idBarbero
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['total'] ?? 0;
    }


    /* =========================
       ACTUALIZAR ESTADO
    ========================== */

    public function actualizarEstadoReservacion(
        $idReservacion,
        $estado
    ) {
        $sql = "UPDATE reservacion

                SET estado = ?

                WHERE id_reservacion = ?";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $estado,
            $idReservacion
        ]);
    }
    /* =========================
       ACTUALIZAR DATOS DE PERFIL
    ========================== */

    public function actualizarDatosPerfil($idUsuario, $nombre, $apellido, $telefono, $correo, $rutaFoto = null)
    {
        try {
            if ($rutaFoto !== null) {
                $sql = "UPDATE usuarios 
                        SET nombre = :nombre, 
                            apellido = :apellido, 
                            telefono = :telefono, 
                            correo = :correo, 
                            foto = :foto 
                        WHERE id_usuario = :id";

                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
                $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
                $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
                $stmt->bindParam(':foto', $rutaFoto, PDO::PARAM_STR);
                $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
            } else {
                $sql = "UPDATE usuarios 
                        SET nombre = :nombre, 
                            apellido = :apellido, 
                            telefono = :telefono, 
                            correo = :correo 
                        WHERE id_usuario = :id";

                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
                $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
                $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
                $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar perfil del barbero: " . $e->getMessage());
            return false;
        }
    }
}
