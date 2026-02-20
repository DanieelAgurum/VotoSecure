<!-- <?php
// session_start();

// // Verificar autenticación
// if (!isset($_SESSION['admin_id'])) {
//     header('Location: login.php');
//     exit();
// }
?> -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo - VotoSecure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../css/admin.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <i class="bi bi-shield-lock"></i>
            <span>VotoSecure</span>
        </div>
        
        <nav>
            <a href="#" class="nav-link active">
                <i class="bi bi-house-door"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-file-earmark"></i>
                <span>Contenido</span>
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-people"></i>
                <span>Usuarios</span>
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-graph-up"></i>
                <span>Reportes</span>
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-gear"></i>
                <span>Configuración</span>
            </a>
            <a href="logout.php" class="nav-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Salir</span>
            </a>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div>
                <h1>Dashboard</h1>
                <p style="color: rgba(255, 255, 255, 0.8); margin: 5px 0;">Bienvenido al panel administrativo</p>
            </div>
            <div class="user-profile">
                <div style="width: 40px; height: 40px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="bi bi-person"></i>
                </div>
                <div>
                    <p style="margin: 0; font-weight: 600; color: #1e293b;">Administrador</p>
                    <small style="color: #64748b;">Admin</small>
                </div>
            </div>
        </div>
        
        <!-- Stats Row -->
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <div class="stat-label">Total Votos</div>
                            <div class="stat-number">2,543</div>
                            <small style="color: var(--success);"><i class="bi bi-arrow-up"></i> 12% este mes</small>
                        </div>
                        <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="stat-card success">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <div class="stat-label">Usuarios Activos</div>
                            <div class="stat-number">1,284</div>
                            <small style="color: var(--success);"><i class="bi bi-arrow-up"></i> 8% este mes</small>
                        </div>
                        <div class="stat-icon"><i class="bi bi-people"></i></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="stat-card warning">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <div class="stat-label">Pendientes</div>
                            <div class="stat-number">42</div>
                            <small style="color: var(--danger);"><i class="bi bi-arrow-down"></i> -5% este mes</small>
                        </div>
                        <div class="stat-icon"><i class="bi bi-hourglass"></i></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="stat-card danger">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <div class="stat-label">Reportes</div>
                            <div class="stat-number">8</div>
                            <small style="color: var(--danger);"><i class="bi bi-arrow-up"></i> 2 nuevos</small>
                        </div>
                        <div class="stat-icon"><i class="bi bi-exclamation-circle"></i></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Row -->
        <div class="row mt-4">
            <div class="col-lg-6">
                <div class="chart-container">
                    <div class="chart-title">Votos por Día</div>
                    <canvas id="votosChart"></canvas>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="chart-container">
                    <div class="chart-title">Distribución de Votos</div>
                    <canvas id="distribucionChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4">
                <div class="chart-container">
                    <div class="chart-title">Actividad Mensual</div>
                    <canvas id="actividadChart"></canvas>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="chart-container">
                    <div class="chart-title">Estadísticas de Usuarios</div>
                    <canvas id="usuariosChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Gráfica de Votos por Día
        const votosCtx = document.getElementById('votosChart').getContext('2d');
        new Chart(votosCtx, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sab', 'Dom'],
                datasets: [{
                    label: 'Votos',
                    data: [340, 400, 350, 450, 490, 430, 400],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });
        
        // Gráfica de Distribución
        const distribucionCtx = document.getElementById('distribucionChart').getContext('2d');
        new Chart(distribucionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Opción A', 'Opción B', 'Opción C'],
                datasets: [{
                    data: [45, 35, 20],
                    backgroundColor: ['#2563eb', '#10b981', '#f59e0b'],
                    borderColor: '#fff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Gráfica de Actividad
        const actividadCtx = document.getElementById('actividadChart').getContext('2d');
        new Chart(actividadCtx, {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
                datasets: [{
                    label: 'Actividad',
                    data: [1200, 1900, 1500, 2200, 1800],
                    backgroundColor: '#2563eb',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                indexAxis: 'x',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // Gráfica de Usuarios
        const usuariosCtx = document.getElementById('usuariosChart').getContext('2d');
        new Chart(usuariosCtx, {
            type: 'bar',
            data: {
                labels: ['Nuevos', 'Activos', 'Inactivos', 'Bloqueados'],
                datasets: [{
                    label: 'Usuarios',
                    data: [250, 1284, 320, 89],
                    backgroundColor: ['#10b981', '#2563eb', '#f59e0b', '#ef4444'],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>