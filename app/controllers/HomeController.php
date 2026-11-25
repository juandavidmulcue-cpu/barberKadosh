<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/BarberoModel.php';

class HomeController
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::conectar();
    }

    public function index(): void
{
    $barberoModel = new BarberoModel($this->db);
    $topBarberos = $barberoModel->obtenerTopBarberos(2);


    require_once __DIR__ . '/../../home.php';
}
}