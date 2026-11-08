
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda del Cliente | Barber Kadosh </title>
    
    <link rel="stylesheet" href="./app/public/css/global.css">
    <link rel="stylesheet" href="./app/public/css/ayuda.css">
</head>

<body>

    <main class="contenedor-ayuda">

        <header class="encabezado-ayuda">
            <a href="index.php?controller=gestionCita&action=misCitas" class="logout-btn">← Volver</a>
            <h1>Centro de ayuda del cliente</h1>
            <p>
                Bienvenido al centro de ayuda de Barber Kadosh.
                Aquí encontrarás información para administrar tus
                reservaciones y utilizar las opciones de tu perfil.
            </p>
            <br>
        </header>

        <input
            type="search"
            id="buscador"
            placeholder="¿Qué necesitas saber?"
            aria-label="Buscar en la ayuda"
        >

        <div id="contenido-ayuda">

            <!-- RESERVAR UNA CITA -->
            <section class="seccion-ayuda">
                <h2>1. Reservar una cita</h2>

                <p>
                    Para realizar una reservación, debes ingresar
                    a tu cuenta de cliente.
                </p>

                <ol>
                    <li>Ubicar la opción de agendar cita.</li>
                    <li>Escoge el servicio que deseas recibir.</li>
                    <li>Selecciona el barbero de tu preferencia.</li>
                    <li>Elige una fecha disponible.</li>
                    <li>Selecciona uno de los horarios disponibles.</li>
                    <li>Revisa los datos de la reservación.</li>
                    <li>Si te preguntan si deseas agregar un producto, selecciona uno o marca «No».</li>
                    <li>Confirma la reservación.</li>
                </ol>

                <p class="nota-ayuda">
                    Recuerda verificar el servicio, el barbero,
                    la fecha y la hora antes de confirmar.
                </p>
            </section>

            <!-- MODIFICAR RESERVACIÓN -->
            <section class="seccion-ayuda">
                <h2>2. Modificar una reservación</h2>

                <ol>
                    <li>Ingresa a tu perfil de cliente.</li>
                    <li>Busca la reservación que deseas modificar.</li>
                    <li>Selecciona la opción para modificarla.</li>
                    <li>Realiza los cambios siguiendo las opciones del sistema.</li>
                    <li>Revisa los datos actualizados.</li>
                    <li>Confirma los cambios.</li>
                </ol>

                <p class="nota-ayuda">
                    Después de modificarla, la reservación permanece
                    en la tabla con la información actualizada.
                </p>
            </section>

            <!-- CANCELAR RESERVACIÓN -->
            <section class="seccion-ayuda">
                <h2>3. Cancelar una reservación</h2>

                <ol>
                    <li>Ingresa a tu perfil de cliente.</li>
                    <li>Ubica la reservación que deseas cancelar.</li>
                    <li>Selecciona la opción para cancelar.</li>
                    <li>Confirma la acción si el sistema lo solicita.</li>
                </ol>

                <p class="nota-ayuda">
                    La reservación cancelada deja de aparecer en la
                    tabla de reservas activas y pasa al historial.
                </p>
            </section>

            <!-- HISTORIAL -->
            <section class="seccion-ayuda">
                <h2>4. Consultar el historial de reservaciones</h2>

                <ol>
                    <li>Ingresa a tu perfil de cliente.</li>
                    <li>Presiona el botón «Ver historial de reservaciones».</li>
                    <li>Consulta las reservaciones canceladas y completadas.</li>
                    <li>Utiliza la búsqueda si está disponible para encontrar una reservación específica.</li>
                </ol>
            </section>

            <!-- EDITAR PERFIL -->
            <section class="seccion-ayuda">
                <h2>5. Editar el perfil</h2>

                <ol>
                    <li>Ingresa a tu perfil de cliente.</li>
                    <li>Presiona el botón «Editar perfil».</li>
                    <li>Modifica los datos que necesites actualizar.</li>
                    <li>Guarda los cambios.</li>
                    <li>Verifica que la información se haya actualizado correctamente.</li>
                </ol>
            </section>

            <!-- RESEÑAS -->
            <section class="seccion-ayuda">
                <h2>6. Dejar una reseña</h2>

                <p>
                    Cuando una reservación se encuentra completada,
                    el sistema puede ofrecerte la opción de dejar
                    una reseña sobre tu experiencia.
                </p>

                <ol>
                    <li>Cuando aparezca la pregunta, selecciona «Sí» si deseas dejar una reseña.</li>
                    <li>Escribe un comentario sobre tu experiencia.</li>
                    <li>Selecciona la calificación que deseas otorgar.</li>
                    <li>Envía la reseña.</li>
                </ol>

                <p class="nota-ayuda">
                    Dejar una reseña es opcional. Si no deseas hacerlo,
                    puedes seleccionar «No».
                </p>
            </section>

            <!-- CERRAR SESIÓN -->
            <section class="seccion-ayuda">
                <h2>7. Cerrar sesión</h2>

                <p>
                    Cuando termines de utilizar tu cuenta, selecciona
                    la opción «Cerrar sesión» para salir de tu perfil
                    de manera segura.
                </p>
            </section>

        </div>

        <p id="sin-resultados" hidden>
            No se encontraron resultados para tu búsqueda.
        </p>

    </main>

    <footer class="footer">

        <div class="footer-container">

            <div class="footer-section">
                <h4>Enlaces</h4>

                <a href="http://localhost/barberkadosh/index.html">Inicio</a>
                <a href="index.php?controller=auth&action=servicios">Servicios</a>
                <a href="#">Política de Privacidad</a>
            </div>

            <div class="footer-section">
                <h4>Contacto</h4>

                <p>📍 Bogotá - Colombia</p>
                <a href="tel:+573003593276">📞 +573003593276</a>
                <a href="mailto:kadosh1234@gmail.com">✉️ kadosh1234@gmail.com</a>
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
            <p>
                © 2026 <strong>KADOSH Barber Shop</strong>. Todos los derechos reservados.
                | Versión 1.0
            </p>
        </div>

    </footer>

    <!-- BUSCADOR DE CONTENIDO -->
    <script>
        const buscador = document.getElementById("buscador");
        const secciones = document.querySelectorAll(".seccion-ayuda");
        const mensaje = document.getElementById("sin-resultados");

        buscador.addEventListener("input", function () {
            const texto = this.value
                .trim()
                .toLocaleLowerCase("es");

            let encontrados = 0;

            secciones.forEach(function (seccion) {
                const contenido = seccion.textContent
                    .toLocaleLowerCase("es");

                const coincide = contenido.includes(texto);

                seccion.hidden = !coincide;

                if (coincide) {
                    encontrados++;
                }
            });

            mensaje.hidden = encontrados > 0;
        });
    </script>

</body>
</html>
