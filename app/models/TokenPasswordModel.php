<?php
require_once 'app/config/conexion.php';

/**
 * TokenPasswordModel
 * -------------------------------------------------
 * Responsabilidad única: validar los tokens de
 * recuperación de contraseña.
 */
class TokenPasswordModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /**
     * Devuelve el usuario si el token es válido y no ha expirado.
     */
    public function validar($id, $token)
    {
        $stmt = $this->db->prepare(
            "SELECT id_usuario
             FROM usuarios
             WHERE id_usuario = :id
               AND token_password = :token
               AND expired_session > :tiempo
               AND request_password = '1'
             LIMIT 1"
        );

        $stmt->execute([
            ':id'     => $id,
            ':token'  => $token,
            ':tiempo' => time()
        ]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}