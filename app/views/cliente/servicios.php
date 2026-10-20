<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios | Kadosh Barber Shop</title>
    <!-- Estilos generales y específicos -->
    <link rel="stylesheet" href="app/public/css/global.css">
    <link rel="stylesheet" href="app/public/css/servicios.css">
</head>
<body>

    <main class="servicios-container">
        <!-- Botón Volver -->
        <div class="nav-top">
            <a href="index.html" class="btn-volver">&larr; Volver al inicio</a>
        </div>

        <header class="servicios-header">
            <h1>NUESTROS SERVICIOS</h1>
            <p>Elige la mejor experiencia de corte y cuidado personal para ti</p>
        </header>

        <section class="servicios-grid">
            <?php if (!empty($servicios) && is_array($servicios)): ?>
                <?php foreach ($servicios as $servicio): ?>
                    <article class="servicio-card">
                        <div class="servicio-info">
                            <h2 class="servicio-nombre"><?= htmlspecialchars($servicio['nombre']); ?></h2>
                            <p class="servicio-descripcion"><?= htmlspecialchars($servicio['descripcion'] ?? 'Sin descripción disponible.'); ?></p>
                        </div>
                        
                        <div class="servicio-footer">
                            <span class="servicio-precio">$<?= number_format($servicio['precio'], 0, ',', '.'); ?></span>
                            <a href="index.php?controller=auth&action=loginCliente" class="btn-reservar">Reservar</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-servicios">Por el momento no hay servicios disponibles.</p>
            <?php endif; ?>
        </section>
    </main>

</body>
</html>