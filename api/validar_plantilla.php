<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../api/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['plantilla_encriptada']) || !isset($data['votos'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    exit;
}

$plantilla_token = $data['plantilla_encriptada'];
$votos = $data['votos'];

try {
    $pdo = (new Conexion())->conectar();
    
    // Buscar token en DB (plantillas válidas)
    $stmt = $pdo->prepare("SELECT id_votante FROM plantillas_validas WHERE token = ? AND usado = 0");
    $stmt->execute([$plantilla_token]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resultado) {
        echo json_encode(['success' => false, 'message' => 'Plantilla NO válida o ya usada']);
        exit;
    }

    $votante_id = $resultado['id_votante'];
    
    // Marcar plantilla usada
    $stmt = $pdo->prepare("UPDATE plantillas_validas SET usado = 1 WHERE token = ?");
    $stmt->execute([$plantilla_token]);

    echo json_encode([
        'success' => true,
        'message' => 'Plantilla VÁLIDA',
        'votante_id' => $votante_id,
        'votos_permitidos' => $votos
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error DB: ' . $e->getMessage()]);
}
?>

