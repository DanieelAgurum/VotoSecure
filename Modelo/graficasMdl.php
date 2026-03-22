<?php
// Modelo está en Modelo/, conexion.php está en api/
require_once __DIR__ . '/config/conexion.php';

class graficasMdl {

    private $pdo;

    public function __construct() {
        // conexion.php define la clase Conexion con método conectar()
        $conn      = new Conexion();
        $this->pdo = $conn->conectar();
    }

    // Votantes únicos que han votado (personas, no filas)
    public function getTotalVotos() {
        $stmt = $this->pdo->query(
            "SELECT COUNT(DISTINCT votante_clave_elector)
             FROM votos_boleta
             WHERE tipo = 'normal' AND votante_clave_elector != ''"
        );
        return (int) $stmt->fetchColumn();
    }

    public function getTotalVotantes() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM votantes");
        return (int) $stmt->fetchColumn();
    }

    public function getPuestos() {
        $stmt = $this->pdo->query(
            "SELECT DISTINCT puesto FROM votos_boleta ORDER BY puesto"
        );
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // 1 fila = 1 voto al candidato en ese puesto
    public function getVotosPorPuesto(string $puesto) {
        $stmt = $this->pdo->prepare(
            "SELECT partido, nombre_candidato, COUNT(*) AS votos
             FROM votos_boleta
             WHERE puesto = :puesto AND tipo = 'normal'
             GROUP BY partido, nombre_candidato
             ORDER BY votos DESC"
        );
        $stmt->execute([':puesto' => $puesto]);
        return $stmt->fetchAll();
    }

    public function getOmitidosPorPuesto(string $puesto) {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM votos_boleta
             WHERE puesto = :puesto AND tipo = 'omitido'"
        );
        $stmt->execute([':puesto' => $puesto]);
        return (int) $stmt->fetchColumn();
    }

    // Partido con más votos acumulados en todos los puestos
    public function getPartidoLider() {
        $stmt = $this->pdo->query(
            "SELECT partido,
                    COUNT(*) AS votos_total,
                    COUNT(DISTINCT puesto) AS puestos_presentes
             FROM votos_boleta
             WHERE tipo = 'normal'
             GROUP BY partido
             ORDER BY votos_total DESC
             LIMIT 1"
        );
        return $stmt->fetch() ?: ['partido' => '—', 'votos_total' => 0, 'puestos_presentes' => 0];
    }

    // Partido que va ganando en cada puesto
    public function getPuestosGanados() {
        $puestos = $this->getPuestos();
        $ganados = [];
        foreach ($puestos as $puesto) {
            $votos = $this->getVotosPorPuesto($puesto);
            if (!empty($votos)) {
                $partido = $votos[0]['partido'];
                $ganados[$partido] = ($ganados[$partido] ?? 0) + 1;
            }
        }
        arsort($ganados);
        return $ganados;
    }

    public function getDashboardData() {
        $puestos    = $this->getPuestos();
        $totalVotos = $this->getTotalVotos();
        $totalReg   = $this->getTotalVotantes();
        $lider      = $this->getPartidoLider();
        $puestosGanados = $this->getPuestosGanados();

        $resultados = [];
        foreach ($puestos as $puesto) {
            $votos       = $this->getVotosPorPuesto($puesto);
            $ganador     = !empty($votos)
                ? $votos[0]
                : ['partido' => '—', 'nombre_candidato' => '—', 'votos' => 0];
            $totalPuesto = array_sum(array_column($votos, 'votos'));

            foreach ($votos as &$v) {
                $v['porcentaje'] = $totalPuesto > 0
                    ? round(($v['votos'] / $totalPuesto) * 100, 1)
                    : 0;
            }
            unset($v);

            $ganador['porcentaje'] = $totalPuesto > 0 && isset($ganador['votos'])
                ? round(($ganador['votos'] / $totalPuesto) * 100, 1)
                : 0;

            $resultados[] = [
                'puesto'   => $puesto,
                'ganador'  => $ganador,
                'votos'    => $votos,
                'omitidos' => $this->getOmitidosPorPuesto($puesto),
                'total'    => $totalPuesto,
            ];
        }

        return [
            'total_votos'     => $totalVotos,
            'total_votantes'  => $totalReg,
            'participacion'   => $totalReg > 0 ? round(($totalVotos / $totalReg) * 100, 1) : 0,
            'partido_lider'   => $lider,
            'puestos_ganados' => $puestosGanados,
            'puestos'         => count($puestos),
            'resultados'      => $resultados,
            'timestamp'       => date('H:i:s'),
        ];
    }
}