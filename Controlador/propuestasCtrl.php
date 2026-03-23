<?php
session_start();

require_once "../modelo/config/conexion.php";
require_once "../Modelo/propuestasMdl.php";

class PropuestasCtrl
{
    private $modelo;

    public function __construct($conexion)
    {
        $this->modelo = new PropuestasMdl($conexion);
    }

    public function crear()
    {
        $candidato_id       = $_POST["candidato_id"] ?? null;
        $titulo             = trim($_POST["titulo"] ?? "");
        $slogan             = trim($_POST["slogan"] ?? "");
        $mision             = trim($_POST["mision"] ?? "");
        $propuesta_detallada = trim($_POST["propuesta_detallada"] ?? "");
        $video_url          = trim($_POST["video_url"] ?? "");

        $errores = [];

        if (empty($candidato_id)) {
            $errores[] = "Debe seleccionar un candidato.";
        }

        if (empty($titulo)) {
            $errores[] = "El título es obligatorio.";
        }

        if (empty($propuesta_detallada)) {
            $errores[] = "La propuesta detallada es obligatoria.";
        }

        if (strlen($titulo) > 150) {
            $errores[] = "El título no puede superar los 150 caracteres.";
        }

        if (!empty($slogan) && strlen($slogan) > 255) {
            $errores[] = "El slogan es demasiado largo.";
        }

        if (!empty($mision) && strlen($mision) > 500) {
            $errores[] = "La misión no puede superar los 500 caracteres.";
        }

        if (strlen($propuesta_detallada) < 20) {
            $errores[] = "La propuesta debe tener al menos 20 caracteres.";
        }

        if (!empty($titulo) && !preg_match("/^[\p{L}0-9\s.,áéíóúÁÉÍÓÚñÑ]+$/u", $titulo)) {
            $errores[] = "El título contiene caracteres no válidos.";
        }

        if (!empty($video_url)) {
            if (!filter_var($video_url, FILTER_VALIDATE_URL)) {
                $errores[] = "La URL del video no es válida.";
            } elseif (!preg_match("/(youtube\.com|youtu\.be)/", $video_url)) {
                $errores[] = "Solo se permiten enlaces de YouTube.";
            }
        }

        if (!empty($candidato_id) && $this->modelo->existePropuestaPorCandidato($candidato_id)) {
            $errores[] = "Este candidato ya tiene una propuesta registrada. Solo se permite una por candidato.";
        }

        if (empty($errores) && $this->modelo->existeTitulo($titulo, $candidato_id)) {
            $errores[] = "Ya existe una propuesta con ese título para este candidato.";
        }

        if (!empty($errores)) {
            $_SESSION["errores"] = $errores;
            header("Location: ../Vista/admin/propuestas.php");
            exit;
        }

        $titulo              = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
        $slogan              = htmlspecialchars($slogan, ENT_QUOTES, 'UTF-8');
        $mision              = htmlspecialchars($mision, ENT_QUOTES, 'UTF-8');
        $propuesta_detallada = htmlspecialchars($propuesta_detallada, ENT_QUOTES, 'UTF-8');
        $video_url           = $this->convertirYoutubeEmbed($video_url);

        if ($this->modelo->crear($candidato_id, $titulo, $slogan, $mision, $propuesta_detallada, $video_url)) {
            $_SESSION["success"] = "Propuesta registrada correctamente.";
        } else {
            $_SESSION["errores"] = ["Error al registrar la propuesta."];
        }

        header("Location: ../Vista/admin/propuestas.php");
        exit;
    }

