<?php
require_once __DIR__ . '/../Controlador/LoginCtrl.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = $_POST['email'] ?? '';
    $contrasena = $_POST['password'] ?? '';

    $loginCtrl = new LoginCtrl();
    $loginCtrl->iniciarSesion($correo, $contrasena);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - VotoSecure</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>

<div class="login-container">
    <div class="login-card">

        <div class="login-header text-center">
            <h1>VotoSecure</h1>
            <p>Sistema de Votación Electrónica Segura</p>
        </div>

        <div class="login-body">
            <form id="loginForm">

                <div class="mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Iniciar Sesión
                </button>

            </form>

            <div id="mensaje" class="mt-3 text-center"></div>

        </div>
    </div>
</div>

<script>
document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {

            if (data.rol == 2) {
                window.location.href = "../Vista/admin/dashboard.php";
            } else {
                window.location.href = "../Vista/usuario/home.php";
            }

        } else {
            document.getElementById("mensaje").innerHTML =
                '<div class="alert alert-danger">' + data.message + '</div>';
        }

    })
    .catch(error => {
        document.getElementById("mensaje").innerHTML =
            '<div class="alert alert-danger">Error en el servidor</div>';
        console.error(error);
    });
});
</script>

</body>
</html>