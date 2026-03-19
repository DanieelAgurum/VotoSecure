<?php

session_start();

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header('Location: /VotoSecure/Vista/login.php');
    exit();
}
require_once '../../Modelo/config/conexion.php';
require_once '../../Modelo/eleccionesMdl.php';
$modelo = new EleccionesMdl($conexion);
$elecciones = $modelo->listar();
$tipos = $modelo->obtenerTipos();
$estados = $modelo->obtenerEstados();
$municipios = $modelo->obtenerMunicipiosPorEstado(isset($_GET['id_estado']) ? $_GET['id_estado'] : 0);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elecciones - VotoSecure</title>
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
                <div class="card-header text-center text-dark">
                    <h5 class="mb-0">
                        Elecciones
                    </h5>
                </div>

                <div class="card-body">
                    <button type="button"
                        class="btn btn-primary mb-3"
                        data-bs-toggle="modal"
                        data-bs-target="#modalAgregarEleccion">
                        Agregar <i class="fa-solid fa-circle-plus"></i>
                    </button>
                    <div class="container mt-3">
                        <?php
                        if (!empty($_SESSION["errores"])) {
                            foreach ($_SESSION["errores"] as $error) {
                                echo "<div class='alert alert-danger alert-dismissible fade show'>
                                $error
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
                            }
                            unset($_SESSION["errores"]);
                        }
                        if (!empty($_SESSION["success"])) {
                            echo "<div class='alert alert-success alert-dismissible fade show'>
                            " . $_SESSION["success"] . "
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
                            unset($_SESSION["success"]);
                        }
                        ?>
                    </div>
                    <!-- TABLA -->
                    <div class="table-responsive">
                        <table id="tablaContenido" class="table table-hover table-borderless align-middle">
                            <thead class="table-info">
                                <tr>
                                    <th class="text-center">No. Elección</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">Descripción</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Fecha Inicio / Fin</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($elecciones && $elecciones->num_rows > 0): ?>
                                    <?php while ($row = $elecciones->fetch_assoc()): ?>
                                        <?php
                                        $puedeModificar = (
                                            $row['estado_calculado'] == 'Programada'
                                            && $row['estado'] == 0
                                        );
                                        ?>
                                        <tr>
                                            <td class="text-center">
                                                <?= htmlspecialchars($row['id_eleccion']); ?>
                                            </td>

                                            <td class="text-center">
                                                <?= htmlspecialchars($row['nombre_eleccion']); ?>
                                            </td>

                                            <td class="text-center">
                                                <?= htmlspecialchars($row['descripcion_eleccion']); ?>
                                            </td>

                                            <td class="text-center">
                                                <?= htmlspecialchars($row['nombre_tipo']); ?>
                                            </td>

                                            <td class="text-center">
                                                <?= date("d/m/Y H:i", strtotime($row['fecha_inicio'])); ?>
                                                <br>
                                                <?= date("d/m/Y H:i", strtotime($row['fecha_fin'])); ?>
                                            </td>

                                            <td class="text-center">
                                                <?php
                                                switch ($row['estado_calculado']) {
                                                    case 'Programada':
                                                        echo '<span class="badge bg-warning text-dark">Programada</span>';
                                                        break;

                                                    case 'Activa':
                                                        echo '<span class="badge bg-success">Activa</span>';
                                                        break;

                                                    case 'Finalizada':
                                                        echo '<span class="badge bg-secondary">Finalizada</span>';
                                                        break;

                                                    case 'Cancelada':
                                                        echo '<span class="badge bg-danger">Cancelada</span>';
                                                        break;
                                                }
                                                ?>
                                            </td>

                                            <td class="text-center">
                                                <?php if ($row['estado_calculado'] == 'Programada'): ?>
                                                    <button class="btn btn-sm btn-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editarEleccion_<?= $row['id_eleccion']; ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#eliminarEleccion_<?= $row['id_eleccion']; ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    <button type="submit" class="btn btn-sm btn-dark"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cancelarEleccion_<?= $row['id_eleccion']; ?>">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                            <?php include 'modales/elecciones.php'; ?>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
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
                    <!-- FIN TABLA -->
                </div>
                <?php include 'modales/elecciones.php' ?>
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