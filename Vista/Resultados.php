<?php
session_start();
if (!isset($_SESSION['rol'])) {
    header('Location: ../login');
    exit;
}
define('BASE_URL', '/VotoSecure');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="/votosecure/img/vs.ico">
    <title>Resultados Electorales - VotoSecure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/candidatos.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/estilos.css">
</head>

<body>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/VotoSecure/components/nav.php'; ?>

    <div class="resultados-section">
        <div class="container">

            <h1 class="resultados-title">
                <i class="bi bi-bar-chart-fill"></i> Resultados Electorales
            </h1>
            <p class="text-center text-muted mb-4" style="margin-top:-20px;font-size:.85rem;">
                <span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:#22C55E;margin-right:5px;animation:blink 1.2s infinite;"></span>
                En vivo &middot; Última actualización: <span id="lastUpdate">—</span>
            </p>

            <!-- Partido líder global -->
            <div class="chart-card mb-4">
                <div class="result-winner">
                    <div class="winner-party">
                        <i class="bi bi-trophy-fill" style="font-size:1.8rem;color:var(--warning)"></i>
                        <div style="margin-left:12px;">
                            <div style="font-size:.75rem;color:#64748B;text-transform:uppercase;letter-spacing:.08em;">
                                Partido con más votos acumulados
                            </div>
                            <div style="font-size:1.4rem;font-weight:800;color:var(--primary);" id="liderPartido">Cargando...</div>
                            <div style="font-size:.8rem;color:#64748B;margin-top:2px;" id="liderDetalle"></div>
                        </div>
                    </div>
                    <div class="winner-votes">
                        <span class="votes-number" id="liderVotos">—</span>
                        <span class="votes-label">votos en total</span>
                    </div>
                </div>
            </div>

            <!-- Stats generales -->
            <div class="stats-row">
                <div class="stat-card-result">
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-number" id="statVotos">—</div>
                    <div class="stat-label">Votantes que han votado</div>
                </div>
                <div class="stat-card-result success">
                    <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                    <div class="stat-number" id="statParticipacion">—</div>
                    <div class="stat-label">Participación</div>
                </div>
                <div class="stat-card-result warning">
                    <div class="stat-icon"><i class="bi bi-building"></i></div>
                    <div class="stat-number" id="statPuestos">—</div>
                    <div class="stat-label">Puestos Electivos</div>
                </div>
                <div class="stat-card-result danger">
                    <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
                    <div class="stat-number" id="statVotantes">—</div>
                    <div class="stat-label">Votantes Registrados</div>
                </div>
            </div>

            <!-- Gráficas por puesto -->
            <div class="resultados-grid" id="resultadosGrid">
                <div class="text-center py-5" id="loadingMsg" style="grid-column:1/-1">
                    <div class="spinner-border" style="color:var(--accent)"></div>
                    <p class="mt-3 text-muted">Cargando resultados...</p>
                </div>
            </div>

        </div>
    </div>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/VotoSecure/components/footer.php'; ?>

    <style>
        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .2
            }
        }
    </style>

    <script>
        const PARTY_COLORS = [
            '#22D3EE', '#22C55E', '#F59E0B', '#EF4444',
            '#8B5CF6', '#EC4899', '#F97316', '#06B6D4',
            '#84CC16', '#A78BFA', '#FB7185', '#34D399'
        ];
        const colorMap = {};
        let colorIdx = 0;

        function getColor(partido) {
            if (!colorMap[partido]) {
                colorMap[partido] = PARTY_COLORS[colorIdx % PARTY_COLORS.length];
                colorIdx++;
            }
            return colorMap[partido];
        }

        const charts = {};

        function upsertChart(canvasId, labels, values) {
            const ctx = document.getElementById(canvasId);
            if (!ctx) return;
            const colors = labels.map(getColor);

            if (charts[canvasId]) {
                charts[canvasId].data.labels = labels;
                charts[canvasId].data.datasets[0].data = values;
                charts[canvasId].data.datasets[0].backgroundColor = colors;
                charts[canvasId].update('none');
            } else {
                charts[canvasId] = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 600
                        },
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
                            },
                            tooltip: {
                                callbacks: {
                                    label: c => ` ${c.label}: ${c.parsed} voto${c.parsed !== 1 ? 's' : ''}`
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
        }

        const ICONS = {
            PRESIDENTE: 'bi-person-badge',
            SENADORES: 'bi-mic-fill',
            ALCALDE: 'bi-bank',
            DIPUTADOS: 'bi-person-lines-fill',
            GOBERNADOR: 'bi-building'
        };

        function renderGrid(resultados) {
            const grid = document.getElementById('resultadosGrid');
            const spinner = document.getElementById('loadingMsg');
            if (spinner) spinner.remove();

            resultados.forEach(r => {
                const safeId = r.puesto.replace(/\s+/g, '_');
                const cardId = 'card_' + safeId;
                const canvasId = 'chart_' + safeId;
                const ganador = r.ganador;
                const icon = ICONS[r.puesto] || 'bi-award-fill';

                if (!document.getElementById(cardId)) {
                    const card = document.createElement('div');
                    card.className = 'chart-card';
                    card.id = cardId;
                    card.innerHTML = `
                <div class="chart-title-result">
                    <i class="bi ${icon}"></i> ${r.puesto}
                    <span style="margin-left:auto;font-size:.75rem;font-weight:400;color:#64748B;"
                          id="wtotal_${safeId}"></span>
                </div>
                <div class="result-winner">
                    <div class="winner-party">
                        <span class="party-badge" id="wbadge_${safeId}">—</span>
                    </div>
                    <div class="winner-votes">
                        <span class="votes-number" id="wvotes_${safeId}">—</span>
                        <span class="votes-label" id="wpct_${safeId}">votos</span>
                    </div>
                </div>
                <div class="chart-canvas-container">
                    <canvas id="${canvasId}"></canvas>
                </div>
            `;
                    grid.appendChild(card);
                }

                // Actualizar datos del ganador
                const badge = document.getElementById('wbadge_' + safeId);
                const wvotes = document.getElementById('wvotes_' + safeId);
                const wpct = document.getElementById('wpct_' + safeId);
                const wtotal = document.getElementById('wtotal_' + safeId);

                if (badge) {
                    badge.textContent = ganador.partido !== '—' ?
                        ganador.partido + (ganador.nombre_candidato && ganador.nombre_candidato !== '—' ?
                            ' · ' + ganador.nombre_candidato : '') :
                        'Sin votos';
                    badge.style.background = getColor(ganador.partido);
                }
                if (wvotes) wvotes.textContent = ganador.votos;
                if (wpct && ganador.porcentaje !== undefined) {
                    wpct.textContent = ganador.porcentaje + '% de los votos';
                }
                if (wtotal) {
                    wtotal.textContent = r.total + ' voto' + (r.total !== 1 ? 's' : '') + ' válidos' +
                        (r.omitidos > 0 ? ' · ' + r.omitidos + ' en blanco' : '');
                }

                if (r.votos.length > 0) {
                    // Etiqueta con partido + % para la dona
                    const labels = r.votos.map(v => v.partido + ' (' + v.porcentaje + '%)');
                    const values = r.votos.map(v => parseInt(v.votos));
                    // Mapear colores por partido (antes del sufijo)
                    const colors = r.votos.map(v => getColor(v.partido));
                    const ctx = document.getElementById(canvasId);
                    if (ctx && charts[canvasId]) {
                        charts[canvasId].data.labels = labels;
                        charts[canvasId].data.datasets[0].data = values;
                        charts[canvasId].data.datasets[0].backgroundColor = colors;
                        charts[canvasId].update('none');
                    } else if (ctx) {
                        charts[canvasId] = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels,
                                datasets: [{
                                    data: values,
                                    backgroundColor: colors,
                                    borderWidth: 0,
                                    hoverOffset: 10
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: {
                                    duration: 600
                                },
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
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: c => ` ${c.label}: ${c.parsed} voto${c.parsed !== 1 ? 's' : ''}`
                                        }
                                    }
                                },
                                cutout: '60%'
                            }
                        });
                    }
                }
            });
        }

        async function fetchResultados() {
            try {
                const res = await fetch('/VotoSecure/api/resultados_data.php', {
                    cache: 'no-store'
                });
                const data = await res.json();

                // Stats — votos = personas únicas, no filas
                document.getElementById('statVotos').textContent = data.total_votos.toLocaleString('es-MX');
                document.getElementById('statParticipacion').textContent = data.participacion + '%';
                document.getElementById('statPuestos').textContent = data.puestos;
                document.getElementById('statVotantes').textContent = data.total_votantes.toLocaleString('es-MX');

                // Líder — con contexto de puestos ganados
                const lider = data.partido_lider;
                document.getElementById('liderPartido').textContent = lider.partido;
                document.getElementById('liderVotos').textContent = lider.votos_total;

                // Detalle: cuántos puestos va ganando
                const pg = data.puestos_ganados;
                const puestosGanados = pg[lider.partido] || 0;
                document.getElementById('liderDetalle').textContent =
                    'Va ganando en ' + puestosGanados + ' de ' + data.puestos + ' puesto' + (data.puestos !== 1 ? 's' : '');

                document.getElementById('lastUpdate').textContent = data.timestamp;

                renderGrid(data.resultados);
            } catch (e) {
                console.error('Error cargando resultados:', e);
            }
        }

        fetchResultados();
        setInterval(fetchResultados, 5000);
    </script>
</body>

</html>