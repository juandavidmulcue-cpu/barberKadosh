<?php

// Seguridad básica
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php?controller=auth&action=loginCliente");
    exit;
}

$nombre = $_SESSION['nombre'] ?? 'Cliente';
$apellido = $_SESSION['apellido'] ?? '';
$rol    = $_SESSION['nombre_rol'] ?? 'cliente';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Perfil Cliente - Kadosh Barber</title>
    <link rel="stylesheet" href="app/public/css/perfilCliente.css">
    <link rel="stylesheet" href="app/public/css/index.css">
</head>

<body>

    <div class="barra">
        HOLA, <?php echo htmlspecialchars($nombre . ' ' . $apellido); ?>! 💈
    </div>

    <div class="perfil">

        <!-- Usuario -->
        <div class="card info-usuario">
            <img src="app/public/assets/img/logo1.jpeg" class="avatar">

            <h2><?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></h2>
            <p class="rol"><?php echo strtoupper(htmlspecialchars($rol)); ?></p>

            <div class="acciones">
                <a href="index.php?controller=gestionCita&action=agendarCita" class="btn btn-primary"><button>AGENDAR</button></a>
                <a href="index.php?controller=auth&action=resetPassword" class="btn"><button class="btn-outline">Cambiar contraseña</button></a>
            </div>

            <a href="index.php?controller=auth&action=logout">Cerrar sesión</a>
        </div>


        <!-- CARRUSEL -->
        <section class="carousel-section">
            <h2 class="carousel-title">Nuestro Estilo</h2>

            <div class="carousel">
                <div class="carousel-track">
                    <div class="carousel-item">
                        <img src="app/public/assets/img/foto1.png" alt="Corte clásico">
                    </div>
                    <div class="carousel-item">
                        <img src="app/public/assets/img/foto2.png" alt="Fade moderno">
                    </div>
                    <div class="carousel-item">
                        <img src="app/public/assets/img/foto3.png" alt="Barba profesional">
                    </div>
                    <div class="carousel-item">
                        <img src="app/public/assets/img/foto4.png" alt="Estilo premium">
                    </div>
                </div>
            </div>
        </section>

        <!-- Dirección -->
        <div class="card direccion">
            <h3>DIRECCIÓN</h3>
            <p>Cra 99 #999-99<br>2do piso – Soacha</p>
            <a href="#">Ver en el mapa</a>
        </div>

    </div>

    <div class="resumen">

        <div class="resumen-card">
            <h4>📅 Próxima cita</h4>
            <span>15/08/2026</span>
        </div>

        <div class="resumen-card">
            <h4>📖 Total de citas</h4>
            <span>8</span>
        </div>

        <div class="resumen-card">
            <h4>📌 Estado</h4>
            <span>Confirmada</span>
        </div>

    </div>
    <div class="panel" id="panel-reservas">

        <h2 class="section-title">Mis Reservas</h2>

        <br>

        <div class="section-card">

            <table class="data-table" id="tablaCitas">

                <thead>
                    <tr>
                        <th>Barbero</th>
                        <th>Servicio</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($citas)): ?>

                        <?php foreach ($citas as $c): ?>

                            <tr>

                                <td><?= htmlspecialchars($c['barbero']) ?></td>

                                <td><?= htmlspecialchars($c['servicio']) ?></td>

                                <td><?= htmlspecialchars($c['fecha_cita']) ?></td>

                                <td><?= htmlspecialchars($c['hora_cita']) ?></td>

                                <td>
                                    <?php
                                    if ($c['estado'] == 'Pendiente') {
                                        echo "<span style='color:orange;font-weight:bold;'>🟡 Pendiente</span>";
                                    } elseif ($c['estado'] == 'Confirmada') {
                                        echo "<span style='color:green;font-weight:bold;'>🟢 Confirmada</span>";
                                    } elseif ($c['estado'] == 'Cancelada') {
                                        echo "<span style='color:red;font-weight:bold;'>🔴 Cancelada</span>";
                                    } else {
                                        echo htmlspecialchars($c['estado']);
                                    }
                                    ?>
                                </td>

                                <td>

                                    <?php if ($c['estado'] != 'Cancelada'): ?>

                                        <a class="btn-danger"
                                            href="index.php?controller=gestionCita&action=cancelar&id=<?= $c['id_reservacion'] ?>"
                                            onclick="return confirm('¿Desea cancelar esta reserva?')">
                                            Cancelar
                                        </a>

                                    <?php else: ?>

                                        <span style="color:gray;">Sin acciones</span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" style="text-align:center;">
                                No tienes reservas registradas.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>