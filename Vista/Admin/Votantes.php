<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header('Location: /VotoSecure/Vista/login.php');
    exit();
}

define('BASE_URL', '/VotoSecure');

require_once __DIR__ . '/../../Controlador/votentesCtrl.php';
$votantesCtrl = new VotantesCtrl();
$votantes = $votantesCtrl->obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Votantes - VotoSecure</title>
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/img/vs.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dash.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include __DIR__ . '/../../components/Admin/Navbar.php'; ?>

    <main class="main-content" id="mainContent">
        <div class="container-fluid mt-4">
            <!-- Estado de conexión ESP32 -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="esp32-status disconnected" id="esp32Status">
                            <span class="pulse"></span>
                            <span id="esp32Text">ESP32 Desconectado</span>
                        </div>
                        <button class="btn btn-primary" id="btnConectarESP32">
                            <i class="bi bi-wifi"></i> Conectar ESP32
                            x </button>
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            <?php
            if (!empty($_SESSION["errores"])) {
                foreach ($_SESSION["errores"] as $error) {
                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <i class='bi bi-exclamation-triangle-fill me-2'></i>$error
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
                }
                unset($_SESSION["errores"]);
            }

            if (!empty($_SESSION["success"])) {
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                <i class='bi bi-check-circle-fill me-2'></i>" . $_SESSION["success"] . "
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
                unset($_SESSION["success"]);
            }
            ?>

            <!-- TABLA DE VOTANTES -->
            <div class="card shadow-sm">
                <div class="card-header text-center text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-people-fill"></i> Gestión de Votantes
                    </h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaContenido" class="table table-hover table-borderless align-middle">
                            <thead class="table-info">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">Dirección</th>
                                    <th class="text-center">Correo</th>
                                    <th class="text-center">Sección</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($votantes)): ?>
                                    <?php foreach ($votantes as $votante): ?>
                                        <tr id="votante-<?= $votante['id'] ?>">
                                            <td class="text-center"><?= htmlspecialchars($votante['id']) ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($votante['nombre'] . ' ' . $votante['apellido_paterno'] . ' ' . $votante['apellido_materno']) ?></strong>
                                            </td>
                                            <td><small><?= htmlspecialchars($votante['direccion_completa']) ?></small></td>
                                            <td><?= htmlspecialchars($votante['correo']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($votante['seccion_electoral']) ?></td>
                                            <td class="text-center">
                                                <?php
                                                switch ($votante['estado']) {
                                                    case 'activo':
                                                        echo '<span class="badge bg-success">Activo</span>';
                                                        break;
                                                    case 'inactivo':
                                                        echo '<span class="badge bg-secondary">Inactivo</span>';
                                                        break;
                                                    case 'votado':
                                                        echo '<span class="badge bg-info">Votado</span>';
                                                        break;
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <!-- Botón Huella -->
                                                    <button class="btn btn-sm btn-outline-primary btn-action btn-huella"
                                                        data-id="<?= $votante['id'] ?>"
                                                        data-nombre="<?= htmlspecialchars($votante['nombre'] . ' ' . $votante['apellido_paterno']) ?>"
                                                        data-fingerprint="<?= htmlspecialchars($votante['finger_id']) ?>"
                                                        title="Cambiar Huella">
                                                        <i class="bi bi-fingerprint"></i>
                                                    </button>

                                                    <!-- Botón NFC -->
                                                    <button class="btn btn-sm btn-outline-danger btn-action btn-nfc"
                                                        data-id="<?= $votante['id'] ?>"
                                                        data-nombre="<?= htmlspecialchars($votante['nombre'] . ' ' . $votante['apellido_paterno']) ?>"
                                                        data-uid="<?= htmlspecialchars($votante['uid_nfc']) ?>"
                                                        title="Cambiar NFC">
                                                        <i class="bi bi-postcard"></i>
                                                    </button>

                                                    <!-- Botón Editar Datos -->
                                                    <button class="btn btn-sm btn-outline-warning btn-action btn-editar"
                                                        data-id="<?= $votante['id'] ?>"
                                                        data-nombre="<?= htmlspecialchars($votante['nombre'] . ' ' . $votante['apellido_paterno']) ?>"
                                                        title="Editar Datos">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- DataTables mostrará automáticamente el mensaje de tabla vacía -->
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- MODALES -->
    <?php include 'modales/votantes.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/js/dash.js"></script>
    <script src="<?= BASE_URL ?>/js/modificarVotante.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable con misma config que Elecciones
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
                lengthMenu: [5, 10, 25, 50],
                destroy: true
            });

            // Auto-cerrar alertas después de 3 segundos
            setTimeout(function() {
                $('.alert').each(function() {
                    // Crear objeto alert de Bootstrap y cerrarlo
                    var alertNode = this;
                    var bsAlert = new bootstrap.Alert(alertNode);
                    if (!alertNode.classList.contains('manually-closed')) {
                        bsAlert.close();
                    }
                });
            }, 3000);

            // Permitir cierre manual de alertas
            $('.alert .btn-close').on('click', function() {
                $(this).closest('.alert').addClass('manually-closed');
            });
        });
    </script>
</body>

</html>