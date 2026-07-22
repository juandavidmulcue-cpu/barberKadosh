<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/MensajeroInterface.php';
require_once __DIR__ . '/../../config/mail.php';

/**
 * CorreoMensajero
 * -------------------------------------------------
 * Implementación de MensajeroInterface basada en
 * PHPMailer + SMTP. Toda la configuración proviene
 * de app/config/mail.php (fuera de git).
 *
 * Antes esta lógica estaba duplicada y con credenciales
 * hardcodeadas en RecordatorioController y logicamail.
 */
class CorreoMensajero implements MensajeroInterface
{
    public function enviar($destinatario, $nombre, $asunto, $cuerpoHtml)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
            $mail->addAddress($destinatario, $nombre);

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $cuerpoHtml;

            $mail->send();

            return true;

        } catch (Exception $e) {
            // El servicio que llama decide qué hacer con el fallo
            // (por ejemplo, registrarlo en el historial).
            return false;
        }
    }

    public function nombreCanal()
    {
        return 'correo';
    }
}
