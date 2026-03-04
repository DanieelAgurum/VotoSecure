<?php
require_once(__DIR__ . "/../Modelo/candidatosMdl.php");

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
        echo json_encode(['success' => false, 'error' => 'Token inválido']);
        exit;
    }

    // Verificar acción
    if (!isset($_POST['accion']) || $_POST['accion'] !== 'guardar') {
        echo json_encode(['success' => false, 'error' => 'Acción inválida']);
        exit;
    }

    // Validar campos requeridos
    $camposRequeridos = ['nombre', 'apellido', 'id_partido', 'id_tipo', 'cargo', 'distrito', 'correo'];
    foreach ($camposRequeridos as $campo) {
        if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
            echo json_encode(['success' => false, 'error' => "El campo $campo es requerido"]);
            exit;
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
        echo json_encode(['success' => false, 'error' => 'Partido o tipo inválido']);
        exit;
    }

    // Procesar foto
    $file = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto'];
    }

    // Guardar candidato
    $candidato = new Candidato();
    $resultado = $candidato->guardar($datos, $file);

    echo json_encode($resultado);
    exit;
}

// Manejar solicitud GET - Eliminar candidato
if (isset($_GET['eliminar'])) {
    
    $id = (int)$_GET['eliminar'];
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        exit;
    }

    $candidato = new Candidato();
    $resultado = $candidato->eliminar($id);

    echo json_encode($resultado);
    exit;
}
