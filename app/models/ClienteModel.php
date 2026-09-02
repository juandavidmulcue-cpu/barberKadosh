<?php
require_once 'app/config/conexion.php';

class ClienteModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    public function actualizarDatosPerfil($idUsuario, $nombre, $apellido, $telefono, $correo, $foto = null)
    {
        if ($foto !== null) {
            $sql = "UPDATE usuarios 
                SET nombre = :nombre, 
                    apellido = :apellido, 
                    telefono = :telefono, 
                    correo = :correo, 
                    foto = :foto 
                WHERE id_usuario = :id_usuario";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':foto', $foto, PDO::PARAM_STR);
        } else {
            $sql = "UPDATE usuarios 
                SET nombre = :nombre, 
                    apellido = :apellido, 
                    telefono = :telefono, 
                    correo = :correo 
                WHERE id_usuario = :id_usuario";

            $stmt = $this->db->prepare($sql);
        }

        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':apellido', $apellido, PDO::PARAM_STR);
        $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindValue(':correo', $correo, PDO::PARAM_STR);
        $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function obtenerBarberosDisponiblesCincoDias()
    {
        $fechaInicio = date('Y-m-d');
        $fechaFin    = date('Y-m-d', strtotime('+4 days'));

        $sql = "SELECT 
                u.id_usuario, 
                u.nombre, 
                u.apellido, 
                u.telefono, 
                u.foto
            FROM usuarios u
            INNER JOIN horarios h ON u.id_usuario = h.id_barbero
            WHERE u.id_rol = 2 
              AND u.estado = 'activo'
              AND h.fecha BETWEEN :fechaInicio AND :fechaFin
            GROUP BY u.id_usuario, u.nombre, u.apellido, u.telefono, u.foto";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':fechaInicio' => $fechaInicio,
            ':fechaFin'    => $fechaFin
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idUsuario)
    {
        try {
            $sql = "SELECT id_usuario, nombre, apellido, telefono, correo, foto, id_rol 
                FROM usuarios 
                WHERE id_usuario = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return false;
        }
    }
}
