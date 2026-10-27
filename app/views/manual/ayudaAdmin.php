<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ayuda del Administrador | Barber Kadosh</title>

    <link rel="stylesheet" href="app/public/css/global.css">
    <link rel="stylesheet" href="app/public/css/ayuda.css">
</head>

<body>

    <main class="contenedor-ayuda">

        <header class="encabezado-ayuda">
            <a href="index.php?controller=admin&action=panel" class="logout-btn">← Volver</a>
            <h1>Centro de ayuda del administrador</h1>

            <p>
                Bienvenido al centro de ayuda del administrador
                de Barber Kadosh. Aquí encontrarás una guía
                para utilizar las opciones de administración del sistema.
            </p>
            <br>
        </header>

        <input
            type="search"
            id="buscador"
            placeholder="¿Qué necesitas saber?"
            aria-label="Buscar en la ayuda">

        <div id="contenido-ayuda">


            <!-- PANEL ADMINISTRATIVO -->
            <section class="seccion-ayuda">
                <h2>2. Conocer el panel de administración</h2>

                <p>
                    El panel administrativo permite acceder a las
                    opciones de gestión disponibles para el administrador.
                </p>

                <ol>
                    <li>Ingresa al panel de administración.</li>
                    <li>Identifica las secciones disponibles en el menú.</li>
                    <li>Selecciona la sección que necesitas consultar o administrar.</li>
                </ol>
            </section>

            <!-- GESTIÓN DE BARBEROS Y EMPLEADOS -->
            <section class="seccion-ayuda">
                <h2>3. Gestionar barberos y empleados</h2>

                <p>
                    Desde la sección correspondiente puedes consultar
                    y administrar la información de los barberos o
                    empleados registrados, según las opciones disponibles.
                </p>

                <ol>
                    <li>Abre la sección de barberos o empleados.</li>
                    <li>Consulta los registros disponibles.</li>
                    <li>Selecciona la opción correspondiente para gestionar la información.</li>
                    <li>Verifica los datos antes de guardar cualquier cambio.</li>
                </ol>
            </section>

            <!-- CLIENTES -->
            <section class="seccion-ayuda">
                <h2>4. Consultar la información de los clientes</h2>

                <ol>
                    <li>Accede a la sección de clientes, si está disponible en el panel.</li>
                    <li>Consulta los clientes registrados.</li>
                    <li>Busca el registro que necesitas revisar.</li>
                    <li>Utiliza las opciones disponibles para consultar o administrar la información.</li>
                </ol>
            </section>

            <!-- SERVICIOS -->
            <section class="seccion-ayuda">
                <h2>5. Gestionar los servicios</h2>

                <p>
                    Si el panel cuenta con una sección de servicios,
                    puedes acceder a ella para consultar los servicios
                    registrados y utilizar las opciones de gestión disponibles.
                </p>

                <ol>
                    <li>Abre la sección de servicios.</li>
                    <li>Consulta los servicios registrados.</li>
                    <li>Selecciona la opción de gestión que necesites.</li>
                    <li>Verifica los datos antes de guardar los cambios.</li>
                </ol>
            </section>

            <!-- PRODUCTOS -->
            <section class="seccion-ayuda">
                <h2>6. Gestionar productos</h2>

                <p>
                    Si tu panel incluye la gestión de productos,
                    entra en la sección correspondiente para consultar
                    los registros y las opciones disponibles.
                </p>

                <ol>
                    <li>Accede a la sección de productos.</li>
                    <li>Consulta la información registrada.</li>
                    <li>Utiliza las opciones que estén habilitadas en el sistema.</li>
                </ol>
            </section>


            <!-- CONSULTAR Y ASIGNAR HORARIOS -->
            <section class="seccion-ayuda">
                <h2>7. Consultar y asignar horarios</h2>

                <p>
                    Desde la sección de horarios, el administrador puede
                    consultar la programación de los barberos y asignarles
                    horarios de acuerdo con el día que corresponda.
                </p>

                <h3>Consultar horarios</h3>

                <ol>
                    <li>Ingresa al panel de administración.</li>
                    <li>Busca la sección de horarios.</li>
                    <li>Consulta la programación de los barberos y los horarios asignados.</li>
                </ol>

                <h3>Asignar un horario a un barbero</h3>

                <ol>
                    <li>Ingresa a la sección de horarios.</li>
                    <li>Ubica al barbero al que deseas asignarle un horario.</li>
                    <li>Arrastra al barbero hasta el día de la semana en el que deseas programarlo.</li>
                    <li>Asigna la hora de inicio de su jornada.</li>
                    <li>Asigna la hora de finalización de su jornada.</li>
                    <li>Verifica que el día y las horas sean correctos.</li>
                    <li>Guarda o confirma la asignación si el sistema lo solicita.</li>
                </ol>

                <p class="nota-ayuda">
                    Antes de finalizar, verifica que el barbero haya quedado
                    asignado al día correcto y que las horas de inicio y fin
                    correspondan al horario que deseas establecer.
                </p>
            </section>


            <!-- PERFIL -->
            <section class="seccion-ayuda">
                <h2>8. Editar el perfil del administrador</h2>

                <ol>
                    <li>Ingresa al perfil del administrador.</li>
                    <li>Presiona el botón «Editar perfil».</li>
                    <li>Actualiza los datos que necesites modificar.</li>
                    <li>Guarda los cambios.</li>
                    <li>Verifica que la información se haya actualizado correctamente.</li>
                </ol>
            </section>

            <!-- REPORTES -->
            <section class="seccion-ayuda">
                <h2>9. Consultar o exportar información</h2>

                <p>
                    Si el sistema cuenta con opciones de reportes o
                    exportación, utiliza la sección correspondiente
                    para consultar o descargar la información disponible.
                </p>

                <ol>
                    <li>Busca la opción de reportes o exportación.</li>
                    <li>Selecciona la información que necesitas consultar.</li>
                    <li>Utiliza las opciones que ofrezca el sistema para visualizarla o exportarla.</li>
                </ol>
            </section>

            <!-- CERRAR SESIÓN -->
            <section class="seccion-ayuda">
                <h2>10. Cerrar sesión</h2>

                <p>
                    Cuando termines de utilizar el sistema,
                    selecciona la opción «Cerrar sesión» para
                    salir del perfil administrativo.
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

        buscador.addEventListener("input", function() {
            const texto = this.value
                .trim()
                .toLocaleLowerCase("es");

            let encontrados = 0;

            secciones.forEach(function(seccion) {
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