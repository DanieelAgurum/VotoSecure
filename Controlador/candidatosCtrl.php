<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . "/../Modelo/candidatosMdl.php");

function respuestaJSON($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function generarTokenCSRF() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['csrf_token']))
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

function validarTokenCSRF($token) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token']))
        respuestaJSON(['success' => false, 'error' => 'Token inválido']);

    if (!isset($_POST['accion']))
        respuestaJSON(['success' => false, 'error' => 'Acción requerida']);

    // ── GUARDAR ──
    if ($_POST['accion'] === 'guardar') {

        $camposRequeridos = ['nombre', 'apellido', 'id_partido', 'id_eleccion', 'cargo'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($_POST[$campo]) || empty(trim($_POST[$campo])))
                respuestaJSON(['success' => false, 'error' => "El campo $campo es requerido"]);
        }

        $datos = [
            'nombre'      => htmlspecialchars(trim($_POST['nombre'])),
            'apellido'    => htmlspecialchars(trim($_POST['apellido'])),
            'id_partido'  => (int)$_POST['id_partido'],
            'id_eleccion' => (int)$_POST['id_eleccion'],
            'cargo'       => htmlspecialchars(trim($_POST['cargo'])),
            'correo'      => isset($_POST['correo'])   ? htmlspecialchars(trim($_POST['correo']))   : '',
            'telefono'    => isset($_POST['telefono']) ? htmlspecialchars(trim($_POST['telefono'])) : '',
            'estatus'     => isset($_POST['estatus'])  ? $_POST['estatus'] : 'activo'
        ];

        if ($datos['id_partido'] <= 0 || $datos['id_eleccion'] <= 0)
            respuestaJSON(['success' => false, 'error' => 'Partido o elección inválido']);

        $file = null;
        if (isset($_FILES['foto']) && !empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK)
            $file = $_FILES['foto'];

        $candidato = new Candidato();
        respuestaJSON($candidato->guardar($datos, $file));

    // ── MODIFICAR ──
    } elseif ($_POST['accion'] === 'modificar') {

        if (!isset($_POST['id']) || empty($_POST['id']))
            respuestaJSON(['success' => false, 'error' => 'ID de candidato requerido']);

        $camposRequeridos = ['nombre', 'apellido', 'id_partido', 'id_eleccion', 'cargo'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($_POST[$campo]) || empty(trim($_POST[$campo])))
                respuestaJSON(['success' => false, 'error' => "El campo $campo es requerido"]);
        }

        $datos = [
            'id'          => (int)$_POST['id'],
            'nombre'      => htmlspecialchars(trim($_POST['nombre'])),
            'apellido'    => htmlspecialchars(trim($_POST['apellido'])),
            'id_partido'  => (int)$_POST['id_partido'],
            'id_eleccion' => (int)$_POST['id_eleccion'],
            'cargo'       => htmlspecialchars(trim($_POST['cargo'])),
            'correo'      => isset($_POST['correo'])   ? htmlspecialchars(trim($_POST['correo']))   : '',
            'telefono'    => isset($_POST['telefono']) ? htmlspecialchars(trim($_POST['telefono'])) : '',
            'estatus'     => isset($_POST['estatus'])  ? $_POST['estatus'] : 'activo'
        ];

        if ($datos['id_partido'] <= 0 || $datos['id_eleccion'] <= 0)
            respuestaJSON(['success' => false, 'error' => 'Partido o elección inválido']);

        $file = null;
        if (isset($_FILES['foto']) && !empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK)
            $file = $_FILES['foto'];

        $candidato = new Candidato();
        respuestaJSON($candidato->modificar($datos, $file));

    } else {
        respuestaJSON(['success' => false, 'error' => 'Acción inválida']);
    }
}

// ── GET Eliminar ──
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    if ($id <= 0) respuestaJSON(['success' => false, 'error' => 'ID inválido']);
    $candidato = new Candidato();
    respuestaJSON($candidato->eliminar($id));
}

// ── GET Obtener ──
if (isset($_GET['obtener'])) {
    $id = (int)$_GET['obtener'];
    if ($id <= 0) respuestaJSON(['success' => false, 'error' => 'ID inválido']);
    $candidato = new Candidato();
    respuestaJSON($candidato->obtenerPorId($id));
}

exit;