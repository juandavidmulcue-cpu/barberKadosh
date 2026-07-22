<?php

/**
 * DisponibilidadService
 * -------------------------------------------------
 * Única fuente de verdad para las reglas de negocio
 * de la agenda (SRP + DRY):
 *  - Genera los horarios (slots) de un barbero en una fecha.
 *  - Marca cada slot como disponible / ocupado / pasado.
 *  - Valida si una reserva es posible (sin conflictos,
 *    sin días no laborales, sin fechas pasadas).
 *  - Construye el calendario mensual de disponibilidad.
 *
 * Los controladores consultan este servicio; nunca
 * repiten estas validaciones por su cuenta.
 */
class DisponibilidadService
{
    // Jornada laboral de la barbería
    const HORA_APERTURA = '09:00';
    const HORA_CIERRE   = '19:00';
    const MINUTOS_SLOT  = 60;

    private $citaModel;
    private $disponibilidadModel;

    public function __construct($citaModel, $disponibilidadModel)
    {
        $this->citaModel = $citaModel;
        $this->disponibilidadModel = $disponibilidadModel;
    }

    /* =====================================================
       SLOTS DE UN DÍA
       ===================================================== */

    /**
     * Devuelve los horarios del barbero para una fecha.
     *
     * @return array Lista de ['hora' => 'HH:MM', 'estado' => 'disponible|ocupado|pasado']
     *               o [] si el día no es laboral.
     */
    public function obtenerSlots($idBarbero, $fecha)
    {
        if ($this->disponibilidadModel->esDiaNoLaboral($idBarbero, $fecha)) {
            return [];
        }

        $ocupadas = $this->citaModel->horasOcupadas($idBarbero, $fecha);

        $slots = [];
        $ahora = new DateTime();

        foreach ($this->generarHoras() as $hora) {

            $inicioSlot = new DateTime("$fecha $hora");

            if (in_array($hora . ':00', $ocupadas) || in_array($hora, $ocupadas)) {
                $estado = 'ocupado';
            } elseif ($inicioSlot <= $ahora) {
                $estado = 'pasado';
            } else {
                $estado = 'disponible';
            }

            $slots[] = ['hora' => $hora, 'estado' => $estado];
        }

        return $slots;
    }

    /**
     * Genera las horas de la jornada según la configuración.
     */
    private function generarHoras()
    {
        $horas = [];

        $actual = new DateTime(self::HORA_APERTURA);
        $cierre = new DateTime(self::HORA_CIERRE);

        while ($actual < $cierre) {
            $horas[] = $actual->format('H:i');
            $actual->modify('+' . self::MINUTOS_SLOT . ' minutes');
        }

        return $horas;
    }

    /* =====================================================
       VALIDACIÓN DE RESERVA
       ===================================================== */

    /**
     * Valida si es posible reservar. Devuelve
     * ['ok' => bool, 'motivo' => string|null].
     */
    public function validarReserva($idBarbero, $fecha, $hora)
    {
        // Formatos
        $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
            return ['ok' => false, 'motivo' => 'La fecha no es válida.'];
        }

        // Hora debe ser uno de los slots de la jornada
        $hora = substr($hora, 0, 5);
        if (!in_array($hora, $this->generarHoras())) {
            return ['ok' => false, 'motivo' => 'La hora seleccionada está fuera del horario de atención.'];
        }

        // No se puede reservar en el pasado
        $inicio = new DateTime("$fecha $hora");
        if ($inicio <= new DateTime()) {
            return ['ok' => false, 'motivo' => 'No es posible reservar en una fecha u hora pasada.'];
        }

        // Día no laboral (del barbero o de toda la barbería)
        if ($this->disponibilidadModel->esDiaNoLaboral($idBarbero, $fecha)) {
            return ['ok' => false, 'motivo' => 'Ese día no hay atención.'];
        }

        // Conflicto: el barbero ya tiene una cita activa a esa hora
        if ($this->citaModel->existeConflicto($idBarbero, $fecha, $hora)) {
            return ['ok' => false, 'motivo' => 'El barbero ya tiene una cita en ese horario. Elige otro.'];
        }

        return ['ok' => true, 'motivo' => null];
    }

    /* =====================================================
       CALENDARIO MENSUAL
       ===================================================== */

    /**
     * Construye el calendario del mes para un barbero.
     * Cada día trae: numero, fecha, estado
     * (pasado | no_laboral | completo | disponible) y
     * cuántos horarios libres quedan.
     */
    public function calendarioMes($idBarbero, $anio, $mes)
    {
        $totalSlots = count($this->generarHoras());

        $primerDia = new DateTime(sprintf('%04d-%02d-01', $anio, $mes));
        $diasEnMes = (int) $primerDia->format('t');

        $hoy = new DateTime('today');

        $noLaborales = $this->disponibilidadModel->diasNoLaboralesDelMes($idBarbero, $anio, $mes);
        $ocupadosPorDia = $this->citaModel->conteoCitasActivasPorDia($idBarbero, $anio, $mes);

        $dias = [];

        for ($d = 1; $d <= $diasEnMes; $d++) {

            $fecha = sprintf('%04d-%02d-%02d', $anio, $mes, $d);
            $fechaObj = new DateTime($fecha);

            $ocupados = $ocupadosPorDia[$fecha] ?? 0;
            $libres = max(0, $totalSlots - $ocupados);

            if ($fechaObj < $hoy) {
                $estado = 'pasado';
            } elseif (in_array($fecha, $noLaborales)) {
                $estado = 'no_laboral';
                $libres = 0;
            } elseif ($libres === 0) {
                $estado = 'completo';
            } else {
                $estado = 'disponible';
            }

            $dias[] = [
                'numero' => $d,
                'fecha' => $fecha,
                'estado' => $estado,
                'libres' => $libres,
                'ocupados' => $ocupados
            ];
        }

        return [
            'dias' => $dias,
            // Día de la semana del 1° (1 = lunes ... 7 = domingo) para alinear la grilla
            'inicia_en' => (int) $primerDia->format('N'),
            'total_slots' => $totalSlots
        ];
    }
}
