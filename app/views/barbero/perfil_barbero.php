<?php
// Datos de ejemplo (luego vendrán de la base de datos)

$barbero = [
    "nombre" => "Laura",
    "especialidad" => "Cortes clásicos y degradados",
    "telefono" => "3001234567",
    "foto" => "img/barbero.jpg"
];


$reservaciones = [

    [
        "id" => 1,
        "cliente" => "Juan Pérez",
        "fecha" => "2026-07-30",
        "hora" => "09:00 AM",
        "estado" => "confirmada"
    ],

    [
        "id" => 2,
        "cliente" => "Andrés López",
        "fecha" => "2026-07-30",
        "hora" => "11:00 AM",
        "estado" => "pendiente"
    ],

    [
        "id" => 3,
        "cliente" => "Camilo Díaz",
        "fecha" => "2026-07-31",
        "hora" => "03:00 PM",
        "estado" => "cancelada"
    ]

];
$horarios = [
    "Lunes | 8:00 AM - 6:00 PM",
    "Martes | 8:00 AM - 6:00 PM",
    "Miércoles | 8:00 AM - 6:00 PM",
    "Jueves | 8:00 AM - 6:00 PM",
    "Viernes | 8:00 AM - 7:00 PM",
    "Sábado | 9:00 AM - 5:00 PM"
];

$reseñas = [
    [
        "usuario" => "Juan",
        "calificacion" => 5,
        "comentario" => "Excelente atención y muy buen corte."
    ],
    [
        "usuario" => "Laura",
        "calificacion" => 4,
        "comentario" => "Muy recomendado."
    ]
];

$totalReservas = count($reservaciones);
$totalResenas = count($reseñas);
$promedio = 4.8;
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Perfil del Barbero</title>

    <link rel="stylesheet" href="app/public/css/perfilBarbero.css">



</head>
<script>
    function mostrarSeccion(opcion) {

        const reservas = document.getElementById("reservas");
        const resenas = document.getElementById("resenas");
        const formulario = document.getElementById("formResena");

        const btnReservas = document.getElementById("btnReservas");
        const btnResenas = document.getElementById("btnResenas");

        reservas.style.display = "none";
        resenas.style.display = "none";
        formulario.style.display = "none";

        btnReservas.classList.remove("activo");
        btnResenas.classList.remove("activo");

        if (opcion === "reservas") {

            reservas.style.display = "block";

            btnReservas.classList.add("activo");

        }

        if (opcion === "resenas") {

            resenas.style.display = "block";

            formulario.style.display = "block";

            btnResenas.classList.add("activo");

        }

    }

    window.onload = function() {

        mostrarSeccion("reservas");

    }
</script>

<body>

    <section class="perfil">

        <div class="imagen">
            <img src="../../public/assets/img/foto5.avif" class="avatar" alt="Foto del barbero">
        </div>

        <div class="informacion">
            <h1><?php echo $barbero['nombre']; ?></h1>

            <p><strong>Especialidad:</strong> <?php echo $barbero['especialidad']; ?></p>

            <p><strong>Teléfono:</strong> <?php echo $barbero['telefono']; ?></p>
        </div>

        <div class="estadisticas">

            <button class="stat activo" id="btnReservas" onclick="mostrarSeccion('reservas')">
                <h3><?php echo $totalReservas; ?></h3>
                <span>Reservas</span>
            </button>

            <button class="stat" id="btnResenas" onclick="mostrarSeccion('resenas')">
                <h3><?php echo $totalResenas; ?></h3>
                <span>Reseñas</span>
            </button>

            <div class="stat">
                <h3>⭐ <?php echo $promedio; ?></h3>
                <span>Calificación</span>
            </div>

        </div>

    </section>

    <section class="card">

        <h2>Horarios de Trabajo</h2>

        <div class="horarios">

            <?php foreach ($horarios as $hora) { ?>

                <div class="horario">

                    <span>🕒</span>

                    <p><?php echo $hora; ?></p>

                </div>

            <?php } ?>

        </div>

    </section>

    <section class="card" id="reservas">

        <h2>Reservaciones</h2>

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

                        <td><?php echo $r['cliente']; ?></td>

                        <td><?php echo $r['fecha']; ?></td>

                        <td><?php echo $r['hora']; ?></td>

                        <td>

                            <form action="../../controllers/BarberoController.php" method="POST">


                                <input type="hidden" name="id_reservacion" value="<?php echo $r['id_reservacion']; ?>">


                                <select name="estado_reserva" onchange="this.form.submit()">


                                    <option value="pendiente" <?php echo ($r['estado'] == "pendiente") ? "selected" : ""; ?>>
                                        🟡 Pendiente
                                    </option>



                                    <option value="cancelada" <?php echo ($r['estado'] == "cancelada") ? "selected" : ""; ?>>
                                        🔴 Cancelada
                                    </option>


                                    <option value="completada" <?php echo ($r['estado'] == "completada") ? "selected" : ""; ?>>
                                        🟢 Completada
                                    </option>


                                </select>


                            </form>


                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </section>

    <section class="card" id="resenas" style="display:none;">

        <h2>Reseñas</h2>

        <div class="contenedorResenas">

            <?php foreach ($reseñas as $r) { ?>

                <div class="review">

                    <h3><?php echo $r['usuario']; ?></h3>

                    <div class="estrellas">

                        <?php

                        for ($i = 1; $i <= 5; $i++) {

                            if ($i <= $r['calificacion']) {

                                echo "⭐";
                            }
                        }

                        ?>

                    </div>

                    <p>

                        <?php echo $r['comentario']; ?>

                    </p>

                </div>

            <?php } ?>

        </div>

    </section>

    <section class="card" id="formResena" style="display:none;">

        <h2>Agregar Reseña</h2>

        <form>

            <input type="text" placeholder="Nombre">

            <select>

                <option>⭐⭐⭐⭐⭐ Excelente</option>
                <option>⭐⭐⭐⭐ Muy Bueno</option>
                <option>⭐⭐⭐ Bueno</option>
                <option>⭐⭐ Regular</option>
                <option>⭐ Malo</option>

            </select>

            <textarea placeholder="Escribe tu reseña..."></textarea>

            <button>

                Publicar Reseña

            </button>

        </form>

    </section>

    </div>

</body>

</html>