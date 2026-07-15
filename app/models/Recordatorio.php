<?php
class Recordatorio {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Registra o actualiza el historial en la tabla de recordatorios
    // Asegúrate de que reciba los tres parámetros limpios
    public function registrarEnvioHistorial($id_cita, $mensaje, $estado) {
        $sql = "INSERT INTO recordatorios_correo (id_cita, fecha_envio, mensaje, estado) 
                VALUES (?, NOW(), ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_cita, $mensaje, $estado]);
    }

    // Trae las citas de MAÑANA que aún no tengan un recordatorio registrado como 'enviado'
    public function obtenerCorreosPendientes() {
        // CURDATE() + INTERVAL 1 DAY selecciona de forma perfecta el día de mañana, sin importar la hora actual.
        $sql = "SELECT c.id AS id_cita, c.email_cliente, c.nombre_cliente, c.fecha_cita, c.hora_cita 
                FROM citas c
                LEFT JOIN recordatorios_correo r ON c.id = r.id_cita AND r.estado = 'enviado'
                WHERE c.fecha_cita = CURDATE() + INTERVAL 1 DAY
                AND r.id IS NULL"; 
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método por compatibilidad con tu controlador anterior
    public function actualizarEstado($id_rec_o_cita, $estado) {
        // En el nuevo flujo dinámico, guardamos directo el estado final al enviar
        return true;
    }
}
?>