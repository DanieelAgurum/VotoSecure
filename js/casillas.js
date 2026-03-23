const CTRL = '../../Controlador/casillasCtrl.php';

// ── Instancia DataTable (guardada para manipularla sin recargar) ──────────
let dt;

// ── Helpers de HTML para celdas ──────────────────────────────────────────
function badgeTipo(tipo) {
    const cls = tipo === 'Especial' ? 'bg-info text-dark' : 'bg-primary';
    return `<span class="badge ${cls}">${tipo}</span>`;
}
function badgeActiva(activa) {
    return activa
        ? '<span class="badge bg-success">Sí</span>'
        : '<span class="badge bg-danger">No</span>';
}
function botonesAccion(id, seccion) {
    return `
        <button class="btn btn-sm btn-warning btn-modificar" data-id="${id}" title="Modificar">
            <i class="bi bi-pencil"></i>
        </button>
        <button class="btn btn-sm btn-danger btn-eliminar" data-id="${id}" data-seccion="${seccion}" title="Eliminar">
            <i class="bi bi-trash"></i>
        </button>`;
}

// ── Rebindear botones tras insertar/actualizar filas ─────────────────────
function bindBotones() {
    // Eliminar listeners anteriores clonando el nodo (evita duplicados)
    document.querySelectorAll('.btn-modificar').forEach(btn => {
        const nuevo = btn.cloneNode(true);
        btn.parentNode.replaceChild(nuevo, btn);
    });
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        const nuevo = btn.cloneNode(true);
        btn.parentNode.replaceChild(nuevo, btn);
    });

    document.querySelectorAll('.btn-modificar').forEach(btn => {
        btn.addEventListener('click', abrirModificar);
    });
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', eliminar);
    });
}

// ── DataTable ─────────────────────────────────────────────────────────────
$(document).ready(function () {
    dt = $('#tablaCasillas').DataTable({
        language: {
            emptyTable:     'No hay casillas registradas',
            info:           'Mostrando _START_ a _END_ de _TOTAL_ entradas',
            infoEmpty:      'Mostrando 0 a 0 de 0 entradas',
            infoFiltered:   '(filtrado de _MAX_ total entradas)',
            lengthMenu:     'Mostrar _MENU_ entradas',
            loadingRecords: 'Cargando...',
            processing:     'Procesando...',
            search:         'Buscar:',
            zeroRecords:    'Sin resultados encontrados',
            paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' }
        },
        responsive:  true,
        pageLength:  10,
        lengthMenu:  [5, 10, 25, 50],
        columnDefs:  [{ orderable: false, targets: [7] }]
    });

    // Bindear botones que ya existen en la carga inicial
    bindBotones();
});

// ── Matcher Select2 ───────────────────────────────────────────────────────
function matcherSeccion(params, data) {
    if (!params.term || params.term.trim() === '') return data;
    const term = params.term.trim().toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    const attr = ($(data.element).data('search') || data.text)
        .toString().toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    return attr.includes(term) ? data : null;
}

function initSelect2(selector) {
    // Destruir instancia previa si ya existe
    if ($(selector).hasClass('select2-hidden-accessible')) {
        $(selector).select2('destroy');
    }
    $(selector).select2({
        theme:          'bootstrap-5',
        placeholder:    '— Busca por número, municipio o estado —',
        allowClear:     true,
        width:          '100%',
        dropdownParent: $(selector).closest('.modal'),
        language: {
            noResults: () => 'No se encontró ninguna sección',
            searching: () => 'Buscando...'
        },
        matcher: matcherSeccion
    });
}

// ── Marcar secciones ocupadas como disabled ───────────────────────────────
async function marcarOcupadas(selector, excluirId = 0) {
    try {
        const res  = await fetch(`${CTRL}?accion=secciones_disponibles&excluir=${excluirId}`);
        const data = await res.json();
        if (!data.success) return;

        const ocupadas = data.ocupadas.map(String);
        $(`${selector} option`).each(function () {
            const val = $(this).val();
            if (!val) return;
            if (ocupadas.includes(String(val))) {
                $(this).prop('disabled', true).text(`Sección ${val} (ocupada)`);
            } else {
                $(this).prop('disabled', false).text(`Sección ${val}`);
            }
        });
        $(selector).trigger('change.select2');
    } catch (_) {}
}

// ══════════════════════════════════════════════
// MODAL AGREGAR
// ══════════════════════════════════════════════
document.getElementById('modalAgregar').addEventListener('shown.bs.modal', function () {
    initSelect2('.select2-seccion-agregar');
    marcarOcupadas('#agregar_seccion');
});

document.getElementById('modalAgregar').addEventListener('hidden.bs.modal', function () {
    document.getElementById('formAgregar').reset();
    $('.select2-seccion-agregar').val(null).trigger('change');
});

