<?php

require_once 'app/config/conexion.php';

class ServicioModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /**
     * Obtiene todos los servicios activos.
     */
    public function obtenerServicios()
    {
        $sql = "SELECT
                    id_servicio,
                    nombre,
                    precio,
                    duracion
                FROM servicios
                ORDER BY nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un servicio por su ID.
     */
    public function obtenerPorId($id)
    {
        $sql = "SELECT *
                FROM servicios
                WHERE id_servicio = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}