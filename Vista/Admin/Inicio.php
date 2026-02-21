<?php 
//session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <!-- CSS GLOBAL -->
    <link rel="stylesheet" href="../../css/Style.css">

    <!-- FontAwesome -->
    <link rel="stylesheet"
     href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<!-- ===== NAVBAR GLOBAL ===== -->
<?php include('../../components/Admin/Navbar.php'); ?>


<!-- ===== CONTENIDO ===== -->

<div class="content">

    <h1>Dashboard</h1>

    <!-- ===== TARJETAS ===== -->
    <div class="cards">

        <div class="card">
            <p>Total Votos</p>
            <h2>2,543</h2>
        </div>

        <div class="card">
            <p>Usuarios Activos</p>
            <h2>1,284</h2>
        </div>

        <div class="card">
            <p>Pendientes</p>
            <h2>42</h2>
        </div>

        <div class="card">
            <p>Reportes</p>
            <h2>128</h2>
        </div>
    </div>


    

</div>

</body>
</html>