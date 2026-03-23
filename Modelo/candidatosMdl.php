<?php
require_once __DIR__ . '/config/conexion.php';

class Candidato {
    private $id;
    private $nombre;
    private $apellido;
    private $id_partido;
    private $id_eleccion;
    private $id_tipo;
    private $cargo;
    private $distrito;
    private $correo;
    private $telefono;
    private $foto;
    private $estatus;

    public function __construct() {}

    private function validarDatos($datos) {
        $errores = [];

        if (empty($datos['nombre']) || strlen(trim($datos['nombre'])) < 2)
            $errores[] = "El nombre debe tener al menos 2 caracteres";

        if (empty($datos['apellido']) || strlen(trim($datos['apellido'])) < 2)
            $errores[] = "El apellido debe tener al menos 2 caracteres";

        if (empty($datos['id_partido']) || !is_numeric($datos['id_partido']))
            $errores[] = "Debe seleccionar un partido";

        if (empty($datos['id_eleccion']) || !is_numeric($datos['id_eleccion']))
            $errores[] = "Debe seleccionar una elección";

        if (empty($datos['cargo']) || strlen(trim($datos['cargo'])) < 2)
            $errores[] = "El cargo debe tener al menos 2 caracteres";

        if (empty($datos['distrito']))
            $errores[] = "El distrito es requerido";

        if (!empty($datos['correo']) && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL))
            $errores[] = "El correo electrónico no es válido";

