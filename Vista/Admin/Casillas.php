<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../../Modelo/casillasMdl.php';

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header('Location: /VotoSecure/Vista/login.php');
    exit();
}

$modelo = new CasillasMdl();
$lista  = $modelo->obtenerTodas();
$grupos = $modelo->obtenerSecciones();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casillas - VotoSecure</title>
    <link rel="icon" type="image/x-icon" href="../../img/vs.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
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
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Gestión de Casillas</h5>
                </div>

                <div class="card-body">

                    <button type="button" class="btn btn-primary mb-3"
                            data-bs-toggle="modal" data-bs-target="#modalAgregar">
                        Agregar Casilla <i class="fa-solid fa-circle-plus ms-1"></i>
                    </button>

                    <div class="table-responsive mt-3">
                        <table id="tablaCasillas" class="table table-hover table-borderless align-middle">
                            <thead class="table-info">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Sección</th>
                                    <th class="text-center">Municipio</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Dirección</th>
                                    <th class="text-center">Activa</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lista as $row): ?>
                                    <tr data-id="<?= $row['id_casilla'] ?>">
                                        <td class="text-center text-muted small"><?= $row['id_casilla'] ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary fs-6">
                                                <?= htmlspecialchars($row['numero_seccion']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?= htmlspecialchars($row['municipio']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['estado']) ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $row['tipo'] === 'Especial' ? 'bg-info text-dark' : 'bg-primary' ?>">
                                                <?= htmlspecialchars($row['tipo']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?= htmlspecialchars($row['direccion']) ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $row['activa'] ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $row['activa'] ? 'Sí' : 'No' ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-warning btn-modificar"
                                                    data-id="<?= $row['id_casilla'] ?>" title="Modificar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger btn-eliminar"
                                                    data-id="<?= $row['id_casilla'] ?>"
                                                    data-seccion="<?= htmlspecialchars($row['numero_seccion']) ?>"
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/modales/casillas.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/VotoSecure/js/dash.js"></script>
    <script src="/VotoSecure/js/casillas.js"></script>

</body>
</html>