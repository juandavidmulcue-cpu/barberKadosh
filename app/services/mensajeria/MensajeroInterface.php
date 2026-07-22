<?php

/**
 * MensajeroInterface
 * -------------------------------------------------
 * Contrato para cualquier canal de mensajería
 * (correo, SMS, WhatsApp, etc.).
 *
 * Principios aplicados:
 * - OCP: para agregar un canal nuevo se crea otra clase
 *   que implemente esta interfaz, sin tocar los servicios.
 * - DIP: los servicios dependen de esta abstracción,
 *   no de PHPMailer ni de ningún proveedor concreto.
 */
interface MensajeroInterface
{
    /**
     * Envía un mensaje al destinatario.
     *
     * @param string $destinatario Dirección/número de destino.
     * @param string $nombre       Nombre del destinatario.
     * @param string $asunto       Asunto del mensaje.
     * @param string $cuerpoHtml   Contenido (HTML permitido).
     * @return bool  true si el envío fue exitoso.
     */
    public function enviar($destinatario, $nombre, $asunto, $cuerpoHtml);

    /**
     * Nombre del canal (para registrar en el historial).
     */
    public function nombreCanal();
}
