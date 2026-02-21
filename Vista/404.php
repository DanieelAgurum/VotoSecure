<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404 - Votación No Encontrada | VotoSecure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/estilos.css">
    </style>
</head>

<body>
    <?php include '../components/nav.php'; ?>

    <div class="error-container">
        <div class="vs-404-card">
            <div class="vs-404-code">404</div>

            <div class="vs-404-title">
                Votación no encontrada
            </div>

            <p class="vs-404-description">
                La votación que intentas consultar no existe, ha finalizado
                o el enlace no es válido. Verifica la URL o regresa al inicio
                para continuar navegando en la plataforma.
            </p>

            <div class="vs-404-actions">
                <a href="/" class="vs-404-btn-primary">Ir al Inicio</a>
                <a href="javascript:history.back()" class="vs-404-btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</body>

</html>