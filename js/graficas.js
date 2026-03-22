const gridColor = 'rgba(15,23,42,0.06)';
const font      = { family: 'Segoe UI', size: 12 };

const PALETTE = ['#2563EB','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#06B6D4','#84CC16'];

function hexToRgba(hex, alpha) {
    const r = parseInt(hex.slice(1,3),16);
    const g = parseInt(hex.slice(3,5),16);
    const b = parseInt(hex.slice(5,7),16);
    return `rgba(${r},${g},${b},${alpha})`;
}

// ── Registro de instancias para destruir en refresh ──
const charts = {};

function destroyChart(id) {
    if (charts[id]) { charts[id].destroy(); delete charts[id]; }
}

// ── Partidos barras horizontales ──
function renderChartPartidos(data) {
    destroyChart('chartPartidos');
    const partidos = Object.keys(data.puestos_ganados);
    const votos    = data.resultados.reduce((acc, r) => {
        r.votos.forEach(v => {
            acc[v.partido] = (acc[v.partido] || 0) + parseInt(v.votos);
        });
        return acc;
    }, {});

    const labels = Object.keys(votos).sort((a,b) => votos[b] - votos[a]);
    const values = labels.map(p => votos[p]);
    const colors = labels.map((_,i) => hexToRgba(PALETTE[i % PALETTE.length], 0.85));
    const borders= labels.map((_,i) => PALETTE[i % PALETTE.length]);

    charts['chartPartidos'] = new Chart(document.getElementById('chartPartidos'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Votos',
                data: values,
                backgroundColor: colors,
                borderColor: borders,
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
                tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.x.toLocaleString()} votos` } }
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { font } },
                y: { grid: { display: false }, ticks: { font, color: '#334155' } }
            }
        }
    });
}

// ── Pie partidos ──
function renderChartPie(data) {
    destroyChart('chartPartidosPie');
    const votos = data.resultados.reduce((acc, r) => {
        r.votos.forEach(v => {
            acc[v.partido] = (acc[v.partido] || 0) + parseInt(v.votos);
        });
        return acc;
    }, {});
    const labels = Object.keys(votos);
    const values = labels.map(p => votos[p]);

    charts['chartPartidosPie'] = new Chart(document.getElementById('chartPartidosPie'), {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: labels.map((_,i) => PALETTE[i % PALETTE.length]),
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
                tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} votos` } }
            }
        }
    });
}

// ── Gráfica por puesto ──
function renderChartPuesto(r, idx) {
    const id = `chartPuesto_${idx}`;
    destroyChart(id);
    const color  = PALETTE[idx % PALETTE.length];
    const labels = r.votos.map(v => v.nombre_candidato);
    const values = r.votos.map(v => parseInt(v.votos));
    const bgColors = values.map((_, i) =>
        hexToRgba(color, i === 0 ? 0.85 : Math.max(0.2, 0.85 - i * 0.2))
    );

    charts[id] = new Chart(document.getElementById(id), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Votos',
                data: values,
                backgroundColor: bgColors,
                borderColor: color,
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font, maxRotation: 30 } },
                y: { grid: { color: gridColor }, ticks: { font } }
            }
        }
    });
}

// ── Omitidos ──
function renderChartOmitidos(data) {
    destroyChart('chartOmitidos');
    const labels = data.resultados.map(r => r.puesto);
    const values = data.resultados.map(r => r.omitidos);

    charts['chartOmitidos'] = new Chart(document.getElementById('chartOmitidos'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Votos omitidos',
                data: values,
                backgroundColor: values.map(v => v > 0 ? 'rgba(239,68,68,0.75)' : 'rgba(226,232,240,0.6)'),
                borderColor: '#EF4444',
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} votos omitidos` } }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font } },
                y: {
                    grid: { color: gridColor },
                    ticks: { font, stepSize: 1 },
                    beginAtZero: true
                }
            }
        }
    });
}

// ── Participación ──
function renderChartParticipacion(data) {
    destroyChart('chartParticipacion');
    charts['chartParticipacion'] = new Chart(document.getElementById('chartParticipacion'), {
        type: 'doughnut',
        data: {
            labels: ['Votaron', 'No votaron'],
            datasets: [{
                data: [data.participacion, 100 - data.participacion],
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
}

// ── Render completo ──
function renderAll(data) {
    renderChartPartidos(data);
    renderChartPie(data);
    data.resultados.forEach((r, idx) => renderChartPuesto(r, idx));
    renderChartOmitidos(data);
    renderChartParticipacion(data);

    // Stats
    document.getElementById('statTotalVotos').textContent    = data.total_votos.toLocaleString();
    document.getElementById('statParticipacion').textContent = data.participacion + '%';
    document.getElementById('statPartidoLider').textContent  = data.partido_lider.partido;
    document.getElementById('statBigPct').textContent        = data.participacion + '%';
    document.getElementById('badgePartido').innerHTML =
        `<i class="bi bi-trophy-fill"></i> ${data.partido_lider.partido} lidera con ${data.partido_lider.votos_total} votos`;
}

// ── Auto-refresh ──
let segundos = 3;
let intervalo;

function iniciarContador() {
    clearInterval(intervalo);
    segundos = 3;

    intervalo = setInterval(async () => {
        segundos--;
       

        if (segundos <= 0) {
            await refreshData();
        }
    }, 1000);
}

async function refreshData() {
    try {
        document.getElementById('refreshIndicator').classList.add('refreshing');
        const res  = await fetch(REFRESH_URL + '?accion=getDashboard');
        const data = await res.json();
        renderAll(data);
    } catch (e) {
        console.error('Error al refrescar:', e);
    } finally {
        document.getElementById('refreshIndicator').classList.remove('refreshing');
        iniciarContador();
    }
}

document.getElementById('btnRefresh').addEventListener('click', () => {
    clearInterval(intervalo);
    refreshData();
});

// ── Arranque ──
renderAll(DASHBOARD_DATA);
iniciarContador();