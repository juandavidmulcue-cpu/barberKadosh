<?php

require_once 'app/config/conexion.php';

class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /* ===============================
       OBTENER USUARIO POR EMAIL
       =============================== */
    public function obtenerPorEmail($email)
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, r.nombre_rol AS roles
             FROM usuarios u
             INNER JOIN roles r ON u.rol_id = r.id_rol
             WHERE u.correo = :email"
        );

        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ===============================
       LISTAR TODOS LOS USUARIOS
       =============================== */
    public function listar()
    {
        $stmt = $this->db->query(
            "SELECT u.id, u.nombre, u.email, r.nombre AS rol
             FROM usuarios u
             INNER JOIN roles r ON u.rol_id = r.id"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===============================
       CREAR USUARIO (CLIENTE / BARBERO)
       =============================== */
    public function crear($nombre, $email, $password, $rol_id)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (nombre, email, password, rol_id)
             VALUES (:n, :e, :p, :r)"
        );

        return $stmt->execute([
            ':n' => $nombre,
            ':e' => $email,
            ':p' => $password,
            ':r' => $rol_id
        ]);
    }
}