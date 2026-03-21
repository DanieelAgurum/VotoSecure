<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../api/conexion.php';
require_once __DIR__ . '/../api/crypto.php';

// ── Recibir JSON ──────────────────────────────────────────────
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['votos']) || !isset($input['huella_votante_id'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$huella_votante_id = (int) $input['huella_votante_id'];
$votos             = $input['votos']; // [ 'Presidente' => candidato_id, ... ]

// ── Buscar votante por finger_id encriptado ───────────────────
// Traemos TODOS (activos y votados) para poder detectar duplicados.
$stmt = $pdo->prepare(
    "SELECT id, nombre, apellido_paterno, apellido_materno,
            seccion_electoral, clave_elector, estado, finger_id
     FROM votantes
     WHERE finger_id IS NOT NULL AND finger_id != ''"
);
$stmt->execute();
$votantes = $stmt->fetchAll();

$votante_encontrado = null;
$debug_decrypts = [];
foreach ($votantes as $v) {
    $decrypted_raw = aes_decrypt($v['finger_id']);
    $finger_id_decrypted = (int) $decrypted_raw;
    $debug_decrypts[] = [
        'id'        => $v['id'],
        'estado'    => $v['estado'],
        'raw'       => $decrypted_raw,
        'as_int'    => $finger_id_decrypted,
        'buscando'  => $huella_votante_id,
        'coincide'  => ($finger_id_decrypted === $huella_votante_id)
    ];

    if ($finger_id_decrypted === $huella_votante_id) {
        $votante_encontrado = $v;
        break;
    }
}

// ── Si no coincide con ningún votante → descartar silenciosamente ──
if (!$votante_encontrado) {
    echo json_encode(['success' => true, '_debug' => $debug_decrypts]);
    exit;
}

// ── Si ya votó → avisar ───────────────────────────────────────
if ($votante_encontrado['estado'] === 'votado') {
    echo json_encode([
        'success' => false,
        'ya_voto' => true,
        'message' => 'Este usuario ya emitió su voto.',
        '_debug_estado' => 'El votante existe pero estado=' . $votante_encontrado['estado'] . '. Resetea con: UPDATE votantes SET estado=\'activo\' WHERE id=' . $votante_encontrado['id']
    ]);
    exit;
}

// ── Obtener todos los puestos disponibles en la boleta ────────
$stmtPuestos = $pdo->query(
    "SELECT DISTINCT cargo FROM candidatos WHERE estatus = 'activo'"
);
$puestos_disponibles = $stmtPuestos->fetchAll(PDO::FETCH_COLUMN);

// ── Preparar inserción ────────────────────────────────────────
$insertStmt = $pdo->prepare(
    "INSERT INTO votos_boleta
        (partido, puesto, nombre_candidato, tipo, fecha,
         votante_nombre, votante_apellido_paterno, votante_apellido_materno,
         votante_seccion, votante_clave_elector)
     VALUES
        (:partido, :puesto, :nombre_candidato, :tipo, NOW(),
         :votante_nombre, :votante_apellido_paterno, :votante_apellido_materno,
         :votante_seccion, :votante_clave_elector)"
);

$datosVotante = [
    ':votante_nombre'           => $votante_encontrado['nombre'],
    ':votante_apellido_paterno' => $votante_encontrado['apellido_paterno'],
    ':votante_apellido_materno' => $votante_encontrado['apellido_materno'] ?? '',
    ':votante_seccion'          => $votante_encontrado['seccion_electoral'],
    ':votante_clave_elector'    => $votante_encontrado['clave_elector'] ?? '',
];

// Debug: ver exactamente qué llega del JS y qué puestos hay en BD
$debug_votos = [
    'votos_recibidos'    => $votos,
    'puestos_en_bd'      => $puestos_disponibles,
    'votante_id'         => $votante_encontrado['id'],
];

$insertados = 0;

foreach ($puestos_disponibles as $puesto) {

    // El name del radio en la boleta es voto[PRESIDENTE] (strtoupper).
    // Object.fromEntries(FormData) entrega exactamente esa clave.
    $form_key     = "voto[" . strtoupper($puesto) . "]";
    $puesto_upper = strtoupper($puesto);

    $candidato_id = isset($votos[$form_key]) && $votos[$form_key] !== ''
        ? (int) $votos[$form_key]
        : null;

    try {
        if ($candidato_id) {
            $stmtCand = $pdo->prepare(
                "SELECT c.nombre, c.apellido, p.nombre_partido
                 FROM candidatos c
                 LEFT JOIN partidos p ON c.id_partido = p.id_partido
                 WHERE c.id = :id AND c.estatus = 'activo'
                 LIMIT 1"
            );
            $stmtCand->execute([':id' => $candidato_id]);
            $cand = $stmtCand->fetch();

            if ($cand) {
                $insertStmt->execute(array_merge($datosVotante, [
                    ':partido'          => $cand['nombre_partido'] ?? 'Independiente',
                    ':puesto'           => $puesto_upper,
                    ':nombre_candidato' => trim($cand['nombre'] . ' ' . $cand['apellido']),
                    ':tipo'             => 'normal'
                ]));
            } else {
                $insertStmt->execute(array_merge($datosVotante, [
                    ':partido'          => 'N/A',
                    ':puesto'           => $puesto_upper,
                    ':nombre_candidato' => 'VOTO EN BLANCO',
                    ':tipo'             => 'omitido'
                ]));
            }
        } else {
            $insertStmt->execute(array_merge($datosVotante, [
                ':partido'          => 'N/A',
                ':puesto'           => $puesto_upper,
                ':nombre_candidato' => 'VOTO EN BLANCO',
                ':tipo'             => 'omitido'
            ]));
        }
        $insertados++;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al insertar voto: ' . $e->getMessage(), '_debug' => $debug_votos]);
        exit;
    }
}

// ── Marcar votante como "votado" ──────────────────────────────
$stmtUpdate = $pdo->prepare(
    "UPDATE votantes SET estado = 'votado' WHERE id = :id"
);
$stmtUpdate->execute([':id' => $votante_encontrado['id']]);

echo json_encode(['success' => true, '_debug' => array_merge($debug_votos, ['insertados' => $insertados])]);