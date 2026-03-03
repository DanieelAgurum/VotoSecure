<?php
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
        echo "Candidato guardado correctamente";
    } else {
        echo "Error al guardar";
    }
}