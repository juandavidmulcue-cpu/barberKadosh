<?php

require_once 'app/config/conexion.php';

class ClienteController
{
    private $db;

    public function __construct()
    {

        // 🔐 SOLO CLIENTES
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
            header("Location: index.html");
            exit;
        }

        $this->db = Database::conectar();
    }

    /* ===============================
       PERFIL DEL CLIENTE
       =============================== */
    public function perfil()
    {
        require 'app/views/cliente/perfil_cliente.php';
    }

    /* ===============================
       IR A AGENDAR CITA
       =============================== */
    public function agendar()
    {
        header("Location: index.php?controller=cita&action=agendar");
        exit;
    }
}