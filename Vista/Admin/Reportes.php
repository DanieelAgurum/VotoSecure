<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header('Location: /VotoSecure/Vista/login.php');
    exit();
}

require_once '../../Modelo/reportesMdl.php';
$modelo     = new ReportesMdl();
$elecciones = $modelo->getElecciones();

$id_default = !empty($elecciones) ? $elecciones[0]['id_eleccion'] : 0;
$data       = $id_default ? $modelo->getReporteCompleto($id_default) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - VotoSecure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/dash.css">
    <link rel="stylesheet" href="../../css/graficas.css">
</head>
<body>

<?php include __DIR__ . '/../../components/Admin/Navbar.php'; ?>

<main class="main-content" id="mainContent">
<div class="container-fluid mt-4">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div class="dashboard-title">
            <i class="bi bi-file-earmark-bar-graph"></i> Reportes
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <select class="form-select form-select-sm" id="selectorEleccion" style="min-width:220px;">
                <?php if (empty($elecciones)): ?>
                    <option value="0">Sin elecciones disponibles</option>
                <?php else: ?>
                    <?php foreach ($elecciones as $e): ?>
                        <option value="<?= $e['id_eleccion'] ?>"
                            <?= $e['id_eleccion'] == $id_default ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nombre_eleccion']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <button class="btn btn-sm btn-success" id="btnExportCSV">
                <i class="bi bi-file-earmark-excel"></i> Exportar CSV
            </button>
          <button class="btn btn-sm btn-danger" id="btnExportPDF">
    <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
</button>
        </div>
    </div>

    <!-- Loading -->
    <div id="loadingOverlay" class="text-center py-5 d-none">
        <div class="spinner-border text-primary"></div>
        <p class="mt-2 text-muted">Cargando reporte...</p>
    </div>

    <div id="contenidoReporte">

        <!-- STATS -->
        <div class="row mb-4">
            <div class="col-6 col-lg-3">
                <div class="stat-card-dash">
                    <div class="stat-card-icon" style="background:rgba(37,99,235,0.10);color:#2563EB;">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div class="stat-card-info">
                        <div class="stat-card-number" id="statVotaron">—</div>
                        <div class="stat-card-label">Votaron</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card-dash">
                    <div class="stat-card-icon" style="background:rgba(16,185,129,0.10);color:#10B981;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-card-info">
                        <div class="stat-card-number" id="statRegistrados">—</div>
                        <div class="stat-card-label">Registrados</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card-dash">
                    <div class="stat-card-icon" style="background:rgba(245,158,11,0.10);color:#F59E0B;">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div class="stat-card-info">
                        <div class="stat-card-number" id="statParticipacion">—</div>
                        <div class="stat-card-label">Participación</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card-dash">
                    <div class="stat-card-icon" style="background:rgba(139,92,246,0.10);color:#8B5CF6;">
                        <i class="bi bi-bar-chart-steps"></i>
                    </div>
                    <div class="stat-card-info">
                        <div class="stat-card-number" id="statPuestos">—</div>
                        <div class="stat-card-label">Puestos</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRÁFICAS FILA 1 -->
        <div class="row">
            <div class="col-lg-7">
                <div class="chart-card">
                    <div class="chart-card-title">
                        <i class="bi bi-bar-chart" style="color:#2563EB;"></i>
                        Votos por Partido
                    </div>
                    <div class="chart-card-subtitle">Total acumulado de votos válidos por partido</div>
                    <canvas id="chartPartidos"></canvas>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="chart-card">
                    <div class="chart-card-title">
                        <i class="bi bi-clock-history" style="color:#10B981;"></i>
                        Votos por Hora
                    </div>
                    <div class="chart-card-subtitle">Distribución de participación durante el día</div>
                    <canvas id="chartHoras"></canvas>
                </div>
            </div>
        </div>

        <!-- TABLA -->
        <div class="row mt-2">
            <div class="col-12">
                <div class="chart-card">
                    <div class="chart-card-title">
                        <i class="bi bi-table" style="color:#8B5CF6;"></i>
                        Resultados Detallados por Puesto
                    </div>
                    <div class="chart-card-subtitle">Todos los candidatos con votos y porcentaje</div>
                    <div class="table-responsive">
                        <table id="tablaResultados" class="table table-hover table-borderless align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Puesto</th>
                                    <th>Candidato</th>
                                    <th>Partido</th>
                                    <th>Votos</th>
                                    <th>%</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoTabla">
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        Selecciona una elección
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN + OMITIDOS -->
        <div class="row mt-2">
            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-card-title">
                        <i class="bi bi-geo-alt" style="color:#EF4444;"></i>
                        Participación por Sección Electoral
                    </div>
                    <div class="chart-card-subtitle">Top 20 secciones con más votantes</div>
                    <canvas id="chartSecciones"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-card-title">
                        <i class="bi bi-slash-circle" style="color:#EF4444;"></i>
                        Votos Omitidos por Puesto
                    </div>
                    <div class="chart-card-subtitle">Cantidad de votos en blanco por cada puesto</div>
                    <canvas id="chartOmitidos"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>
</main>

<script>
const REPORTE_INICIAL     = <?= $data ? json_encode($data) : 'null' ?>;
const ID_ELECCION_DEFAULT = <?= $id_default ?>;
const CTRL_URL            = '../../Controlador/reportesCtrl.php';
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/VotoSecure/js/dash.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="/VotoSecure/js/reportes.js"></script>

</body>
</html>