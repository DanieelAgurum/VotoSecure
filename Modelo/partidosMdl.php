<?php

class PartidosMdl
{
    private $conexion;
    private $tabla = 'partidos';

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function existeNombre($nombre, $idExcluir = null)
    {

        if ($idExcluir) {
            $query = "SELECT id_partido FROM partidos 
                  WHERE nombre_partido = ? AND id_partido != ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("si", $nombre, $idExcluir);
        } else {
            $query = "SELECT id_partido FROM partidos 
                  WHERE nombre_partido = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("s", $nombre);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function existeSiglas($siglas, $idExcluir = null)
    {

        if ($idExcluir) {
            $query = "SELECT id_partido FROM partidos 
                  WHERE siglas = ? AND id_partido != ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("si", $siglas, $idExcluir);
        } else {
            $query = "SELECT id_partido FROM partidos 
                  WHERE siglas = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("s", $siglas);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function crear($nombre, $siglas, $logo, $estatus)
    {
        $query = "INSERT INTO {$this->tabla} 
                  (nombre_partido, siglas, logo_partido, estatus) 
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("sssi", $nombre, $siglas, $logo, $estatus);
        return $stmt->execute();
    }

    public function obtenerTodos()
    {
        $query = "SELECT * FROM {$this->tabla} ORDER BY nombre_partido ASC";
        $result = $this->conexion->query($query);

        $partidos = [];
        while ($row = $result->fetch_assoc()) {
            $partidos[] = $row;
        }
        return $partidos;
    }

    public function obtenerPorId($id)
    {
        $query = "SELECT * FROM {$this->tabla} WHERE id_partido = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function actualizar($id, $nombre, $siglas, $logo, $estatus)
    {
        $query = "UPDATE {$this->tabla} 
                  SET nombre_partido = ?, siglas = ?, logo_partido = ?, estatus = ? 
                  WHERE id_partido = ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("sssii", $nombre, $siglas, $logo, $estatus, $id);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $query = "DELETE FROM {$this->tabla} WHERE id_partido = ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function cambiarEstado($id, $nuevoEstado)
    {
        $query = "UPDATE {$this->tabla} SET estatus = ? WHERE id_partido = ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ii", $nuevoEstado, $id);
        return $stmt->execute();
    }
}
