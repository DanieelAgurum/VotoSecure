<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    http_response_code(403); exit;
}

require_once __DIR__ . '/../Modelo/propuestasMdl.php';

header('Content-Type: application/json; charset=utf-8');

function respuesta($data) {
    echo json_encode($data); exit;
}

function convertirYoutubeEmbed($url) {
    if (empty($url)) return null;
    if (strpos($url, 'embed') !== false) return $url;
    if (strpos($url, 'watch?v=') !== false) {
        parse_str(parse_url($url, PHP_URL_QUERY), $p);
        return isset($p['v']) ? 'https://www.youtube.com/embed/' . $p['v'] : $url;
    }
    if (strpos($url, 'youtu.be/') !== false) {
        return 'https://www.youtube.com/embed/' . basename(parse_url($url, PHP_URL_PATH));
    }
    return $url;
}

$modelo = new PropuestasMdl();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {

    case 'listar':
        respuesta(['success' => true, 'data' => $modelo->obtenerTodas()]);

    case 'obtener':
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) respuesta(['success' => false, 'error' => 'ID inválido']);
        $prop = $modelo->obtenerPorId($id);
        respuesta($prop ? ['success' => true, 'data' => $prop] : ['success' => false, 'error' => 'No encontrada']);

    case 'candidatos':
        respuesta(['success' => true, 'data' => $modelo->obtenerCandidatos()]);

    case 'crear':
        $titulo   = trim($_POST['titulo'] ?? '');
        $cid      = (int)($_POST['candidato_id'] ?? 0);
        $slogan   = trim($_POST['slogan'] ?? '');
        $mision   = trim($_POST['mision'] ?? '');
        $detalle  = trim($_POST['propuesta_detallada'] ?? '');
        $video    = convertirYoutubeEmbed(trim($_POST['video_url'] ?? ''));

        if (empty($titulo) || $cid <= 0 || strlen($detalle) < 20)
            respuesta(['success' => false, 'error' => 'Faltan campos requeridos']);

        if ($modelo->existeTitulo($titulo, $cid))
            respuesta(['success' => false, 'error' => 'Ya existe una propuesta con ese título para este candidato']);

        $ok = $modelo->crear($cid, $titulo, $slogan, $mision, $detalle, $video);
        respuesta($ok ? ['success' => true, 'message' => 'Propuesta creada correctamente']
                      : ['success' => false, 'error' => 'Error al crear la propuesta']);

    case 'actualizar':
        $id      = (int)($_POST['id'] ?? 0);
        $titulo  = trim($_POST['titulo'] ?? '');
        $cid     = (int)($_POST['candidato_id'] ?? 0);
        $slogan  = trim($_POST['slogan'] ?? '');
        $mision  = trim($_POST['mision'] ?? '');
        $detalle = trim($_POST['propuesta_detallada'] ?? '');
        $video   = convertirYoutubeEmbed(trim($_POST['video_url'] ?? ''));

        if ($id <= 0 || empty($titulo) || $cid <= 0)
            respuesta(['success' => false, 'error' => 'Faltan campos requeridos']);

        if ($modelo->existeTitulo($titulo, $cid, $id))
            respuesta(['success' => false, 'error' => 'Ya existe otra propuesta con ese título']);

        $ok = $modelo->actualizar($id, $cid, $titulo, $slogan, $mision, $detalle, $video);
        respuesta($ok ? ['success' => true, 'message' => 'Propuesta actualizada correctamente']
                      : ['success' => false, 'error' => 'Error al actualizar']);

    case 'eliminar':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) respuesta(['success' => false, 'error' => 'ID inválido']);
        $ok = $modelo->eliminar($id);
        respuesta($ok ? ['success' => true, 'message' => 'Propuesta eliminada']
                      : ['success' => false, 'error' => 'Error al eliminar']);

    default:
        respuesta(['success' => false, 'error' => 'Acción no válida']);
}