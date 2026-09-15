<?php
if (!isset($_SESSION['id'])) {
    header("Location: index.php?controller=auth&action=loginCliente");
    exit;
}

$servicios        = $servicios ?? [];
$barberos         = $barberos ?? [];
$productos        = $productos ?? [];
$promosServicios  = $promosServicios ?? [];
$promosProductos  = $promosProductos ?? [];

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
                <select name="servicio" id="servicio" required onchange="verificarPromoServicio(this)">
                    <option value="">Seleccione un servicio...</option>
                    <?php foreach ($servicios as $servicio):
                        $idServ = $servicio['id_servicio'];
                        $hasPromo = isset($promosServicios[$idServ]);
                        $desc = $hasPromo ? $promosServicios[$idServ]['descuento'] : 0;
                    ?>
                        <option value="<?= htmlspecialchars($idServ) ?>"
                            data-precio="<?= htmlspecialchars($servicio['precio']) ?>"
                            data-descuento="<?= $desc ?>">
                            <?= htmlspecialchars($servicio['nombre']) ?> - $<?= number_format($servicio['precio'], 0, ',', '.') ?>
                            <?= $hasPromo ? " ({$desc}% OFF)" : '' ?>
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

            <!-- CAMPOS OCULTOS DE PROMOCIONES Y PRODUCTOS -->
            <input type="hidden" name="precio_servicio_final" id="precio_servicio_final">
            <input type="hidden" name="productos_seleccionados" id="productos_seleccionados" value="">

            <button type="submit">CONFIRMAR Y AGENDAR CITA</button>

        </form>

        <!-- SECCIÓN BARBEROS DISPONIBLES (5 DÍAS) -->
        <div class="seccion-barberos">
            <h2 class="titulo-seccion-barberos">💈 Barberos Disponibles</h2>

            <div class="grid-barberos">
                <?php if (!empty($barberos)): ?>
                    <?php foreach ($barberos as $barbero): ?>
                        <?php
                        $fotoBD = !empty($barbero['foto']) ? trim($barbero['foto']) : '';
                        $fotoBarbero = !empty($fotoBD) ? $fotoBD : 'app/public/assets/img/avatar.png';
                        ?>
                        <div class="tarjeta-barbero">
                            <div class="avatar-barbero-container">
                                <img src="<?= htmlspecialchars($fotoBarbero); ?>"
                                    class="avatar-barbero"
                                    alt="Foto de <?= htmlspecialchars($barbero['nombre']); ?>"
                                    onerror="this.onerror=null; this.src='app/public/assets/img/avatar.png';">
                            </div>

                            <div class="info-barbero">
                                <h3>
                                    <?php
                                    $nombreBarbero = trim(($barbero['nombre'] ?? '') . ' ' . ($barbero['apellido'] ?? ''));
                                    echo !empty($nombreBarbero) ? htmlspecialchars($nombreBarbero) : 'Barbero sin nombre';
                                    ?>
                                </h3>

                                <?php if (!empty($barbero['telefono'])): ?>
                                    <p class="telefono-barbero">📱 <?= htmlspecialchars($barbero['telefono']); ?></p>
                                <?php endif; ?>

                                <span class="badge-disponible">Disponible 5 días seguidos</span>
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

    <!-- MODAL CONFIRMACIÓN PROMO SERVICIO -->
    <div id="modalPromoServicio" class="modal-producto-overlay">
        <div class="modal-producto-content">
            <h2>¡Promoción Disponible!</h2>
            <p id="textoPromoServicio"></p>
            <div class="modal-producto-botones">
                <button type="button" id="btnAplicarPromoServicio" class="btn-producto-si">Sí, aplicar descuento</button>
                <button type="button" id="btnRechazarPromoServicio" class="btn-producto-no">No, precio normal</button>
            </div>
        </div>
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
                    $hasPromo = isset($promosProductos[$idProd]);
                    $desc = $hasPromo ? $promosProductos[$idProd]['descuento'] : 0;
                ?>
                    <div class="item-producto">
                        <input type="checkbox" class="chk-producto"
                            value="<?= $idProd ?>"
                            data-precio-orig="<?= $prod['precio'] ?>"
                            data-descuento="<?= $desc ?>"
                            data-nombre="<?= htmlspecialchars($prod['nombre']) ?>">
                        <span class="nombre-producto"><?= htmlspecialchars($prod['nombre']) ?></span>
                        <span class="precio-producto">
                            $<?= number_format($prod['precio'], 0, ',', '.') ?>
                            <?= $hasPromo ? " ({$desc}% OFF)" : '' ?>
                        </span>
                    </div>
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
        let precioServicioBase = 0;

        function verificarPromoServicio(select) {
            const option = select.options[select.selectedIndex];
            if (!option.value) return;

            precioServicioBase = parseFloat(option.getAttribute('data-precio'));
            const descuento = parseFloat(option.getAttribute('data-descuento'));

            if (descuento > 0) {
                const precioConDesc = precioServicioBase * (1 - (descuento / 100));
                document.getElementById('textoPromoServicio').innerText =
                    `Este servicio tiene un descuento del ${descuento}%. El precio bajará de $${precioServicioBase.toLocaleString()} a $${precioConDesc.toLocaleString()}. ¿Desea aplicarlo?`;

                document.getElementById('modalPromoServicio').style.display = 'flex';

                document.getElementById('btnAplicarPromoServicio').onclick = function() {
                    document.getElementById('precio_servicio_final').value = precioConDesc;
                    document.getElementById('modalPromoServicio').style.display = 'none';
                };

                document.getElementById('btnRechazarPromoServicio').onclick = function() {
                    document.getElementById('precio_servicio_final').value = precioServicioBase;
                    document.getElementById('modalPromoServicio').style.display = 'none';
                };
            } else {
                document.getElementById('precio_servicio_final').value = precioServicioBase;
            }
        }

        // Modal Productos
        document.getElementById('btnSiProducto').onclick = function() {
            document.getElementById('modalProducto').style.display = 'none';
            document.getElementById('modalProductos').style.display = 'flex';
        };

        document.getElementById('btnNoProducto').onclick = function() {
            document.getElementById('modalProducto').style.display = 'none';
            document.getElementById('formAgendar').submit();
        };

        document.getElementById('btnContinuarProducto').onclick = function() {
            const seleccionados = [];
            const checkboxes = document.querySelectorAll('.chk-producto:checked');

            checkboxes.forEach(chk => {
                const idProd = chk.value;
                const desc = parseFloat(chk.getAttribute('data-descuento'));
                const precioOrig = parseFloat(chk.getAttribute('data-precio-orig'));

                let aplicarDesc = true;
                if (desc > 0) {
                    aplicarDesc = confirm(`El producto "${chk.getAttribute('data-nombre')}" tiene un ${desc}% de descuento. ¿Desea aplicarlo?`);
                }

                const precioFinal = (aplicarDesc && desc > 0) ? precioOrig * (1 - (desc / 100)) : precioOrig;

                seleccionados.push({
                    id_producto: idProd,
                    cantidad: 1,
                    precio_aplicado: precioFinal
                });
            });

            document.getElementById('productos_seleccionados').value = JSON.stringify(seleccionados);
            document.getElementById('modalProductos').style.display = 'none';
            document.getElementById('formAgendar').submit();
        };

        document.getElementById('btnCancelarProducto').onclick = function() {
            document.getElementById('modalProductos').style.display = 'none';
        };
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
                const desc = parseFloat(chk.getAttribute('data-descuento')) || 0;
                const precioOrig = parseFloat(chk.getAttribute('data-precio-orig')) || 0;
                const nombre = chk.getAttribute('data-nombre');

                // 1️⃣ Leemos si es 'producto' o 'servicio'
                const tipo = chk.getAttribute('data-tipo') || 'producto';

                let aplicarDesc = true;

                // 2️⃣ Usamos la variable "tipo" en el confirm
                if (desc > 0) {
                    aplicarDesc = confirm(`Este ${tipo} "${nombre}" tiene una promoción del ${desc}%. ¿Desea aplicarla?`);
                }

                const precioFinal = (aplicarDesc && desc > 0) ? precioOrig * (1 - (desc / 100)) : precioOrig;

                seleccionados.push({
                    id_producto: idProd,
                    cantidad: 1,
                    precio_aplicado: precioFinal
                });
            });

            document.getElementById('productos_seleccionados').value = JSON.stringify(seleccionados);
            document.getElementById('modalProductos').style.display = 'none';
            document.getElementById('formAgendar').submit();
        };

        btnCancelarProducto.addEventListener('click', function() {
            modalProductos.style.display = 'none';
            modalProducto.style.display = 'flex';
        });

        // 6. CARGAR PRODUCTOS DESDE LA BD CON LA ESTRUCTURA CORRECTA
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
                        const desc = producto.descuento || 0; // Si el endpoint envía el % de descuento
                        const label = document.createElement('label');
                        label.className = 'item-producto';
                        label.innerHTML = `
                    <input type="checkbox" class="chk-producto" name="productoSeleccionado" 
                           value="${producto.id_producto}"
                           data-tipo="producto"
                           data-precio-orig="${producto.precio}"
                           data-descuento="${desc}"
                           data-nombre="${producto.nombre}">
                    <span class="nombre-producto">${producto.nombre}</span>
                    <span class="precio-producto">
                        $${Number(producto.precio).toLocaleString('es-CO')}
                        ${desc > 0 ? ` (${desc}% OFF)` : ''}
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

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174101-A2E9JALD.js" defer></script>

</body>

</html>