<?php
if (!isset($_SESSION['id'])) {
    header("Location: index.php?controller=auth&action=loginCliente");
    exit;
}

$servicios = $servicios ?? [];
$barberos = $barberos ?? [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar cita - KADOSH Barber</title>
    <link rel="stylesheet" href="app/public/css/global.css">
    <link rel="stylesheet" href="app/public/css/agendar.css">
</head>

<body>

    <div class="container">

        <h1>Agendar cita</h1>

        <?php if (isset($_SESSION['error_cita'])): ?>

            <div style="
        background: #f8d7da;
        color: #842029;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 15px;
        text-align: center;
    ">
                <?= $_SESSION['error_cita']; ?>
            </div>

            <?php unset($_SESSION['error_cita']); ?>

        <?php endif; ?>


        <form action="index.php?controller=gestionCita&action=guardar" method="POST">

            <!-- SERVICIO -->
            <div class="form-group">

                <label for="servicio">
                    Servicio
                </label>

                <select name="servicio" id="servicio" required>

                    <option value="">
                        Seleccione un servicio
                    </option>

                    <?php foreach ($servicios as $servicio): ?>

                        <option value="<?= htmlspecialchars($servicio['id_servicio']) ?>">

                            <?= htmlspecialchars($servicio['nombre']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- BARBERO -->
            <div class="form-group">

                <label for="barbero">
                    Barbero
                </label>

                <select name="barbero" id="barbero" required>

                    <option value="">
                        Seleccione un barbero
                    </option>

                    <?php foreach ($barberos as $barbero): ?>

                        <option value="<?= htmlspecialchars($barbero['id_usuario']) ?>">

                            <?= htmlspecialchars(
                                $barbero['nombre'] . ' ' . $barbero['apellido']
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- FECHA -->
            <div class="form-group">

                <label for="fecha">
                    Fecha
                </label>

                <input
                    type="date"
                    name="fecha"
                    id="fecha"
                    required>

            </div>


            <!-- HORARIOS -->
            <div class="form-group">

                <label>
                    Horario disponible
                </label>

                <div id="horariosDisponibles">

                    <p>
                        Seleccione un servicio, un barbero y una fecha.
                    </p>

                </div>

                <input
                    type="hidden"
                    name="hora"
                    id="hora"
                    required>

            </div>


            <!-- BOTÓN -->
            <button type="submit">
                AGENDAR CITA
            </button>

        </form>


        <br>

        <a href="index.php?controller=gestionCita&action=misCitas">
            ← Volver a mis citas
        </a>

    </div>


    <script>
        const servicio = document.getElementById('servicio');
        const barbero = document.getElementById('barbero');
        const fecha = document.getElementById('fecha');

        const horariosDisponibles =
            document.getElementById('horariosDisponibles');

        const horaInput =
            document.getElementById('hora');


        function consultarHorario() {

            const idServicio = servicio.value;
            const idBarbero = barbero.value;
            const fechaSeleccionada = fecha.value;

            horariosDisponibles.innerHTML = '';
            horaInput.value = '';

            if (!idServicio || !idBarbero || !fechaSeleccionada) {

                horariosDisponibles.innerHTML = `
                <p>
                    Seleccione un servicio, un barbero y una fecha.
                </p>
            `;

                return;
            }

            fetch(
                    `index.php?controller=gestionCita&action=obtenerHorario&barbero=${idBarbero}&fecha=${fechaSeleccionada}&servicio=${idServicio}`
                )

                .then(response => response.json())

                .then(data => {

                    console.log(data);

                    if (!data.horario) {

                        horariosDisponibles.innerHTML = `
                    <p style="color:red;">
                        ❌ Este barbero no tiene horario disponible
                        para este día.
                    </p>
                `;

                        return;
                    }

                    const inicio = data.horario.hora_inicio;
                    const fin = data.horario.hora_fin;
                    const duracion = data.duracion;

                    // Mostrar horario laboral
                    horariosDisponibles.innerHTML = `
                <p style="color:green;">
                    🟢 Horario del barbero:
                    <strong>${inicio}</strong>
                    a
                    <strong>${fin}</strong>
                </p>

                <div id="listaHoras"></div>
            `;

                    generarHoras(
                        inicio,
                        fin,
                        duracion,
                        data.ocupadas
                    );

                })

                .catch(error => {

                    console.error(error);

                    horariosDisponibles.innerHTML = `
                <p style="color:red;">
                    ❌ Error al consultar el horario.
                </p>
            `;

                });
        }


        function generarHoras(inicio, fin, duracion, ocupadas) {

            const listaHoras = document.getElementById('listaHoras');

            listaHoras.innerHTML = '';

            // ==========================================
            // Convertir HH:MM:SS a minutos
            // ==========================================

            function horaAMinutos(hora) {

                const partes = hora.split(':');

                return (
                    parseInt(partes[0]) * 60 +
                    parseInt(partes[1])
                );
            }


            // ==========================================
            // Convertir minutos a HH:MM
            // ==========================================

            function minutosAHora(minutos) {

                const horas = Math.floor(minutos / 60);

                const minutosRestantes = minutos % 60;

                return (
                    String(horas).padStart(2, '0') +
                    ':' +
                    String(minutosRestantes).padStart(2, '0')
                );
            }


            // ==========================================
            // Duración del servicio seleccionado
            // ==========================================

            const duracionMinutos = horaAMinutos(duracion);


            // ==========================================
            // Horario laboral
            // ==========================================

            const inicioMinutos = horaAMinutos(inicio);

            const finMinutos = horaAMinutos(fin);


            // ==========================================
            // Recorrer posibles horarios
            // ==========================================

            let minutosActuales = inicioMinutos;

            let hayDisponibilidad = false;


            while (
                minutosActuales + duracionMinutos <= finMinutos
            ) {

                const inicioNuevaCita = minutosActuales;

                const finNuevaCita =
                    minutosActuales + duracionMinutos;


                // ==========================================
                // Comprobar si se cruza con una cita existente
                // ==========================================

                let estaOcupada = false;


                ocupadas.forEach(cita => {

                    const inicioCita =
                        horaAMinutos(cita.hora_cita);

                    const duracionCita =
                        horaAMinutos(cita.duracion);

                    const finCita =
                        inicioCita + duracionCita;


                    // ¿Existe cruce?
                    if (
                        inicioNuevaCita < finCita &&
                        finNuevaCita > inicioCita
                    ) {

                        estaOcupada = true;

                    }

                });


                // ==========================================
                // Crear botón si está disponible
                // ==========================================

                if (!estaOcupada) {

                    hayDisponibilidad = true;

                    const horaFormateada =
                        minutosAHora(minutosActuales);


                    const boton =
                        document.createElement('button');


                    boton.type = 'button';

                    boton.textContent = horaFormateada;


                    boton.style.margin = '5px';

                    boton.style.padding = '10px 15px';

                    boton.style.cursor = 'pointer';


                    boton.addEventListener(
                        'click',
                        function() {

                            // Quitar selección anterior

                            document
                                .querySelectorAll('#listaHoras button')
                                .forEach(btn => {

                                    btn.style.backgroundColor = '';

                                    btn.style.color = '';

                                });


                            // Marcar hora seleccionada

                            boton.style.backgroundColor =
                                '#6f42c1';

                            boton.style.color =
                                'white';


                            // Guardar hora

                            horaInput.value =
                                horaFormateada;

                        }
                    );


                    listaHoras.appendChild(boton);

                }


                // ==========================================
                // Siguiente horario
                // ==========================================

                minutosActuales += duracionMinutos;

            }


            // ==========================================
            // Si no hay horarios
            // ==========================================

            if (!hayDisponibilidad) {

                listaHoras.innerHTML = `
            <p style="color:red;">
                ❌ No hay horarios disponibles
                para este día.
            </p>
        `;

            }

        }

        servicio.addEventListener('change', consultarHorario);
        barbero.addEventListener('change', consultarHorario);
        fecha.addEventListener('change', consultarHorario);
    </script>


    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>