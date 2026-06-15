<?php

require_once 'app/config/conexion.php';

class CitaController
{
    private $db;

    public function __construct()
    {
        session_start();

        if (!isset($_SESSION['id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $this->db = Database::conectar();
    }

    /* =====================================================
       CLIENTE: VER SUS CITAS
       ===================================================== */
    public function misCitas()
    {
        if ($_SESSION['rol'] !== 'cliente') {
            header("Location: index.php");
            exit;
        }

        $stmt = $this->db->prepare(
            "SELECT c.id, c.fecha, c.hora, c.estado,
                    u.nombre AS barbero
             FROM citas c
             INNER JOIN usuarios u ON c.barbero_id = u.id
             WHERE c.cliente_id = :cliente"
        );

        $stmt->execute([
            ':cliente' => $_SESSION['id']
        ]);

        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require 'app/views/cliente/mis_citas.php';
    }

    /* =====================================================
       CLIENTE: AGENDAR CITA
       ===================================================== */
    public function agendarCita()
    {
        if ($_SESSION['rol'] !== 'cliente') {
            header("Location: index.php");
            exit;
        }

        // Obtener barberos
        $barberos = $this->db->query(
            "SELECT id, nombre FROM usuarios WHERE rol_id = 2"
        )->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $stmt = $this->db->prepare(
                "INSERT INTO citas (cliente_id, barbero_id, fecha, hora, estado)
                 VALUES (:cliente, :barbero, :fecha, :hora, 'activa')"
            );

            $stmt->execute([
                ':cliente' => $_SESSION['id'],
                ':barbero' => $_POST['barbero_id'],
                ':fecha'   => $_POST['fecha'],
                ':hora'    => $_POST['hora']
            ]);

            header("Location: index.php?controller=cita&action=misCitas");
            exit;
        }

        require 'app/views/cliente/agendar.php';
    }

    /* =====================================================
       CLIENTE: CANCELAR CITA
       ===================================================== */
    public function cancelar()
    {
        if ($_SESSION['rol'] !== 'cliente') {
            header("Location: index.php");
            exit;
        }

        $stmt = $this->db->prepare(
            "UPDATE citas
             SET estado = 'cancelada'
             WHERE id = :id AND cliente_id = :cliente"
        );

        $stmt->execute([
            ':id'      => $_GET['id'],
            ':cliente' => $_SESSION['id']
        ]);

        header("Location: index.php?controller=cita&action=misCitas");
        exit;
    }

    /* =====================================================
       BARBERO: VER SUS CITAS
       ===================================================== */
    public function citasBarbero()
    {
        if ($_SESSION['rol'] !== 'barbero') {
            header("Location: index.php");
            exit;
        }

        $stmt = $this->db->prepare(
            "SELECT c.id, c.fecha, c.hora, c.estado,
                    u.nombre AS cliente
             FROM citas c
             INNER JOIN usuarios u ON c.cliente_id = u.id
             WHERE c.barbero_id = :barbero"
        );

        $stmt->execute([
            ':barbero' => $_SESSION['id']
        ]);

        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require 'app/views/barbero/citas.php';
    }
}

