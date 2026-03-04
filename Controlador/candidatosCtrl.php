<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . "/../Modelo/candidatosMdl.php");

// Función para responder con JSON
function respuestaJSON($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

// Generar token CSRF
function generarTokenCSRF() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validar token CSRF
function validarTokenCSRF($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Manejar solicitud POST - Guardar candidato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
        respuestaJSON(['success' => false, 'error' => 'Token inválido']);
    }

    // Verificar acción
    if (!isset($_POST['accion']) || $_POST['accion'] !== 'guardar') {
        respuestaJSON(['success' => false, 'error' => 'Acción inválida']);
    }

    // Validar campos requeridos
    $camposRequeridos = ['nombre', 'apellido', 'id_partido', 'id_tipo', 'cargo', 'distrito', 'correo'];
    foreach ($camposRequeridos as $campo) {
        if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
            respuestaJSON(['success' => false, 'error' => "El campo $campo es requerido"]);
        }
    }

    // Sanitizar datos
    $datos = [
        'nombre' => htmlspecialchars(trim($_POST['nombre'])),
        'apellido' => htmlspecialchars(trim($_POST['apellido'])),
        'id_partido' => (int)$_POST['id_partido'],
        'id_tipo' => (int)$_POST['id_tipo'],
        'cargo' => htmlspecialchars(trim($_POST['cargo'])),
        'distrito' => htmlspecialchars(trim($_POST['distrito'])),
        'correo' => htmlspecialchars(trim($_POST['correo'])),
        'telefono' => isset($_POST['telefono']) ? htmlspecialchars(trim($_POST['telefono'])) : '',
        'estatus' => isset($_POST['estatus']) ? $_POST['estatus'] : 'activo'
    ];

    // Validar id_partido e id_tipo
    if ($datos['id_partido'] <= 0 || $datos['id_tipo'] <= 0) {
        respuestaJSON(['success' => false, 'error' => 'Partido o tipo inválido']);
    }

    // Procesar foto - verificar que sea un array válido
    $file = null;
    if (isset($_FILES['foto']) && is_array($_FILES['foto']) && !empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto'];
    }

    // Guardar candidato
    $candidato = new Candidato();
    $resultado = $candidato->guardar($datos, $file);

    respuestaJSON($resultado);
}

// Manejar solicitud GET - Eliminar candidato
if (isset($_GET['eliminar'])) {
    
    $id = (int)$_GET['eliminar'];
    
    if ($id <= 0) {
        respuestaJSON(['success' => false, 'error' => 'ID inválido']);
    }

    $candidato = new Candidato();
    $resultado = $candidato->eliminar($id);

    respuestaJSON($resultado);
}

// Si no es POST ni GET con eliminar, no hacer nada
exit;

