<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header('Location: /VotoSecure/Vista/login.php');
    exit();
}

require_once '../../Modelo/graficasMdl.php';
$modelo = new graficasMdl();
$data   = $modelo->getDashboardData();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - VotoSecure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/dash.css">
    <link rel="stylesheet" href="../../css/graficas.css">
</head>
<body>

<?php include __DIR__ . '/../../components/Admin/Navbar.php'; ?>

<div class="main-content">

    <!-- HEADER -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="dashboard-title">
            <i class="bi bi-bar-chart-fill"></i> Resultados en Tiempo Real
        </div>
        <div class="refresh-indicator" id="refreshIndicator">
            <span class="refresh-dot"></span>
             <span id="countdown">   En vivo </span>
            <button class="btn-refresh" id="btnRefresh" title="Actualizar ahora">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>

    <!-- STATS RÁPIDOS -->
    <div class="row mb-4" id="statsRow">
        <div class="col-6 col-lg-3">
            <div class="stat-card-dash">
                <div class="stat-card-icon" style="background:rgba(37,99,235,0.12);color:#2563EB;">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-number" id="statTotalVotos"><?= number_format($data['total_votos']) ?></div>
                    <div class="stat-card-label">Votos emitidos</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card-dash">
                <div class="stat-card-icon" style="background:rgba(16,185,129,0.12);color:#10B981;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-number" id="statParticipacion"><?= $data['participacion'] ?>%</div>
                    <div class="stat-card-label">Participación</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card-dash">
                <div class="stat-card-icon" style="background:rgba(245,158,11,0.12);color:#F59E0B;">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-number" id="statPartidoLider"><?= htmlspecialchars($data['partido_lider']['partido']) ?></div>
                    <div class="stat-card-label">Partido líder</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card-dash">
                <div class="stat-card-icon" style="background:rgba(139,92,246,0.12);color:#8B5CF6;">
                    <i class="bi bi-bar-chart-steps"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-number"><?= $data['puestos'] ?></div>
                    <div class="stat-card-label">Puestos en juego</div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILA 1 — Partidos -->
    <div class="row">
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="bi bi-bar-chart" style="color:#2563EB;"></i>
                    Votos por Partido
                </div>
                <div class="chart-card-subtitle">Comparativa general acumulada</div>
                <div class="winner-badge" id="badgePartido">
                    <i class="bi bi-trophy-fill"></i>
                    <?= htmlspecialchars($data['partido_lider']['partido']) ?> lidera con <?= $data['partido_lider']['votos_total'] ?> votos
                </div>
                <canvas id="chartPartidos"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="bi bi-pie-chart" style="color:#F59E0B;"></i>
                    Distribución
                </div>
                <div class="chart-card-subtitle">Porcentaje del total de votos</div>
                <canvas id="chartPartidosPie"></canvas>
                <div class="mt-3" id="listPartidos">
                    <?php
                    $colores = ['linear-gradient(90deg,#2563EB,#22D3EE)','linear-gradient(90deg,#10B981,#34D399)','linear-gradient(90deg,#F59E0B,#FBBF24)','linear-gradient(90deg,#EF4444,#F87171)','linear-gradient(90deg,#8B5CF6,#A78BFA)'];
                    $medallas = ['🥇','🥈','🥉','4️⃣','5️⃣'];
                    $puestosGanados = $data['puestos_ganados'];
                    $totalVotos = $data['partido_lider']['votos_total'];
                    $i = 0;
                    foreach ($puestosGanados as $partido => $ganados):
                        $pct = $data['total_votos'] > 0 ? round(($ganados / array_sum($puestosGanados)) * 100, 1) : 0;
                    ?>
                    <div class="stat-mini">
                        <div style="flex:1;">
                            <div class="stat-mini-name"><?= ($medallas[$i] ?? '▪') . ' ' . htmlspecialchars($partido) ?></div>
                            <div class="progress-bar-custom">
                                <div class="progress-fill" style="width:<?= $pct ?>%;background:<?= $colores[$i] ?? '#2563EB' ?>;"></div>
                            </div>
                        </div>
                        <div class="stat-mini-pct ms-3"><?= $ganados ?> ptos</div>
                    </div>
                    <?php $i++; endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- FILA 2 — Candidatos por puesto (dinámico) -->
    <div class="section-label mt-2"><i class="bi bi-person-badge"></i> Resultados por Puesto</div>

    <div class="row" id="rowPuestos">
        <?php
        $coloresPuesto = ['#EF4444','#10B981','#8B5CF6','#2563EB','#F59E0B','#EC4899'];
        foreach ($data['resultados'] as $idx => $r):
            $color = $coloresPuesto[$idx % count($coloresPuesto)];
            $colorRgb = hexdec(substr($color,1,2)) . ',' . hexdec(substr($color,3,2)) . ',' . hexdec(substr($color,5,2));
        ?>
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="bi bi-person-fill" style="color:<?= $color ?>;"></i>
                    <?= htmlspecialchars($r['puesto']) ?>
                </div>
                <div class="chart-card-subtitle">
                    <?= $r['total'] ?> votos válidos
                    <?php if ($r['omitidos'] > 0): ?>
                        · <span class="text-danger"><?= $r['omitidos'] ?> omitidos</span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($r['ganador']['nombre_candidato']) && $r['ganador']['nombre_candidato'] !== '—'): ?>
                <div class="winner-badge">
                    <i class="bi bi-trophy-fill"></i>
                    <?= htmlspecialchars($r['ganador']['nombre_candidato']) ?>
                    · <?= $r['ganador']['porcentaje'] ?>%
                </div>
                <?php endif; ?>
                <canvas id="chartPuesto_<?= $idx ?>"></canvas>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- FILA 3 — Votos omitidos por puesto -->
    <div class="section-label mt-2"><i class="bi bi-slash-circle"></i> Votos Omitidos por Puesto</div>

    <div class="row">
        <div class="col-12">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="bi bi-x-circle" style="color:#EF4444;"></i>
                    Votos Omitidos
                </div>
                <div class="chart-card-subtitle">Cantidad de votos en blanco u omitidos por cada puesto</div>
                <canvas id="chartOmitidos" style="max-height:200px!important;"></canvas>
            </div>
        </div>
    </div>

    <!-- FILA 4 — Participación -->
    <div class="row">
        <div class="col-lg-5">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="bi bi-people-fill" style="color:#10B981;"></i>
                    Participación Ciudadana
                </div>
                <div class="chart-card-subtitle">Votantes registrados vs votos emitidos</div>
                <canvas id="chartParticipacion"></canvas>
                <div class="mt-3 text-center">
                    <span class="stat-big-number" id="statBigPct"><?= $data['participacion'] ?>%</span>
                    <div style="font-size:12px;color:#64748B;">de participación total</div>
                </div>
            </div>
        </div>

        <!-- TABLA DETALLADA -->
        <div class="col-lg-7">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="bi bi-table" style="color:#2563EB;"></i>
                    Tabla de Resultados Detallada
                </div>
                <div class="chart-card-subtitle">Todos los candidatos ordenados por votos</div>
                <div class="table-responsive">
                    <table class="table-resultados">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Candidato</th>
                                <th>Partido</th>
                                <th>Puesto</th>
                                <th>Votos</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $pos = 1;
                            foreach ($data['resultados'] as $r):
                                foreach ($r['votos'] as $v):
                            ?>
                            <tr class="<?= $pos === 1 ? 'fila-lider' : '' ?>">
                                <td>
                                    <?php if ($pos === 1): ?>
                                        <span class="medal gold">🥇</span>
                                    <?php elseif ($pos === 2): ?>
                                        <span class="medal">🥈</span>
                                    <?php elseif ($pos === 3): ?>
                                        <span class="medal">🥉</span>
                                    <?php else: ?>
                                        <span class="pos-num"><?= $pos ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="candidato-nombre"><?= htmlspecialchars($v['nombre_candidato']) ?></td>
                                <td><span class="partido-tag"><?= htmlspecialchars($v['partido']) ?></span></td>
                                <td class="text-muted" style="font-size:12px;"><?= htmlspecialchars($r['puesto']) ?></td>
                                <td class="votos-num"><?= number_format($v['votos']) ?></td>
                                <td>
                                    <div class="pct-bar-wrap">
                                        <div class="pct-bar-fill" style="width:<?= $v['porcentaje'] ?>%"></div>
                                        <span><?= $v['porcentaje'] ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php $pos++; endforeach; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Data para JS -->
<script>
const DASHBOARD_DATA = <?= json_encode($data) ?>;
const REFRESH_URL    = '../../Controlador/graficasCtrl.php';
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../js/dash.js"></script>
<script src="../../js/graficas.js"></script>

</body>
</html>