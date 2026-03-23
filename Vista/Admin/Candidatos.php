<?php
session_start();

require_once("../../Modelo/candidatosMdl.php");

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header('Location: /VotoSecure/Vista/login');
    exit();
}

function generarTokenCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

$csrfToken  = generarTokenCSRF();
$candidato  = new Candidato();
$lista      = $candidato->obtenerCandidatos();
$partidos   = $candidato->obtenerPartidos();
$elecciones = $candidato->obtenerElecciones();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos - VotoSecure</title>
    <link rel="icon" type="image/x-icon" href="../../img/vs.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/dash.css">
</head>
<body>

    <?php include __DIR__ . '/../../components/Admin/Navbar.php'; ?>

    <main class="main-content" id="mainContent">
        <div class="container-fluid mt-4">
            <div class="card shadow-sm">
                <div class="card-header text-center text-dark">
                    <h5 class="mb-0">Gestión de Candidatos</h5>
                </div>

                <div class="card-body">
                    <button type="button" class="btn btn-primary mb-3"
                            data-bs-toggle="modal" data-bs-target="#modalAgregar">
                        Agregar Candidato <i class="fa-solid fa-circle-plus"></i>
                    </button>

                    <div id="mensajeAlert"></div>

                    <div class="container mt-3">
                        <div class="table-responsive">
                            <table id="tablaCandidatos" class="table table-hover table-borderless align-middle">
                                <thead class="table-info">
                                    <tr>
                                        <th class="text-center">Foto</th>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Nombre</th>
                                        <th class="text-center">Partido</th>
                                        <th class="text-center">Tipo</th>
                                        <th class="text-center">Cargo</th>
                                        <th class="text-center">Estatus</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($lista)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No hay candidatos registrados</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($lista as $row): ?>
                                            <tr data-id="<?= $row['id'] ?>">
                                                <td class="text-center">
                                                    <?php if (!empty($row['foto'])): ?>
                                                        <img src="<?= htmlspecialchars($row['foto']) ?>"
                                                             alt="Foto" class="rounded-circle"
                                                             style="width:45px;height:45px;object-fit:cover;">
                                                    <?php else: ?>
                                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                                             style="width:45px;height:45px;">
                                                            <i class="bi bi-person text-white"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?= $row['id'] ?></td>
                                                <td class="text-center"><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) ?></td>
                                                <td class="text-center"><?= htmlspecialchars($row['partido_nombre']) ?></td>
                                                <td class="text-center"><?= htmlspecialchars($row['tipo_nombre']) ?></td>
                                                <td class="text-center"><?= htmlspecialchars($row['cargo']) ?></td>
                                                <td class="text-center">
                                                    <?php if ($row['estatus'] == 'activo'): ?>
                                                        <span class="badge bg-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-warning btn-modificar"
                                                            data-id="<?= $row['id'] ?>" title="Modificar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-eliminar"
                                                            data-id="<?= $row['id'] ?>" title="Eliminar">
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
        </div>
    </main>

    <?php include __DIR__ . '/modales/candidatos.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/VotoSecure/js/dash.js"></script>

    <script>
        $(document).ready(function() {
            $('#tablaCandidatos').DataTable({
                language: {
                    decimal:        "",
                    emptyTable:     "No hay candidatos registrados",
                    info:           "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                    infoEmpty:      "Mostrando 0 a 0 de 0 entradas",
                    infoFiltered:   "(filtrado de _MAX_ total entradas)",
                    thousands:      ",",
                    lengthMenu:     "Mostrar _MENU_ entradas",
                    loadingRecords: "Cargando...",
                    processing:     "Procesando...",
                    search:         "Buscar:",
                    zeroRecords:    "Sin resultados encontrados",
                    paginate: {
                        first:    "Primero",
                        last:     "Último",
                        next:     "Siguiente",
                        previous: "Anterior"
                    }
                },
                responsive:  true,
                pageLength:  5,
                lengthMenu:  [5, 10, 25, 50],
                columnDefs: [
                    { orderable: false, targets: [0, 7] }
                ]
            });
        });
    </script>

    <script src="/VotoSecure/js/candidatos.js"></script>

</body>
</html>