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
    <title>Resultados Electorales - VotoSecure</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/votosecure/css/candidatos.css">
    <link rel="stylesheet" href="/votosecure/css/estilos.css">
</head>

<body>
    <!-- Navbar -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/VotoSecure/components/nav.php'; ?>

    <div class="resultados-section">
        <div class="container">
            <h1 class="resultados-title">
                <i class="bi bi-bar-chart-fill"></i> Resultados Electorales
            </h1>

            <!-- Estadísticas Generales -->
            <div class="stats-row">
                <div class="stat-card-result">
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-number">15,432</div>
                    <div class="stat-label">Total Votos</div>
                </div>
                <div class="stat-card-result success">
                    <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                    <div class="stat-number">68.5%</div>
                    <div class="stat-label">Participación</div>
                </div>
                <div class="stat-card-result warning">
                    <div class="stat-icon"><i class="bi bi-building"></i></div>
                    <div class="stat-number">5</div>
                    <div class="stat-label">Puestos Electivos</div>
                </div>
                <div class="stat-card-result danger">
                    <div class="stat-icon"><i class="bi bi-flag-fill"></i></div>
                    <div class="stat-number">8</div>
                    <div class="stat-label">Partidos</div>
                </div>
            </div>

            <!-- Resultados por Puesto -->
            <div class="resultados-grid">

                <!-- Presidente -->
                <div class="chart-card">
                    <div class="chart-title-result">
                        <i class="bi bi-person-badge"></i> Presidente
                    </div>
                    <div class="result-winner">
                        <div class="winner-party">
                            <span class="party-badge" style="background: #22C55E;">Partido Verde</span>
                        </div>
                        <div class="winner-votes">
                            <span class="votes-number">5,234</span>
                            <span class="votes-label">votos</span>
                        </div>
                    </div>
                    <div class="chart-canvas-container">
                        <canvas id="chartPresidente"></canvas>
                    </div>
                </div>

                <!-- Gobernadores -->
                <div class="chart-card">
                    <div class="chart-title-result">
                        <i class="bi bi-building"></i> Gobernadores
                    </div>
                    <div class="result-winner">
                        <div class="winner-party">
                            <span class="party-badge" style="background: #3B82F6;">Partido Azul</span>
                        </div>
                        <div class="winner-votes">
                            <span class="votes-number">4,891</span>
                            <span class="votes-label">votos</span>
                        </div>
                    </div>
                    <div class="chart-canvas-container">
                        <canvas id="chartGobernadores"></canvas>
                    </div>
                </div>

                <!-- Alcaldes -->
                <div class="chart-card">
                    <div class="chart-title-result">
                        <i class="bi bi-bank"></i> Alcaldes
                    </div>
                    <div class="result-winner">
                        <div class="winner-party">
                            <span class="party-badge" style="background: #EF4444;">Partido Rojo</span>
                        </div>
                        <div class="winner-votes">
                            <span class="votes-number">3,567</span>
                            <span class="votes-label">votos</span>
                        </div>
                    </div>
                    <div class="chart-canvas-container">
                        <canvas id="chartAlcaldes"></canvas>
                    </div>
                </div>

                <!-- Diputados -->
                <div class="chart-card">
                    <div class="chart-title-result">
                        <i class="bi bi-person-lines-fill"></i> Diputados
                    </div>
                    <div class="result-winner">
                        <div class="winner-party">
                            <span class="party-badge" style="background: #F59E0B;">Partido Naranja</span>
                        </div>
                        <div class="winner-votes">
                            <span class="votes-number">1,234</span>
                            <span class="votes-label">votos</span>
                        </div>
                    </div>
                    <div class="chart-canvas-container">
                        <canvas id="chartDiputados"></canvas>
                    </div>
                </div>

                <!-- Alcaldes Municipales -->
                <div class="chart-card">
                    <div class="chart-title-result">
                        <i class="bi bi-house-door"></i> Alcaldes Municipales
                    </div>
                    <div class="result-winner">
                        <div class="winner-party">
                            <span class="party-badge" style="background: #8B5CF6;">Partido Morado</span>
                        </div>
                        <div class="winner-votes">
                            <span class="votes-number">506</span>
                            <span class="votes-label">votos</span>
                        </div>
                    </div>
                    <div class="chart-canvas-container">
                        <canvas id="chartAlcaldesMuni"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/VotoSecure/components/footer.php'; ?>

    <script>
        // Colores para los partidos
        const partyColors = {
            'Partido Verde': '#22C55E',
            'Partido Azul': '#3B82F6',
            'Partido Rojo': '#EF4444',
            'Partido Naranja': '#F59E0B',
            'Partido Morado': '#8B5CF6',
            'Partido Amarillo': '#EAB308',
            'Partido Cyan': '#06B6D4',
            'Partido Rosa': '#EC4899'
        };

        // Función para crear gráfico de dona
        function createDoughnutChart(canvasId, data) {
            new Chart(document.getElementById(canvasId), {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: data.labels.map(label => partyColors[label] || '#6B7280'),
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }

        // Presidente
        createDoughnutChart('chartPresidente', {
            labels: ['Partido Verde', 'Partido Azul', 'Partido Rojo', 'Partido Naranja'],
            values: [5234, 4120, 3890, 2188]
        });

        // Gobernadores
        createDoughnutChart('chartGobernadores', {
            labels: ['Partido Azul', 'Partido Verde', 'Partido Rojo', 'Partido Morado'],
            values: [4891, 4234, 3567, 2740]
        });

        // Alcaldes
        createDoughnutChart('chartAlcaldes', {
            labels: ['Partido Rojo', 'Partido Verde', 'Partido Azul', 'Partido Naranja'],
            values: [3567, 2890, 2456, 1519]
        });

        // Diputados
        createDoughnutChart('chartDiputados', {
            labels: ['Partido Naranja', 'Partido Verde', 'Partido Azul', 'Partido Rojo'],
            values: [1234, 1089, 876, 543]
        });

        // Alcaldes Municipales
        createDoughnutChart('chartAlcaldesMuni', {
            labels: ['Partido Morado', 'Partido Verde', 'Partido Azul', 'Partido Rojo'],
            values: [506, 423, 312, 189]
        });
    </script>
</body>

</html>