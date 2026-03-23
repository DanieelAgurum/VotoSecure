const PALETTE = ['#1E3A5F','#2E6DA4','#4A9FC4','#C8A84B','#5C8A3C','#8B2E2E','#6B5B95','#7FC8E8'];
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

function htmlEncode(str) {
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
}

// ── Stats ──
function renderStats(data) {
    const votaron = data.resumen.total_votantes_que_votaron || 0;
    const reg     = data.total_reg || 0;
    const pct     = reg > 0 ? (votaron / reg * 100).toFixed(1) : 0;

    // Partido líder
    const votos = data.por_partido;
    const lider = votos.length > 0 ? votos[0].partido : '—';

    document.getElementById('statPartidoLider').textContent  = lider;
    document.getElementById('statVotaron').textContent       = votaron.toLocaleString();
    document.getElementById('statParticipacion').textContent = pct + '%';
    document.getElementById('statPuestos').textContent       = data.resumen.total_puestos || 0;
}

// ── Ganadores por puesto ──
function renderGanadores(data) {
    const container = document.getElementById('rowGanadores');
    let html = '';

    for (const [puesto, votos] of Object.entries(data.por_puesto)) {
        const normales = votos.filter(v => v.tipo === 'normal');
        const total    = normales.reduce((s,v) => s + parseInt(v.votos), 0);
        const ganador  = normales[0];

        if (!ganador) {
            html += `
            <div class="col-lg-4 col-md-6">
                <div class="sin-votos-card">
                    <div class="ganador-puesto"><i class="bi bi-dash-circle"></i>${htmlEncode(puesto)}</div>
                    <div class="ganador-nombre">Sin votos registrados</div>
                </div>
            </div>`;
            continue;
        }

        const pct = total > 0 ? ((parseInt(ganador.votos) / total) * 100).toFixed(1) : 0;

        html += `
        <div class="col-lg-4 col-md-6">
            <div class="ganador-card">
                <div class="ganador-puesto"><i class="bi bi-award-fill"></i>${htmlEncode(puesto)}</div>
                <div class="ganador-nombre">${htmlEncode(ganador.nombre_candidato)}</div>
                <div class="ganador-partido">${htmlEncode(ganador.partido)}</div>
                <div class="ganador-votos">${parseInt(ganador.votos).toLocaleString()}</div>
                <div class="ganador-pct">${pct}% de los votos válidos · ${total.toLocaleString()} total</div>
                <div class="ganador-barra">
                    <div class="ganador-barra-fill" style="width:${pct}%"></div>
                </div>
            </div>
        </div>`;
    }

    container.innerHTML = html || '<div class="col-12 text-center text-muted py-4">Sin datos</div>';
}

// ── Chart partidos barras ──
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