document.getElementById('formAgregar').addEventListener('submit', async function (e) {
    e.preventDefault();

    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    const fd = new FormData(this);
    fd.append('accion', 'crear');

    try {
        const res  = await fetch(CTRL, { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalAgregar')).hide();

            // ── Insertar fila en DataTable sin recargar ──
            const c = data.casilla;
            dt.row.add([
                `<span class="text-muted small">${c.id_casilla}</span>`,
                `<span class="badge bg-secondary fs-6">${c.numero_seccion}</span>`,
                c.municipio,
                c.estado,
                badgeTipo(c.tipo),
                c.direccion,
                badgeActiva(c.activa),
                botonesAccion(c.id_casilla, c.numero_seccion)
            ]).node().setAttribute('data-id', c.id_casilla);
            dt.draw(false);
            bindBotones();

            Swal.fire({
                icon: 'success', title: '¡Casilla registrada!',
                text: data.message, showConfirmButton: false, timer: 1800
            });
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.error });
        }
    } catch (_) {
        Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.' });
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Guardar';
});

// ══════════════════════════════════════════════
// MODAL MODIFICAR
// ══════════════════════════════════════════════
document.getElementById('modalModificar').addEventListener('shown.bs.modal', function () {
    initSelect2('.select2-seccion-modificar');
});

async function abrirModificar() {
    const id = this.getAttribute('data-id');
    try {
        const res  = await fetch(`${CTRL}?accion=obtener&id=${id}`);
        const data = await res.json();

        if (!data.success) {
            Swal.fire({ icon: 'error', title: 'Error', text: data.error });
            return;
        }

        const c = data.data;
        document.getElementById('mod_id_casilla').value = c.id_casilla;
        document.getElementById('mod_tipo').value       = c.tipo;
        document.getElementById('mod_activa').value     = c.activa ? '1' : '0';
        document.getElementById('mod_direccion').value  = c.direccion;

        await marcarOcupadas('#mod_seccion', c.id_casilla);
        $('#mod_seccion').val(c.numero_seccion).trigger('change');

        new bootstrap.Modal(document.getElementById('modalModificar')).show();
    } catch (_) {
        Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudieron cargar los datos.' });
    }
}

document.getElementById('formModificar').addEventListener('submit', async function (e) {
    e.preventDefault();

    const btn = document.getElementById('btnModificar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Actualizando...';

    const fd = new FormData(this);
    fd.append('accion', 'modificar');

    try {
        const res  = await fetch(CTRL, { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalModificar')).hide();

            // ── Actualizar fila existente en DataTable sin recargar ──
            const c = data.casilla;
            const fila = $(`#tablaCasillas tr[data-id="${c.id_casilla}"]`);
            const dtRow = dt.row(fila);
            dtRow.data([
                `<span class="text-muted small">${c.id_casilla}</span>`,
                `<span class="badge bg-secondary fs-6">${c.numero_seccion}</span>`,
                c.municipio,
                c.estado,
                badgeTipo(c.tipo),
                c.direccion,
                badgeActiva(c.activa),
                botonesAccion(c.id_casilla, c.numero_seccion)
            ]).draw(false);
            // Restaurar data-id en el nodo (DataTable lo regenera)
            dtRow.node().setAttribute('data-id', c.id_casilla);
            bindBotones();

            Swal.fire({
                icon: 'success', title: '¡Actualizada!',
                text: data.message, showConfirmButton: false, timer: 1800
            });
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.error });
        }
    } catch (_) {
        Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.' });
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Actualizar';
});

// ══════════════════════════════════════════════
// ELIMINAR
// ══════════════════════════════════════════════
async function eliminar() {
    const id      = this.getAttribute('data-id');
    const seccion = this.getAttribute('data-seccion');

    const conf = await Swal.fire({
        title: '¿Eliminar casilla?',
        html:  `<strong>Sección ${seccion}</strong><br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
        icon:  'warning',
        showCancelButton:   true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor:  '#6c757d',
        confirmButtonText:  'Sí, eliminar',
        cancelButtonText:   'Cancelar'
    });

    if (!conf.isConfirmed) return;

    const fd = new FormData();
    fd.append('accion', 'eliminar');
    fd.append('id', id);

    try {
        const res  = await fetch(CTRL, { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            // ── Eliminar fila de DataTable sin recargar ──
            const fila = $(`#tablaCasillas tr[data-id="${id}"]`);
            dt.row(fila).remove().draw(false);

            Swal.fire({
                icon: 'success', title: '¡Eliminada!',
                text: data.message, showConfirmButton: false, timer: 1800
            });
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.error });
        }
    } catch (_) {
        Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.' });
    }
}