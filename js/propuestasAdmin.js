const CTRL = '../../Controlador/propuestasCtrl.php';

// ── DataTable ──
$(document).ready(function() {
    $('#tablaPropuestas').DataTable({
        language: {
            emptyTable:  "Sin propuestas registradas",
            info:        "Mostrando _START_ a _END_ de _TOTAL_",
            infoEmpty:   "0 registros",
            lengthMenu:  "Mostrar _MENU_ entradas",
            search:      "Buscar:",
            zeroRecords: "Sin resultados",
            paginate:    { next: "Siguiente", previous: "Anterior" }
        },
        responsive:  true,
        pageLength:  10,
        lengthMenu:  [10, 25, 50],
        columnDefs:  [{ orderable: false, targets: [6] }]
    });
});

// ── Guardar ──
document.getElementById('formAgregar').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true; btn.innerHTML = 'Guardando...';

    const fd = new FormData(this);
    fd.append('accion', 'crear');

    try {
        const res  = await fetch(CTRL, { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalAgregar')).hide();
            Swal.fire({ icon: 'success', title: '¡Guardada!', text: data.message,
                showConfirmButton: false, timer: 2000 }).then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.error });
        }
    } catch(err) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
    }
    btn.disabled = false; btn.innerHTML = 'Guardar';
});

// ── Abrir editar ──
document.querySelectorAll('.btn-editar').forEach(btn => {
    btn.addEventListener('click', async function() {
        const id = this.getAttribute('data-id');
        try {
            const res  = await fetch(`${CTRL}?accion=obtener&id=${id}`);
            const data = await res.json();
            if (data.success) {
                const p = data.data;
                document.getElementById('editar_id').value        = p.id_propuesta;
                document.getElementById('editar_candidato').value = p.candidato_id;
                document.getElementById('editar_titulo').value    = p.titulo;
                document.getElementById('editar_slogan').value    = p.slogan || '';
                document.getElementById('editar_mision').value    = p.mision || '';
                document.getElementById('editar_detalle').value   = p.propuesta_detallada;
                document.getElementById('editar_video').value     = p.video_url || '';
                new bootstrap.Modal(document.getElementById('modalEditar')).show();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.error });
            }
        } catch(err) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
        }
    });
});

// ── Actualizar ──
document.getElementById('formEditar').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnActualizar');
    btn.disabled = true; btn.innerHTML = 'Actualizando...';

    const fd = new FormData(this);
    fd.append('accion', 'actualizar');

    try {
        const res  = await fetch(CTRL, { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
            Swal.fire({ icon: 'success', title: '¡Actualizada!', text: data.message,
                showConfirmButton: false, timer: 2000 }).then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.error });
        }
    } catch(err) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
    }
    btn.disabled = false; btn.innerHTML = 'Actualizar';
});

// ── Eliminar ──
document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', async function() {
        const id     = this.getAttribute('data-id');
        const titulo = this.getAttribute('data-titulo');

        const conf = await Swal.fire({
            title: '¿Eliminar propuesta?',
            html: `<strong>${titulo}</strong><br><small class="text-muted">Esta acción no se puede deshacer</small>`,
            icon: 'warning',
            showCancelButton: true,
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
                Swal.fire({ icon: 'success', title: '¡Eliminada!', text: data.message,
                    showConfirmButton: false, timer: 2000 }).then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.error });
            }
        } catch(err) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
        }
    });
});