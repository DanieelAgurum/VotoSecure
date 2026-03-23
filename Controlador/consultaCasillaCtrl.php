<?php
// Endpoint público — no requiere sesión
// Ruta: Controlador/consultaCasillaCtrl.php

require_once __DIR__ . '/../Modelo/casillasMdl.php';

header('Content-Type: application/json; charset=utf-8');

function respuesta($data) {
    echo json_encode($data);
    exit;
}

// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuesta(['success' => false, 'error' => 'Método no permitido']);
}

$curp = trim($_POST['curp'] ?? '');

// Validar formato CURP básico (18 caracteres alfanuméricos)
if (strlen($curp) !== 18 || !preg_match('/^[A-Z0-9]{18}$/i', $curp)) {
    respuesta(['success' => false, 'error' => 'CURP inválida. Debe tener exactamente 18 caracteres alfanuméricos.']);
}

$modelo    = new CasillasMdl();
$resultado = $modelo->consultarPorCurp($curp);

if (!$resultado['encontrado']) {
    respuesta(['success' => false, 'error' => $resultado['error']]);
}

respuesta(['success' => true, 'data' => $resultado]);