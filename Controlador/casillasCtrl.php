<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    http_response_code(403);
    exit;
}

require_once __DIR__ . '/../Modelo/casillasMdl.php';

header('Content-Type: application/json; charset=utf-8');

function respuesta($data) {
    echo json_encode($data);
    exit;
}

$modelo = new CasillasMdl();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {

    // ── Listar todas ───────────────────────────────────────────────────────
    case 'listar':
        respuesta(['success' => true, 'data' => $modelo->obtenerTodas()]);

    // ── Obtener una por ID ─────────────────────────────────────────────────
    case 'obtener':
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) respuesta(['success' => false, 'error' => 'ID inválido']);
        $casilla = $modelo->obtenerPorId($id);
        respuesta($casilla
            ? ['success' => true, 'data' => $casilla]
            : ['success' => false, 'error' => 'Casilla no encontrada']
        );

    // ── Secciones disponibles (excluye las ya ocupadas) ────────────────────
    case 'secciones_disponibles':
        $excluir = (int)($_GET['excluir'] ?? 0);
        respuesta([
            'success'  => true,
            'ocupadas' => $modelo->seccionesOcupadas($excluir ?: null)
        ]);

    // ── Crear ──────────────────────────────────────────────────────────────
    case 'crear':
        $datos = [
            'numero_seccion' => $_POST['numero_seccion'] ?? '',
            'tipo'           => $_POST['tipo']           ?? '',
            'direccion'      => trim($_POST['direccion'] ?? ''),
            'activa'         => isset($_POST['activa']) ? (int)$_POST['activa'] : 1,
        ];
        respuesta($modelo->crear($datos));

    // ── Modificar ──────────────────────────────────────────────────────────
    case 'modificar':
        $id = (int)($_POST['id_casilla'] ?? 0);
        if ($id <= 0) respuesta(['success' => false, 'error' => 'ID inválido']);
        $datos = [
            'id_casilla'     => $id,
            'numero_seccion' => $_POST['numero_seccion'] ?? '',
            'tipo'           => $_POST['tipo']           ?? '',
            'direccion'      => trim($_POST['direccion'] ?? ''),
            'activa'         => isset($_POST['activa']) ? (int)$_POST['activa'] : 1,
        ];
        respuesta($modelo->modificar($datos));

    // ── Eliminar ───────────────────────────────────────────────────────────
    case 'eliminar':
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id <= 0) respuesta(['success' => false, 'error' => 'ID inválido']);
        respuesta($modelo->eliminar($id));

    default:
        respuesta(['success' => false, 'error' => 'Acción no válida']);
}