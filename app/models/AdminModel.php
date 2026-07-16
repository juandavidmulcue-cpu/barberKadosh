<?php

require_once 'app/config/conexion.php';

class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /* =========================
       BARBEROS
    ========================= */

    public function obtenerBarberos($buscar = '', $orden = 'az')
    {
        $sql = "SELECT id_usuario, nombre, apellido, telefono, correo
                FROM usuarios
                WHERE id_rol = 2";

        if (!empty($buscar)) {
            $sql .= " AND (nombre LIKE :buscar OR apellido LIKE :buscar)";
        }

        $sql .= ($orden === 'za')
            ? " ORDER BY nombre DESC"
            : " ORDER BY nombre ASC";

        $stmt = $this->db->prepare($sql);

        if (!empty($buscar)) {
            $stmt->bindValue(':buscar', "%$buscar%");
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function registrarBarbero($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO usuarios
            (id_usuario, id_rol, nombre, apellido, telefono, correo, password)
            VALUES (:id, 2, :nombre, :apellido, :telefono, :correo, :password)
        ");

        return $stmt->execute($data);
    }

    public function eliminarBarbero($id)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM usuarios WHERE id_usuario = :id"
        );

        return $stmt->execute([':id' => $id]);
    }

    public function obtenerBarberoPorId($id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios
             WHERE id_usuario = :id"
        );

        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarBarbero($data)
    {
        $stmt = $this->db->prepare(
            "UPDATE usuarios
             SET nombre=:nombre,
                 apellido=:apellido,
                 telefono=:telefono,
                 correo=:correo
             WHERE id_usuario=:id"
        );

        return $stmt->execute($data);
    }

    public function getUsuarioById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM usuarios
             WHERE id_usuario = :id
             LIMIT 1"
        );

        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       CLIENTES
    ========================= */

    public function obtenerClientes($buscar = '', $orden = 'az')
    {
        $sql = "SELECT id_usuario, nombre, apellido, telefono, correo
                FROM usuarios
                WHERE id_rol = 3";

        if (!empty($buscar)) {
            $sql .= " AND (nombre LIKE :buscar OR apellido LIKE :buscar)";
        }

        $sql .= ($orden === 'za')
            ? " ORDER BY nombre DESC"
            : " ORDER BY nombre ASC";

        $stmt = $this->db->prepare($sql);

        if (!empty($buscar)) {
            $stmt->bindValue(':buscar', "%$buscar%");
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function eliminarCliente($id)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM usuarios
             WHERE id_usuario = :id"
        );

        return $stmt->execute([':id' => $id]);
    }
}