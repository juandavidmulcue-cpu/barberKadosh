<?php

// Seguridad básica
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php?controller=auth&action=loginCliente");
    exit;
}

$nombre = $_SESSION['nombre'] ?? 'Cliente';
$rol    = $_SESSION['nombre_rol'] ?? 'cliente';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil Cliente - Kadosh Barber</title>
    <link rel="stylesheet" href="app/public/css/perfilCliente.css">
</head>
<body>

    <div class="barra">
        BIENVENID@ <?php echo htmlspecialchars($nombre); ?>! 💈
    </div>

    <div class="perfil">

        <!-- Usuario -->
        <div class="card info-usuario">
            <img src="../img/mono_perfil.jpg" class="avatar">

            <h2><?php echo htmlspecialchars($nombre); ?></h2>
            <p class="rol"><?php echo strtoupper(htmlspecialchars($rol)); ?></p>

            <div class="acciones">
                <a href="../view/servicios.php" class="btn"><button>Mirar servicios</button></a>
                <a href="../view/cambiar_contraseña.php" class="btn"><button class="btn-outline">Cambiar contraseña</button></a>
            </div>

            <a href="index.php?controller=auth&action=logout">Cerrar sesión</a>
        </div>

        <!-- Imagen -->
        <div class="card galeria">
            <div class="imagen-principal">
                <p>Tu estilo 💈</p>
            </div>
            <div class="puntos">
                <span></span><span></span><span></span>
            </div>
        </div>

        <!-- Dirección -->
        <div class="card direccion">
            <h3>DIRECCIÓN</h3>
            <p>Cra 99 #999-99<br>2do piso – Soacha</p>
            <a href="#">Ver en el mapa</a>
        </div>

    </div>

    <div class="card reservas">
        <h3>Mis Reservas</h3>

        <table>
            <thead>
                <tr>
                    <th>Barbero</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Servicio</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sin datos</td>
                    <td>--</td>
                    <td>--</td>
                    <td>--</td>
                </tr>
            </tbody>
        </table>

        <div class="acciones-tabla">
            <a href="../view/cancelar_cita.php" class="btn"><button>Cancelar cita</button></a>
            <a href="../view/reagendar.php" class="btn"><button>Reagendar</button></a>
        </div>
    </div>

</body>
</html>