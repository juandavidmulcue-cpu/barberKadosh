<?php

// Seguridad básica
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php?controller=auth&action=loginBarbero");
    exit;
}

$barbero = $barbero ?? [];
$totalReservas = $totalReservas ?? 0;
$totalResenas = $totalResenas ?? 0;
$promedio = $promedio ?? 0;
$historial = $historial ?? [];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Barbero</title>
    <link rel="stylesheet" href="app/public/css/perfilBarbero.css">
</head>

<body>
    <div class="contenedorPerfil">
        <section class="perfil">
            <div class="imagen">
                <img src="app/public/assets/img/fotobarbero.jpg" class="avatar" alt="Foto del barbero">
            </div>
            <div class="informacion">
                <h1>
                    <?php echo htmlspecialchars($barbero['nombre'] . ' ' . $barbero['apellido']); ?>
                </h1>


                <p>
                    <strong>Teléfono:</strong>
                    <?php echo htmlspecialchars($barbero['telefono']); ?>
                </p>
                <p>
                    <strong>Correo:</strong>
                    <?php echo htmlspecialchars($barbero['correo']); ?>
                </p>

                <a href="index.php?controller=auth&action=logout" class="btn btn-outline">Cerrar sesión</a>
            </div>


            <!-- =========================
             ESTADÍSTICAS
        ========================== -->

            <div class="estadisticas">

                <button
                    class="stat activo"
                    id="btnReservas"
                    onclick="mostrarSeccion('reservas')">

                    <h3>
                        <?php echo $totalReservas; ?>
                    </h3>

                    <span>Reservas</span>

                </button>

                <button
                    class="stat"
                    id="btnHistorial"
                    onclick="mostrarSeccion('historial')">

                    <h3>
                        <?php echo count($historial ?? []); ?>
                    </h3>

                    <span>Historial</span>

                </button>

                <button
                    class="stat"
                    id="btnResenas"
                    onclick="mostrarSeccion('resenas')">

                    <h3>
                        <?php echo $totalResenas; ?>
                    </h3>

                    <span>Reseñas</span>

                </button>


                <!-- CALIFICACIÓN -->

                <div class="stat">

                    <h3>

                        <?php
                        echo number_format(
                            (float)$promedio,
                            1
                        );
                        ?>

                        / 5

                    </h3>


                    <div class="estrellasPromedio">

                        <?php

                        $promedioRedondeado = round(
                            (float)$promedio
                        );

                        for ($i = 1; $i <= 5; $i++) {

                            if ($i <= $promedioRedondeado) {

                                echo '<span>⭐</span>';
                            } else {

                                echo '<span>☆</span>';
                            }
                        }

                        ?>

                    </div>


                    <span>Calificación</span>

                </div>

            </div>

        </section>



        <!-- =========================
         HORARIOS
    ========================== -->

        <section class="card">

            <h2>Horarios de Trabajo</h2>


            <div class="horarios">

                <?php if (!empty($horarios)) { ?>


                    <?php foreach ($horarios as $hora) { ?>

                        <div class="horario">

                            <span>🕒</span>

                            <p>

                                <strong>

                                    <?php
                                    echo date(
                                        'd/m/Y',
                                        strtotime($hora['fecha'])
                                    );
                                    ?>

                                </strong>

                                <br>

                                <?php

                                echo date(
                                    'h:i A',
                                    strtotime($hora['hora_inicio'])
                                );

                                ?>

                                -

                                <?php

                                echo date(
                                    'h:i A',
                                    strtotime($hora['hora_fin'])
                                );

                                ?>

                            </p>

                        </div>

                    <?php } ?>


                <?php } else { ?>

                    <p class="sinDatos">
                        No tienes horarios registrados.
                    </p>

                <?php } ?>

            </div>

        </section>



        <!-- =========================
         RESERVACIONES
    ========================== -->

        <section
            class="card"
            id="reservas">

            <h2>Reservaciones</h2>


            <?php if (!empty($reservaciones)) { ?>


                <div class="tablaResponsive">

                    <table>

                        <thead>

                            <tr>

                                <th>Cliente</th>

                                <th>Fecha</th>

                                <th>Hora</th>

                                <th>Estado</th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php foreach ($reservaciones as $r) { ?>

                                <tr>

                                    <!-- CLIENTE -->

                                    <td>

                                        <?php

                                        echo htmlspecialchars(
                                            $r['cliente']
                                        );

                                        ?>

                                    </td>


                                    <!-- FECHA -->

                                    <td>

                                        <?php

                                        echo date(
                                            'd/m/Y',
                                            strtotime($r['fecha_cita'])
                                        );

                                        ?>

                                    </td>


                                    <!-- HORA -->

                                    <td>

                                        <?php

                                        echo date(
                                            'h:i A',
                                            strtotime($r['hora_cita'])
                                        );

                                        ?>

                                    </td>


                                    <!-- ESTADO -->

                                    <td>

                                        <?php if ($r['estado'] === 'Pendiente') { ?>

                                            <span class="estado pendiente">
                                                🟡 Pendiente
                                            </span>

                                        <?php } elseif ($r['estado'] === 'Cancelada') { ?>

                                            <span class="estado cancelada">
                                                🔴 Cancelada
                                            </span>

                                        <?php } elseif ($r['estado'] === 'Completada') { ?>

                                            <span class="estado completada">
                                                🟢 Completada
                                            </span>

                                        <?php } else { ?>

                                            <span class="estado">
                                                <?php echo htmlspecialchars($r['estado']); ?>
                                            </span>

                                        <?php } ?>

                                    </td>

                                </tr>

                            <?php } ?>


                        </tbody>

                    </table>

                </div>


            <?php } else { ?>

                <div class="sinDatos">

                    <p>
                        No tienes reservaciones actualmente.
                    </p>

                </div>

            <?php } ?>

        </section>

        <!-- =========================
     HISTORIAL
