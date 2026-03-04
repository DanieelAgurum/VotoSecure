<?php

require_once __DIR__ . '/config/conexion.php';

class Candidato {
    private $id;
    private $nombre;
    private $apellido;
    private $id_partido;
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

        if (empty($datos['nombre']) || strlen(trim($datos['nombre'])) < 2) {
            $errores[] = "El nombre debe tener al menos 2 caracteres";
        }

        if (empty($datos['apellido']) || strlen(trim($datos['apellido'])) < 2) {
            $errores[] = "El apellido debe tener al menos 2 caracteres";
        }

        if (empty($datos['id_partido']) || !is_numeric($datos['id_partido'])) {
            $errores[] = "Debe seleccionar un partido";
        }

        if (empty($datos['id_tipo']) || !is_numeric($datos['id_tipo'])) {
            $errores[] = "Debe seleccionar un tipo de elección";
        }

        if (empty($datos['cargo']) || strlen(trim($datos['cargo'])) < 2) {
            $errores[] = "El cargo debe tener al menos 2 caracteres";
        }

        if (empty($datos['distrito'])) {
            $errores[] = "El distrito es requerido";
        }

        if (!empty($datos['correo']) && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo electrónico no es válido";
        }

        return $errores;
    }

    private function procesarFoto($file) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return null;
        }

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir la foto");
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception("Solo se permiten imágenes JPEG, PNG, GIF o WebP");
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception("La foto no puede superar los 5MB");
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'candidato_' . uniqid() . '.' . $extension;

        $directorio = __DIR__ . '/../../img/candidatos/';
        
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $rutaDestino = $directorio . $nombreArchivo;

        if (!move_uploaded_file($file['tmp_name'], $rutaDestino)) {
            throw new Exception("Error al guardar la foto");
        }

        return $nombreArchivo;
    }

    public function guardar($datos, $file = null) {
        try {
            $errores = $this->validarDatos($datos);
            if (!empty($errores)) {
                return [
                    'success' => false,
                    'error' => implode(", ", $errores)
                ];
            }

            $foto = null;
            if ($file !== null) {
                $foto = $this->procesarFoto($file);
            }

            $conexion = (new Conexion())->conectar();
            
            $sql = "INSERT INTO candidatos 
                    (nombre, apellido, id_partido, id_tipo, cargo, distrito, correo, telefono, foto, estatus)
                    VALUES 
                    (:nombre, :apellido, :id_partido, :id_tipo, :cargo, :distrito, :correo, :telefono, :foto, :estatus)";

            $stmt = $conexion->prepare($sql);
            
            $resultado = $stmt->execute([
                ':nombre' => trim($datos['nombre']),
                ':apellido' => trim($datos['apellido']),
                ':id_partido' => (int)$datos['id_partido'],
                ':id_tipo' => (int)$datos['id_tipo'],
                ':cargo' => trim($datos['cargo']),
                ':distrito' => trim($datos['distrito']),
                ':correo' => trim($datos['correo']),
                ':telefono' => trim($datos['telefono']),
                ':foto' => $foto,
                ':estatus' => $datos['estatus'] === 'inactivo' ? 'inactivo' : 'activo'
            ]);

            if ($resultado) {
                return [
                    'success' => true,
                    'message' => 'Candidato guardado correctamente'
                ];
            }

            return [
                'success' => false,
                'error' => 'Error al guardar el candidato'
            ];

        } catch (PDOException $e) {
            error_log("Error en guardar candidato: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error de base de datos'
            ];
        } catch (Exception $e) {
            error_log("Error en guardar candidato: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function obtenerCandidatos() {
        try {
            $conexion = (new Conexion())->conectar();

            $sql = "SELECT c.*, 
                           p.nombre_partido AS partido_nombre, 
                           t.nombre_tipo AS tipo_nombre
                    FROM candidatos c
                    INNER JOIN partidos p ON c.id_partido = p.id_partido
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
                    FROM partidos 
                    WHERE estatus = 1 
                    ORDER BY nombre_partido ASC";

            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerPartidos: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerTiposEleccion() {
        try {
            $conexion = (new Conexion())->conectar();

            $sql = "SELECT id_tipo, nombre_tipo 
                    FROM tipos_eleccion 
                    ORDER BY nombre_tipo ASC";

            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerTiposEleccion: " . $e->getMessage());
            return [];
        }
    }

    public function eliminar($id) {
        try {
            if (empty($id) || !is_numeric($id)) {
                return [
                    'success' => false,
                    'error' => 'ID de candidato inválido'
                ];
            }

            $conexion = (new Conexion())->conectar();

            $sql = "SELECT foto FROM candidatos WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            $candidato = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($candidato && !empty($candidato['foto'])) {
                $rutaFoto = __DIR__ . '/../../img/candidatos/' . $candidato['foto'];
                if (file_exists($rutaFoto)) {
                    unlink($rutaFoto);
                }
            }

            $sql = "DELETE FROM candidatos WHERE id = :id";
            $stmt = $conexion->prepare($sql);

            $resultado = $stmt->execute([':id' => $id]);

            if ($resultado) {
                return [
                    'success' => true,
                    'message' => 'Candidato eliminado correctamente'
                ];
            }

            return [
                'success' => false,
                'error' => 'Error al eliminar el candidato'
            ];

        } catch (PDOException $e) {
            error_log("Error en eliminar candidato: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error de base de datos'
            ];
        } catch (Exception $e) {
            error_log("Error en eliminar candidato: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error al eliminar el candidato'
            ];
        }
    }
}
