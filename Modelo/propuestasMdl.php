<?php
require_once __DIR__ . '/config/conexion.php';

class PropuestasMdl {

    private $pdo;

    public function __construct($conexion = null) {
        $this->pdo = (new Conexion())->conectar();
    }

    // ✅ Alias para compatibilidad con el código del equipo
    public function obtenerConCandidato() {
        return $this->obtenerTodas();
    }

    public function obtenerTodas() {
        $stmt = $this->pdo->query(
            "SELECT p.*, c.nombre, c.apellido, c.cargo, c.foto,
                    par.nombre_partido, par.siglas
             FROM propuestas p
             INNER JOIN candidatos c ON p.candidato_id = c.id
             LEFT JOIN partidos par ON c.id_partido = par.id_partido
             ORDER BY p.created_at DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, c.nombre, c.apellido, c.cargo, c.foto, c.correo, c.telefono, c.distrito,
                    par.nombre_partido, par.siglas, par.logo_partido
             FROM propuestas p
             INNER JOIN candidatos c ON p.candidato_id = c.id
             LEFT JOIN partidos par ON c.id_partido = par.id_partido
             WHERE p.id_propuesta = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPorCandidato($candidato_id) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM propuestas WHERE candidato_id = :id ORDER BY created_at DESC"
        );
        $stmt->execute([':id' => $candidato_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCandidatos() {
        $stmt = $this->pdo->query(
            "SELECT id, nombre, apellido, cargo FROM candidatos
             WHERE estatus = 'activo' ORDER BY nombre ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeTitulo($titulo, $candidato_id, $idExcluir = null) {
        if ($idExcluir) {
            $stmt = $this->pdo->prepare(
                "SELECT id_propuesta FROM propuestas
                 WHERE titulo = :titulo AND candidato_id = :cid AND id_propuesta != :id"
            );
            $stmt->execute([':titulo' => $titulo, ':cid' => $candidato_id, ':id' => $idExcluir]);
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT id_propuesta FROM propuestas
                 WHERE titulo = :titulo AND candidato_id = :cid"
            );
            $stmt->execute([':titulo' => $titulo, ':cid' => $candidato_id]);
        }
        return $stmt->rowCount() > 0;
    }

    public function crear($candidato_id, $titulo, $slogan, $mision, $propuesta_detallada, $video_url) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO propuestas (candidato_id, titulo, slogan, mision, propuesta_detallada, video_url)
             VALUES (:cid, :titulo, :slogan, :mision, :detalle, :video)"
        );
        return $stmt->execute([
            ':cid'     => $candidato_id,
            ':titulo'  => $titulo,
            ':slogan'  => $slogan,
            ':mision'  => $mision,
            ':detalle' => $propuesta_detallada,
            ':video'   => $video_url
        ]);
    }

    public function actualizar($id, $candidato_id, $titulo, $slogan, $mision, $propuesta_detallada, $video_url) {
        $stmt = $this->pdo->prepare(
            "UPDATE propuestas
             SET candidato_id=:cid, titulo=:titulo, slogan=:slogan,
                 mision=:mision, propuesta_detallada=:detalle, video_url=:video
             WHERE id_propuesta=:id"
        );
        return $stmt->execute([
            ':id'      => $id,
            ':cid'     => $candidato_id,
            ':titulo'  => $titulo,
            ':slogan'  => $slogan,
            ':mision'  => $mision,
            ':detalle' => $propuesta_detallada,
            ':video'   => $video_url
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM propuestas WHERE id_propuesta = :id");
        return $stmt->execute([':id' => $id]);
    }
}