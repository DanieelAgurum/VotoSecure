<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../api/conexion.php';
require_once __DIR__ . '/../Modelo/config/conexion.php';

use PDO;

function respuesta($success, $message, $data = null) {
    error_log("=== API GUARDAR_VOTO ===");
    error_log("Success: " . ($success ? 'true' : 'false'));
    error_log("Message: " . $message);
    if ($data) error_log("Data: " . json_encode($data));
    
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuesta(false, 'Método no permitido');
}

// Verificar sesión de votante (asumir existe desde login)
if (!isset($_SESSION['votante_id']) || empty($_SESSION['votante_id'])) {
    respuesta(false, 'Votante no autenticado. Inicia sesión primero.');
}

$votante_id = (int)$_SESSION['votante_id'];

// Obtener datos del voto
$input = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE || !isset($input['votos']) || !is_array($input['votos'])) {
    respuesta(false, 'Datos de voto inválidos');
}

$votos = $input['votos']; // {puesto: candidato_id}
$fecha_voto = date('Y-m-d H:i:s');

try {
    $pdo = (new Conexion())->conectar();
    
    // 1. Verificar si votante YA votó
    $stmt = $pdo->prepare("SELECT id FROM votos WHERE votante_id = ? LIMIT 1");
    $stmt->execute([$votante_id]);
    if ($stmt->fetch()) {
        respuesta(false, '¡Ya has votado! Un votante solo puede votar una vez.');
    }
    
    // 2. Verificar candidatos existen y están activos
    $candidatosInvalidos = [];
    foreach ($votos as $puesto => $cand_id) {
        $stmt = $pdo->prepare("SELECT id FROM candidatos WHERE id = ? AND estatus = 'activo'");
        $stmt->execute([$cand_id]);
        if (!$stmt->fetch()) {
            $candidatosInvalidos[] = "$puesto (ID: $cand_id)";
        }
    }
    if ($candidatosInvalidos) {
        respuesta(false, 'Candidatos inválidos: ' . implode(', ', $candidatosInvalidos));
    }
    
    // 3. Insertar votos (uno por puesto)
    $pdo->beginTransaction();
    
    foreach ($votos as $puesto => $cand_id) {
        $stmt = $pdo->prepare("
            INSERT INTO votos (votante_id, candidato_id, puesto, fecha_voto) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$votante_id, $cand_id, $puesto, $fecha_voto]);
    }
    
    $pdo->commit();
    
    // 4. Marcar votante como votó
    $stmt = $pdo->prepare("UPDATE votantes SET ha_votado = 1 WHERE id = ?");
    $stmt->execute([$votante_id]);
    
    respuesta(true, '¡Voto registrado exitosamente! Gracias por participar en la democracia.', [
        'votos' => count($votos),
        'timestamp' => $fecha_voto
    ]);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error guardando voto: " . $e->getMessage());
    respuesta(false, 'Error interno del sistema. Intenta nuevamente.');
}
?>

