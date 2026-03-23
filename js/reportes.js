const PALETTE = ['#1E3A5F','#2E6DA4','#4A9FC4','#7FC8E8','#C8A84B','#5C8A3C','#8B2E2E','#6B5B95'];
const gridColor = 'rgba(30,58,95,0.07)';
const font = { family: 'Segoe UI', size: 12 };
const charts = {};

function hexToRgba(hex, alpha) {
    const r = parseInt(hex.slice(1,3),16);
    const g = parseInt(hex.slice(3,5),16);
    const b = parseInt(hex.slice(5,7),16);
    return `rgba(${r},${g},${b},${alpha})`;
}

function destroyChart(id) {
    if (charts[id]) { charts[id].destroy(); delete charts[id]; }
}

// ── Stats ──
function renderStats(data) {
    const votaron  = data.resumen.total_votantes_que_votaron || 0;
    const reg      = data.total_reg || 0;
    const pct      = reg > 0 ? (votaron / reg * 100).toFixed(1) : 0;

    document.getElementById('statVotaron').textContent      = votaron.toLocaleString();
    document.getElementById('statRegistrados').textContent  = reg.toLocaleString();
    document.getElementById('statParticipacion').textContent = pct + '%';
    document.getElementById('statPuestos').textContent      = data.resumen.total_puestos || 0;
}

// ── Votos por partido ──
function renderChartPartidos(data) {
    destroyChart('chartPartidos');
    const labels = data.por_partido.map(p => p.partido);
    const values = data.por_partido.map(p => parseInt(p.votos));

    charts['chartPartidos'] = new Chart(document.getElementById('chartPartidos'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Votos',
                data: values,
                backgroundColor: labels.map((_,i) => hexToRgba(PALETTE[i % PALETTE.length], 0.85)),
                borderColor:     labels.map((_,i) => PALETTE[i % PALETTE.length]),
                borderWidth: 2,
                borderRadius: 6,
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

// ── Votos por hora ──
function renderChartHoras(data) {
    destroyChart('chartHoras');
    const horas  = data.por_hora.map(h => h.hora + ':00');
    const values = data.por_hora.map(h => parseInt(h.votantes));

    charts['chartHoras'] = new Chart(document.getElementById('chartHoras'), {
        type: 'line',
        data: {
            labels: horas,
            datasets: [{
                label: 'Votantes',
                data: values,
                borderColor: '#10B981',
                backgroundColor: 'rgba(16,185,129,0.08)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10B981',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: gridColor }, ticks: { font } },
                y: { grid: { color: gridColor }, ticks: { font }, beginAtZero: true }
            }
        }
    });
}

// ── Participación por sección ──
function renderChartSecciones(data) {
    destroyChart('chartSecciones');
    const labels = data.por_seccion.map(s => 'Secc. ' + s.seccion);
    const values = data.por_seccion.map(s => parseInt(s.votaron));

    charts['chartSecciones'] = new Chart(document.getElementById('chartSecciones'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Votantes',
                data: values,
                backgroundColor: hexToRgba('#EF4444', 0.75),
                borderColor: '#EF4444',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font, maxRotation: 45 } },
                y: { grid: { color: gridColor }, ticks: { font }, beginAtZero: true }
            }
        }
    });
}

// ── Omitidos ──
function renderChartOmitidos(data) {
    destroyChart('chartOmitidos');
    const labels = data.omitidos.map(o => o.puesto);
    const values = data.omitidos.map(o => parseInt(o.omitidos));

    charts['chartOmitidos'] = new Chart(document.getElementById('chartOmitidos'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Omitidos',
                data: values,
                backgroundColor: 'rgba(239,68,68,0.65)',
                borderColor: '#EF4444',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font } },
                y: { grid: { color: gridColor }, ticks: { font, stepSize: 1 }, beginAtZero: true }
            }
        }
    });
}

