<?php

require_once 'app/models/ReservacionModel.php';

class ReservacionController
{

    private $model;

    public function __construct()
    {
        $this->model=new Reservacion();
    }

    public function horarios()
    {
        $barberos=$this->model->obtenerBarberos();

        $horarios=$this->model->obtenerHorarios();

        require 'app/views/reservacion/horarios.php';
    }

}