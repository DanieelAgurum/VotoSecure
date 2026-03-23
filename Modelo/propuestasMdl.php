<?php

class PropuestasMdl
{
    private $conexion;
    private $tabla = 'propuestas';

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function existePropuestaPorCandidato($candidato_id, $idExcluir = null)
    {
        if ($idExcluir) {
            $query = "SELECT id_propuesta FROM {$this->tabla}
                      WHERE candidato_id = ? AND id_propuesta != ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("ii", $candidato_id, $idExcluir);
        } else {
            $query = "SELECT id_propuesta FROM {$this->tabla}
                      WHERE candidato_id = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $candidato_id);
        }

        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function existeTitulo($titulo, $candidato_id, $idExcluir = null)
    {
        if ($idExcluir) {
            $query = "SELECT id_propuesta FROM {$this->tabla}
                      WHERE titulo = ? AND candidato_id = ? AND id_propuesta != ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("sii", $titulo, $candidato_id, $idExcluir);
        } else {
            $query = "SELECT id_propuesta FROM {$this->tabla}
                      WHERE titulo = ? AND candidato_id = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("si", $titulo, $candidato_id);
        }

        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function crear($candidato_id, $titulo, $slogan, $mision, $propuesta_detallada, $video_url)
    {
        $query = "INSERT INTO {$this->tabla}
                  (candidato_id, titulo, slogan, mision, propuesta_detallada, video_url)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("isssss", $candidato_id, $titulo, $slogan, $mision, $propuesta_detallada, $video_url);
        return $stmt->execute();
    }

    public function actualizar($id, $candidato_id, $titulo, $slogan, $mision, $propuesta_detallada, $video_url)
    {
        $query = "UPDATE {$this->tabla}
                  SET candidato_id = ?, titulo = ?, slogan = ?, mision = ?,
                      propuesta_detallada = ?, video_url = ?
                  WHERE id_propuesta = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("isssssi", $candidato_id, $titulo, $slogan, $mision, $propuesta_detallada, $video_url, $id);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $query = "DELETE FROM {$this->tabla} WHERE id_propuesta = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function obtenerConCandidato()
    {
        $query = "SELECT p.*,
                         c.nombre, c.apellido, c.cargo, c.foto,
                         c.correo, c.telefono, c.distrito,
                         par.nombre_partido, par.siglas, par.logo_partido
                  FROM {$this->tabla} p
                  INNER JOIN candidatos c ON p.candidato_id = c.id
                  LEFT  JOIN partidos   par ON c.id_partido = par.id_partido
                  ORDER BY p.id_propuesta DESC";
        $result = $this->conexion->query($query);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function obtenerTodas()
    {
        return $this->obtenerConCandidato();
    }

    public function obtenerPorId($id)
    {
        $query = "SELECT p.*,
                         c.nombre, c.apellido, c.cargo, c.foto,
                         c.correo, c.telefono, c.distrito,
                         par.nombre_partido, par.siglas, par.logo_partido
                  FROM {$this->tabla} p
                  INNER JOIN candidatos c ON p.candidato_id = c.id
                  LEFT  JOIN partidos   par ON c.id_partido = par.id_partido
                  WHERE p.id_propuesta = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerPorCandidato($candidato_id)
    {
        $query = "SELECT * FROM {$this->tabla} WHERE candidato_id = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $candidato_id);
        $stmt->execute();

        $propuestas = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $propuestas[] = $row;
        }
        return $propuestas;
    }

    public function obtenerCandidatos()
    {
        $query = "SELECT id, nombre, apellido FROM candidatos ORDER BY nombre";
        $result = $this->conexion->query($query);

        $candidatos = [];
        while ($row = $result->fetch_assoc()) {
            $candidatos[] = $row;
        }
        return $candidatos;
    }
}
