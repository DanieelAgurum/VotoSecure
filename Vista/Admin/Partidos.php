<?php
// session_start();
// if (!isset($_SESSION['admin_id'])) {
//     header('Location: login.php');
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo Resultados - VotoSecure</title>

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
                    <!-- TABLA -->
                    <div class="table-responsive">
                        <table id="tablaContenido" class="table table-hover table-borderless align-middle">
                            <thead class="table-info">
                                <tr>
                                    <th>ID</th>
                                    <th>Elección</th>
                                    <th>Candidato</th>
                                    <th>Partido</th>
                                    <th>Votos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Elección 2026</td>
                                    <td>Juan Pérez</td>
                                    <td>Partido Azul</td>
                                    <td>1520</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td>Elección 2026</td>
                                    <td>María López</td>
                                    <td>Partido Verde</td>
                                    <td>980</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <!-- FIN TABLA -->
                </div>
                <?php ?>
            </div>
        </div>
    </main>
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