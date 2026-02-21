<?php 
//session_start(); ?>

<link rel="stylesheet" href="../../css/admin.css">

<div class="layout">

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">

        <div class="logo">
            🛡️ <span>VotoSecure</span>
        </div>

        <ul class="menu">
            <li ><a href="inicio.php">🏠 Dashboard</a></li>
            <li><a href="contenido.php">📄 Contenido</a></li>
            <li><a href="Candidatos.php">👥 Candidatos</a></li>
            <li><a href="reportes.php">📊 Reportes</a></li>
            <li><a href="configuracion.php">⚙️ Configuración</a></li>
            <li><a href="../../logout.php">🚪 Salir</a></li>
        </ul>

    </aside>


    <!-- ===== CONTENEDOR PRINCIPAL ===== -->
    <main class="content">

        <!-- ===== TOPBAR ===== -->
        <div class="topbar">

            <div>
                <h1>Dashboard</h1>
                <p>Bienvenido al panel administrativo</p>
            </div>

            <div class="user">
                <div class="avatar">
                    <?= strtoupper(substr($_SESSION['admin_nombre'] ?? 'A',0,1)) ?>
                </div>

                <div>
                    <small>Administrador</small>
                    <b><?= $_SESSION['admin_nombre'] ?? 'Admin' ?></b>
                </div>
            </div>

        </div>