<?php
if (!isset($_SESSION['id'])) {
    header("Location: index.php?controller=auth&action=loginCliente");
    exit;
}

$servicios = $servicios ?? [];
$barberos  = $barberos ?? [];
$productos = $productos ?? [];

$foto = !empty($_SESSION['foto']) ? $_SESSION['foto'] : 'app/public/assets/img/avatar.png';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita - KADOSH Barber</title>
    <link rel="stylesheet" href="app/public/css/global.css">
    <link rel="stylesheet" href="app/public/css/agendar.css">
</head>

<body>

    <div class="container">

        <h1>AGENDAR TU CITA</h1>

        <?php if (isset($_SESSION['error_cita'])): ?>
            <div style="padding: 12px 16px; margin-bottom: 20px; border-radius: 10px;">
                ⚠️ <?= $_SESSION['error_cita']; ?>
            </div>
            <?php unset($_SESSION['error_cita']); ?>
        <?php endif; ?>

        <!-- FORMULARIO DE RESERVA -->
        <form id="formAgendar" action="index.php?controller=gestionCita&action=guardar" method="POST">

            <!-- SERVICIO -->
            <div class="form-group">
                <label for="servicio">Servicio Principal</label>
                <select name="servicio" id="servicio" required onchange="actualizarPrecioServicio(this)">
                    <option value="">Seleccione un servicio...</option>
                    <?php foreach ($servicios as $servicio):
                        $idServ = $servicio['id_servicio'];
                    ?>
                        <option value="<?= htmlspecialchars($idServ) ?>"
                            data-precio="<?= htmlspecialchars($servicio['precio']) ?>">
                            <?= htmlspecialchars($servicio['nombre']) ?> - $<?= number_format($servicio['precio'], 0, ',', '.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- BARBERO -->
            <div class="form-group">
                <label for="barbero">Barbero Profesional</label>
                <select name="barbero" id="barbero" required>
                    <option value="">Seleccione un barbero...</option>
                    <?php foreach ($barberos as $barbero): ?>
                        <option value="<?= htmlspecialchars($barbero['id_usuario']) ?>">
                            <?= htmlspecialchars($barbero['nombre'] . ' ' . $barbero['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- FECHA -->
            <div class="form-group">
                <label for="fecha">Fecha de la Cita</label>
                <input type="date" name="fecha" id="fecha" required>
            </div>

            <!-- HORARIOS DISPONIBLES -->
            <div class="form-group">
                <label>Horarios Disponibles</label>
                <div id="horariosDisponibles">
                    <p>Seleccione servicio, barbero y fecha para consultar los turnos disponibles.</p>
                </div>
                <input type="hidden" name="hora" id="hora" required>
            </div>

            <!-- CAMPOS OCULTOS DE SERVICIO Y PRODUCTOS -->
            <input type="hidden" name="precio_servicio_final" id="precio_servicio_final">
            <input type="hidden" name="productos_seleccionados" id="productos_seleccionados" value="">

            <button type="submit">CONFIRMAR Y AGENDAR CITA</button>

        </form>

        <!-- MODAL DE RESEÑAS DEL BARBERO -->
        <div id="modalResenasBarbero" class="modal-resenas-overlay">

            <div class="modal-resenas-content">

                <button type="button"
                    class="cerrar-modal-resenas"
                    id="cerrarModalResenas">
                    ×
                </button>

                <div id="contenidoResenasBarbero">

                    <p class="cargando-resenas">
                        Cargando información del barbero...
                    </p>

                </div>

            </div>

        </div>

        <!-- SECCIÓN BARBEROS DISPONIBLES (5 DÍAS) -->
        <div class="seccion-barberos">
            <h2 class="titulo-seccion-barberos">💈 Barberos Disponibles</h2>

            <div class="grid-barberos">
                <?php if (!empty($barberos)): ?>

                    <?php foreach ($barberos as $barbero): ?>
                        <?php
                        $fotoBD = !empty($barbero['foto']) ? trim($barbero['foto']) : '';
                        $fotoBarbero = !empty($fotoBD) ? $fotoBD : 'app/public/assets/img/avatar.jpg';
                        $nombreBarbero = trim(($barbero['nombre'] ?? '') . ' ' . ($barbero['apellido'] ?? ''));
                        ?>
                        <div class="tarjeta-barbero"
                            data-barbero-id="<?= htmlspecialchars($barbero['id_usuario']); ?>"
                            role="button"
                            tabindex="0"
                            aria-label="Ver reseñas de <?= htmlspecialchars(!empty($nombreBarbero) ? $nombreBarbero : $barbero['nombre']); ?>">
                            <div class="avatar-barbero-container">
                                <img src="<?= htmlspecialchars($fotoBarbero); ?>"
                                    class="avatar-barbero"
                                    alt="Foto de <?= htmlspecialchars($barbero['nombre']); ?>"
                                    onerror="this.onerror=null; this.src='app/public/assets/img/avatar.jpg';">
                            </div>

                            <div class="info-barbero">
                                <h3>
                                    <?= !empty($nombreBarbero) ? htmlspecialchars($nombreBarbero) : 'Barbero sin nombre'; ?>
                                </h3>

                                <?php if (!empty($barbero['telefono'])): ?>
                                    <p class="telefono-barbero">📱 <?= htmlspecialchars($barbero['telefono']); ?></p>
                                <?php endif; ?>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="sin-barberos">No hay barberos con horario asignado para los próximos 5 días.</p>
                <?php endif; ?>
            </div>
        </div>

        <a href="index.php?controller=gestionCita&action=misCitas">← Volver a mis citas</a>

    </div>

    <!-- MODAL PREGUNTA PRODUCTO -->
    <div id="modalProducto" class="modal-producto-overlay">
        <div class="modal-producto-content">
            <h2>¿Desea añadir productos?</h2>
            <p>Puede añadir uno o más productos a su cita antes de finalizar.</p>
            <div class="modal-producto-botones">
                <button type="button" id="btnSiProducto" class="btn-producto-si">Sí, añadir productos</button>
                <button type="button" id="btnNoProducto" class="btn-producto-no">No, continuar</button>
            </div>
        </div>
    </div>

    <!-- MODAL LISTA DE PRODUCTOS -->
    <div id="modalProductos" class="modal-producto-overlay">
        <div class="modal-producto-content">
            <h2>Seleccionar Productos</h2>
            <p>Seleccione los productos que desea añadir a su cita.</p>
            <div id="listaProductos">
                <?php foreach ($productos as $prod):
                    $idProd = $prod['id_producto'];
                ?>
                    <label class="item-producto">
                        <input type="checkbox" class="chk-producto"
                            value="<?= htmlspecialchars($idProd) ?>"
                            data-precio-orig="<?= htmlspecialchars($prod['precio']) ?>"
                            data-nombre="<?= htmlspecialchars($prod['nombre']) ?>">
                        <span class="nombre-producto"><?= htmlspecialchars($prod['nombre']) ?></span>
                        <span class="precio-producto">
                            $<?= number_format($prod['precio'], 0, ',', '.') ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="modal-producto-botones">
                <button type="button" id="btnContinuarProducto" class="btn-producto-si">Continuar</button>
                <button type="button" id="btnCancelarProducto" class="btn-producto-no">Cancelar</button>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>Enlaces</h4>
                <a href="index.php">Inicio</a>
                <a href="#">Servicios</a>
                <a href="#">Contacto</a>
                <a href="#">Política de Privacidad</a>
            </div>
            <div class="footer-section">
                <h4>Contacto</h4>
                <p>📍 Bogotá - Colombia</p>
                <p>📞 +57 300 359 3276</p>
                <p>✉️ kadosh1234@gmail.com</p>
            </div>
            <div class="footer-section">
                <h4>Desarrollado por:</h4>
                <p>Daniela Yara, Laura Buitrago, Juan Acuña, Juan Mulcue, Jose Cuastumal</p>
                <br>
                <p><strong>SENA - ADSO</strong></p>
                <p>Ficha: 3171693</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 <strong>KADOSH Barber Shop</strong>. Todos los derechos reservados. | Versión 1.0</p>
        </div>
    </footer>


    <!-- ========================================== -->
    <!-- SCRIPTS                                    -->
    <!-- ========================================== -->

    <script>
        function actualizarPrecioServicio(select) {
            const option = select.options[select.selectedIndex];
            const precioInput = document.getElementById('precio_servicio_final');

            if (!option || !option.value) {
                precioInput.value = '';
                return;
            }

            const precioBase = parseFloat(option.getAttribute('data-precio')) || 0;
            precioInput.value = precioBase;
        }
    </script>

    <script>
        // 1. CAPTURA DE ELEMENTOS DEL DOM
        const servicio = document.getElementById('servicio');
        const barbero = document.getElementById('barbero');
        const fecha = document.getElementById('fecha');
        const horariosDisponibles = document.getElementById('horariosDisponibles');
        const horaInput = document.getElementById('hora');

        // 2. FUNCIÓN PARA CONSULTAR HORARIOS DISPONIBLES
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

            horariosDisponibles.innerHTML = `<p>Cargando horarios disponibles...</p>`;

            fetch(`index.php?controller=gestionCita&action=obtenerHorario&barbero=${idBarbero}&fecha=${fechaSeleccionada}&servicio=${idServicio}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.horario) {
                        horariosDisponibles.innerHTML = `<p style="color:red;">Este barbero no tiene horario disponible para este día.</p>`;
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
                    console.error('Error fetching horarios:', error);
                    horariosDisponibles.innerHTML = `<p style="color:red;">Error al consultar el horario.</p>`;
                });
        }

        // 3. GENERACIÓN DE BOTONES DE HORAS
        function generarHoras(inicio, fin, duracion, ocupadas) {
            const listaHoras = document.getElementById('listaHoras');
            listaHoras.innerHTML = '';

            function horaAMinutos(hora) {
                if (typeof hora === 'number') return hora;
                if (!isNaN(hora)) return parseInt(hora);

                if (typeof hora === 'string' && hora.includes(':')) {
                    const partes = hora.split(':');
                    return parseInt(partes[0]) * 60 + parseInt(partes[1]);
                }

                return 30;
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

                if (Array.isArray(ocupadas)) {
                    ocupadas.forEach(cita => {
                        const inicioCita = horaAMinutos(cita.hora_cita);
                        const duracionCita = cita.duracion ? horaAMinutos(cita.duracion) : duracionMinutos;
                        const finCita = inicioCita + duracionCita;

                        if (inicioNuevaCita < finCita && finNuevaCita > inicioCita) {
                            estaOcupada = true;
                        }
                    });
                }

                if (!estaOcupada) {
                    hayDisponibilidad = true;
                    const horaFormateada = minutosAHora(minutosActuales);
                    const boton = document.createElement('button');

                    boton.type = 'button';
                    boton.textContent = horaFormateada;
                    boton.className = 'btn-horario';

                    boton.addEventListener('click', function() {
                        document.querySelectorAll('#listaHoras button').forEach(btn => {
                            btn.classList.remove('selected');
                        });
                        boton.classList.add('selected');
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

        // 4. ESCUCHADORES DE CAMBIO
        servicio.addEventListener('change', consultarHorario);
        barbero.addEventListener('change', consultarHorario);
        fecha.addEventListener('change', consultarHorario);

        // 5. MANEJO DE MODALES DE PRODUCTOS Y ENVÍO DEL FORMULARIO
        const formulario = document.getElementById('formAgendar');
        const modalProducto = document.getElementById('modalProducto');
        const btnSiProducto = document.getElementById('btnSiProducto');
        const btnNoProducto = document.getElementById('btnNoProducto');
        const modalProductos = document.getElementById('modalProductos');
        const listaProductos = document.getElementById('listaProductos');
        const btnContinuarProducto = document.getElementById('btnContinuarProducto');
        const btnCancelarProducto = document.getElementById('btnCancelarProducto');
        const productosInput = document.getElementById('productos_seleccionados');

        formulario.addEventListener('submit', function(event) {
            event.preventDefault();

            if (!horaInput.value) {
                alert('Por favor seleccione un horario antes de continuar.');
                return;
            }

            if (!document.getElementById('precio_servicio_final').value) {
                actualizarPrecioServicio(servicio);
            }

            modalProducto.style.display = 'flex';
        });

        btnNoProducto.addEventListener('click', function() {
            modalProducto.style.display = 'none';
            productosInput.value = JSON.stringify([]);
            formulario.submit();
        });

        btnSiProducto.addEventListener('click', function() {
            modalProducto.style.display = 'none';
            modalProductos.style.display = 'flex';
            cargarProductos();
        });

        btnContinuarProducto.onclick = function() {
            const checkboxes = document.querySelectorAll('.chk-producto:checked');
            const seleccionados = [];

            checkboxes.forEach(chk => {
                const idProd = chk.value;
                const precioOrig = parseFloat(chk.getAttribute('data-precio-orig')) || 0;

                seleccionados.push({
                    id_producto: idProd,
                    cantidad: 1,
                    precio_aplicado: precioOrig
                });
            });

            productosInput.value = JSON.stringify(seleccionados);
            modalProductos.style.display = 'none';
            formulario.submit();
        };

        btnCancelarProducto.addEventListener('click', function() {
            modalProductos.style.display = 'none';
            modalProducto.style.display = 'flex';
        });

        // 6. CARGAR PRODUCTOS DESDE LA BD
        function cargarProductos() {
            listaProductos.innerHTML = `<p style="text-align:center; color:#ccc;">Cargando productos...</p>`;

            fetch('index.php?controller=clienteProducto&action=obtenerProductosDisponibles')
                .then(response => response.json())
                .then(productos => {
                    listaProductos.innerHTML = '';

                    if (!productos || productos.length === 0) {
                        listaProductos.innerHTML = `<p style="text-align:center; color:#ff8d8d;">No hay productos disponibles en stock.</p>`;
                        return;
                    }

                    productos.forEach(producto => {
                        const label = document.createElement('label');
                        label.className = 'item-producto';
                        label.innerHTML = `
                            <input type="checkbox" class="chk-producto" name="productoSeleccionado" 
                                   value="${producto.id_producto}"
                                   data-precio-orig="${producto.precio}"
                                   data-nombre="${producto.nombre}">
                            <span class="nombre-producto">${producto.nombre}</span>
                            <span class="precio-producto">
                                $${Number(producto.precio).toLocaleString('es-CO')}
                            </span>
                        `;
                        listaProductos.appendChild(label);
                    });
                })
                .catch(error => {
                    console.error(error);
                    listaProductos.innerHTML = `<p style="color:red; text-align:center;">❌ Error al cargar los productos.</p>`;
                });
        }
    </script>

    <script>
        const tarjetasBarberos = document.querySelectorAll('.tarjeta-barbero');
        const modalResenas = document.getElementById('modalResenasBarbero');
        const cerrarModalResenas = document.getElementById('cerrarModalResenas');
        const contenidoResenas = document.getElementById('contenidoResenasBarbero');

        function escaparHTML(texto) {
            const elemento = document.createElement('div');
            elemento.textContent = texto ?? '';
            return elemento.innerHTML;
        }

        function generarEstrellas(calificacion) {
            const valor = Math.max(0, Math.min(5, Number(calificacion) || 0));
            const llenas = Math.round(valor);

            return '★'.repeat(llenas) + '☆'.repeat(5 - llenas);
        }

        function abrirModalResenas(idBarbero) {

            modalResenas.style.display = 'flex';

            contenidoResenas.innerHTML = `
                <p class="cargando-resenas">
                    Cargando reseñas del barbero...
                </p>
            `;

            fetch(`index.php?controller=resena&action=obtenerResenasBarbero&id=${encodeURIComponent(idBarbero)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al consultar las reseñas.');
                    }

                    return response.json();
                })
                .then(data => {

                    if (!data.success) {
                        throw new Error(data.mensaje || 'No se pudo obtener la información.');
                    }

                    const barbero = data.barbero;
                    const resenas = data.resenas;

                    let contenidoLista = '';

                    if (resenas.length > 0) {

                        contenidoLista = resenas.map(resena => {

                            const comentario = resena.comentario ?
                                escaparHTML(resena.comentario) :
                                'El cliente no dejó un comentario.';

                            return `
                                <div class="resena-item">

                                    <h4>${escaparHTML(resena.nombre_cliente || 'Cliente')}</h4>

                                    <div class="estrellas-resenas">
                                        ${generarEstrellas(resena.calificacion)}
                                    </div>

                                    <p class="comentario-resena">
                                        ${comentario}
                                    </p>

                                    <span class="fecha-resena">
                                        ${escaparHTML(resena.fecha || '')}
                                    </span>

                                </div>
                            `;

                        }).join('');

                    } else {

                        contenidoLista = `
                            <p class="sin-resenas">
                                Este barbero todavía no tiene reseñas.
                                ¡Sé el primero en calificar su servicio!
                            </p>
                        `;

                    }

                    contenidoResenas.innerHTML = `

                        <div class="perfil-resenas">

                            <h2>${escaparHTML(barbero.nombre)}</h2>

                            <div class="promedio-resenas">
                                ${Number(barbero.promedio).toFixed(1)}
                            </div>

                            <div class="estrellas-resenas">
                                ${generarEstrellas(barbero.promedio)}
                            </div>

                            <p>
                                ${barbero.total} reseña(s)
                            </p>

                        </div>

                        <h3 class="titulo-lista-resenas">
                            Reseñas de clientes
                        </h3>

                        <div class="lista-resenas">
                            ${contenidoLista}
                        </div>

                    `;

                })
                .catch(error => {

                    console.error(error);

                    contenidoResenas.innerHTML = `
                        <p class="sin-resenas">
                            No fue posible cargar las reseñas.
                            Inténtalo nuevamente.
                        </p>
                    `;

                });
        }

        tarjetasBarberos.forEach(tarjeta => {

            tarjeta.addEventListener('click', function() {
                abrirModalResenas(this.dataset.barberoId);
            });

            tarjeta.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    abrirModalResenas(this.dataset.barberoId);
                }
            });

        });

        cerrarModalResenas.addEventListener('click', function() {
            modalResenas.style.display = 'none';
        });

        modalResenas.addEventListener('click', function(event) {
            if (event.target === modalResenas) {
                modalResenas.style.display = 'none';
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                modalResenas.style.display = 'none';
            }
        });
    </script>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>