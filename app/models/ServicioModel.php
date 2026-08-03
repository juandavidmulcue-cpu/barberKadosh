<?php

require_once 'app/config/conexion.php';

class ServicioModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    // Obtener todos los servicios
    public function obtenerServicios()
    {
        $sql = "SELECT
                    id_servicio,
                    nombre,
                    descripcion,
                    precio,
                    duracion
                FROM servicios
                ORDER BY nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear servicio
    public function crearServicio($nombre, $descripcion, $precio, $duracion)
    {
        // Buscar el último ID
        $sqlId = "SELECT id_servicio
                  FROM servicios
                  ORDER BY id_servicio DESC
                  LIMIT 1";

        $stmtId = $this->db->prepare($sqlId);
        $stmtId->execute();

        $ultimo = $stmtId->fetch(PDO::FETCH_ASSOC);

        if ($ultimo && !empty($ultimo['id_servicio'])) {

            $numero = intval(substr($ultimo['id_servicio'], 3)) + 1;
        } else {

            $numero = 1;
        }

        // Crear ID SER001, SER002, etc.
        $id_servicio = 'SER' . str_pad(
            $numero,
            3,
            '0',
            STR_PAD_LEFT
        );

        $sql = "INSERT INTO servicios
                (
                    id_servicio,
                    nombre,
                    descripcion,
                    precio,
                    duracion
                )
                VALUES
                (
                    :id_servicio,
                    :nombre,
                    :descripcion,
                    :precio,
                    :duracion
                )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id_servicio' => $id_servicio,
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':precio' => $precio,
            ':duracion' => $duracion
        ]);
    }

    // Eliminar servicio
    public function eliminarServicio($id_servicio)
    {
        $sql = "DELETE FROM servicios
                WHERE id_servicio = :id_servicio";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id_servicio' => $id_servicio
        ]);
    }

    // Obtener un servicio por ID
    public function obtenerServicioPorId($id_servicio)
    {
        $sql = "SELECT
                id_servicio,
                nombre,
                descripcion,
                precio,
                duracion
            FROM servicios
            WHERE id_servicio = :id_servicio
            LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id_servicio' => $id_servicio
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Actualizar servicio
    public function actualizarServicio(
        $id_servicio,
        $nombre,
        $descripcion,
        $precio,
        $duracion
    ) {
        $sql = "UPDATE servicios
            SET nombre = :nombre,
                descripcion = :descripcion,
                precio = :precio,
                duracion = :duracion
            WHERE id_servicio = :id_servicio";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id_servicio' => $id_servicio,
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':precio' => $precio,
            ':duracion' => $duracion
        ]);
    }
}
