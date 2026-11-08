
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda del Barbero | Barber Kadosh</title>

    <link rel="stylesheet" href="app/public/css/global.css">
    <link rel="stylesheet" href="app/public/css/ayuda.css">
</head>

<body>

    <main class="contenedor-ayuda">

        <header class="encabezado-ayuda">
            <a href="index.php?controller=barbero&action=perfil" class="logout-btn">← Volver</a>
            <h1>Centro de ayuda del barbero</h1>

            <p>
                Bienvenido al centro de ayuda para barberos de
                Barber Kadosh. Aquí encontrarás información
                para consultar tus horarios, administrar tus
                reservaciones y revisar las opiniones de los clientes.
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

            <!-- HORARIOS -->
            <section class="seccion-ayuda">
                <h2>2. Consultar los horarios</h2>

                <p>
                    Desde tu perfil puedes consultar la información
                    relacionada con tus horarios.
                </p>

                <ol>
                    <li>Ingresa a tu perfil de barbero.</li>
                    <li>Ubica la sección de horarios.</li>
                    <li>Consulta los horarios que aparecen registrados.</li>
                </ol>
            </section>

            <!-- RESERVAS PENDIENTES -->
            <section class="seccion-ayuda">
                <h2>3. Consultar las reservaciones pendientes</h2>

                <p>
                    En esta sección puedes revisar las reservaciones
                    que tienes pendientes.
                </p>

                <ol>
                    <li>Ingresa a tu perfil de barbero.</li>
                    <li>Busca la sección de reservaciones pendientes.</li>
                    <li>Consulta la información de las citas registradas.</li>
                    <li>Revisa los datos de cada reservación para conocer los servicios agendados.</li>
                </ol>
            </section>

            <!-- HISTORIAL -->
            <section class="seccion-ayuda">
                <h2>4. Consultar el historial de reservaciones</h2>

                <ol>
                    <li>Ingresa a tu perfil de barbero.</li>
                    <li>Presiona el botón «Ver historial de reservaciones».</li>
                    <li>Consulta las reservaciones canceladas y completadas.</li>
                    <li>Utiliza la búsqueda si está disponible para localizar una reservación específica.</li>
                </ol>
            </section>

            <!-- RESEÑAS -->
            <section class="seccion-ayuda">
                <h2>5. Consultar reseñas y calificaciones</h2>

                <p>
                    Desde tu perfil puedes consultar las opiniones
                    y calificaciones que los clientes han dejado
                    sobre sus experiencias.
                </p>

                <ol>
                    <li>Ingresa a tu perfil de barbero.</li>
                    <li>Ubica la sección de reseñas o calificaciones.</li>
                    <li>Consulta los comentarios publicados por los clientes.</li>
                    <li>Revisa tu calificación promedio.</li>
                </ol>
            </section>

            <!-- EDITAR PERFIL -->
            <section class="seccion-ayuda">
                <h2>6. Editar el perfil</h2>

                <ol>
                    <li>Ingresa a tu perfil de barbero.</li>
                    <li>Presiona el botón «Editar perfil».</li>
                    <li>Modifica la información que necesites actualizar.</li>
                    <li>Guarda los cambios realizados.</li>
                    <li>Verifica que la información se haya actualizado correctamente.</li>
                </ol>
            </section>

            <!-- CERRAR SESIÓN -->
            <section class="seccion-ayuda">
                <h2>7. Cerrar sesión</h2>

                <p>
                    Cuando termines de utilizar el sistema, selecciona
                    la opción «Cerrar sesión» para salir de tu perfil.
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

    <!-- BUSCADOR -->
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
