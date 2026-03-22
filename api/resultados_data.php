<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
header('Cache-Control: no-store');

// api/ → subir un nivel → Modelo/
require_once __DIR__ . '/../Modelo/graficasMdl.php';

try {
    $mdl = new graficasMdl();
    echo json_encode($mdl->getDashboardData());
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}