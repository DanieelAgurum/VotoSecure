<?php

require_once __DIR__ . '/config/conexion.php';

class Candidato{
    private $id;
    private $nombre;
    private $apellido;
    private $id_partido;
    private $id_tipo;
    private $cargo;
    private $distrito;
    private $correo;
    private $telefono;
    private $estatus;

    public function __construct() {}

    //metodo guardar
    public function guardar($datos){
       $conexion = (new Conexion())->conectar();
       $sql = "INSERT INTO candidatos 
            (nombre, apellido, id_partido, id_tipo, cargo, distrito, correo, telefono, estatus)
            VALUES 
            (:nombre, :apellido, :id_partido, :id_tipo, :cargo, :distrito, :correo, :telefono, :estatus)";

            $stmt = $conexion->prepare($sql);
            return $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':apellido' => $datos['apellido'],
                ':id_partido' => $datos['id_partido'],
                ':id_tipo' => $datos['id_tipo'],
                ':cargo' => $datos['cargo'],
                ':distrito' => $datos['distrito'],
                ':correo' => $datos['correo'],
                ':telefono' => $datos['telefono'],
                ':estatus' => 1
            ]);
    }
   public function obtenerCandidatos(){

    $conexion = (new Conexion())->conectar();

    $sql = "SELECT c.*, 
                   p.nombre_partido AS partido_nombre, 
                   t.nombre_tipo AS tipo_nombre
            FROM candidatos c
            INNER JOIN partidos p ON c.id_partido = p.id_partido
            INNER JOIN tipos_eleccion t ON c.id_tipo = t.id_tipo";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function eliminar($id){

    $conexion = (new Conexion())->conectar();

    $sql = "DELETE FROM candidatos WHERE id = :id";

    $stmt = $conexion->prepare($sql);

    return $stmt->execute([
        ':id' => $id
    ]);
}
}



?>