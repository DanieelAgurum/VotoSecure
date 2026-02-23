<?php
session_start();
// config.php
define('BASE_URL', '/VotoSecure');
?>

<link rel="stylesheet" href="../../css/admin.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div class="layout">

    <aside class="sidebar">
        <nav>
            <a href="#" class="nav-link">
                <i class="bi bi-file-earmark"></i>
                <span>Contenido</span>
            </a>
            <a href="<?= BASE_URL ?>/Vista/Admin/Candidatos.php" class="nav-link">
                <i class="bi bi-people"></i>
                <span>Candidatos</span>
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-graph-up"></i>
                <span>Reportes</span>
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-gear"></i>
                <span>Configuración</span>
            </a>
            <a href="<?= BASE_URL ?>/logout.php" class="nav-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Salir</span>
            </a>
            <!-- Dashboard al final, centrado y con color diferente -->
            <a href="<?= BASE_URL ?>/Vista/Admin/Index.php" class="nav-link logo" style="justify-content: center; color: var(--accent); font-weight: bold; margin-top: 30px;">
                <i class="bi bi-house-door"></i>
                <span>Dashboard</span>
            </a>
        </nav>
    </aside>

    <main class="content">

        <!-- ===== HEADER / TOPBAR ===== -->
        <div class="header" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; background: var(--header-bg);">

            <div>
                <h1></h1>
                <p>Bienvenido al panel administrativo</p>

            </div>

            <div class="user-profile" style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 40px; height: 40px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                    <?= strtoupper(substr($_SESSION['admin_nombre'] ?? 'A', 0, 1)) ?>
                </div>
                <div>
                    <p style="margin:0; font-weight:600; color:#1e293b;">Administrador</p>
                    <small style="color:#64748b;"><?= $_SESSION['admin_nombre'] ?? 'Admin' ?></small>
                </div>
            </div>
        </div>