<nav class="navbar-admin navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">
        <div class="d-flex align-items-center gap-3">
            <button class="toggle-sidebar btn" id="toggleSidebar" type="button">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand" href="#">
                <i class="bi bi-shield-check"></i>
                VotoSecure
            </a>
        </div>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="ms-auto d-flex align-items-center gap-3">

                <div class="dropdown">
                    <button class="user-menu-dropdown dropdown-toggle"
                            type="button"
                            id="userMenu"
                            aria-expanded="false">
                        <i class="bi bi-person-circle"></i> Administrador
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</nav>

<aside class="sidebar" id="sidebar">
    <ul class="sidebar-menu">
        <li><a href="#/dashboard" class="#"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="Votos.php"><i class="bi bi-people"></i> Votantes</a></li>
        <li><a href="Elecciones.php"><i class="bi bi-calendar-check"></i> Elecciones</a></li>
        <li><a href="Resultados.php"><i class="bi bi-bar-chart"></i> Resultados</a></li>
        <li><a href="Candidatos.php"><i class="bi bi-person-badge"></i> Candidatos</a></li>
        <li><a href="#/security"><i class="bi bi-lock"></i> Seguridad</a></li>
        <li><a href="Registros.php"><i class="bi bi-file-earmark-text"></i> Registros</a></li>
        <li><a href="Reportes.php"><i class="bi bi-printer"></i> Reportes</a></li>
    </ul>
</aside>