========================== -->

        <section
            class="card"
            id="historial"
            style="display:none;">

            <h2>Historial de Reservaciones</h2>

            <?php if (!empty($historial)) { ?>

                <div class="tablaResponsive">

                    <table>

                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Servicio</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($historial as $h) { ?>

                                <tr>

                                    <!-- CLIENTE -->
                                    <td>
                                        <?php echo htmlspecialchars($h['cliente']); ?>
                                    </td>

                                    <!-- SERVICIO -->
                                    <td>
                                        <?php echo htmlspecialchars($h['servicio']); ?>
                                    </td>

                                    <!-- FECHA -->
                                    <td>
                                        <?php
                                        echo date(
                                            'd/m/Y',
                                            strtotime($h['fecha_cita'])
                                        );
                                        ?>
                                    </td>

                                    <!-- HORA -->
                                    <td>
                                        <?php
                                        echo date(
                                            'h:i A',
                                            strtotime($h['hora_cita'])
                                        );
                                        ?>
                                    </td>

                                    <!-- ESTADO -->
                                    <td>

                                        <?php if ($h['estado'] === 'Completada') { ?>

                                            <span class="estado completada">
                                                🟢 Completada
                                            </span>

                                        <?php } elseif ($h['estado'] === 'Cancelada') { ?>

                                            <span class="estado cancelada">
                                                🔴 Cancelada
                                            </span>

                                        <?php } ?>

                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            <?php } else { ?>

                <div class="sinDatos">

                    <p>
                        No tienes reservaciones en tu historial.
                    </p>

                </div>

            <?php } ?>

        </section>

        <!-- =========================
         RESEÑAS
    ========================== -->

        <section
            class="card"
            id="resenas"
            style="display:none;">

            <h2>Reseñas de Clientes</h2>


            <?php if (!empty($reseñas)) { ?>


                <div class="contenedorResenas">


                    <?php foreach ($reseñas as $r) { ?>

                        <div class="review">


                            <h3>

                                <?php

                                echo htmlspecialchars(
                                    $r['usuario']
                                );

                                ?>

                            </h3>


                            <!-- ESTRELLAS -->

                            <div class="estrellas">

                                <?php

                                $calificacion = (int)$r['calificacion'];

                                for ($i = 1; $i <= 5; $i++) {

                                    if ($i <= $calificacion) {

                                        echo '<span>⭐</span>';
                                    } else {

                                        echo '<span>☆</span>';
                                    }
                                }

                                ?>

                            </div>


                            <!-- COMENTARIO -->

                            <p>

                                <?php

                                echo htmlspecialchars(
                                    $r['comentario']
                                );

                                ?>

                            </p>


                            <!-- FECHA -->

                            <?php if (!empty($r['fecha'])) { ?>

                                <small>

                                    <?php

                                    echo date(
                                        'd/m/Y',
                                        strtotime($r['fecha'])
                                    );

                                    ?>

                                </small>

                            <?php } ?>


                        </div>

                    <?php } ?>


                </div>


            <?php } else { ?>


                <div class="sinDatos">

                    <p>
                        Este barbero todavía no tiene reseñas.
                    </p>

                </div>


            <?php } ?>

        </section>


    </div>



    <script>
        function mostrarSeccion(opcion) {

            const reservas = document.getElementById("reservas");
            const historial = document.getElementById("historial");
            const resenas = document.getElementById("resenas");

            const btnReservas = document.getElementById("btnReservas");
            const btnHistorial = document.getElementById("btnHistorial");
            const btnResenas = document.getElementById("btnResenas");


            // Ocultar secciones

            reservas.style.display = "none";
            historial.style.display = "none";
            resenas.style.display = "none";


            // Quitar estado activo

            btnReservas.classList.remove("activo");
            btnHistorial.classList.remove("activo");
            btnResenas.classList.remove("activo");


            // Mostrar reservas

            if (opcion === "reservas") {

                reservas.style.display = "block";

                btnReservas.classList.add("activo");

            }

            // Mostrar historial
            if (opcion === "historial") {
                historial.style.display = "block";
                btnHistorial.classList.add("activo");
            }

            // Mostrar reseñas
            if (opcion === "resenas") {

                resenas.style.display = "block";

                btnResenas.classList.add("activo");

            }

        }


        window.onload = function() {

            mostrarSeccion("reservas");

        };
    </script>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>