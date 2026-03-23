// ── Consulta de Sector Electoral (CSE) ───────────────────────────────────
// Archivo: js/consultaCasilla.js
// Usado en: index.php

const CTRL_CONSULTA = 'Controlador/consultaCasillaCtrl.php';

const inputCurp   = document.getElementById('cse-curp-input');
const charCount   = document.getElementById('cse-char-count');
const hintText    = document.getElementById('cse-hint-text');
const iconDefault = document.getElementById('cse-icon-default');
const iconOk      = document.getElementById('cse-icon-ok');
const iconErr     = document.getElementById('cse-icon-err');
const resultBox   = document.getElementById('cse-result');
const resultTitle = document.getElementById('cse-result-title');
const resultBody  = document.getElementById('cse-result-body');

// ── Contador de caracteres + validación visual en tiempo real ─────────────
inputCurp.addEventListener('input', function () {
    const val = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    this.value = val;

    const len = val.length;
    charCount.textContent = `${len}/18`;

    // Icono y hint
    if (len === 0) {
        setIcon('default');
        hintText.textContent = 'La CURP tiene exactamente 18 caracteres alfanuméricos.';
        hintText.style.color = '';
    } else if (len < 18) {
        setIcon('default');
        hintText.textContent = `Faltan ${18 - len} caracteres.`;
        hintText.style.color = '#b08000';
    } else {
        setIcon('ok');
        hintText.textContent = 'Formato correcto. Puedes consultar.';
        hintText.style.color = '#1A7340';
    }

    // Limpiar resultado previo al editar
    ocultarResultado();
});

function setIcon(estado) {
    iconDefault.style.display = estado === 'default' ? '' : 'none';
    iconOk.style.display      = estado === 'ok'      ? '' : 'none';
    iconErr.style.display     = estado === 'err'     ? '' : 'none';
}

function ocultarResultado() {
    resultBox.classList.remove('cse-result--show', 'cse-result--success', 'cse-result--error');
    resultTitle.textContent = '';
    resultBody.innerHTML    = '';
}

function mostrarError(msg) {
    setIcon('err');
    resultBox.classList.remove('cse-result--success');
    resultBox.classList.add('cse-result--show', 'cse-result--error');
    resultTitle.textContent = '⚠ No encontrado';
    resultBody.innerHTML    = `<span>${msg}</span>`;
}

function mostrarResultado(data) {
    const v = data.votante;
    const c = data.casilla;

    const nombreCompleto = `${v.nombre} ${v.apellido_paterno} ${v.apellido_materno}`.trim();

    // ── Bloque casilla ──
    let htmlCasilla;
    if (c && c.activa) {
        htmlCasilla = `
            <div class="cse-info-block cse-casilla-block">
                <div class="cse-info-label"><i class="cse-ico">🏛</i> Casilla asignada</div>
                <div class="cse-info-value cse-value-big">Sección ${c.numero_seccion} — ${c.tipo}</div>
                <div class="cse-info-sub">${c.direccion}</div>
                <div class="cse-info-sub">${c.municipio_casilla}, ${c.estado_casilla}</div>
            </div>`;
    } else {
        htmlCasilla = `
            <div class="cse-info-block cse-casilla-block cse-no-casilla">
                <div class="cse-info-label"><i class="cse-ico">🏛</i> Casilla</div>
                <div class="cse-info-value">Tu sección (${v.seccion}) aún no tiene casilla asignada.</div>
            </div>`;
    }

    resultBox.classList.remove('cse-result--error');
    resultBox.classList.add('cse-result--show', 'cse-result--success');
    resultTitle.textContent = `✓ Registro encontrado`;
    resultBody.innerHTML = `
        <div class="cse-result-grid">

            <div class="cse-info-block">
                <div class="cse-info-label"><i class="cse-ico">👤</i> Nombre completo</div>
                <div class="cse-info-value">${esc(nombreCompleto)}</div>
            </div>

            <div class="cse-info-block">
                <div class="cse-info-label"><i class="cse-ico">🪪</i> Clave de Elector</div>
                <div class="cse-info-value cse-mono">${esc(v.clave_elector || '—')}</div>
            </div>

            <div class="cse-info-block">
                <div class="cse-info-label"><i class="cse-ico">📍</i> Sección Electoral</div>
                <div class="cse-info-value">${v.seccion}</div>
            </div>

            <div class="cse-info-block">
                <div class="cse-info-label"><i class="cse-ico">🗺</i> Domicilio registrado</div>
                <div class="cse-info-value">${esc(v.colonia ? v.colonia + ', ' : '')}${esc(v.municipio)}, ${esc(v.entidad)}</div>
            </div>

            ${htmlCasilla}
        </div>
    `;
}

// Escapar HTML para evitar XSS
function esc(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// ── Submit ────────────────────────────────────────────────────────────────
document.getElementById('cse-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const curp = inputCurp.value.trim().toUpperCase();

    if (curp.length !== 18) {
        hintText.textContent = 'La CURP debe tener exactamente 18 caracteres.';
        hintText.style.color = '#C0392B';
        setIcon('err');
        return;
    }

    // Estado de carga
    const btnSubmit = this.querySelector('.cse-btn');
    const textoOriginal = btnSubmit.innerHTML;
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = `
        <svg viewBox="0 0 24 24" style="animation:spin .8s linear infinite">
            <path d="M12 4V2A10 10 0 0 0 2 12h2a8 8 0 0 1 8-8z"/>
        </svg>
        Consultando...`;

    ocultarResultado();

    try {
        const fd = new FormData();
        fd.append('curp', curp);

        const res  = await fetch(CTRL_CONSULTA, { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            mostrarResultado(data.data);
            setIcon('ok');
        } else {
            mostrarError(data.error);
        }
    } catch (_) {
        mostrarError('Error de conexión. Intenta nuevamente.');
    }

    btnSubmit.disabled = false;
    btnSubmit.innerHTML = textoOriginal;
});