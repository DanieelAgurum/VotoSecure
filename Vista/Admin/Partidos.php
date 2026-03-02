<?php
session_start();
require_once '../../Modelo/config/conexion.php';
require_once '../../Modelo/partidosMdl.php';
$modelo = new PartidosMdl($conexion);
$partidos = $modelo->obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partidos Políticos - VotoSecure</title>
    <link rel="icon" type="image/x-icon" href="../../img/vs.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/dash.css">
</head>

<body>
    <?php include __DIR__ . '/../../components/Admin/Navbar.php'; ?>

    <main class="main-content" id="mainContent">
        <div class="container-fluid mt-4">
            <div class="card shadow-sm">
                <div class="card-header text-dark bg-info">
                    <h5 class="mb-0">
                        <i class="bi bi-building-fill-check"></i> Partidos Políticos
                    </h5>
                </div>

                <div class="card-body">
                    <button type="button"
                        class="btn btn-primary mb-3"
                        data-bs-toggle="modal"
                        data-bs-target="#agregarContenido">
                        Agregar <i class="fa-solid fa-circle-plus"></i>
                    </button>
                    <?php
                    if (!empty($_SESSION["errores"])) {
                        foreach ($_SESSION["errores"] as $error) {
                            echo "<div class='alert alert-danger alert-dismissible fade show'>$error
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>";
                        }
                        unset($_SESSION["errores"]);
                    }

                    if (!empty($_SESSION["success"])) {
                        echo "<div class='alert alert-success alert-dismissible fade show'>" . $_SESSION["success"] . "
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>";
                        unset($_SESSION["success"]);
                    }
                    ?>
                    <!-- TABLA -->
                    <div class="table-responsive">
                        <table id="tablaContenido" class="table table-hover table-borderless align-middle">
                            <thead class="table-info">
                                <tr>
                                    <th class="text-center">No. Partido</th>
                                    <th class="text-center">Logo</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">Siglas</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($partidos)) : ?>
                                    <?php foreach ($partidos as $row) : ?>
                                        <tr>
                                            <td class="text-center"><?= htmlspecialchars($row['id_partido']); ?></td>

                                            <td class="text-center">
                                                <?php if (!empty($row['logo_partido'])) : ?>
                                                    <img src="../../<?= htmlspecialchars($row['logo_partido']); ?>"
                                                        width="50" height="50"
                                                        style="object-fit:cover;">
                                                <?php else : ?>
                                                    <img src="../../img/partidos/sl.jpg"
                                                        width="50" height="50"
                                                        style="object-fit:cover;">
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-center"><?= htmlspecialchars($row['nombre_partido']); ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['siglas']); ?></td>
                                            <td class="text-center">
                                                <?php if ($row['estatus'] == 1): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Desactivado</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-center">
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" title="Editar Partido"
                                                    data-bs-target="#editarPartido_<?= $row['id_partido']; ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" title="Eliminar Partido"
                                                    data-bs-target="#eliminarPartido_<?= $row['id_partido']; ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>

                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" title="Cambiar Estado"
                                                    data-bs-target="#estadoPartido_<?= $row['id_partido']; ?>">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </td>
                                            <?php include 'modales/partidos.php' ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No hay partidos registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                    <!-- FIN TABLA -->
                </div>
                <?php include 'modales/partidos.php' ?>
            </div>
        </div>
    </main>
    <script>
        function previewImage(event, previewId) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById(previewId);
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
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