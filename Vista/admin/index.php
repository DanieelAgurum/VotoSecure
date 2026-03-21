<?php
session_start();

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
    <title>Dashboard Administrativo - VotoSecure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/dash.css">
    <style>
        .dashboard-title {
            color: var(--text-light);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chart-card {
            background: rgba(248, 250, 252, 0.97);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 8px 32px rgba(15, 23, 42, 0.18);
            border: 1px solid rgba(255,255,255,0.2);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .chart-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.25);
        }

        .chart-card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-card-subtitle {
            font-size: 12px;
            color: #64748B;
            margin-bottom: 20px;
        }

        .winner-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #F59E0B, #FBBF24);
            color: #1E293B;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-mini {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #F1F5F9;
        }

        .stat-mini:last-child {
            border-bottom: none;
        }

        .stat-mini-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
        }

        .stat-mini-pct {
            font-size: 13px;
            font-weight: 700;
            color: #2563EB;
        }

        .progress-bar-custom {
            height: 6px;
            background: #E2E8F0;
            border-radius: 3px;
            margin-top: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
            background: linear-gradient(90deg, #2563EB, #22D3EE);
        }

        .section-label {
            color: rgba(248, 250, 252, 0.7);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            margin-top: 10px;
        }

        canvas {
            max-height: 260px !important;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../components/Admin/Navbar.php'; ?>

<div class="main-content">

    <div class="dashboard-title">
        <i class="bi bi-bar-chart-fill"></i> Resultados en Tiempo Real
    </div>

    <!-- FILA 1 — Partido ganando + Candidato ganando general -->
    <div class="row">
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="bi bi-bar-chart" style="color:#2563EB;"></i>
                    Votos por Partido
                </div>
                <div class="chart-card-subtitle">Comparativa general de todos los partidos</div>
                <div class="winner-badge">
                    <i class="bi bi-trophy-fill"></i> Partido Final lidera con 42%
                </div>
                <canvas id="chartPartidos"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="bi bi-pie-chart" style="color:#F59E0B;"></i>
                    Distribución de Partidos
                </div>
                <div class="chart-card-subtitle">Porcentaje del total de votos</div>
                <canvas id="chartPartidosPie"></canvas>
                <div class="mt-3">
                    <div class="stat-mini">
                        <div>
                            <div class="stat-mini-name">🥇 Partido Final</div>
                            <div class="progress-bar-custom"><div class="progress-fill" style="width:42%;background:linear-gradient(90deg,#2563EB,#22D3EE);"></div></div>
                        </div>
                        <div class="stat-mini-pct">42%</div>
                    </div>
                    <div class="stat-mini">
                        <div>
                            <div class="stat-mini-name">🥈 Partido Principal</div>
                            <div class="progress-bar-custom"><div class="progress-fill" style="width:35%;background:linear-gradient(90deg,#10B981,#34D399);"></div></div>
                        </div>
                        <div class="stat-mini-pct">35%</div>
                    </div>
                    <div class="stat-mini">
                        <div>
                            <div class="stat-mini-name">🥉 Partido Republicano</div>
                            <div class="progress-bar-custom"><div class="progress-fill" style="width:23%;background:linear-gradient(90deg,#F59E0B,#FBBF24);"></div></div>
                        </div>
                        <div class="stat-mini-pct">23%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILA 2 — Candidatos por cargo -->
    <div class="section-label"><i class="bi bi-person-badge"></i> Candidatos por Cargo</div>

    <div class="row">
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="bi bi-star-fill" style="color:#EF4444;"></i>
                    Candidatos a Presidente
                </div>
                <div class="chart-card-subtitle">Elección presidencial</div>
                <div class="winner-badge">
                    <i class="bi bi-trophy-fill"></i> Claudia Sheinbaum lidera
                </div>
                <canvas id="chartPresidente"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="bi bi-building" style="color:#10B981;"></i>
                    Candidatos a Alcalde
                </div>
                <div class="chart-card-subtitle">Elección municipal</div>
                <div class="winner-badge">
                    <i class="bi bi-trophy-fill"></i> Jaime Flores lidera
                </div>
                <canvas id="chartAlcalde"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="bi bi-geo-alt-fill" style="color:#8B5CF6;"></i>
                    Candidatos a Gobernador
                </div>
                <div class="chart-card-subtitle">Elección estatal</div>
                <div class="winner-badge">
                    <i class="bi bi-trophy-fill"></i> Ricardo Anaya lidera
                </div>
                <canvas id="chartGobernador"></canvas>
            </div>
        </div>
    </div>

    <!-- FILA 3 — Tendencia votos + Participación -->
    <div class="row">
        <div class="col-lg-7">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="bi bi-graph-up-arrow" style="color:#2563EB;"></i>
                    Tendencia de Votos por Día
                </div>
                <div class="chart-card-subtitle">Acumulado de los últimos 7 días</div>
                <canvas id="chartTendencia"></canvas>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="bi bi-people-fill" style="color:#F59E0B;"></i>
                    Participación Ciudadana
                </div>
                <div class="chart-card-subtitle">Votantes registrados vs votantes activos</div>
                <canvas id="chartParticipacion"></canvas>
                <div class="mt-3 text-center">
                    <span style="font-size:28px;font-weight:700;color:#10B981;">68%</span>
                    <div style="font-size:12px;color:#64748B;">de participación total</div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../js/dash.js"></script>
<script>
const gridColor = 'rgba(15,23,42,0.06)';
const font = { family: 'Segoe UI', size: 12 };

// ── Partidos barras horizontales ──
new Chart(document.getElementById('chartPartidos'), {
    type: 'bar',
    data: {
        labels: ['Partido Final', 'Partido Principal', 'Partido Republicano'],
        datasets: [{
            label: 'Votos',
            data: [1240, 1035, 680],
            backgroundColor: [
                'rgba(37,99,235,0.85)',
                'rgba(16,185,129,0.85)',
                'rgba(245,158,11,0.85)'
            ],
            borderColor: ['#2563EB','#10B981','#F59E0B'],
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.parsed.x.toLocaleString()} votos`
                }
            }
        },
        scales: {
            x: {
                grid: { color: gridColor },
                ticks: { font }
            },
            y: {
                grid: { display: false },
                ticks: { font, color: '#334155' }
            }
        }
    }
});

// ── Partidos pie ──
new Chart(document.getElementById('chartPartidosPie'), {
    type: 'doughnut',
    data: {
        labels: ['Partido Final', 'Partido Principal', 'Partido Republicano'],
        datasets: [{
            data: [42, 35, 23],
            backgroundColor: ['#2563EB','#10B981','#F59E0B'],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        cutout: '68%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.parsed}%`
                }
            }
        }
    }
});

