<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar que sea admin
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header('Location: /VotoSecure/Vista/login.php');
    exit();
}

$correoAdmin = $_SESSION['correo'];
?>

<nav class="navbar-admin navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">
        <div class="d-flex align-items-center gap-3">
            <button class="toggle-sidebar btn btn-outline-light" id="toggleSidebar" type="button">
                <i class="bi bi-arrow-left-short text-dark"></i>
            </button>
            <a class="navbar-brand" href="#">
                <i class="bi bi-shield-check"></i>
                VotoSecure
            </a>
        </div>

        <button class="navbar-toggler bg-light" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon text-white bg-light"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="ms-auto d-flex align-items-center gap-3">

                <div class="dropdown">
                    <button class="user-menu-dropdown dropdown-toggle"
                        type="button"
                        id="userMenu"
                        aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($correoAdmin); ?>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Configuración</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="/VotoSecure/Controlador/LoginCtrl.php?action=logout"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</nav>
<aside class="sidebar" id="sidebar">
    <ul class="sidebar-menu">

        <li><a href="index"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="votantes"><i class="bi bi-people"></i> Votantes</a></li>
        <li><a href="elecciones"><i class="bi bi-calendar-check"></i> Elecciones</a></li>
        <li><a href="resultados"><i class="bi bi-bar-chart"></i> Resultados</a></li>
        <li><a href="partidos"><i class="bi bi-building-fill-check"></i> Partidos</a></li>
        <li><a href="casillas"><i class="bi bi-clipboard-check"></i> Casillas</a></li>
        <li><a href="propuestas"><i class="bi bi-lightbulb"></i> Propuestas</a></li>
        <li><a href="candidatos"><i class="bi bi-person-badge"></i> Candidatos</a></li>
        <li><a href="reportes"><i class="bi bi-printer"></i> Reportes</a></li>

    </ul>
</aside>