// ── Chart pie ──
function renderChartPie(data) {
    destroyChart('chartPie');
    const labels = data.por_partido.map(p => p.partido);
    const values = data.por_partido.map(p => parseInt(p.votos));

    charts['chartPie'] = new Chart(document.getElementById('chartPie'), {
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
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font, usePointStyle: true, padding: 14 }
                },
                tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} votos` } }
            }
        }
    });
}

// ── Gráficas por puesto ──
function renderGraficasPuestos(data) {
    const container = document.getElementById('rowGraficasPuestos');
    let html = '';
    let idx  = 0;

    for (const [puesto, votos] of Object.entries(data.por_puesto)) {
        const normales = votos.filter(v => v.tipo === 'normal');
        if (normales.length === 0) continue;
        const color = PALETTE[idx % PALETTE.length];
        html += `
        <div class="col-lg-4 col-md-6">
            <div class="puesto-grafica-card">
                <div class="puesto-grafica-title">
                    <i class="bi bi-person-fill" style="color:${color};"></i>
                    ${htmlEncode(puesto)}
                </div>
                <div class="puesto-grafica-sub">${normales.length} candidato(s)</div>
                <canvas id="chartPuesto_${idx}"></canvas>
            </div>
        </div>`;
        idx++;
    }

    container.innerHTML = html;

    // Renderizar cada canvas
    idx = 0;
    for (const [puesto, votos] of Object.entries(data.por_puesto)) {
        const normales = votos.filter(v => v.tipo === 'normal');
        if (normales.length === 0) continue;

        const color  = PALETTE[idx % PALETTE.length];
        const labels = normales.map(v => v.nombre_candidato);
        const values = normales.map(v => parseInt(v.votos));
        const bgColors = values.map((_,i) => hexToRgba(color, i === 0 ? 0.85 : Math.max(0.2, 0.7 - i*0.15)));

        const id = `chartPuesto_${idx}`;
        destroyChart(id);

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
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font, maxRotation: 30 } },
                    y: { grid: { color: gridColor }, ticks: { font }, beginAtZero: true }
                }
            }
        });

        idx++;
    }
}

// ── Tabla completa ──
function renderTabla(data) {
    if ($.fn.DataTable.isDataTable('#tablaResultados')) {
        $('#tablaResultados').DataTable().destroy();
    }

    let html = '';
    let pos  = 1;
    const totales = {};

    for (const [puesto, votos] of Object.entries(data.por_puesto)) {
        totales[puesto] = votos
            .filter(v => v.tipo === 'normal')
            .reduce((s,v) => s + parseInt(v.votos), 0);
    }

    for (const [puesto, votos] of Object.entries(data.por_puesto)) {
        const total = totales[puesto] || 1;
        votos.filter(v => v.tipo === 'normal').forEach((v, idx) => {
            const pct       = ((parseInt(v.votos) / total) * 100).toFixed(1);
            const esGanador = idx === 0;
            const medal     = idx === 0 ? '🥇' : idx === 1 ? '🥈' : idx === 2 ? '🥉' : pos;

            html += `<tr class="${esGanador ? 'fila-ganador' : ''}">
                <td>${medal}</td>
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
                <td>${esGanador
                    ? '<span class="badge" style="background:#F59E0B;color:#1E293B;">🏆 Ganando</span>'
                    : '<span class="badge bg-light text-dark">En curso</span>'}</td>
            </tr>`;
            pos++;
        });
    }

    document.getElementById('cuerpoTabla').innerHTML = html ||
        '<tr><td colspan="7" class="text-center text-muted py-4">Sin datos</td></tr>';

    $('#tablaResultados').DataTable({
        language: {
            emptyTable: "Sin resultados",
            info: "Mostrando _START_ a _END_ de _TOTAL_",
            infoEmpty: "0 registros",
            lengthMenu: "Mostrar _MENU_ entradas",
            search: "Buscar:",
            zeroRecords: "Sin resultados",
            paginate: { next: "Siguiente", previous: "Anterior" }
        },
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50]
    });
}

// ── Render completo ──
function renderAll(data) {
    renderStats(data);
    renderGanadores(data);
    renderChartPartidos(data);
    renderChartPie(data);
    renderGraficasPuestos(data);
    renderTabla(data);
}

// ── Cargar elección ──
async function cargarEleccion(id) {
    document.getElementById('loadingOverlay').classList.remove('d-none');
    document.getElementById('contenidoResultados').style.opacity = '0.4';
    try {
        const res  = await fetch(`${CTRL_URL}?accion=getReporte&id_eleccion=${id}`);
        const data = await res.json();
        renderAll(data);
    } catch(e) {
        console.error(e);
    } finally {
        document.getElementById('loadingOverlay').classList.add('d-none');
        document.getElementById('contenidoResultados').style.opacity = '1';
    }
}

// ── Selector ──
document.getElementById('selectorEleccion').addEventListener('change', function() {
    cargarEleccion(this.value);
});

// ══════════════════════════════
// ACTA PDF
// ══════════════════════════════
document.getElementById('btnPDF').addEventListener('click', async function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generando acta...';

    try {
        const { jsPDF } = window.jspdf;
        const pdf    = new jsPDF('p', 'mm', 'a4');
        const pageW  = pdf.internal.pageSize.getWidth();
        const pageH  = pdf.internal.pageSize.getHeight();
        const margin = 14;
        let   y      = margin;

        const nombreEleccion = document.getElementById('selectorEleccion').selectedOptions[0]?.text || 'Elección';

        // ── Membrete ──
        pdf.setFillColor(30, 58, 95);
        pdf.rect(0, 0, pageW, 28, 'F');
        pdf.setFillColor(200, 168, 75);
        pdf.rect(0, 28, pageW, 2, 'F');

        pdf.setTextColor(200, 168, 75);
        pdf.setFontSize(16);
        pdf.setFont('helvetica', 'bold');
        pdf.text('VOTOSECURE', pageW / 2, 12, { align: 'center' });

        pdf.setTextColor(180, 200, 220);
        pdf.setFontSize(9);
        pdf.setFont('helvetica', 'normal');
        pdf.text('Plataforma Digital de Votación Segura', pageW / 2, 20, { align: 'center' });

        y = 40;

        // ── Título del acta ──
        pdf.setTextColor(30, 58, 95);
        pdf.setFontSize(13);
        pdf.setFont('helvetica', 'bold');
        pdf.text('ACTA OFICIAL DE RESULTADOS', pageW / 2, y, { align: 'center' });
        y += 6;

        pdf.setFontSize(10);
        pdf.setFont('helvetica', 'normal');
        pdf.setTextColor(100, 116, 139);
        pdf.text(nombreEleccion, pageW / 2, y, { align: 'center' });
        y += 4;
        pdf.text('Generado: ' + new Date().toLocaleString('es-MX'), pageW / 2, y, { align: 'center' });
        y += 3;

        // Línea separadora
        pdf.setDrawColor(200, 168, 75);
        pdf.setLineWidth(0.5);
        pdf.line(margin, y, pageW - margin, y);
        y += 8;

        // ── Stats resumen ──
        const stats = [
            ['Votaron',       document.getElementById('statVotaron').textContent],
            ['Participación', document.getElementById('statParticipacion').textContent],
            ['Partido líder', document.getElementById('statPartidoLider').textContent],
            ['Puestos',       document.getElementById('statPuestos').textContent],
        ];

        pdf.setFontSize(8);
        pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(30, 58, 95);
        pdf.text('RESUMEN GENERAL', margin, y);
        y += 5;

        const colW = (pageW - margin * 2) / 4;
        stats.forEach(([label, value], i) => {
            const x = margin + i * colW;
            pdf.setFillColor(240, 244, 249);
            pdf.roundedRect(x, y, colW - 3, 16, 2, 2, 'F');
            pdf.setDrawColor(200, 210, 225);
            pdf.roundedRect(x, y, colW - 3, 16, 2, 2, 'S');
            pdf.setTextColor(30, 58, 95);
            pdf.setFontSize(11);
            pdf.setFont('helvetica', 'bold');
            pdf.text(String(value), x + (colW - 3) / 2, y + 8, { align: 'center' });
            pdf.setFontSize(7);
            pdf.setFont('helvetica', 'normal');
            pdf.setTextColor(100, 116, 139);
            pdf.text(label, x + (colW - 3) / 2, y + 14, { align: 'center' });
        });
        y += 22;

        // ── Ganadores por puesto ──
        pdf.setFontSize(8);
        pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(30, 58, 95);
        pdf.text('GANADORES POR PUESTO', margin, y);
        y += 5;

        const ganadorCards = document.querySelectorAll('.ganador-card');
        ganadorCards.forEach(card => {
            if (y + 22 > pageH - margin) { pdf.addPage(); y = margin; }

            const puesto  = card.querySelector('.ganador-puesto')?.textContent.trim().replace(/[^\w\s]/g,'').trim() || '';
            const nombre  = card.querySelector('.ganador-nombre')?.textContent.trim() || '';
            const partido = card.querySelector('.ganador-partido')?.textContent.trim() || '';
            const votos   = card.querySelector('.ganador-votos')?.textContent.trim() || '';
            const pct     = card.querySelector('.ganador-pct')?.textContent.trim() || '';

            pdf.setFillColor(255, 251, 235);
            pdf.setDrawColor(245, 158, 11);
            pdf.setLineWidth(0.3);
            pdf.roundedRect(margin, y, pageW - margin * 2, 20, 3, 3, 'FD');

            // Borde izquierdo dorado
            pdf.setFillColor(245, 158, 11);
            pdf.rect(margin, y, 3, 20, 'F');

            pdf.setFontSize(7);
            pdf.setFont('helvetica', 'bold');
            pdf.setTextColor(146, 64, 14);
            pdf.text(puesto.toUpperCase(), margin + 6, y + 5);

            pdf.setFontSize(10);
            pdf.setFont('helvetica', 'bold');
            pdf.setTextColor(30, 58, 95);
            pdf.text(nombre, margin + 6, y + 11);

            pdf.setFontSize(8);
            pdf.setFont('helvetica', 'normal');
            pdf.setTextColor(120, 113, 108);
            pdf.text(partido, margin + 6, y + 17);

            // Votos a la derecha
            pdf.setFontSize(14);
            pdf.setFont('helvetica', 'bold');
            pdf.setTextColor(245, 158, 11);
            pdf.text(votos, pageW - margin - 4, y + 11, { align: 'right' });

            pdf.setFontSize(7);
            pdf.setFont('helvetica', 'normal');
            pdf.setTextColor(120, 113, 108);
            pdf.text(pct.split('·')[0].trim(), pageW - margin - 4, y + 17, { align: 'right' });

            y += 24;
        });

        // ── Gráficas ──
        pdf.setFontSize(8);
        pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(30, 58, 95);

        if (y + 10 > pageH - margin) { pdf.addPage(); y = margin; }
        pdf.text('COMPARATIVA DE VOTOS POR PARTIDO', margin, y);
        y += 4;

        const canvasPartidos = document.getElementById('chartPartidos');
        if (canvasPartidos) {
            const imgP = canvasPartidos.toDataURL('image/png', 1.0);
            const h    = 60;
            if (y + h + 4 > pageH - margin) { pdf.addPage(); y = margin; }
            pdf.setFillColor(248, 250, 252);
            pdf.roundedRect(margin, y, pageW - margin*2, h + 4, 3, 3, 'F');
            pdf.addImage(imgP, 'PNG', margin + 2, y + 2, pageW - margin*2 - 4, h);
            y += h + 10;
        }

        // Gráficas por puesto (2 columnas)
        if (y + 10 > pageH - margin) { pdf.addPage(); y = margin; }
        pdf.setFontSize(8);
        pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(30, 58, 95);
        pdf.text('DETALLE POR PUESTO', margin, y);
        y += 4;

        const canvasesPuesto = document.querySelectorAll('[id^="chartPuesto_"]');
        const halfW = (pageW - margin * 2 - 4) / 2;
        const hGraf = 45;
        let col = 0;

        canvasesPuesto.forEach(canvas => {
            if (col === 0 && y + hGraf + 4 > pageH - margin) {
                pdf.addPage(); y = margin;
            }
            const x = margin + col * (halfW + 4);
            pdf.setFillColor(248, 250, 252);
            pdf.roundedRect(x, y, halfW, hGraf + 4, 3, 3, 'F');
            const img = canvas.toDataURL('image/png', 1.0);
            pdf.addImage(img, 'PNG', x + 2, y + 2, halfW - 4, hGraf);
            col++;
            if (col >= 2) { col = 0; y += hGraf + 8; }
        });
        if (col === 1) y += hGraf + 8;

        // ── Tabla completa ──
        if (y + 20 > pageH - margin) { pdf.addPage(); y = margin; }

        pdf.setFontSize(8);
        pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(30, 58, 95);
        pdf.text('TABLA COMPLETA DE CANDIDATOS', margin, y);
        y += 5;

        const cols  = ['Puesto', 'Candidato', 'Partido', 'Votos', '%', 'Estado'];
        const colsW = [28, 52, 34, 18, 16, 24];
        const rowH  = 7;

        pdf.setFillColor(30, 58, 95);
        pdf.rect(margin, y, pageW - margin*2, rowH, 'F');
        pdf.setTextColor(184, 204, 224);
        pdf.setFontSize(7);
        pdf.setFont('helvetica', 'bold');
        let xp = margin + 2;
        cols.forEach((c,i) => { pdf.text(c, xp, y+5); xp += colsW[i]; });
        y += rowH;

        let ri = 0;
        document.querySelectorAll('#cuerpoTabla tr').forEach(fila => {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length < 5) return;
            if (y + rowH > pageH - margin) { pdf.addPage(); y = margin + 5; }

            const esGanador = fila.classList.contains('fila-ganador');
            pdf.setFillColor(esGanador ? 255 : (ri%2===0?255:248), esGanador ? 248 : (ri%2===0?255:250), esGanador ? 230 : (ri%2===0?255:252));
            pdf.rect(margin, y, pageW - margin*2, rowH, 'F');
            if (esGanador) { pdf.setFillColor(245,158,11); pdf.rect(margin, y, 2, rowH, 'F'); }

            pdf.setTextColor(51, 65, 85);
            pdf.setFontSize(7);
            pdf.setFont('helvetica', esGanador ? 'bold' : 'normal');

            const textos = [
                celdas[1]?.textContent.trim() || '',
                celdas[2]?.textContent.trim() || '',
                celdas[3]?.textContent.trim() || '',
                celdas[4]?.textContent.trim() || '',
                celdas[5]?.textContent.trim().replace(/[^\d.%]/g,'') || '',
                esGanador ? 'Ganando' : 'En curso',
            ];

            xp = margin + 2;
            textos.forEach((txt,i) => {
                const max = colsW[i] - 2;
                const t   = txt.length > max/1.8 ? txt.substring(0, Math.floor(max/1.8)) + '..' : txt;
                pdf.text(t, xp, y+5);
                xp += colsW[i];
            });

            pdf.setDrawColor(226,232,240);
            pdf.line(margin, y+rowH, pageW-margin, y+rowH);
            y += rowH; ri++;
        });

        // ── Firma / sello ──
        if (y + 35 > pageH - margin) { pdf.addPage(); y = margin; }
        y += 10;

        pdf.setDrawColor(200, 168, 75);
        pdf.setLineWidth(0.3);
        pdf.line(margin, y, pageW - margin, y);
        y += 6;

        pdf.setTextColor(30, 58, 95);
        pdf.setFontSize(8);
        pdf.setFont('helvetica', 'bold');
        pdf.text('CERTIFICACIÓN', pageW/2, y, { align: 'center' });
        y += 5;
        pdf.setFont('helvetica', 'normal');
        pdf.setFontSize(7);
        pdf.setTextColor(100, 116, 139);
        pdf.text('Este documento ha sido generado automáticamente por el sistema VotoSecure.', pageW/2, y, { align: 'center' });
        y += 4;
        pdf.text('Los resultados aquí presentados son de carácter oficial y han sido verificados por el sistema.', pageW/2, y, { align: 'center' });
        y += 10;

        // Líneas de firma
        const firmaW = 55;
        const firmas = ['Administrador del Sistema', 'Responsable Electoral', 'Testigo'];
        firmas.forEach((f, i) => {
            const x = margin + i * ((pageW - margin*2) / 3) + ((pageW - margin*2) / 3 - firmaW) / 2;
            pdf.setDrawColor(30, 58, 95);
            pdf.setLineWidth(0.3);
            pdf.line(x, y, x + firmaW, y);
            pdf.setTextColor(100, 116, 139);
            pdf.setFontSize(7);
            pdf.text(f, x + firmaW/2, y+4, { align: 'center' });
        });

        // ── Pie en todas las páginas ──
        const total = pdf.internal.getNumberOfPages();
        for (let i = 1; i <= total; i++) {
            pdf.setPage(i);
            pdf.setFillColor(30, 58, 95);
            pdf.rect(0, pageH - 10, pageW, 10, 'F');
            pdf.setTextColor(180, 200, 220);
            pdf.setFontSize(7);
            pdf.setFont('helvetica', 'normal');
            pdf.text('VotoSecure — Documento Confidencial', margin, pageH - 4);
            pdf.setTextColor(200, 168, 75);
            pdf.text(`Página ${i} de ${total}`, pageW - margin, pageH - 4, { align: 'right' });
        }

        pdf.save(`acta_resultados_${Date.now()}.pdf`);

    } catch(err) {
        console.error('Error generando acta:', err);
        alert('Error al generar el acta PDF');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Acta PDF';
    }
});

// ── Arranque ──
if (RESULTADOS_INICIAL) {
    renderAll(RESULTADOS_INICIAL);
} else if (ID_ELECCION_DEFAULT > 0) {
    cargarEleccion(ID_ELECCION_DEFAULT);
}