    public function actualizar()
    {
        $id                  = $_POST["id"] ?? null;
        $candidato_id        = $_POST["candidato_id"] ?? null;
        $titulo              = trim($_POST["titulo"] ?? "");
        $slogan              = trim($_POST["slogan"] ?? "");
        $mision              = trim($_POST["mision"] ?? "");
        $propuesta_detallada = trim($_POST["propuesta_detallada"] ?? "");
        $video_url           = trim($_POST["video_url"] ?? "");

        $errores = [];

        if (empty($id)) {
            $errores[] = "ID inválido.";
        }

        if (empty($candidato_id)) {
            $errores[] = "Debe seleccionar un candidato.";
        }

        if (empty($titulo)) {
            $errores[] = "El título es obligatorio.";
        }

        if (empty($propuesta_detallada)) {
            $errores[] = "La propuesta detallada es obligatoria.";
        }

        if (strlen($titulo) > 150) {
            $errores[] = "El título es demasiado largo.";
        }

        if (!empty($slogan) && strlen($slogan) > 255) {
            $errores[] = "El slogan es demasiado largo.";
        }

        if (!empty($mision) && strlen($mision) > 500) {
            $errores[] = "La misión es demasiado larga.";
        }

        if (strlen($propuesta_detallada) < 20) {
            $errores[] = "La propuesta debe tener al menos 20 caracteres.";
        }

        if (!empty($titulo) && !preg_match("/^[\p{L}0-9\s.,áéíóúÁÉÍÓÚñÑ]+$/u", $titulo)) {
            $errores[] = "El título contiene caracteres no válidos.";
        }

        if (!empty($video_url)) {
            if (!filter_var($video_url, FILTER_VALIDATE_URL)) {
                $errores[] = "La URL del video no es válida.";
            } elseif (!preg_match("/(youtube\.com|youtu\.be)/", $video_url)) {
                $errores[] = "Solo se permiten enlaces de YouTube.";
            }
        }

        if (
            !empty($candidato_id) && !empty($id) &&
            $this->modelo->existePropuestaPorCandidato($candidato_id, $id)
        ) {
            $errores[] = "Este candidato ya tiene otra propuesta registrada.";
        }

        if (empty($errores) && $this->modelo->existeTitulo($titulo, $candidato_id, $id)) {
            $errores[] = "Ya existe otra propuesta con ese título para este candidato.";
        }

        if (!empty($errores)) {
            $_SESSION["errores"] = $errores;
            header("Location: ../Vista/admin/propuestas.php");
            exit;
        }

        $titulo = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
        $slogan = htmlspecialchars($slogan, ENT_QUOTES, 'UTF-8');
        $mision = htmlspecialchars($mision, ENT_QUOTES, 'UTF-8');
        $propuesta_detallada = htmlspecialchars($propuesta_detallada, ENT_QUOTES, 'UTF-8');
        $video_url = $this->convertirYoutubeEmbed($video_url);

        if ($this->modelo->actualizar($id, $candidato_id, $titulo, $slogan, $mision, $propuesta_detallada, $video_url)) {
            $_SESSION["success"] = "Propuesta actualizada correctamente.";
        } else {
            $_SESSION["errores"] = ["Error al actualizar la propuesta."];
        }

        header("Location: ../Vista/admin/propuestas.php");
        exit;
    }

    public function eliminar()
    {
        $id = $_POST["id"] ?? null;

        if (empty($id)) {
            $_SESSION["errores"] = ["ID inválido para eliminar."];
            header("Location: ../Vista/admin/propuestas.php");
            exit;
        }

        if ($this->modelo->eliminar($id)) {
            $_SESSION["success"] = "Propuesta eliminada correctamente.";
        } else {
            $_SESSION["errores"] = ["Error al eliminar la propuesta."];
        }

        header("Location: ../Vista/admin/propuestas.php");
        exit;
    }

    private function convertirYoutubeEmbed($url)
    {
        if (empty($url)) return null;

        if (strpos($url, "embed") !== false) return $url;

        if (strpos($url, "watch?v=") !== false) {
            parse_str(parse_url($url, PHP_URL_QUERY), $params);
            if (isset($params['v'])) {
                return "https://www.youtube.com/embed/" . $params['v'];
            }
        }

        if (strpos($url, "youtu.be/") !== false) {
            $videoId = basename(parse_url($url, PHP_URL_PATH));
            return "https://www.youtube.com/embed/" . $videoId;
        }

        return $url;
    }
}

$controlador = new PropuestasCtrl($conexion);

if (isset($_POST["accion"])) {
    switch ($_POST["accion"]) {
        case "crear":
            $controlador->crear();
            break;
        case "actualizar":
            $controlador->actualizar();
            break;
        case "eliminar":
            $controlador->eliminar();
            break;
    }
}
