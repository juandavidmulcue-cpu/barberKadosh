<?php

$codigoError = $_GET['codigo'] ?? ($codigoError ?? 404);
$codigoError = (int) $codigoError;

$imagenesErrores = [
    403 => 'error403.jpg',
    404 => 'error404.jpg',
    429 => 'error429.jpg',
    500 => 'error500.jpg',
    502 => 'error502.jpg',
    504 => 'error504.jpg'
];

$imagen = $imagenesErrores[$codigoError] ?? 'error404.jpg';

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Error <?php echo $codigoError; ?> - KADOSH</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            background-color: #000;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .error-imagen {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .error-imagen img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
</head>

<body>

    <div class="error-imagen">
        <img
            src="/barberKadosh/app/public/assets/img/errores/<?php echo $imagen; ?>"
            alt="Error <?php echo $codigoError; ?>">
    </div>

</body>

</html>