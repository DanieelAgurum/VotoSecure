<?php
session_start();

require_once("../../Modelo/candidatosMdl.php");

// Verificar sesión
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header('Location: /VotoSecure/Vista/login.php');
    exit();
}

// Función para generar token CSRF
function generarTokenCSRF()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Generar token CSRF
$csrfToken = generarTokenCSRF();

// Instanciar modelo
$candidato = new Candidato();

// Obtener datos
$lista = $candidato->obtenerCandidatos();
$partidos = $candidato->obtenerPartidos();
$tiposEleccion = $candidato->obtenerTiposEleccion();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Candidatos - VotoSecure</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/dash.css">
    <link rel="stylesheet" href="../../css/CandidatosAd.css">
</head>

<body>

    <?php include __DIR__ . '/../../components/Admin/Navbar.php'; ?>

    <div class="main-content">

        <div class="card shadow">
            <div class="card-header text-center fw-bold bg-primary text-white py-3">
                <h4 class="mb-0">Gestión de Candidatos</h4>
            </div>

            <div class="card-body">

                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                    <i class="bi bi-person-plus"></i> Agregar Candidato
                </button>

                <!-- Mensaje de error/success -->
                <div id="mensajeAlert"></div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Foto</th>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Partido</th>
                                <th>Tipo</th>
                                <th>Cargo</th>
                                <th>Distrito</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($lista)): ?>
                                <tr>
                                    <td colspan="9" class="text-center">No hay candidatos registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($lista as $row): ?>
                                    <tr data-id="<?= $row['id'] ?>">
                                        <td>
                                            <?php if (!empty($row['foto'])): ?>
                                                <?php if (strpos($row['foto'], 'data:') === 0): ?>
                                                    <img src="<?= htmlspecialchars($row['foto']) ?>"
                                                        alt="Foto" class="rounded-circle"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <img src="../../img/candidatos/<?= htmlspecialchars($row['foto']) ?>"
                                                        alt="Foto" class="rounded-circle"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 50px;">
                                                    <i class="bi bi-person text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= htmlspecialchars($row['nombre'] . " " . $row['apellido']) ?></td>
                                        <td><?= htmlspecialchars($row['partido_nombre']) ?></td>
                                        <td><?= htmlspecialchars($row['tipo_nombre']) ?></td>
                                        <td><?= htmlspecialchars($row['cargo']) ?></td>
                                        <td><?= htmlspecialchars($row['distrito']) ?></td>
                                        <td>
                                            <?php if ($row['estatus'] == 'activo'): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm btn-modificar" data-id="<?= $row['id'] ?>" title="Modificar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm btn-eliminar" data-id="<?= $row['id'] ?>" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/modales/candidatos.php'; ?>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/VotoSecure/js/dash.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Guardar candidato con AJAX -->
    <script>
        document.getElementById('formAgregarCandidato').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn = document.getElementById('btnGuardar');
            btn.disabled = true;
            btn.innerHTML = 'Guardando...';

            const formData = new FormData(this);

            try {
                const response = await fetch('../../Controlador/candidatosCtrl.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Cerrar el modal primero
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalAgregar'));
                    modal.hide();

                    // Mostrar SweetAlert igual que en eliminar
                    Swal.fire({
                        icon: 'success',
                        title: '¡Guardado!',
                        text: result.message || 'Candidato guardado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.error || 'Error al guardar'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión'
                });
            }

            btn.disabled = false;
            btn.innerHTML = 'Guardar';
        });
    </script>

    <!-- Eliminar candidato con AJAX -->
    <script>
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', async function() {
                const id = this.getAttribute('data-id');

                const result = await Swal.fire({
                    title: '¿Eliminar candidato?',
                    text: "Esta acción no se puede deshacer",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch('../../Controlador/candidatosCtrl.php?eliminar=' + id);
                        const data = await response.json();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: data.message || 'Candidato eliminado',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.error || 'Error al eliminar'
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error de conexión'
                        });
                    }
                }
            });
        });
    </script>

    <!-- Preview de foto -->
    <script>
        document.getElementById('foto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('previewFoto');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                };

                reader.readAsDataURL(file);
            }
        });

        // Resetear preview al cerrar el modal
        document.getElementById('modalAgregar').addEventListener('hidden.bs.modal', function() {
            const preview = document.getElementById('previewFoto');
            const input = document.getElementById('foto');

            preview.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' fill='%23dee2e6' viewBox='0 0 16 16'%3E%3Cpath d='M8 4a4 4 0 1 1 0 8 4 4 0 0 1 0-8zM2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z'/%3E%3Cpath d='M3.5 0a.5.5 0 0 1 .5.5V1a.5.5 0 0 1 .5.5h7a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5V1a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1h2a1.5 1.5 0 0 0 1.5-1.5V1a1.5 1.5 0 0 0-1.5-1.5h-7A1.5 1.5 0 0 0 1 1v12a1.5 1.5 0 0 0 1.5 1.5h7a1.5 1.5 0 0 0 1.5-1.5V3a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2z'/%3E%3C/svg%3E";
            input.value = '';

            // Resetear el formulario
            document.getElementById('formAgregarCandidato').reset();
        });
    </script>

    <!-- Preview de foto para modificar -->
    <script>
        document.getElementById('modificar_foto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('modificar_previewFoto');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                };

                reader.readAsDataURL(file);
            }
        });

        // Resetear preview al cerrar el modal de modificar
        document.getElementById('modalModificar').addEventListener('hidden.bs.modal', function() {
            document.getElementById('formModificarCandidato').reset();
            document.getElementById('modificar_previewFoto').src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' fill='%23dee2e6' viewBox='0 0 16 16'%3E%3Cpath d='M8 4a4 4 0 1 1 0 8 4 4 0 0 1 0-8zM2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z'/%3E%3Cpath d='M3.5 0a.5.5 0 0 1 .5.5V1a.5.5 0 0 1 .5.5h7a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5V1a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1h2a1.5 1.5 0 0 0 1.5-1.5V1a1.5 1.5 0 0 0-1.5-1.5h-7A1.5 1.5 0 0 0 1 1v12a1.5 1.5 0 0 0 1.5 1.5h7a1.5 1.5 0 0 0 1.5-1.5V3a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2z'/%3E%3C/svg%3E";
        });
    </script>

    <!-- Modificar candidato con AJAX -->
    <script>
        document.querySelectorAll('.btn-modificar').forEach(btn => {
            btn.addEventListener('click', async function() {
                const id = this.getAttribute('data-id');

                try {
                    const response = await fetch('../../Controlador/candidatosCtrl.php?obtener=' + id);
                    const data = await response.json();

                    if (data.success) {
                        const candidato = data.candidato;

                        // Llenar el formulario de modificar
                        document.getElementById('modificar_id').value = candidato.id;
                        document.getElementById('modificar_nombre').value = candidato.nombre;
                        document.getElementById('modificar_apellido').value = candidato.apellido;
                        document.getElementById('modificar_id_partido').value = candidato.id_partido;
                        document.getElementById('modificar_id_tipo').value = candidato.id_tipo;
                        document.getElementById('modificar_cargo').value = candidato.cargo;
                        document.getElementById('modificar_distrito').value = candidato.distrito;
                        document.getElementById('modificar_correo').value = candidato.correo;
                        document.getElementById('modificar_telefono').value = candidato.telefono || '';

                        // Foto
                        if (candidato.foto) {
                            document.getElementById('modificar_previewFoto').src = candidato.foto;
                        } else {
                            document.getElementById('modificar_previewFoto').src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' fill='%23dee2e6' viewBox='0 0 16 16'%3E%3Cpath d='M8 4a4 4 0 1 1 0 8 4 4 0 0 1 0-8zM2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z'/%3E%3Cpath d='M3.5 0a.5.5 0 0 1 .5.5V1a.5.5 0 0 1 .5.5h7a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5V1a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1h2a1.5 1.5 0 0 0 1.5-1.5V1a1.5 1.5 0 0 0-1.5-1.5h-7A1.5 1.5 0 0 0 1 1v12a1.5 1.5 0 0 0 1.5 1.5h7a1.5 1.5 0 0 0 1.5-1.5V3a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2z'/%3E%3C/svg%3E";
                        }

                        // Estatus
                        if (candidato.estatus === 'activo') {
                            document.getElementById('modificar_estatus_activo').checked = true;
                        } else {
                            document.getElementById('modificar_estatus_inactivo').checked = true;
                        }

                        // Abrir el modal
                        const modal = new bootstrap.Modal(document.getElementById('modalModificar'));
                        modal.show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error || 'Error al obtener datos del candidato'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión'
                    });
                }
            });
        });

        // Enviar formulario de modificar
        document.getElementById('formModificarCandidato').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn = document.getElementById('btnModificar');
            btn.disabled = true;
            btn.innerHTML = 'Actualizando...';

            const formData = new FormData(this);

            try {
                const response = await fetch('../../Controlador/candidatosCtrl.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Cerrar el modal primero
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalModificar'));
                    modal.hide();

                    // Mostrar SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: result.message || 'Candidato actualizado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        // Actualizar la fila directamente sin recargar la página
                        actualizarFilaCandidato(result.candidato);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.error || 'Error al actualizar'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión'
                });
            }

            btn.disabled = false;
            btn.innerHTML = 'Actualizar';
        });

        // Función para actualizar una fila de la tabla sin recargar
        function actualizarFilaCandidato(candidato) {
            const fila = document.querySelector(`tr[data-id="${candidato.id}"]`);

            if (fila) {
                // Actualizar foto
                const celdaFoto = fila.querySelector('td:first-child');
                if (candidato.foto) {
                    celdaFoto.innerHTML = `<img src="${candidato.foto}" alt="Foto" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">`;
                } else {
                    celdaFoto.innerHTML = `<div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="bi bi-person text-white"></i></div>`;
                }

                // Actualizar nombre
                fila.children[2].textContent = candidato.nombre + ' ' + candidato.apellido;

                // Actualizar partido
                const partidoSelect = document.getElementById('modificar_id_partido');
                const partidoNombre = partidoSelect.options[partidoSelect.selectedIndex].text;
                fila.children[3].textContent = partidoNombre;

                // Actualizar tipo
                const tipoSelect = document.getElementById('modificar_id_tipo');
                const tipoNombre = tipoSelect.options[tipoSelect.selectedIndex].text;
                fila.children[4].textContent = tipoNombre;

                // Actualizar cargo
                fila.children[5].textContent = candidato.cargo;

                // Actualizar distrito
                fila.children[6].textContent = candidato.distrito;

                // Actualizar estatus
                const celdaEstatus = fila.children[7];
                if (candidato.estatus === 'activo') {
                    celdaEstatus.innerHTML = '<span class="badge bg-success">Activo</span>';
                } else {
                    celdaEstatus.innerHTML = '<span class="badge bg-danger">Inactivo</span>';
                }
            } else {
                // Si no encuentra la fila, recargar la página
                location.reload();
            }
        }
    </script>

</body>

</html>