// ── Tabla resultados ──
function renderTabla(data) {
    if ($.fn.DataTable.isDataTable('#tablaResultados')) {
        $('#tablaResultados').DataTable().destroy();
    }

    let pos = 1;
    let html = '';
    const totalesPorPuesto = {};

    // Calcular totales por puesto
    for (const [puesto, votos] of Object.entries(data.por_puesto)) {
        totalesPorPuesto[puesto] = votos
            .filter(v => v.tipo === 'normal')
            .reduce((s, v) => s + parseInt(v.votos), 0);
    }

    for (const [puesto, votos] of Object.entries(data.por_puesto)) {
        const total = totalesPorPuesto[puesto] || 1;
        votos.forEach((v, idx) => {
            const pct = v.tipo === 'normal' ? ((parseInt(v.votos) / total) * 100).toFixed(1) : 0;
            const esGanador = idx === 0 && v.tipo === 'normal';
            const badgeTipo = v.tipo === 'normal'
                ? `<span class="badge bg-success">Normal</span>`
                : `<span class="badge bg-secondary">Omitido</span>`;
            const badgeEstado = esGanador
                ? `<span class="badge" style="background:#F59E0B;color:#1E293B;">🏆 Ganando</span>`
                : `<span class="badge bg-light text-dark">En curso</span>`;

            html += `<tr>
                <td>${pos}</td>
                <td><strong>${htmlEncode(puesto)}</strong></td>
                <td>${htmlEncode(v.nombre_candidato)}</td>
                <td><span class="partido-tag">${htmlEncode(v.partido)}</span></td>
                <td class="votos-num">${parseInt(v.votos).toLocaleString()}</td>
                <td>
                    <div class="pct-bar-wrap">
                        <div class="pct-bar-fill" style="width:${pct}%"></div>
                        <span>${pct}%</span>
                    </div>
                </td>
                <td>${badgeTipo}</td>
                <td>${badgeEstado}</td>
            </tr>`;
            pos++;
        });
    }

    document.getElementById('cuerpoTabla').innerHTML = html || '<tr><td colspan="8" class="text-center text-muted py-4">Sin datos</td></tr>';

    $('#tablaResultados').DataTable({
        language: {
            emptyTable:  "Sin resultados",
            info:        "Mostrando _START_ a _END_ de _TOTAL_",
            infoEmpty:   "0 registros",
            lengthMenu:  "Mostrar _MENU_ entradas",
            search:      "Buscar:",
            zeroRecords: "Sin resultados",
            paginate: { next: "Siguiente", previous: "Anterior" }
        },
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50]
    });
}

function htmlEncode(str) {
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
}

// ── Render completo ──
function renderAll(data) {
    renderStats(data);
    renderChartPartidos(data);
    renderChartHoras(data);
    renderChartSecciones(data);
    renderChartOmitidos(data);
    renderTabla(data);
}

// ── Cargar por elección ──
async function cargarEleccion(id) {
    document.getElementById('loadingOverlay').classList.remove('d-none');
    document.getElementById('contenidoReporte').style.opacity = '0.4';

    try {
        const res  = await fetch(`${CTRL_URL}?accion=getReporte&id_eleccion=${id}`);
        const data = await res.json();
        renderAll(data);
    } catch(e) {
        console.error('Error cargando reporte:', e);
    } finally {
        document.getElementById('loadingOverlay').classList.add('d-none');
        document.getElementById('contenidoReporte').style.opacity = '1';
    }
}

// ── Selector de elección ──
document.getElementById('selectorEleccion').addEventListener('change', function() {
    cargarEleccion(this.value);
});

// ── Exportar CSV ──
document.getElementById('btnExportCSV').addEventListener('click', function() {
    const id = document.getElementById('selectorEleccion').value;
    window.location.href = `${CTRL_URL}?accion=exportCSV&id_eleccion=${id}`;
});

