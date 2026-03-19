<?php
session_start();

require_once "../modelo/config/conexion.php";
require_once "../Modelo/eleccionesMdl.php";

class EleccionesCtrl
{
    private $modelo;

    public function __construct($conexion)
    {
        $this->modelo = new EleccionesMdl($conexion);
    }

    public function crear()
    {
        $nombre = trim($_POST["nombre_eleccion"]);
        $descripcion = trim($_POST["descripcion_eleccion"]);
        $id_tipo = $_POST["id_tipo"];
        $fecha_inicio = $_POST["fecha_inicio"];
        $fecha_fin = $_POST["fecha_fin"];

        $id_estado = $_POST["id_estado"] ?? null;
        $id_municipio = $_POST["id_municipio"] ?? null;

        $errores = [];
        $ahora = date("Y-m-d H:i:s");

        if (!isset($_POST["id_tipo"]) || empty($_POST["id_tipo"])) {
            $errores[] = "El tipo de elección es obligatorio.";
        }

        if (strlen($nombre) < 5) {
            $errores[] = "El nombre debe tener al menos 5 caracteres.";
        }

        if (strlen($descripcion) < 10) {
            $errores[] = "La descripción debe tener al menos 10 caracteres.";
        }

        if ($fecha_inicio <= $ahora) {
            $errores[] = "La fecha de inicio debe ser mayor a la fecha y hora actual.";
        }

        if ($fecha_fin <= $fecha_inicio) {
            $errores[] = "La fecha fin debe ser mayor a la fecha inicio.";
        }

        if ($this->modelo->existeTraslape($id_tipo, $fecha_inicio, $fecha_fin)) {
            $errores[] = "Ya existe una elección de este tipo en ese rango de fechas.";
        }

        if ($id_tipo == 2 && empty($id_estado)) {
            $errores[] = "Debe seleccionar un estado para elección estatal.";
        }

        if ($id_tipo == 3) {
            if (empty($id_estado)) {
                $errores[] = "Debe seleccionar un estado.";
            }
            if (empty($id_municipio)) {
                $errores[] = "Debe seleccionar un municipio.";
            }
        }

        if (!empty($errores)) {
            $_SESSION["errores"] = $errores;
            header("Location: ../Vista/admin/elecciones.php");
            exit;
        }

        if ($this->modelo->crear(
            $nombre,
            $descripcion,
            $id_tipo,
            $fecha_inicio,
            $fecha_fin,
            $id_estado,
            $id_municipio
        )) {
            $_SESSION["success"] = "Elección registrada correctamente.";
        } else {
            $_SESSION["errores"] = ["Error al registrar la elección."];
        }

        header("Location: ../Vista/admin/elecciones.php");
        exit;
    }

    public function actualizar()
    {
        $id = $_POST["id_eleccion"];
        $nombre = trim($_POST["nombre_eleccion"]);
        $descripcion = trim($_POST["descripcion_eleccion"]);
        $id_tipo = $_POST["id_tipo"];
        $fecha_inicio = $_POST["fecha_inicio"];
        $fecha_fin = $_POST["fecha_fin"];

        $id_estado = $_POST["id_estado"] ?? null;
        $id_municipio = $_POST["id_municipio"] ?? null;

        $errores = [];
        $ahora = date("Y-m-d H:i:s");

        $eleccionActual = $this->modelo->obtenerPorId($id);

        if (!$eleccionActual) {
            $errores[] = "La elección no existe.";
        }

        if ($eleccionActual["fecha_inicio"] <= $ahora) {
            $errores[] = "No se puede modificar una elección que ya inició o finalizó.";
        }

        if ($fecha_inicio <= $ahora) {
            $errores[] = "La nueva fecha de inicio debe ser mayor a la fecha actual.";
        }

        if ($fecha_fin <= $fecha_inicio) {
            $errores[] = "La fecha fin debe ser mayor a la fecha inicio.";
        }

        if ($this->modelo->existeTraslape($id_tipo, $fecha_inicio, $fecha_fin, $id)) {
            $errores[] = "No se puede actualizar: las fechas se traslapan con otra elección.";
        }

        if ($id_tipo == 2 && empty($id_estado)) {
            $errores[] = "Debe seleccionar un estado.";
        }

        if ($id_tipo == 3) {
            if (empty($id_estado)) {
                $errores[] = "Debe seleccionar un estado.";
            }
            if (empty($id_municipio)) {
                $errores[] = "Debe seleccionar un municipio.";
            }
        }

        if (!empty($errores)) {
            $_SESSION["errores"] = $errores;
            header("Location: ../Vista/admin/elecciones.php");
            exit;
        }

        if ($this->modelo->actualizar(
            $id,
            $nombre,
            $descripcion,
            $id_tipo,
            $fecha_inicio,
            $fecha_fin,
            $id_estado,
            $id_municipio
        )) {
            $_SESSION["success"] = "Elección actualizada correctamente.";
        } else {
            $_SESSION["errores"] = ["Error al actualizar la elección."];
        }

        header("Location: ../Vista/admin/elecciones.php");
        exit;
    }


