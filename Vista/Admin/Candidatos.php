<?php
session_start();

require_once("../../Modelo/candidatosMdl.php");

// Verificar sesión
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header('Location: /VotoSecure/Vista/login.php');
    exit();
}

// Función para generar token CSRF
function generarTokenCSRF() {
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
                        <?php if(empty($lista)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay candidatos registrados</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($lista as $row): ?>
                            <tr>
                                <td>
                                    <?php if(!empty($row['foto'])): ?>
                                        <?php if(strpos($row['foto'], 'data:') === 0): ?>
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
                                    <?php if($row['estatus'] == 'activo'): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm btn-eliminar" data-id="<?= $row['id'] ?>">
                                        <i class="bi bi-trash"></i> Eliminar
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


<!-- MODAL AGREGAR CANDIDATO -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form id="formAgregarCandidato" enctype="multipart/form-data">
        
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="accion" value="guardar">

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalAgregarLabel">
              <i class="bi bi-person-plus"></i> Agregar Candidato
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="nombre" id="nombre" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Apellido <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="apellido" id="apellido" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Partido <span class="text-danger">*</span></label>
                        <select class="form-select" name="id_partido" id="id_partido" required>
                            <option value="">Seleccione un partido...</option>
                            <?php if(!empty($partidos)): ?>
                                <?php foreach($partidos as $partido): ?>
                                    <option value="<?= $partido['id_partido'] ?>">
                                        <?= htmlspecialchars($partido['nombre_partido']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Elección <span class="text-danger">*</span></label>
                        <select class="form-select" name="id_tipo" id="id_tipo" required>
                            <option value="">Seleccione un tipo...</option>
                            <?php if(!empty($tiposEleccion)): ?>
                                <?php foreach($tiposEleccion as $tipo): ?>
                                    <option value="<?= $tipo['id_tipo'] ?>">
                                        <?= htmlspecialchars($tipo['nombre_tipo']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Cargo <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="cargo" id="cargo" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Distrito <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="distrito" id="distrito" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Correo <span class="text-danger">*</span></label>
                        <input class="form-control" type="email" name="correo" id="correo" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input class="form-control" type="tel" name="telefono" id="telefono">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Foto del Candidato</label>
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative">
                        <img id="previewFoto" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' fill='%23dee2e6' viewBox='0 0 16 16'%3E%3Cpath d='M8 4a4 4 0 1 1 0 8 4 4 0 0 1 0-8zM2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z'/%3E%3Cpath d='M3.5 0a.5.5 0 0 1 .5.5V1a.5.5 0 0 1 .5.5h7a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5V1a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1h2a1.5 1.5 0 0 0 1.5-1.5V1a1.5 1.5 0 0 0-1.5-1.5h-7A1.5 1.5 0 0 0 1 1v12a1.5 1.5 0 0 0 1.5 1.5h7a1.5 1.5 0 0 0 1.5-1.5V3a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2z'/%3E%3C/svg%3E" 
                             alt="Preview" class="rounded-circle" 
                             style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #dee2e6; cursor: pointer;">
                        <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/gif,image/webp" 
                               class="position-absolute" 
                               style="opacity: 0; width: 120px; height: 120px; top: 0; left: 0; cursor: pointer;">
                    </div>
                    <div>
                        <small class="text-muted">Formatos: JPEG, PNG, GIF, WebP</small><br>
                        <small class="text-muted">Tamaño máximo: 5MB</small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Estatus</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="estatus" id="estatus_activo" value="activo" checked>
                        <label class="form-check-label" for="estatus_activo">Activo</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="estatus" id="estatus_inactivo" value="inactivo">
                        <label class="form-check-label" for="estatus_inactivo">Inactivo</label>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success" id="btnGuardar">Guardar</button>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../js/dash.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- CSS para que SweetAlert aparezca sobre el modal -->
<style>
.swal2-container {
    z-index: 9999 !important;
}
.swal2-backdrop-show {
    z-index: 9998 !important;
}
</style>

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

</body>
</html>

