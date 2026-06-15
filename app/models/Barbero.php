<?php

require_once 'app/models/Database.php';

class Barbero
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /* =========================
       LISTAR BARBEROS
    ========================== */
    public function getAll()
    {
        $sql = "SELECT id_usuario, nombre, apellido, telefono, correo 
                FROM usuarios 
                WHERE id_rol = 2"; // 2 = barbero

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       BUSCAR BARBERO POR ID
    ========================== */
    public function getById($id)
    {
        $sql = "SELECT id_usuario, nombre, apellido, telefono, correo 
                FROM usuarios 
                WHERE id_usuario = :id AND id_rol = 2";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       ELIMINAR BARBERO
    ========================== */
    public function delete($id)
    {
        $sql = "DELETE FROM usuarios 
                WHERE id_usuario = :id_usuario AND id_rol = 2";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id_usuario' => $id
        ]);
    }

    /* =========================
       ACTUALIZAR BARBERO
    ========================== */
    public function update($id, $nombre, $apellido, $telefono, $correo)
    {
        $sql = "UPDATE usuarios 
                SET nombre = :nombre, apellido = :apellido, telefono = :telefono, correo = :correo
                WHERE id_usuario = :id_usuario AND id_rol = 2";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id_usuario' => $id,   
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':telefono' => $telefono,
            ':correo' => $correo
        ]);
    }
}