        return $errores;
    }

    private function obtenerTipoPorEleccion($id_eleccion, $conexion) {
        $sql = "SELECT id_tipo FROM elecciones WHERE id_eleccion = :id_eleccion";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':id_eleccion' => (int)$id_eleccion]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) throw new Exception("La elección seleccionada no existe");
        return (int)$row['id_tipo'];
    }

    private function procesarFoto($file) {
        if (!isset($file['error']) || is_array($file['error'])) return null;
        if ($file['error'] === UPLOAD_ERR_NO_FILE) return null;
        if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception("Error al subir la foto");

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedTypes))
            throw new Exception("Solo se permiten imágenes JPEG, PNG, GIF o WebP");

        if ($file['size'] > 5 * 1024 * 1024)
            throw new Exception("La foto no puede superar los 5MB");

        return "data:" . $mimeType . ";base64," . base64_encode(file_get_contents($file['tmp_name']));
    }

    public function guardar($datos, $file = null) {
        try {
            $errores = $this->validarDatos($datos);
            if (!empty($errores))
                return ['success' => false, 'error' => implode(", ", $errores)];

            $foto = $file !== null ? $this->procesarFoto($file) : null;

            $conexion = (new Conexion())->conectar();
            if ($conexion === null)
                return ['success' => false, 'error' => 'Error de conexión a la base de datos'];

            $id_tipo = $this->obtenerTipoPorEleccion($datos['id_eleccion'], $conexion);

            $sql = "INSERT INTO candidatos
                        (nombre, apellido, id_partido, id_eleccion, id_tipo, cargo, distrito, correo, telefono, foto, estatus)
                    VALUES
                        (:nombre, :apellido, :id_partido, :id_eleccion, :id_tipo, :cargo, :distrito, :correo, :telefono, :foto, :estatus)";

            $stmt = $conexion->prepare($sql);
            $resultado = $stmt->execute([
                ':nombre'      => trim($datos['nombre']),
                ':apellido'    => trim($datos['apellido']),
                ':id_partido'  => (int)$datos['id_partido'],
                ':id_eleccion' => (int)$datos['id_eleccion'],
                ':id_tipo'     => $id_tipo,
                ':cargo'       => trim($datos['cargo']),
                ':distrito'    => trim($datos['distrito']),
                ':correo'      => trim($datos['correo']),
                ':telefono'    => trim($datos['telefono']),
                ':foto'        => $foto,
                ':estatus'     => $datos['estatus'] === 'inactivo' ? 'inactivo' : 'activo'
            ]);

            return $resultado
                ? ['success' => true, 'message' => 'Candidato guardado correctamente']
                : ['success' => false, 'error' => 'Error al guardar el candidato'];

        } catch (PDOException $e) {
            error_log("Error en guardar candidato: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error de base de datos'];
        } catch (Exception $e) {
            error_log("Error en guardar candidato: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function obtenerCandidatos() {
        try {
            $conexion = (new Conexion())->conectar();

            $sql = "SELECT c.*, 
                           p.nombre_partido AS partido_nombre,
                           e.nombre_eleccion AS eleccion_nombre,
                           t.nombre_tipo AS tipo_nombre
                    FROM candidatos c
                    INNER JOIN partidos p ON c.id_partido = p.id_partido
                    INNER JOIN elecciones e ON c.id_eleccion = e.id_eleccion
                    INNER JOIN tipos_eleccion t ON c.id_tipo = t.id_tipo
                    ORDER BY c.id DESC";

            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerCandidatos: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPartidos() {
        try {
            $conexion = (new Conexion())->conectar();
            $sql = "SELECT id_partido, nombre_partido, siglas, logo_partido 
                    FROM partidos WHERE estatus = 1 ORDER BY nombre_partido ASC";
            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerPartidos: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerElecciones() {
        try {
            $conexion = (new Conexion())->conectar();
            // ✅ Sin filtro de estado — trae todas las elecciones
            $sql = "SELECT id_eleccion, nombre_eleccion, id_tipo 
                    FROM elecciones 
                    ORDER BY nombre_eleccion ASC";
            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerElecciones: " . $e->getMessage());
            return [];
        }
    }

    public function eliminar($id) {
        try {
            if (empty($id) || !is_numeric($id))
                return ['success' => false, 'error' => 'ID de candidato inválido'];

            $conexion = (new Conexion())->conectar();
            if ($conexion === null)
                return ['success' => false, 'error' => 'Error de conexión a la base de datos'];

            $stmt = $conexion->prepare("DELETE FROM candidatos WHERE id = :id");
            $resultado = $stmt->execute([':id' => $id]);

            return $resultado
                ? ['success' => true, 'message' => 'Candidato eliminado correctamente']
                : ['success' => false, 'error' => 'Error al eliminar el candidato'];

        } catch (PDOException $e) {
            error_log("Error en eliminar candidato: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error de base de datos'];
        }
    }

    public function obtenerPorId($id) {
        try {
            if (empty($id) || !is_numeric($id))
                return ['success' => false, 'error' => 'ID de candidato inválido'];

            $conexion = (new Conexion())->conectar();
            if ($conexion === null)
                return ['success' => false, 'error' => 'Error de conexión a la base de datos'];

            $sql = "SELECT c.*, 
                           p.nombre_partido AS partido_nombre,
                           e.nombre_eleccion AS eleccion_nombre,
                           t.nombre_tipo AS tipo_nombre
                    FROM candidatos c
                    INNER JOIN partidos p ON c.id_partido = p.id_partido
                    INNER JOIN elecciones e ON c.id_eleccion = e.id_eleccion
                    INNER JOIN tipos_eleccion t ON c.id_tipo = t.id_tipo
                    WHERE c.id = :id";

            $stmt = $conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            $candidato = $stmt->fetch(PDO::FETCH_ASSOC);

            return $candidato
                ? ['success' => true, 'candidato' => $candidato]
                : ['success' => false, 'error' => 'Candidato no encontrado'];

        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error de base de datos'];
        }
    }

    public function modificar($datos, $file = null) {
        try {
            $errores = $this->validarDatos($datos);
            if (!empty($errores))
                return ['success' => false, 'error' => implode(", ", $errores)];

            $foto = $file !== null ? $this->procesarFoto($file) : null;

            $conexion = (new Conexion())->conectar();
            if ($conexion === null)
                return ['success' => false, 'error' => 'Error de conexión a la base de datos'];

            $id_tipo = $this->obtenerTipoPorEleccion($datos['id_eleccion'], $conexion);

            $sql = $foto !== null
                ? "UPDATE candidatos SET nombre=:nombre, apellido=:apellido, id_partido=:id_partido,
                       id_eleccion=:id_eleccion, id_tipo=:id_tipo, cargo=:cargo, distrito=:distrito,
                       correo=:correo, telefono=:telefono, foto=:foto, estatus=:estatus WHERE id=:id"
                : "UPDATE candidatos SET nombre=:nombre, apellido=:apellido, id_partido=:id_partido,
                       id_eleccion=:id_eleccion, id_tipo=:id_tipo, cargo=:cargo, distrito=:distrito,
                       correo=:correo, telefono=:telefono, estatus=:estatus WHERE id=:id";

            $stmt = $conexion->prepare($sql);

            $params = [
                ':id'          => $datos['id'],
                ':nombre'      => trim($datos['nombre']),
                ':apellido'    => trim($datos['apellido']),
                ':id_partido'  => (int)$datos['id_partido'],
                ':id_eleccion' => (int)$datos['id_eleccion'],
                ':id_tipo'     => $id_tipo,
                ':cargo'       => trim($datos['cargo']),
                ':distrito'    => trim($datos['distrito']),
                ':correo'      => trim($datos['correo']),
                ':telefono'    => trim($datos['telefono']),
                ':estatus'     => $datos['estatus'] === 'inactivo' ? 'inactivo' : 'activo'
            ];

            if ($foto !== null) $params[':foto'] = $foto;

            $resultado = $stmt->execute($params);

            if ($resultado) {
                $candidatoActualizado = $this->obtenerPorId($datos['id']);
                return [
                    'success'   => true,
                    'message'   => 'Candidato actualizado correctamente',
                    'candidato' => $candidatoActualizado['candidato']
                ];
            }

            return ['success' => false, 'error' => 'Error al actualizar el candidato'];

        } catch (PDOException $e) {
            error_log("Error en modificar candidato: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error de base de datos'];
        } catch (Exception $e) {
            error_log("Error en modificar candidato: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getCandidatosEleccionCercana($limit = 3) {
        try {
            $conexion = (new Conexion())->conectar();
            if ($conexion === null) return [];

            // Encontrar ID de la elección más cercana (priorizando futuras)
            $sqlEleccion = "SELECT id_eleccion FROM elecciones 
                            ORDER BY 
                              CASE 
                                WHEN fecha_inicio > CURDATE() THEN (fecha_inicio - CURDATE()) 
                                ELSE (CURDATE() - fecha_fin) * -1 
                              END ASC 
                            LIMIT 1";
            $stmtEleccion = $conexion->prepare($sqlEleccion);
            $stmtEleccion->execute();
            $eleccion = $stmtEleccion->fetch(PDO::FETCH_ASSOC);
            
            if (!$eleccion) return [];

            $idEleccion = $eleccion['id_eleccion'];

            // Obtener candidatos activos de esa elección
            $sql = "SELECT c.*, 
                           p.nombre_partido AS partido_nombre,
                           e.nombre_eleccion AS eleccion_nombre
                    FROM candidatos c
                    INNER JOIN partidos p ON c.id_partido = p.id_partido
                    INNER JOIN elecciones e ON c.id_eleccion = e.id_eleccion
                    WHERE c.id_eleccion = :id_eleccion AND c.estatus = 'activo'
                    ORDER BY c.id DESC 
                    LIMIT :limit";
            
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':id_eleccion', $idEleccion, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en getCandidatosEleccionCercana: " . $e->getMessage());
            return [];
        }
    }
}