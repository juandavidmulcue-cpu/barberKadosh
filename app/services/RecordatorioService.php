<?php

require_once __DIR__ . '/mensajeria/MensajeroInterface.php';

/**
 * RecordatorioService
 * -------------------------------------------------
 * Lógica de negocio de los recordatorios automáticos.
 *
 * Recibe uno o varios MensajeroInterface (correo hoy;
 * SMS/WhatsApp mañana) y el modelo de datos. No conoce
 * PHPMailer ni SQL: solo orquesta (SRP + DIP).
 */
class RecordatorioService
{
    private $recordatorioModel;

    /** @var MensajeroInterface[] */
    private $mensajeros;

    public function __construct($recordatorioModel, array $mensajeros)
    {
        $this->recordatorioModel = $recordatorioModel;
        $this->mensajeros = $mensajeros;
    }

    /**
     * Envía recordatorios a las citas activas de mañana
     * que aún no lo hayan recibido.
     *
     * @return array Resumen: ['pendientes' => n, 'enviados' => n, 'fallidos' => n]
     */
    public function enviarRecordatoriosPendientes()
    {
        $pendientes = $this->recordatorioModel->obtenerCorreosPendientes();

        $resumen = [
            'pendientes' => count($pendientes),
            'enviados'   => 0,
            'fallidos'   => 0
        ];

        foreach ($pendientes as $cita) {

            $asunto = 'Recordatorio de tu cita en Barbería Kadosh 💈';

            $cuerpo = "Hola <strong>" . htmlspecialchars($cita['nombre_cliente']) . "</strong>,<br><br>" .
                      "Te recordamos que tienes una cita con nosotros el día de mañana " .
                      "<strong>" . htmlspecialchars($cita['fecha_cita']) . "</strong> a las " .
                      "<strong>" . htmlspecialchars(substr($cita['hora_cita'], 0, 5)) . "</strong>.<br>" .
                      "¡Te esperamos!";

            foreach ($this->mensajeros as $mensajero) {

                $ok = $mensajero->enviar(
                    $cita['email_cliente'],
                    $cita['nombre_cliente'],
                    $asunto,
                    $cuerpo
                );

                if ($ok) {
                    $this->recordatorioModel->registrarEnvioHistorial(
                        $cita['id_cita'],
                        "Recordatorio enviado por " . $mensajero->nombreCanal() . " para la cita de mañana",
                        'enviado'
                    );
                    $resumen['enviados']++;
                } else {
                    $this->recordatorioModel->registrarEnvioHistorial(
                        $cita['id_cita'],
                        "Error al enviar por " . $mensajero->nombreCanal(),
                        'fallido'
                    );
                    $resumen['fallidos']++;
                }
            }
        }

        return $resumen;
    }
}
