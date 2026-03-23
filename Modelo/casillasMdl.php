<?php
require_once __DIR__ . '/config/conexion.php';

class CasillasMdl {

    private $pdo;

    public function __construct() {
        $this->pdo = (new Conexion())->conectar();
    }

    // ── Todas las casillas ────────────────────────────────────────────────
    public function obtenerTodas() {
        $stmt = $this->pdo->query(
            "SELECT c.id_casilla,
                    c.numero_seccion,
                    c.tipo,
                    c.direccion,
                    c.activa,
                    m.nombre AS municipio,
                    e.nombre AS estado
             FROM   casillas c
             JOIN   secciones  s ON c.numero_seccion = s.numero_seccion
             JOIN   municipios m ON s.id_municipio   = m.id_municipio
             JOIN   estados    e ON m.id_estado      = e.id_estado
             ORDER  BY c.id_casilla DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Una casilla por ID ────────────────────────────────────────────────
    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare(
            "SELECT c.id_casilla,
                    c.numero_seccion,
                    c.tipo,
                    c.direccion,
                    c.activa,
                    m.nombre AS municipio,
                    e.nombre AS estado
             FROM   casillas c
             JOIN   secciones  s ON c.numero_seccion = s.numero_seccion
             JOIN   municipios m ON s.id_municipio   = m.id_municipio
             JOIN   estados    e ON m.id_estado      = e.id_estado
             WHERE  c.id_casilla = :id"
        );
        $stmt->execute([':id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ── Secciones agrupadas por estado — municipio ────────────────────────
    public function obtenerSecciones() {
        $stmt = $this->pdo->query(
            "SELECT s.numero_seccion,
                    m.nombre AS municipio,
                    e.nombre AS estado
             FROM   secciones  s
             JOIN   municipios m ON s.id_municipio = m.id_municipio
             JOIN   estados    e ON m.id_estado    = e.id_estado
             ORDER  BY e.nombre, m.nombre, s.numero_seccion"
        );
        $grupos = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $key = $row['estado'] . ' — ' . $row['municipio'];
            $grupos[$key][] = $row['numero_seccion'];
        }
        return $grupos;
    }

    // ── Secciones ya ocupadas ─────────────────────────────────────────────
    public function seccionesOcupadas($excluirId = null) {
        if ($excluirId) {
            $stmt = $this->pdo->prepare(
                "SELECT numero_seccion FROM casillas WHERE id_casilla != :id"
            );
            $stmt->execute([':id' => (int)$excluirId]);
        } else {
            $stmt = $this->pdo->query("SELECT numero_seccion FROM casillas");
        }
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'numero_seccion');
    }

    // ── Crear — devuelve la fila completa para insertar en la tabla ───────
    public function crear($datos) {
        try {
            $err = $this->validar($datos);
            if (!empty($err))
                return ['success' => false, 'error' => implode(', ', $err)];

            if ($this->seccionEnUso($datos['numero_seccion']))
                return ['success' => false, 'error' => 'Esa sección ya está asignada a otra casilla'];

            $stmt = $this->pdo->prepare(
                "INSERT INTO casillas (numero_seccion, tipo, direccion, activa)
                 VALUES (:seccion, :tipo, :direccion, :activa)"
            );
            $stmt->execute([
                ':seccion'   => (int)$datos['numero_seccion'],
                ':tipo'      => $datos['tipo'],
                ':direccion' => trim($datos['direccion']),
                ':activa'    => (int)$datos['activa'],
            ]);

            $id = (int)$this->pdo->lastInsertId();
            $fila = $this->obtenerPorId($id);

            return ['success' => true, 'message' => 'Casilla registrada correctamente', 'casilla' => $fila];
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error al crear: ' . $e->getMessage()];
        }
    }

    // ── Modificar — devuelve la fila actualizada ──────────────────────────
    public function modificar($datos) {
        try {
            $err = $this->validar($datos);
            if (!empty($err))
                return ['success' => false, 'error' => implode(', ', $err)];

            if ($this->seccionEnUso($datos['numero_seccion'], $datos['id_casilla']))
                return ['success' => false, 'error' => 'Esa sección ya está asignada a otra casilla'];

            $stmt = $this->pdo->prepare(
                "UPDATE casillas
                 SET numero_seccion = :seccion,
                     tipo           = :tipo,
                     direccion      = :direccion,
                     activa         = :activa
                 WHERE id_casilla   = :id"
            );
            $stmt->execute([
                ':seccion'   => (int)$datos['numero_seccion'],
                ':tipo'      => $datos['tipo'],
                ':direccion' => trim($datos['direccion']),
                ':activa'    => (int)$datos['activa'],
                ':id'        => (int)$datos['id_casilla'],
            ]);

            $fila = $this->obtenerPorId((int)$datos['id_casilla']);

            return ['success' => true, 'message' => 'Casilla actualizada correctamente', 'casilla' => $fila];
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }

    // ── Eliminar ──────────────────────────────────────────────────────────
    public function eliminar($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM casillas WHERE id_casilla = :id");
            $stmt->execute([':id' => (int)$id]);
            if ($stmt->rowCount() === 0)
                return ['success' => false, 'error' => 'Casilla no encontrada'];
            return ['success' => true, 'message' => 'Casilla eliminada correctamente'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error al eliminar: ' . $e->getMessage()];
        }
    }

    // ── Validaciones ──────────────────────────────────────────────────────
    private function validar($datos) {
        $errores = [];
        if (empty($datos['numero_seccion']) || !is_numeric($datos['numero_seccion']))
            $errores[] = 'Debe seleccionar una sección';
        if (!in_array($datos['tipo'] ?? '', ['Normal', 'Especial']))
            $errores[] = 'El tipo de casilla es inválido';
        if (empty(trim($datos['direccion'] ?? '')))
            $errores[] = 'La dirección es requerida';
        return $errores;
    }

    // ── ¿La sección ya tiene casilla asignada? ────────────────────────────
    private function seccionEnUso($numero_seccion, $excluirId = null) {
        if ($excluirId) {
            $stmt = $this->pdo->prepare(
                "SELECT id_casilla FROM casillas
                 WHERE numero_seccion = :seccion AND id_casilla != :id"
            );
            $stmt->execute([':seccion' => (int)$numero_seccion, ':id' => (int)$excluirId]);
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT id_casilla FROM casillas WHERE numero_seccion = :seccion"
            );
            $stmt->execute([':seccion' => (int)$numero_seccion]);
        }
        return $stmt->rowCount() > 0;
    }

    // ── Consulta pública: CURP → votante + casilla asignada ───────────────
    // Solo devuelve datos NO sensibles (sin NFC, huella, RFC, etc.)
    public function consultarPorCurp($curp) {
        // 1. Buscar votante por CURP
        $stmt = $this->pdo->prepare(
            "SELECT id, nombre, apellido_paterno, apellido_materno,
                    genero, seccion_electoral, clave_elector,
                    municipio, entidad, colonia, codigo_postal
             FROM   votantes
             WHERE  curp = :curp AND (estado = 'activo' OR estado = 'votado')
             LIMIT  1"
        );
        $stmt->execute([':curp' => strtoupper(trim($curp))]);
        $votante = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$votante) {
            return ['encontrado' => false, 'error' => 'No se encontró ningún votante registrado con esa CURP.'];
        }

        $seccion = (int)$votante['seccion_electoral'];

        // 2. Buscar casilla asignada a esa sección
        $stmt2 = $this->pdo->prepare(
            "SELECT c.id_casilla,
                    c.numero_seccion,
                    c.tipo,
                    c.direccion,
                    c.activa,
                    m.nombre AS municipio_casilla,
                    e.nombre AS estado_casilla
             FROM   casillas   c
             JOIN   secciones  s ON c.numero_seccion = s.numero_seccion
             JOIN   municipios m ON s.id_municipio   = m.id_municipio
             JOIN   estados    e ON m.id_estado      = e.id_estado
             WHERE  c.numero_seccion = :seccion
             LIMIT  1"
        );
        $stmt2->execute([':seccion' => $seccion]);
        $casilla = $stmt2->fetch(PDO::FETCH_ASSOC);

        return [
            'encontrado' => true,
            'votante'    => [
                'nombre'          => $votante['nombre'],
                'apellido_paterno' => $votante['apellido_paterno'],
                'apellido_materno' => $votante['apellido_materno'],
                'genero'          => $votante['genero'],
                'clave_elector'   => $votante['clave_elector'],
                'seccion'         => $seccion,
                'municipio'       => $votante['municipio'],
                'entidad'         => $votante['entidad'],
            ],
            'casilla'    => $casilla ?: null,
        ];
    }
}