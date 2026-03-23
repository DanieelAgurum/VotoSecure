<?php

session_start();

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header('Location: /VotoSecure/Vista/login.php');
    exit();
}

require_once '../../Modelo/config/conexion.php';
require_once '../../Modelo/propuestasMdl.php';

$modelo = new PropuestasMdl($conexion);
$propuestas = $modelo->obtenerConCandidato();
$candidatos = $modelo->obtenerCandidatos();


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propuestas - VotoSecure</title>
    <link rel="icon" type="image/x-icon" href="../../img/vs.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/dash.css">
</head>

<body>
    <?php include __DIR__ . '/../../components/Admin/Navbar.php'; ?>

    <main class="main-content">
        <div class="container-fluid mt-4">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    <h5>Propuestas</h5>
                </div>
                <div class="card-body">
                    <!-- BOTÓN -->
                    <button class="btn btn-primary mb-3"
                        data-bs-toggle="modal"
                        data-bs-target="#agregarPropuesta">
                        Agregar <i class="fa-solid fa-circle-plus"></i>
                    </button>
                    <?php if (!empty($_SESSION["errores"])): ?>
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: '¡Atención!',
                                html: `<ul class="text-start">
                                <?php foreach ($_SESSION["errores"] as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                    </ul>`,
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'Entendido'
                            });
                        </script>
                    <?php unset($_SESSION["errores"]);
                    endif; ?>

                    <?php if (!empty($_SESSION["success"])): ?>
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: '<?= htmlspecialchars($_SESSION["success"]) ?>',
                                timer: 2500,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        </script>
                    <?php unset($_SESSION["success"]);
                    endif; ?>
                    <!-- TABLA -->
                    <div class="table-responsive">
                        <table id="tablaContenido" class="table table-hover table-borderless align-middle">
                            <thead class="table-info text-center">
                                <tr>
                                    <th>No.</th>
                                    <th>Candidato</th>
                                    <th>Título</th>
                                    <th>Slogan</th>
                                    <th>Misión</th>
                                    <th>Propuesta</th>
                                    <th>Video</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($propuestas)): ?>
                                    <?php foreach ($propuestas as $row): ?>
                                        <tr class="text-center">
                                            <td><?= $row['id_propuesta']; ?></td>
                                            <td><?= htmlspecialchars($row['nombre']); ?> <?= htmlspecialchars($row['apellido']); ?></td>
                                            <td><?= htmlspecialchars($row['titulo']); ?></td>
                                            <td><?= htmlspecialchars($row['slogan']); ?></td>
                                            <td>
                                                <?= substr(htmlspecialchars($row['mision']), 0, 50); ?>...
                                            </td>
                                            <td>
                                                <?= substr(htmlspecialchars($row['propuesta_detallada']), 0, 50); ?>...
                                            </td>
                                            <td>
                                                <?php if (!empty($row['video_url'])): ?>
                                                    <a href="<?= $row['video_url']; ?>" target="_blank" class="btn btn-sm btn-dark">
                                                        Ver
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Sin video</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" title="Editar propuesta"
                                                    data-bs-target="#editarPropuesta_<?= $row['id_propuesta']; ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form class="form-eliminar d-inline" method="POST" action="../../Controlador/propuestasCtrl.php">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id" value="<?= $row['id_propuesta'] ?>">
                                                    <input type="hidden" name="nombre_candidato" value="<?= htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar propuesta">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <?php include 'modales/propuestas.php' ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- MODAL GLOBAL -->
                    <?php include 'modales/propuestas.php'; ?>
                </div>
            </div>
        </div>
    </main>
    <script>
        document.querySelectorAll('.form-eliminar').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const nombre = this.querySelector('[name="nombre_candidato"]').value;

                Swal.fire({
                    title: '¿Eliminar propuesta?',
                    html: `¿Estás seguro de eliminar la propuesta de <strong>"${nombre}"</strong>?<br>
                       <small class="text-muted">Esta acción no se puede deshacer.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            $('#tablaContenido').DataTable({
                language: {
                    decimal: "",
                    emptyTable: "No hay información",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                    infoEmpty: "Mostrando 0 a 0 de 0 entradas",
                    infoFiltered: "(filtrado de _MAX_ total entradas)",
                    thousands: ",",
                    lengthMenu: "Mostrar _MENU_ entradas",
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    search: "Buscar:",
                    zeroRecords: "Sin resultados encontrados",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                },
                responsive: true,
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50]
            });

        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/dash.js"></script>
</body>

</html>