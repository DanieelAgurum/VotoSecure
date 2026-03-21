const SVG_PLACEHOLDER = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' fill='%23dee2e6' viewBox='0 0 16 16'%3E%3Cpath d='M8 4a4 4 0 1 1 0 8 4 4 0 0 1 0-8zM2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z'/%3E%3C/svg%3E";
const CTRL = '../../Controlador/candidatosCtrl.php';

// ── Guardar ──
document.getElementById('formAgregarCandidato').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = 'Guardando...';

    try {
        const response = await fetch(CTRL, { method: 'POST', body: new FormData(this) });
        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalAgregar')).hide();
            Swal.fire({
                icon: 'success', title: '¡Guardado!',
                text: result.message || 'Candidato guardado correctamente',
                showConfirmButton: false, timer: 2000
            }).then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: result.error || 'Error al guardar' });
        }
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
    }

    btn.disabled = false;
    btn.innerHTML = 'Guardar';
});

// ── Eliminar ──
document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', async function() {
        const id = this.getAttribute('data-id');
        const confirm = await Swal.fire({
            title: '¿Eliminar candidato?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (confirm.isConfirmed) {
            try {
                const response = await fetch(CTRL + '?eliminar=' + id);
                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success', title: '¡Eliminado!',
                        text: data.message || 'Candidato eliminado',
                        showConfirmButton: false, timer: 2000
                    }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'Error al eliminar' });
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
            }
        }
    });
});

// ── Preview foto agregar ──
document.getElementById('foto').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => document.getElementById('previewFoto').src = e.target.result;
    reader.readAsDataURL(file);
});

document.getElementById('modalAgregar').addEventListener('hidden.bs.modal', function() {
    document.getElementById('formAgregarCandidato').reset();
    document.getElementById('previewFoto').src = SVG_PLACEHOLDER;
    document.getElementById('foto').value = '';
});

// ── Preview foto modificar ──
document.getElementById('modificar_foto').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => document.getElementById('modificar_previewFoto').src = e.target.result;
    reader.readAsDataURL(file);
});

document.getElementById('modalModificar').addEventListener('hidden.bs.modal', function() {
    document.getElementById('formModificarCandidato').reset();
    document.getElementById('modificar_previewFoto').src = SVG_PLACEHOLDER;
});

// ── Abrir modal modificar ──
document.querySelectorAll('.btn-modificar').forEach(btn => {
    btn.addEventListener('click', async function() {
        const id = this.getAttribute('data-id');
        try {
            const response = await fetch(CTRL + '?obtener=' + id);
            const data = await response.json();

            if (data.success) {
                const c = data.candidato;
                document.getElementById('modificar_id').value           = c.id;
                document.getElementById('modificar_nombre').value       = c.nombre;
                document.getElementById('modificar_apellido').value     = c.apellido;
                document.getElementById('modificar_id_partido').value   = c.id_partido;
                document.getElementById('modificar_id_eleccion').value  = c.id_eleccion; // ✅
                document.getElementById('modificar_cargo').value        = c.cargo;
                document.getElementById('modificar_distrito').value     = c.distrito;
                document.getElementById('modificar_correo').value       = c.correo;
                document.getElementById('modificar_telefono').value     = c.telefono || '';
                document.getElementById('modificar_previewFoto').src    = c.foto || SVG_PLACEHOLDER;
                document.getElementById(
                    c.estatus === 'activo' ? 'modificar_estatus_activo' : 'modificar_estatus_inactivo'
                ).checked = true;

                new bootstrap.Modal(document.getElementById('modalModificar')).show();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'Error al obtener candidato' });
            }
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
        }
    });
});

// ── Enviar modificar ──
document.getElementById('formModificarCandidato').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnModificar');
    btn.disabled = true;
    btn.innerHTML = 'Actualizando...';

    try {
        const response = await fetch(CTRL, { method: 'POST', body: new FormData(this) });
        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalModificar')).hide();
            Swal.fire({
                icon: 'success', title: '¡Actualizado!',
                text: result.message || 'Candidato actualizado correctamente',
                showConfirmButton: false, timer: 2000
            }).then(() => actualizarFila(result.candidato));
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: result.error || 'Error al actualizar' });
        }
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
    }

    btn.disabled = false;
    btn.innerHTML = 'Actualizar';
});

// ── Actualizar fila sin recargar ──
function actualizarFila(c) {
    const fila = document.querySelector(`tr[data-id="${c.id}"]`);
    if (!fila) { location.reload(); return; }

    fila.children[0].innerHTML = c.foto
        ? `<img src="${c.foto}" alt="Foto" class="rounded-circle" style="width:50px;height:50px;object-fit:cover;">`
        : `<div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;"><i class="bi bi-person text-white"></i></div>`;

    fila.children[2].textContent = c.nombre + ' ' + c.apellido;

    const partidoSelect = document.getElementById('modificar_id_partido');
    fila.children[3].textContent = partidoSelect.options[partidoSelect.selectedIndex].text;

    fila.children[4].textContent = c.tipo_nombre; // ✅ viene del servidor
    fila.children[5].textContent = c.cargo;
    fila.children[6].textContent = c.distrito;
    fila.children[7].innerHTML   = c.estatus === 'activo'
        ? '<span class="badge bg-success">Activo</span>'
        : '<span class="badge bg-danger">Inactivo</span>';
}