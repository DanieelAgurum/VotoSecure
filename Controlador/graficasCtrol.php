<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    http_response_code(403);
    exit(json_encode(['error' => 'No autorizado']));
}

require_once __DIR__ . '/../Modelo/graficasMdl.php';

header('Content-Type: application/json; charset=utf-8');

$accion = $_GET['accion'] ?? '';

if ($accion === 'getDashboard') {
    $modelo = new graficasMdl();
    echo json_encode($modelo->getDashboardData());
    exit;
}

echo json_encode(['error' => 'Acción no válida']);