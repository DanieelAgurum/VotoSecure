<?php
session_start();

require_once "../modelo/config/conexion.php";
require_once "../Modelo/partidosMdl.php";

class PartidosCtrl
{

    private $modelo;

    public function __construct($conexion)
    {
        $this->modelo = new PartidosMdl($conexion);
    }

    public function crear()
    {

        $nombre = trim($_POST["nombre"]);
        $siglas = trim($_POST["siglas"]);
        $estatus = $_POST["estatus"];

        $logo = "";
        $errores = [];

        if (!empty($_FILES["logo"]["name"])) {

            $ruta = "../img/partidos/";

            if (!is_dir($ruta)) {
                mkdir($ruta, 0755, true);
            }

            $archivo = $_FILES["logo"];
            $nombreTmp = $archivo["tmp_name"];
            $tamano = $archivo["size"];
            $tipo = mime_content_type($nombreTmp);

            $tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];
            $tamanoMaximo = 2 * 1024 * 1024;

            if (getimagesize($nombreTmp) === false) {
                $errores[] = "El archivo no es una imagen válida.";
            }

            if (!in_array($tipo, $tiposPermitidos)) {
                $errores[] = "Solo se permiten imágenes JPG, PNG o WEBP.";
            }

            if ($tamano > $tamanoMaximo) {
                $errores[] = "La imagen supera 2MB.";
            }

            if (empty($errores)) {
                $extension = pathinfo($archivo["name"], PATHINFO_EXTENSION);
                $nombreUnico = uniqid("partido_", true) . "." . $extension;
                $logo = "img/partidos/" . $nombreUnico;

                if (!move_uploaded_file($nombreTmp, "../" . $logo)) {
                    $errores[] = "Error al subir la imagen.";
                }
            }
        }

        if ($this->modelo->existeNombre($nombre)) {
            $errores[] = "Ya existe un partido con ese nombre.";
        }

        if ($this->modelo->existeSiglas($siglas)) {
            $errores[] = "Las siglas ya están registradas.";
        }

        if (!empty($errores)) {
            $_SESSION["errores"] = $errores;
            header("Location: ../Vista/admin/partidos.php");
            exit;
        }

        if ($this->modelo->crear($nombre, $siglas, $logo, $estatus)) {
            $_SESSION["success"] = "Partido registrado correctamente.";
        } else {
            $_SESSION["errores"] = ["Error al registrar el partido."];
        }

        header("Location: ../Vista/admin/partidos.php");
        exit;
    }

    public function actualizar()
    {

        $id = $_POST["id_partido"];
        $nombre = trim($_POST["nombre"]);
        $siglas = trim($_POST["siglas"]);
        $estatus = $_POST["estatus"];
        $logo = $_POST["logo_actual"];

        $errores = [];

        if (!empty($_FILES["logo"]["name"])) {

            $ruta = "../img/partidos/";

            if (!is_dir($ruta)) {
                mkdir($ruta, 0755, true);
            }

            $archivo = $_FILES["logo"];
            $nombreTmp = $archivo["tmp_name"];
            $tamano = $archivo["size"];
            $tipo = mime_content_type($nombreTmp);

            $tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];
            $tamanoMaximo = 2 * 1024 * 1024;

            if (getimagesize($nombreTmp) === false) {
                $errores[] = "El archivo no es una imagen válida.";
            }

            if (!in_array($tipo, $tiposPermitidos)) {
                $errores[] = "Solo se permiten imágenes JPG, PNG o WEBP.";
            }

            if ($tamano > $tamanoMaximo) {
                $errores[] = "La imagen supera 2MB.";
            }

            if (empty($errores)) {
                $extension = pathinfo($archivo["name"], PATHINFO_EXTENSION);
                $nombreUnico = uniqid("partido_", true) . "." . $extension;
                $logo = "img/partidos/" . $nombreUnico;

                if (!move_uploaded_file($nombreTmp, "../" . $logo)) {
                    $errores[] = "Error al subir la imagen.";
                }
            }
        }

        if ($this->modelo->existeNombre($nombre, $id)) {
            $errores[] = "Ya existe otro partido con ese nombre.";
        }

        if ($this->modelo->existeSiglas($siglas, $id)) {
            $errores[] = "Las siglas ya están registradas en otro partido.";
        }


        if (!empty($errores)) {
            $_SESSION["errores"] = $errores;
            header("Location: ../Vista/admin/partidos.php");
            exit;
        }

        if ($this->modelo->actualizar($id, $nombre, $siglas, $logo, $estatus)) {
            $_SESSION["success"] = "Partido actualizado correctamente.";
        } else {
            $_SESSION["errores"] = ["Error al actualizar el partido."];
        }

        header("Location: ../Vista/admin/partidos.php");
        exit;
    }

    public function eliminar()
    {

        $id = $_POST["id_partido"];

        if ($this->modelo->eliminar(id: $id)) {
            $_SESSION["success"] = "Partido eliminado correctamente.";
        } else {
            $_SESSION["errores"] = ["Error al eliminar el partido."];
        }

        header("Location: ../Vista/admin/partidos.php");
        exit;
    }
    public function cambiarEstado()
    {
        $id = $_POST["id_partido"];
        $nuevoEstado = $_POST["nuevo_estado"];

        if ($this->modelo->cambiarEstado($id, $nuevoEstado)) {
            $_SESSION["success"] = "Estado del partido actualizado correctamente.";
        } else {
            $_SESSION["errores"] = ["Error al actualizar el estado del partido."];
        }

        header("Location: ../Vista/admin/partidos.php");
        exit;
    }
}

$controlador = new PartidosCtrl($conexion);

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
        case "cambiarEstado":
            $controlador->cambiarEstado();
            break;
    }
}