// ── Presidente ──
new Chart(document.getElementById('chartPresidente'), {
    type: 'bar',
    data: {
        labels: ['C. Sheinbaum', 'R. Anaya', 'J. Flores'],
        datasets: [{
            label: 'Votos',
            data: [890, 620, 430],
            backgroundColor: ['rgba(239,68,68,0.85)','rgba(239,68,68,0.4)','rgba(239,68,68,0.25)'],
            borderColor: '#EF4444',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font } },
            y: { grid: { color: gridColor }, ticks: { font } }
        }
    }
});

// ── Alcalde ──
new Chart(document.getElementById('chartAlcalde'), {
    type: 'bar',
    data: {
        labels: ['J. Flores', 'C. Sheinbaum', 'R. Anaya'],
        datasets: [{
            label: 'Votos',
            data: [540, 410, 310],
            backgroundColor: ['rgba(16,185,129,0.85)','rgba(16,185,129,0.4)','rgba(16,185,129,0.25)'],
            borderColor: '#10B981',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font } },
            y: { grid: { color: gridColor }, ticks: { font } }
        }
    }
});

// ── Gobernador ──
new Chart(document.getElementById('chartGobernador'), {
    type: 'bar',
    data: {
        labels: ['R. Anaya', 'C. Sheinbaum', 'J. Flores'],
        datasets: [{
            label: 'Votos',
            data: [720, 580, 290],
            backgroundColor: ['rgba(139,92,246,0.85)','rgba(139,92,246,0.4)','rgba(139,92,246,0.25)'],
            borderColor: '#8B5CF6',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font } },
            y: { grid: { color: gridColor }, ticks: { font } }
        }
    }
});

// ── Tendencia ──
new Chart(document.getElementById('chartTendencia'), {
    type: 'line',
    data: {
        labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
        datasets: [
            {
                label: 'Partido Final',
                data: [140, 190, 175, 210, 245, 220, 260],
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37,99,235,0.08)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#2563EB',
                pointRadius: 4
            },
            {
                label: 'Partido Principal',
                data: [120, 155, 145, 180, 200, 185, 210],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16,185,129,0.08)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10B981',
                pointRadius: 4
            },
            {
                label: 'Partido Republicano',
                data: [80, 100, 95, 115, 130, 120, 140],
                borderColor: '#F59E0B',
                backgroundColor: 'rgba(245,158,11,0.08)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#F59E0B',
                pointRadius: 4
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font, usePointStyle: true, padding: 16 }
            }
        },
        scales: {
            x: { grid: { color: gridColor }, ticks: { font } },
            y: { grid: { color: gridColor }, ticks: { font } }
        }
    }
});

// ── Participación ──
new Chart(document.getElementById('chartParticipacion'), {
    type: 'doughnut',
    data: {
        labels: ['Votaron', 'No votaron'],
        datasets: [{
            data: [68, 32],
            backgroundColor: ['#10B981','#E2E8F0'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        cutout: '75%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font, usePointStyle: true, padding: 16 }
            }
        }
    }
});
</script>

</body>
</html>