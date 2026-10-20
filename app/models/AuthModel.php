<?php

class AuthModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /* =========================
       LOGIN (por rol)
    ========================== */
    public function getUserByEmailAndRole($correo, $rol)
    {
        $sql = "
        SELECT u.id_usuario, u.nombre, u.apellido, u.telefono, u.correo, u.password, u.estado, u.foto
        FROM usuarios u
        INNER JOIN roles r ON u.id_rol = r.id_rol
        WHERE u.correo = :correo
        AND r.nombre_rol = :rol
        LIMIT 1
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':correo' => $correo,
            ':rol' => $rol
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       REGISTRO CLIENTE
    ========================== */
    public function registerClient($data)
    {
        $sql = "
            INSERT INTO usuarios 
            (id_usuario, id_rol, nombre, apellido, telefono, correo, password)
            VALUES 
            (:id, :rol, :nombre, :apellido, :telefono, :correo, :password)
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /* =========================
       VERIFICAR SI CORREO EXISTE
    ========================== */
    public function emailExists($correo)
    {
        $stmt = $this->db->prepare("
            SELECT id_usuario 
            FROM usuarios 
            WHERE correo = :correo
            LIMIT 1
        ");

        $stmt->execute([':correo' => $correo]);

        return $stmt->fetchColumn();
    }

    /* =========================
       VERIFICAR SI ID EXISTE
    ========================== */
    public function idExists($id)
    {
        $stmt = $this->db->prepare("
            SELECT 1 
            FROM usuarios 
            WHERE id_usuario = :id
            LIMIT 1
        ");

        $stmt->execute([':id' => $id]);

        return $stmt->fetchColumn();
    }

    /* =========================
       OBTENER USUARIO POR ID
    ========================== */
    public function getUserById($id)
    {
        $stmt = $this->db->prepare("
            SELECT id_usuario, nombre, apellido, telefono, correo, id_rol
            FROM usuarios
            WHERE id_usuario = :id
            LIMIT 1
        ");

        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       ACTUALIZAR PERFIL
    ========================== */
    public function updateUser($data)
    {
        $sql = "
            UPDATE usuarios 
            SET nombre = :nombre,
                apellido = :apellido,
                telefono = :telefono,
                correo = :correo
            WHERE id_usuario = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':telefono' => $data['telefono'],
            ':correo' => $data['correo'],
            ':id' => $data['id']
        ]);
    }

    /* =========================
       CAMBIAR PASSWORD
    ========================== */
    public function updatePassword($id, $passwordHash)
    {
        $stmt = $this->db->prepare("
            UPDATE usuarios 
            SET password = :password
            WHERE id_usuario = :id
        ");

        return $stmt->execute([
            ':password' => $passwordHash,
            ':id' => $id
        ]);
    }

    /* =========================
       VALIDAR LOGIN (extra seguridad opcional)
    ========================== */
    public function verifyPassword($correo, $rol)
    {
        $stmt = $this->db->prepare("
            SELECT password 
            FROM usuarios u
            INNER JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.correo = :correo
            AND r.nombre_rol = :rol
            LIMIT 1
        ");

        $stmt->execute([
            ':correo' => $correo,
            ':rol' => $rol
        ]);

        return $stmt->fetchColumn();
    }
}
