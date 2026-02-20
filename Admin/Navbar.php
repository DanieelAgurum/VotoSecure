<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Prueba Navbar</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- ✅ TU CSS -->
<link rel="stylesheet" href="Style.css">

</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-lock-open"></i>
        </div>
        <h5>VotoSecure</h5>
    </div>

    <ul class="sidebar-menu">
        <li><a href="#"><i class="fas fa-th-large"></i> <span>Dashboard</span></a></li>
        <li><a href="#"><i class="fas fa-users"></i> <span>Usuarios</span></a></li>
    </ul>
</aside>

<!-- MAIN -->
<div class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <button class="toggle-btn" id="toggleBtn">
                <i class="fas fa-bars"></i>
            </button>

            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar...">
            </div>
        </div>

        <div class="topbar-right">
            <div class="notification-badge">
                <i class="fas fa-bell"></i>
                <span class="badge">3</span>
            </div>

            <div class="user-profile">
                <div class="avatar">
                    <?= strtoupper(substr($_SESSION['admin_nombre'] ?? 'A', 0, 1)) ?>
                </div>
                <div>
                    <small style="color:#999;">Administrador</small>
                    <p style="margin:0;font-weight:600;">
                        <?= $_SESSION['admin_nombre'] ?? 'Admin' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <h1 style="color:white;">Contenido de prueba</h1>
</div>

<!-- ✅ TU JS -->
<script src="admin.js"></script>

</body>
</html>