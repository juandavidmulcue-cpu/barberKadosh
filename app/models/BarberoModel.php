<?php

require_once 'app/config/conexion.php';

class BarberoModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /**
     * Obtiene todos los barberos activos.
     */
    public function obtenerBarberosDisponibles()
    {
        $sql = "SELECT
                    id_usuario,
                    nombre,
                    apellido
                FROM usuarios
                WHERE id_rol = 2
                  AND estado = 'activo'
                ORDER BY nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un barbero por su ID.
     */
    public function obtenerPorId($id)
    {
        $sql = "SELECT
                    id_usuario,
                    nombre,
                    apellido,
                    telefono,
                    correo
                FROM usuarios
                WHERE id_usuario = :id
                  AND id_rol = 2";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}