<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . "/../Modelo/candidatosMdl.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $candidato = new Candidato();

    $datos = [
        "nombre" => $_POST["nombre"],
        "apellido" => $_POST["apellido"],
        "id_partido" => $_POST["id_partido"],
        "id_tipo" => $_POST["id_tipo"],
        "cargo" => $_POST["cargo"],
        "distrito" => $_POST["distrito"],
        "correo" => $_POST["correo"],
        "telefono" => $_POST["telefono"],
        "estatus" => $_POST["estatus"]
    ];

    $resultado = $candidato->guardar($datos);

    if ($resultado) {
       header("Location: ../Vista/Admin/candidatos.php?success=1");
     exit();
    } else {
        echo "Error al guardar";
    }
}

if (isset($_GET['eliminar'])) {

    $candidato = new Candidato();
    $resultado = $candidato->eliminar($_GET['eliminar']);

    if ($resultado) {
     header("Location: ../Vista/Admin/candidatos.php?deleted=1");
      exit();
        exit();
    } else {
        echo "Error al eliminar";
    }
}