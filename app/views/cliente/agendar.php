<?php
if (!isset($_SESSION['id'])) {
    header("Location: index.php?controller=auth&action=loginCliente");
    exit;
}

$servicios = $servicios ?? [];
$barberos = $barberos ?? [];
$productos = $productos ?? [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar cita - KADOSH Barber</title>
    <link rel="stylesheet" href="app/public/css/global.css">
    <link rel="stylesheet" href="app/public/css/agendar.css">

    <style>
        /* ESTILOS FORZADOS DE SUPERPOSICIÓN PARA LOS MODALES */
        .modal-producto-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background-color: rgba(0, 0, 0, 0.65) !important;
            z-index: 99999 !important;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="container">

        <h1>Agendar cita</h1>

        <?php if (isset($_SESSION['error_cita'])): ?>
            <div style="background: #f8d7da; color: #842029; padding: 12px; border-radius: 8px; margin-bottom: 15px; text-align: center;">
                <?= $_SESSION['error_cita']; ?>
            </div>
            <?php unset($_SESSION['error_cita']); ?>
        <?php endif; ?>

        <form id="formAgendar" action="index.php?controller=gestionCita&action=guardar" method="POST">

            <!-- SERVICIO -->
            <div class="form-group">
                <label for="servicio">Servicio</label>
                <select name="servicio" id="servicio" required>
                    <option value="">Seleccione un servicio</option>
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?= htmlspecialchars($servicio['id_servicio']) ?>" data-precio="<?= htmlspecialchars($servicio['precio']) ?>">
                            <?= htmlspecialchars($servicio['nombre']) ?> - $<?= number_format($servicio['precio'], 0, ',', '.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- BARBERO -->
            <div class="form-group">
                <label for="barbero">Barbero</label>
                <select name="barbero" id="barbero" required>
                    <option value="">Seleccione un barbero</option>
                    <?php foreach ($barberos as $barbero): ?>
                        <option value="<?= htmlspecialchars($barbero['id_usuario']) ?>">
                            <?= htmlspecialchars($barbero['nombre'] . ' ' . $barbero['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- FECHA -->
            <div class="form-group">
                <label for="fecha">Fecha</label>
                <input type="date" name="fecha" id="fecha" required>
            </div>

            <!-- HORARIOS -->
            <div class="form-group">
                <label>Horario disponible</label>
                <div id="horariosDisponibles">
                    <p>Seleccione un servicio, un barbero y una fecha.</p>
                </div>
                <input type="hidden" name="hora" id="hora" required>
            </div>

            <!-- BOTÓN -->
            <button type="submit">AGENDAR CITA</button>
            <input type="hidden" name="id_producto" id="id_producto" value="">

        </form>

        <br>
        <a href="index.php?controller=gestionCita&action=misCitas">← Volver a mis citas</a>

    </div>

    <!-- MODAL CONFIRMACIÓN PRODUCTO -->
    <div id="modalProducto" class="modal-producto-overlay" style="display: none;">
        <div class="modal-producto-content">
            <h2>¿Desea añadir un producto?</h2>
            <p>Puede añadir un producto a su cita antes de finalizar.</p>
            <div class="modal-producto-botones">
                <button type="button" id="btnSiProducto" class="btn-producto-si">Sí, añadir producto</button>
                <button type="button" id="btnNoProducto" class="btn-producto-no">No, continuar</button>
            </div>
        </div>
    </div>

    <!-- MODAL LISTA DE PRODUCTOS -->
    <div id="modalProductos" class="modal-producto-overlay" style="display: none;">
        <div class="modal-producto-content">
            <h2>Seleccionar producto</h2>
            <p>Seleccione el producto que desea añadir a su cita.</p>
            <div id="listaProductos" style="max-height: 250px; overflow-y: auto; margin: 15px 0;">
                <p>Cargando productos...</p>
            </div>
            <div class="modal-producto-botones">
                <button type="button" id="btnContinuarProducto" class="btn-producto-si">Continuar</button>
                <button type="button" id="btnCancelarProducto" class="btn-producto-no">Cancelar</button>
            </div>
        </div>
    </div>


    <!-- ========================================== -->
    <!-- SCRIPTS                                    -->
    <!-- ========================================== -->
    <script>
        const servicio = document.getElementById('servicio');
        const barbero = document.getElementById('barbero');
        const fecha = document.getElementById('fecha');
        const horariosDisponibles = document.getElementById('horariosDisponibles');
        const horaInput = document.getElementById('hora');

        function consultarHorario() {
            const idServicio = servicio.value;
            const idBarbero = barbero.value;
            const fechaSeleccionada = fecha.value;

            horariosDisponibles.innerHTML = '';
            horaInput.value = '';

            if (!idServicio || !idBarbero || !fechaSeleccionada) {
                horariosDisponibles.innerHTML = `<p>Seleccione un servicio, un barbero y una fecha.</p>`;
                return;
            }

            fetch(`index.php?controller=gestionCita&action=obtenerHorario&barbero=${idBarbero}&fecha=${fechaSeleccionada}&servicio=${idServicio}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.horario) {
                        horariosDisponibles.innerHTML = `<p style="color:red;">❌ Este barbero no tiene horario disponible para este día.</p>`;
                        return;
                    }

                    const inicio = data.horario.hora_inicio;
                    const fin = data.horario.hora_fin;
                    const duracion = data.duracion;

                    horariosDisponibles.innerHTML = `
                        <p style="color:green;">🟢 Horario del barbero: <strong>${inicio}</strong> a <strong>${fin}</strong></p>
                        <div id="listaHoras"></div>
                    `;

                    generarHoras(inicio, fin, duracion, data.ocupadas);
                })
                .catch(error => {
                    console.error(error);
                    horariosDisponibles.innerHTML = `<p style="color:red;">❌ Error al consultar el horario.</p>`;
                });
        }

        function generarHoras(inicio, fin, duracion, ocupadas) {
            const listaHoras = document.getElementById('listaHoras');
            listaHoras.innerHTML = '';

            function horaAMinutos(hora) {
                const partes = hora.split(':');
                return parseInt(partes[0]) * 60 + parseInt(partes[1]);
            }

            function minutosAHora(minutos) {
                const horas = Math.floor(minutos / 60);
                const minutosRestantes = minutos % 60;
                return String(horas).padStart(2, '0') + ':' + String(minutosRestantes).padStart(2, '0');
            }

            const duracionMinutos = horaAMinutos(duracion);
            const inicioMinutos = horaAMinutos(inicio);
            const finMinutos = horaAMinutos(fin);

            let minutosActuales = inicioMinutos;
            let hayDisponibilidad = false;

            while (minutosActuales + duracionMinutos <= finMinutos) {
                const inicioNuevaCita = minutosActuales;
                const finNuevaCita = minutosActuales + duracionMinutos;
                let estaOcupada = false;

                ocupadas.forEach(cita => {
                    const inicioCita = horaAMinutos(cita.hora_cita);
                    const duracionCita = horaAMinutos(cita.duracion);
                    const finCita = inicioCita + duracionCita;

                    if (inicioNuevaCita < finCita && finNuevaCita > inicioCita) {
                        estaOcupada = true;
                    }
                });

                if (!estaOcupada) {
                    hayDisponibilidad = true;
                    const horaFormateada = minutosAHora(minutosActuales);
                    const boton = document.createElement('button');

                    boton.type = 'button';
                    boton.textContent = horaFormateada;
                    boton.style.margin = '5px';
                    boton.style.padding = '10px 15px';
                    boton.style.cursor = 'pointer';

                    boton.addEventListener('click', function() {
                        document.querySelectorAll('#listaHoras button').forEach(btn => {
                            btn.style.backgroundColor = '';
                            btn.style.color = '';
                        });
                        boton.style.backgroundColor = '#6f42c1';
                        boton.style.color = 'white';
                        horaInput.value = horaFormateada;
                    });

                    listaHoras.appendChild(boton);
                }

                minutosActuales += duracionMinutos;
            }

            if (!hayDisponibilidad) {
                listaHoras.innerHTML = `<p style="color:red;">❌ No hay horarios disponibles para este día.</p>`;
            }
        }

        servicio.addEventListener('change', consultarHorario);
        barbero.addEventListener('change', consultarHorario);
        fecha.addEventListener('change', consultarHorario);

        // MODAL Y FORMULARIO
        const formulario = document.getElementById('formAgendar');
        const modalProducto = document.getElementById('modalProducto');
        const btnSiProducto = document.getElementById('btnSiProducto');
        const btnNoProducto = document.getElementById('btnNoProducto');
        const modalProductos = document.getElementById('modalProductos');
        const listaProductos = document.getElementById('listaProductos');
        const btnContinuarProducto = document.getElementById('btnContinuarProducto');
        const btnCancelarProducto = document.getElementById('btnCancelarProducto');
        const idProducto = document.getElementById('id_producto');

        formulario.addEventListener('submit', function(event) {
            event.preventDefault();

            if (!horaInput.value) {
                alert('Por favor seleccione un horario.');
                return;
            }

            // Mostrar modal flotante
            modalProducto.style.display = 'flex';
        });

        // Respuesta: NO agregar producto
        btnNoProducto.addEventListener('click', function() {
            modalProducto.style.display = 'none';
            idProducto.value = "";
            formulario.submit();
        });

        // Respuesta: SI agregar producto
        btnSiProducto.addEventListener('click', function() {
            modalProducto.style.display = 'none';
            modalProductos.style.display = 'flex';
            cargarProductos();
        });

        // Confirmar producto seleccionado
        btnContinuarProducto.addEventListener('click', function() {
            const productoSeleccionado = document.querySelector('input[name="productoSeleccionado"]:checked');
            if (!productoSeleccionado) {
                alert('Por favor seleccione un producto.');
                return;
            }
            idProducto.value = productoSeleccionado.value;
            modalProductos.style.display = 'none';
            formulario.submit();
        });

        // Cancelar desde modal de lista de productos
        btnCancelarProducto.addEventListener('click', function() {
            modalProductos.style.display = 'none';
            modalProducto.style.display = 'flex';
        });

        function cargarProductos() {
            listaProductos.innerHTML = `<p>Cargando productos...</p>`;

            fetch('index.php?controller=clienteProducto&action=obtenerProductosDisponibles')
                .then(response => response.json())
                .then(productos => {
                    listaProductos.innerHTML = '';

                    if (!productos || productos.length === 0) {
                        listaProductos.innerHTML = `<p>No hay productos disponibles.</p>`;
                        return;
                    }

                    productos.forEach(producto => {
                        const div = document.createElement('div');
                        div.classList.add('producto-opcion');
                        div.style.padding = "8px";
                        div.style.textAlign = "left";
                        div.innerHTML = `
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="productoSeleccionado" value="${producto.id_producto}">
                                <span style="flex-grow: 1;">${producto.nombre}</span>
                                <strong>$${Number(producto.precio).toLocaleString('es-CO')}</strong>
                            </label>
                        `;
                        listaProductos.appendChild(div);
                    });
                })
                .catch(error => {
                    console.error(error);
                    listaProductos.innerHTML = `<p style="color:red;">❌ Error al cargar los productos.</p>`;
                });
        }
    </script>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>