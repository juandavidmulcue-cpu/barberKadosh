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
                <a href="../view/servicios.php" class="btn"><button>AGENDAR</button></a>
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

    <div class="panel" id="panel-reservas">

            <h2 class="section-title">Reservas</h2>

            <div class="action-row">
                <a href="index.php?controller=gestionCita&action=obtenerCita" class="btn btn-primary">
                    Gestionar Reservas
                </a>
            </div>

            <br>

            <div class="section-card">

                <table class="data-table" id="tablaProductos">
                    <thead>
                        <tr>
                            <th>Nombre del Barbero</th>
                            <th>Servicio</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($productos)): ?>
                            <?php foreach ($productos as $p): ?>
                                <tr>
                                    <td><?php echo $p['nombre']; ?></td>
                                    <td><?php echo $p['servicio']; ?></td>
                                    <td><?php echo $p['fecha']; ?></td>
                                    <td><?php echo $p['hora']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No hay reservas registradas</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>

        </div>
    
    <!-- ===== JS CARRUSEL ===== -->
    <script>
        const track = document.querySelector(".carousel-track");
        const items = document.querySelectorAll(".carousel-item");
        let index = 0;

        function updateCarousel() {
            track.style.transform = `translateX(-${index * 100}%)`;
        }

        setInterval(() => {
            index = (index + 1) % items.length;
            updateCarousel();
        }, 5000);
    </script>

</body>
</html>