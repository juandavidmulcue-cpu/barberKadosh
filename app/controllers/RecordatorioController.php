<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// CORREGIDO: Subimos niveles para encontrar la carpeta vendor y app en la raíz
require '../../vendor/autoload.php';
require_once '../config/conexion.php';
require_once '../models/Recordatorio.php';

class RecordatorioController {
    private $modelo;

    public function __construct($conexion) {
        $this->modelo = new Recordatorio($conexion);
    }

    public function enviarRecordatoriosPendientes() {
        $pendientes = $this->modelo->obtenerCorreosPendientes();

        if (empty($pendientes)) {
            return "No hay citas programadas para mañana que requieran recordatorio actualmente.";
        }

        $enviados = 0;

        foreach ($pendientes as $correo) {
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';                                     
                $mail->SMTPAuth   = true;                                                 
                $mail->Username   = 'kadoshbar1230@gmail.com';       
                $mail->Password   = 'rbcn efqd qklw jsev';        
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       
                $mail->Port       = 587;                                                                  
                $mail->CharSet    = 'UTF-8';                                              

                $mail->setFrom('kadoshbar1230@gmail.com', 'Barbería Kadosh');
                $mail->addAddress($correo['email_cliente'], $correo['nombre_cliente']); 

                $mail->isHTML(true);                                                      
                $mail->Subject = 'Recordatorio de tu cita en Barbería Kadosh 💈';
                
                $mail->Body    = "Hola <strong>" . htmlspecialchars($correo['nombre_cliente']) . "</strong>,<br><br>" .
                                 "Te recordamos que tienes una cita pendiente con nosotros, programada para el día de mañana.<br>" .
                                 "¡Te esperamos!";                                      

                $mail->send();
                
                $this->modelo->registrarEnvioHistorial($correo['id_cita'], "Recordatorio enviado para la cita de mañana", 'enviado');
                $enviados++;

            } catch (Exception $e) {
                $this->modelo->registrarEnvioHistorial($correo['id_cita'], "Error al enviar correo: " . $e->getMessage(), 'fallido');
            }
        }

        return "Correos enviados con éxito: $enviados";
    }
}

// --- INSTANCIACIÓN AUTOMÁTICA CON INTERFAZ ESTILIZADA ---
try {
    $conexion = Database::conectar();
    $controlador = new RecordatorioController($conexion);
    $resultado = $controlador->enviarRecordatoriosPendientes(); 
    $tipo_alerta = (strpos($resultado, 'éxito') !== false) ? 'success' : 'info';
} catch (Exception $e) {
    $resultado = "Error en la ejecución del controlador: " . $e->getMessage();
    $tipo_alerta = 'danger';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disparador de Envíos | Barbería Kadosh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .kadosh-card {
            background-color: #1a1a1a;
            border: 1px solid #333;
            border-top: 4px solid #ffc107; /* Dorado Kadosh */
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
            padding: 2.5rem;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .icon-wrapper {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }
        .btn-kadosh {
            background-color: #ffc107;
            color: #111;
            font-weight: 600;
            border: none;
            padding: 0.6rem 1.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-kadosh:hover {
            background-color: #e0a800;
            color: #000;
            transform: translateY(-2px);
        }
        .text-muted-kadosh {
            color: #a0a0a0;
        }
    </style>
</head>
<body>

<div class="kadosh-card">
    <div class="icon-wrapper">
        <?php echo ($tipo_alerta === 'success') ? '⚡' : 'ℹ️'; ?>
    </div>
    <h3 class="mb-3 text-white">Gestor de Notificaciones</h3>
    <p class="fs-5 <?php echo ($tipo_alerta === 'success') ? 'text-warning' : 'text-muted-kadosh'; ?>">
        <?php echo $resultado; ?>
    </p>
    <hr class="my-4" style="border-color: #444;">
    <a href="../../index.php?controller=admin&action=panel" class="btn btn-kadosh rounded-pill w-100 d-block">
    Volver al Panel Administrativo
</a>
</div>

</body>
</html>