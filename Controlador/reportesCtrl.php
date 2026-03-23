<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    http_response_code(403);
    exit(json_encode(['error' => 'No autorizado']));
}

require_once __DIR__ . '/../Modelo/reportesMdl.php';

header('Content-Type: application/json; charset=utf-8');

$modelo   = new ReportesMdl();
$accion   = $_GET['accion'] ?? '';
$id       = isset($_GET['id_eleccion']) ? (int)$_GET['id_eleccion'] : 0;

switch ($accion) {

    case 'getElecciones':
        echo json_encode($modelo->getElecciones());
        break;

    case 'getReporte':
        if ($id <= 0) {
            echo json_encode(['error' => 'ID inválido']);
            break;
        }
        echo json_encode($modelo->getReporteCompleto($id));
        break;

    case 'exportCSV':
        if ($id <= 0) { echo json_encode(['error' => 'ID inválido']); break; }
        $data = $modelo->getReporteCompleto($id);
        exportarCSV($data, $id);
        break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
}

function exportarCSV($data, $id_eleccion) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="reporte_eleccion_' . $id_eleccion . '_' . date('Ymd_His') . '.csv"');

    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

    // Encabezado resumen
    fputcsv($out, ['RESUMEN GENERAL']);
    fputcsv($out, ['Votantes que votaron', $data['resumen']['total_votantes_que_votaron']]);
    fputcsv($out, ['Votantes registrados', $data['total_reg']]);
    $pct = $data['total_reg'] > 0
        ? round(($data['resumen']['total_votantes_que_votaron'] / $data['total_reg']) * 100, 1)
        : 0;
    fputcsv($out, ['Participación %', $pct . '%']);
    fputcsv($out, ['Puestos en juego', $data['resumen']['total_puestos']]);
    fputcsv($out, ['Primer voto', $data['resumen']['primer_voto']]);
    fputcsv($out, ['Último voto', $data['resumen']['ultimo_voto']]);
    fputcsv($out, []);

    // Resultados por puesto
    fputcsv($out, ['RESULTADOS POR PUESTO']);
    fputcsv($out, ['Puesto', 'Candidato', 'Partido', 'Votos', 'Tipo']);
    foreach ($data['por_puesto'] as $puesto => $votos) {
        foreach ($votos as $v) {
            fputcsv($out, [$puesto, $v['nombre_candidato'], $v['partido'], $v['votos'], $v['tipo']]);
        }
    }
    fputcsv($out, []);

    // Votos por partido
    fputcsv($out, ['VOTOS POR PARTIDO']);
    fputcsv($out, ['Partido', 'Votos']);
    foreach ($data['por_partido'] as $p) {
        fputcsv($out, [$p['partido'], $p['votos']]);
    }
    fputcsv($out, []);

    // Participación por sección
    fputcsv($out, ['PARTICIPACIÓN POR SECCIÓN']);
    fputcsv($out, ['Sección', 'Votantes']);
    foreach ($data['por_seccion'] as $s) {
        fputcsv($out, [$s['seccion'], $s['votaron']]);
    }

    fclose($out);
    exit;
}