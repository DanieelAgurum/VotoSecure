<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 403 - Acceso Denegado | VotoSecure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/VotoSecure/css/estilos.css">
</head>

<body>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/VotoSecure/components/nav.php'; ?>

    <div class="error-container">
        <div class="vs-404-card">
            <div class="vs-404-code">403</div>

            <div class="vs-404-title">
                Acceso Denegado
            </div>

            <p class="vs-404-description">
                No tienes permisos para acceder a esta página.
                Si crees que esto es un error, contacta al administrador
                del sistema para verificar tus permisos.
            </p>

            <div class="vs-404-actions">
                <a href="/VotoSecure/" class="vs-404-btn-primary">Ir al Inicio</a>
                <a href="javascript:history.back()" class="vs-404-btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</body>

</html>

