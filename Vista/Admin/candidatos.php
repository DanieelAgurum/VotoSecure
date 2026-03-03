<?php
session_start();
require_once("../../Modelo/candidatosMdl.php");
require_once(__DIR__ . "/../../Controlador/candidatosCtrl.php");
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header('Location: /VotoSecure/Vista/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo Candidatos - VotoSecure</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/dash.css">
</head>

<body>
<?php include __DIR__ . '/../../components/Admin/Navbar.php'; ?>

<form action="../../Controlador/candidatosCtrl.php" method="POST">

    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="text" name="apellido" placeholder="Apellido" required>

    <input type="number" name="id_partido" placeholder="ID Partido" required>
    <input type="number" name="id_tipo" placeholder="ID Tipo" required>

    <input type="text" name="cargo" placeholder="Cargo" required>
    <input type="text" name="distrito" placeholder="Distrito" required>

    <input type="email" name="correo" placeholder="Correo" required>
    <input type="text" name="telefono" placeholder="Teléfono">

    <select name="estatus">
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
    </select>

    <button type="submit">Guardar</button>

</form>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../js/dash.js"></script>
</body>
</html>