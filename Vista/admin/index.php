<?php
// session_start();
// if (!isset($_SESSION['admin_id'])) {
//     header('Location: login.php');
//     exit();
// }
?>

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
    <link rel="stylesheet" href="../../css/dash.css">
</head>

<body>
<?php include __DIR__ . '/../../components/Admin/Navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 col-lg-10 main-content">
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div style="display:flex;justify-content:space-between;">
                            <div>
                                <div class="stat-label">Total Votos</div>
                                <div class="stat-number">2,543</div>
                                <small style="color:var(--success);">
                                    <i class="bi bi-arrow-up"></i> 12% este mes
                                </small>
                            </div>
                            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="stat-card success">
                        <div style="display:flex;justify-content:space-between;">
                            <div>
                                <div class="stat-label">Usuarios Activos</div>
                                <div class="stat-number">1,284</div>
                                <small style="color:var(--success);">
                                    <i class="bi bi-arrow-up"></i> 8% este mes
                                </small>
                            </div>
                            <div class="stat-icon"><i class="bi bi-people"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="stat-card warning">
                        <div style="display:flex;justify-content:space-between;">
                            <div>
                                <div class="stat-label">Pendientes</div>
                                <div class="stat-number">42</div>
                                <small style="color:var(--danger);">
                                    <i class="bi bi-arrow-down"></i> -5% este mes
                                </small>
                            </div>
                            <div class="stat-icon"><i class="bi bi-hourglass"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="stat-card danger">
                        <div style="display:flex;justify-content:space-between;">
                            <div>
                                <div class="stat-label">Reportes</div>
                                <div class="stat-number">8</div>
                                <small style="color:var(--danger);">
                                    <i class="bi bi-arrow-up"></i> 2 nuevos
                                </small>
                            </div>
                            <div class="stat-icon"><i class="bi bi-exclamation-circle"></i></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- GRÁFICAS -->
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../js/dash.js"></script>
<script>
// Votos por día
new Chart(document.getElementById('votosChart'), {
    type: 'line',
    data: {
        labels: ['Lun','Mar','Mié','Jue','Vie','Sab','Dom'],
        datasets: [{
            data: [340,400,350,450,490,430,400],
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: { responsive: true }
});

// Distribución
new Chart(document.getElementById('distribucionChart'), {
    type: 'doughnut',
    data: {
        labels: ['Opción A','Opción B','Opción C'],
        datasets: [{
            data: [45,35,20],
            backgroundColor: ['#2563eb','#10b981','#f59e0b']
        }]
    }
});

// Actividad
new Chart(document.getElementById('actividadChart'), {
    type: 'bar',
    data: {
        labels: ['Ene','Feb','Mar','Abr','May'],
        datasets: [{
            data: [1200,1900,1500,2200,1800],
            backgroundColor: '#2563eb'
        }]
    }
});

// Usuarios
new Chart(document.getElementById('usuariosChart'), {
    type: 'bar',
    data: {
        labels: ['Nuevos','Activos','Inactivos','Bloqueados'],
        datasets: [{
            data: [250,1284,320,89],
            backgroundColor: ['#10b981','#2563eb','#f59e0b','#ef4444']
        }]
    }
});
</script>

</body>
</html>