    public function eliminar()
    {
        $id = $_POST["id_eleccion"];
        $hoy = date("Y-m-d");

        $eleccion = $this->modelo->obtenerPorId($id);

        if ($eleccion["estado"] == 1) {
            $_SESSION["errores"] = ["No se puede eliminar una elección activa."];
            header("Location: ../Vista/admin/elecciones.php");
            exit;
        }

        if ($eleccion["fecha_inicio"] <= $hoy) {
            $_SESSION["errores"] = ["No se puede eliminar una elección que ya inició."];
            header("Location: ../Vista/admin/elecciones.php");
            exit;
        }

        if (date("Y-m-d") >= $eleccion["fecha_inicio"]) {
            $_SESSION["errores"] = ["No se puede eliminar una elección iniciada o finalizada."];
            header("Location: ../Vista/admin/elecciones.php");
            exit;
        }

        if ($this->modelo->eliminar($id)) {
            $_SESSION["success"] = "Elección eliminada correctamente.";
        } else {
            $_SESSION["errores"] = ["Error al eliminar la elección."];
        }

        header("Location: ../Vista/admin/elecciones.php");
        exit;
    }
    public function cancelar()
    {
        $id = $_POST["cancelar_id"];
        $hoy = date("Y-m-d");

        $eleccion = $this->modelo->obtenerPorId($id);

        if ($eleccion["estado"] == 1) {
            $_SESSION["errores"] = ["No se puede cancelar una elección activa."];
            header("Location: ../Vista/admin/elecciones.php");
            exit;
        }

        if ($eleccion["fecha_inicio"] <= $hoy) {
            $_SESSION["errores"] = ["No se puede cancelar una elección que ya inició."];
            header("Location: ../Vista/admin/elecciones.php");
            exit;
        }

        if (date("Y-m-d") >= $eleccion["fecha_inicio"]) {
            $_SESSION["errores"] = ["No se puede cancelar una elección iniciada o finalizada."];
            header("Location: ../Vista/admin/elecciones.php");
            exit;
        }

        if ($this->modelo->cancelar($id)) {
            $_SESSION["success"] = "Elección cancelada correctamente.";
        } else {
            $_SESSION["errores"] = ["Error al cancelar la elección."];
        }

        header("Location: ../Vista/admin/elecciones.php");
        exit;
    }
}


if (isset($_GET['accion']) && $_GET['accion'] == 'obtener_municipios') {

    $id_estado = $_GET['id_estado'];

    $stmt = $conexion->prepare("SELECT id_municipio, nombre FROM municipios WHERE id_estado = ?");
    $stmt->bind_param("i", $id_estado);
    $stmt->execute();

    $result = $stmt->get_result();
    $municipios = $result->fetch_all(MYSQLI_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($municipios);
    exit;
}


$controlador = new EleccionesCtrl($conexion);

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
        case "cancelar":
            $controlador->cancelar();
            break;
    }
}
