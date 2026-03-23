<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_POST['accion']) || isset($_GET['accion'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'No autorizado']);
    } else {
        header('Location: /VotoSecure/Vista/login.php');
    }
    exit;
}

require_once __DIR__ . '/../Modelo/propuestasMdl.php';

function respuesta($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data); exit;
}

function redirigir($mensaje = null, $error = null) {
    if ($mensaje) $_SESSION['success'] = $mensaje;
    if ($error)   $_SESSION['errores'] = [$error];
    header('Location: ../Vista/Admin/Propuestas.php');
    exit;
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

// Detectar si es petición AJAX/fetch o form POST clásico
$esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
          !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false ||
          isset($_GET['accion']);

$modelo = new PropuestasMdl();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {

    case 'listar':
        respuesta(['success' => true, 'data' => $modelo->obtenerTodas()]);

    case 'obtener':
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) respuesta(['success' => false, 'error' => 'ID inválido']);
        $prop = $modelo->obtenerPorId($id);
        respuesta($prop
            ? ['success' => true, 'data' => $prop]
            : ['success' => false, 'error' => 'No encontrada']);

    case 'candidatos':
        respuesta(['success' => true, 'data' => $modelo->obtenerCandidatos()]);

    case 'crear':
        $titulo  = trim($_POST['titulo'] ?? '');
        $cid     = (int)($_POST['candidato_id'] ?? 0);
        $slogan  = trim($_POST['slogan'] ?? '');
        $mision  = trim($_POST['mision'] ?? '');
        $detalle = trim($_POST['propuesta_detallada'] ?? '');
        $video   = convertirYoutubeEmbed(trim($_POST['video_url'] ?? ''));

        if (empty($titulo) || $cid <= 0 || strlen($detalle) < 20) {
            $esAjax
                ? respuesta(['success' => false, 'error' => 'Faltan campos requeridos'])
                : redirigir(null, 'Faltan campos requeridos');
        }

        if ($modelo->existeTitulo($titulo, $cid)) {
            $esAjax
                ? respuesta(['success' => false, 'error' => 'Ya existe una propuesta con ese título'])
                : redirigir(null, 'Ya existe una propuesta con ese título para este candidato');
        }

        $ok = $modelo->crear($cid, $titulo, $slogan, $mision, $detalle, $video);
        $esAjax
            ? respuesta($ok
                ? ['success' => true,  'message' => 'Propuesta creada correctamente']
                : ['success' => false, 'error'   => 'Error al crear la propuesta'])
            : redirigir($ok ? 'Propuesta registrada correctamente.' : null,
                        $ok ? null : 'Error al registrar la propuesta.');
        break;

    case 'actualizar':
        $id      = (int)($_POST['id'] ?? 0);
        $titulo  = trim($_POST['titulo'] ?? '');
        $cid     = (int)($_POST['candidato_id'] ?? 0);
        $slogan  = trim($_POST['slogan'] ?? '');
        $mision  = trim($_POST['mision'] ?? '');
        $detalle = trim($_POST['propuesta_detallada'] ?? '');
        $video   = convertirYoutubeEmbed(trim($_POST['video_url'] ?? ''));

        if ($id <= 0 || empty($titulo) || $cid <= 0) {
            $esAjax
                ? respuesta(['success' => false, 'error' => 'Faltan campos requeridos'])
                : redirigir(null, 'Faltan campos requeridos');
        }

        if ($modelo->existeTitulo($titulo, $cid, $id)) {
            $esAjax
                ? respuesta(['success' => false, 'error' => 'Ya existe otra propuesta con ese título'])
                : redirigir(null, 'Ya existe otra propuesta con ese título para este candidato');
        }

        $ok = $modelo->actualizar($id, $cid, $titulo, $slogan, $mision, $detalle, $video);
        $esAjax
            ? respuesta($ok
                ? ['success' => true,  'message' => 'Propuesta actualizada correctamente']
                : ['success' => false, 'error'   => 'Error al actualizar'])
            : redirigir($ok ? 'Propuesta actualizada correctamente.' : null,
                        $ok ? null : 'Error al actualizar la propuesta.');
        break;

    case 'eliminar':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $esAjax
                ? respuesta(['success' => false, 'error' => 'ID inválido'])
                : redirigir(null, 'ID inválido');
        }

        $ok = $modelo->eliminar($id);
        $esAjax
            ? respuesta($ok
                ? ['success' => true,  'message' => 'Propuesta eliminada']
                : ['success' => false, 'error'   => 'Error al eliminar'])
            : redirigir($ok ? 'Propuesta eliminada correctamente.' : null,
                        $ok ? null : 'Error al eliminar la propuesta.');
        break;

    default:
        $esAjax
            ? respuesta(['success' => false, 'error' => 'Acción no válida'])
            : redirigir(null, 'Acción no válida');
}