// ── Arranque ──
if (REPORTE_INICIAL) {
    renderAll(REPORTE_INICIAL);
} else if (ID_ELECCION_DEFAULT > 0) {
    cargarEleccion(ID_ELECCION_DEFAULT);
}
document.getElementById('btnExportPDF').addEventListener('click', async function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generando PDF...';

    try {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const pageW = pdf.internal.pageSize.getWidth();
        const pageH = pdf.internal.pageSize.getHeight();
        const margin = 12;
        let cursorY = margin;

        // ── Encabezado ──
        pdf.setFillColor(30, 58, 95);
        pdf.rect(0, 0, pageW, 22, 'F');
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(14);
        pdf.setFont('helvetica', 'bold');
        pdf.text('VotoSecure — Reporte de Resultados', margin, 14);
        pdf.setFontSize(9);
        pdf.setFont('helvetica', 'normal');
        pdf.text('Generado: ' + new Date().toLocaleString('es-MX'), pageW - margin, 14, { align: 'right' });

        cursorY = 30;

        // ── Stats en texto ──
        pdf.setTextColor(30, 58, 95);
        pdf.setFontSize(11);
        pdf.setFont('helvetica', 'bold');
        pdf.text('Resumen General', margin, cursorY);
        cursorY += 7;

        const stats = [
            ['Votaron',       document.getElementById('statVotaron').textContent],
            ['Registrados',   document.getElementById('statRegistrados').textContent],
            ['Participación', document.getElementById('statParticipacion').textContent],
            ['Puestos',       document.getElementById('statPuestos').textContent],
        ];

        pdf.setFontSize(9);
        pdf.setFont('helvetica', 'normal');
        pdf.setTextColor(51, 65, 85);

        const colW = (pageW - margin * 2) / 4;
        stats.forEach(([label, value], i) => {
            const x = margin + i * colW;
            pdf.setFillColor(240, 244, 249);
            pdf.roundedRect(x, cursorY, colW - 4, 18, 2, 2, 'F');
            pdf.setTextColor(30, 58, 95);
            pdf.setFontSize(13);
            pdf.setFont('helvetica', 'bold');
            pdf.text(value, x + (colW - 4) / 2, cursorY + 10, { align: 'center' });
            pdf.setFontSize(8);
            pdf.setFont('helvetica', 'normal');
            pdf.setTextColor(100, 116, 139);
            pdf.text(label, x + (colW - 4) / 2, cursorY + 16, { align: 'center' });
        });

        cursorY += 26;

        // ── Función para capturar un canvas de Chart.js ──
        async function agregarGrafica(canvasId, titulo, alto = 65) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;

            // Captura el canvas directamente (más fiel que html2canvas)
            const imgData = canvas.toDataURL('image/png', 1.0);
            const imgW    = pageW - margin * 2;
            const imgH    = alto;

            // Nueva página si no cabe
            if (cursorY + imgH + 14 > pageH - margin) {
                pdf.addPage();
                cursorY = margin;
            }

            // Título de la gráfica
            pdf.setFillColor(248, 250, 252);
            pdf.roundedRect(margin, cursorY, imgW, imgH + 12, 3, 3, 'F');
            pdf.setDrawColor(200, 210, 220);
            pdf.roundedRect(margin, cursorY, imgW, imgH + 12, 3, 3, 'S');

            pdf.setTextColor(30, 58, 95);
            pdf.setFontSize(9);
            pdf.setFont('helvetica', 'bold');
            pdf.text(titulo, margin + 4, cursorY + 7);

            pdf.addImage(imgData, 'PNG', margin + 2, cursorY + 10, imgW - 4, imgH);
            cursorY += imgH + 18;
        }

        // ── Gráficas ──
        await agregarGrafica('chartPartidos',  'Votos por Partido', 55);
        await agregarGrafica('chartHoras',     'Votos por Hora', 55);
        await agregarGrafica('chartSecciones', 'Participación por Sección', 55);
        await agregarGrafica('chartOmitidos',  'Votos Omitidos por Puesto', 45);

        // ── Tabla de resultados ──
        if (cursorY + 20 > pageH - margin) {
            pdf.addPage();
            cursorY = margin;
        }

        pdf.setTextColor(30, 58, 95);
        pdf.setFontSize(11);
        pdf.setFont('helvetica', 'bold');
        pdf.text('Resultados Detallados por Puesto', margin, cursorY);
        cursorY += 7;

        // Encabezados tabla
        const cols   = ['Puesto', 'Candidato', 'Partido', 'Votos', '%', 'Estado'];
        const colsW  = [30, 55, 35, 18, 16, 25];
        const rowH   = 7;

        pdf.setFillColor(30, 58, 95);
        pdf.rect(margin, cursorY, pageW - margin * 2, rowH, 'F');
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(7.5);
        pdf.setFont('helvetica', 'bold');

        let xPos = margin + 2;
        cols.forEach((col, i) => {
            pdf.text(col, xPos, cursorY + 5);
            xPos += colsW[i];
        });
        cursorY += rowH;

        // Filas
        const filas = document.querySelectorAll('#cuerpoTabla tr');
        let rowIndex = 0;

        filas.forEach(fila => {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length < 6) return;

            if (cursorY + rowH > pageH - margin) {
                pdf.addPage();
                cursorY = margin + 5;
            }

            const esGanador = fila.querySelector('.badge') &&
                              fila.querySelector('.badge').textContent.includes('Ganando');

            if (esGanador) {
                pdf.setFillColor(255, 248, 230);
            } else {
                pdf.setFillColor(rowIndex % 2 === 0 ? 255 : 248, rowIndex % 2 === 0 ? 255 : 250, rowIndex % 2 === 0 ? 255 : 252);
            }
            pdf.rect(margin, cursorY, pageW - margin * 2, rowH, 'F');

            pdf.setTextColor(51, 65, 85);
            pdf.setFontSize(7);
            pdf.setFont('helvetica', 'normal');

            // Extraer texto limpio de cada celda
            const textos = [
                celdas[1]?.textContent.trim() || '',  // Puesto
                celdas[2]?.textContent.trim() || '',  // Candidato
                celdas[3]?.textContent.trim() || '',  // Partido
                celdas[4]?.textContent.trim() || '',  // Votos
                celdas[5]?.textContent.trim().replace(/[^\d.%]/g,'') || '', // %
                esGanador ? 'Ganando' : 'En curso',
            ];

            xPos = margin + 2;
            textos.forEach((txt, i) => {
                const maxW = colsW[i] - 2;
                const truncado = pdf.getStringUnitWidth(txt) * 7 / pdf.internal.scaleFactor > maxW
                    ? txt.substring(0, Math.floor(maxW / 3)) + '...'
                    : txt;
                pdf.text(truncado, xPos, cursorY + 5);
                xPos += colsW[i];
            });

            // Línea separadora
            pdf.setDrawColor(226, 232, 240);
            pdf.line(margin, cursorY + rowH, pageW - margin, cursorY + rowH);

            cursorY += rowH;
            rowIndex++;
        });

        // ── Pie de página en todas las páginas ──
        const totalPages = pdf.internal.getNumberOfPages();
        for (let i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFillColor(240, 244, 249);
            pdf.rect(0, pageH - 10, pageW, 10, 'F');
            pdf.setTextColor(100, 116, 139);
            pdf.setFontSize(7);
            pdf.setFont('helvetica', 'normal');
            pdf.text('VotoSecure — Reporte confidencial', margin, pageH - 4);
            pdf.text(`Página ${i} de ${totalPages}`, pageW - margin, pageH - 4, { align: 'right' });
        }

        pdf.save(`reporte_votosecure_${Date.now()}.pdf`);

    } catch(err) {
        console.error('Error generando PDF:', err);
        alert('Error al generar el PDF');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Exportar PDF';
    }
});