<?php

require_once 'app/controllers/Controller.php';
require_once 'app/models/ServicioModel.php';

class ServiciosPublicoController extends Controller
{
    private $servicioModel;

    public function __construct()
    {
        parent::__construct();
        // Sin restricción de roles (acceso público)
        $this->servicioModel = new ServicioModel();
    }

    // Acción principal para listar los servicios
    public function index()
    {
        // 1. Obtener la lista de servicios desde la BD
        $servicios = $this->servicioModel->obtenerServicios();

        // 2. Cargar la vista pública
        require 'app/views/cliente/servicios.php';
    }
}