<?php
require_once __DIR__ . '/config/conexion.php';

class ReportesMdl {

    private $pdo;

    public function __construct() {
        $this->pdo = (new Conexion())->conectar();
    }

    // Elecciones — solo para el selector visual, no filtra votos
    public function getElecciones() {
        $stmt = $this->pdo->query(
            "SELECT id_eleccion, nombre_eleccion FROM elecciones ORDER BY fecha_creacion DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Resumen general (sin filtro por elección — no existe esa columna)
    public function getResumenEleccion($id_eleccion = null) {
        $stmt = $this->pdo->query(
            "SELECT
                COUNT(DISTINCT votante_clave_elector) AS total_votantes_que_votaron,
                COUNT(*) AS total_filas,
                COUNT(DISTINCT puesto) AS total_puestos,
                MIN(fecha) AS primer_voto,
                MAX(fecha) AS ultimo_voto
             FROM votos_boleta
             WHERE tipo = 'normal' AND votante_clave_elector != ''"
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalVotantes() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM votantes");
        return (int) $stmt->fetchColumn();
    }

    public function getResultadosPorPuesto($id_eleccion = null) {
        $stmt = $this->pdo->query(
            "SELECT puesto, nombre_candidato, partido, COUNT(*) AS votos, tipo
             FROM votos_boleta
             GROUP BY puesto, nombre_candidato, partido, tipo
             ORDER BY puesto ASC, votos DESC"
        );
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $result[$row['puesto']][] = $row;
        }
        return $result;
    }

    public function getParticipacionPorSeccion($id_eleccion = null) {
        $stmt = $this->pdo->query(
            "SELECT
                votante_seccion AS seccion,
                COUNT(DISTINCT votante_clave_elector) AS votaron
             FROM votos_boleta
             WHERE tipo = 'normal' AND votante_clave_elector != ''
             GROUP BY votante_seccion
             ORDER BY votaron DESC
             LIMIT 20"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVotosPorPartido($id_eleccion = null) {
        $stmt = $this->pdo->query(
            "SELECT partido, COUNT(*) AS votos
             FROM votos_boleta
             WHERE tipo = 'normal'
             GROUP BY partido
             ORDER BY votos DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOmitidosPorPuesto($id_eleccion = null) {
        $stmt = $this->pdo->query(
            "SELECT puesto, COUNT(*) AS omitidos
             FROM votos_boleta
             WHERE tipo = 'omitido'
             GROUP BY puesto"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVotosPorHora($id_eleccion = null) {
        $stmt = $this->pdo->query(
            "SELECT
                HOUR(fecha) AS hora,
                COUNT(DISTINCT votante_clave_elector) AS votantes
             FROM votos_boleta
             WHERE tipo = 'normal' AND votante_clave_elector != ''
             GROUP BY HOUR(fecha)
             ORDER BY hora ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReporteCompleto($id_eleccion = null) {
        return [
            'resumen'     => $this->getResumenEleccion(),
            'total_reg'   => $this->getTotalVotantes(),
            'por_puesto'  => $this->getResultadosPorPuesto(),
            'por_partido' => $this->getVotosPorPartido(),
            'por_seccion' => $this->getParticipacionPorSeccion(),
            'omitidos'    => $this->getOmitidosPorPuesto(),
            'por_hora'    => $this->getVotosPorHora(),
        ];
    }
}