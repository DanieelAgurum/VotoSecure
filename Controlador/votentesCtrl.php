<?php
// Solo iniciar sesión si no hay una sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../Modelo/config/conexion.php";
require_once __DIR__ . "/../Modelo/votantesMdl.php";

class VotantesCtrl
{

    private $modelo;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->modelo = new VotantesMdl();
    }

    /**
     * Obtiene todos los votantes (para cargar la tabla)
     */
    public function obtenerTodos()
    {
        return $this->modelo->obtenerTodos();
    }

    /**
     * Obtiene un votante por ID
     */
    public function obtenerPorId($id)
    {
        return $this->modelo->obtenerPorId($id);
    }

    /**
     * Obtiene un votante por ID via AJAX
     */
    public function obtenerVotanteAjax()
    {
        header('Content-Type: application/json');
        
        $id = $_POST['id'] ?? 0;

        if (empty($id)) {
            echo json_encode([
                'success' => false,
                'message' => 'ID no proporcionado'
            ]);
            return;
        }

        $votante = $this->modelo->obtenerPorId($id);

        if ($votante) {
            echo json_encode([
                'success' => true,
                'votante' => $votante
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Votante no encontrado'
            ]);
        }
    }

    /**
     * Actualiza la huella digital via AJAX
     */
    public function actualizarHuellaAjax()
    {
        header('Content-Type: application/json');
        
        $id = $_POST['id'] ?? 0;
        $finger_id = $_POST['finger_id'] ?? '';

        if (empty($id) || empty($finger_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos incompletos'
            ]);
            return;
        }

        $resultado = $this->modelo->actualizarHuella($id, $finger_id);

        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Huella actualizada correctamente',
                'finger_id' => $finger_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar la huella'
            ]);
        }
    }

    /**
     * Actualiza los datos NFC via AJAX
     */
    public function actualizarNFCAjax()
    {
        header('Content-Type: application/json');
        
        $id = $_POST['id'] ?? 0;
        $uid_nfc = $_POST['uid_nfc'] ?? '';
        $token_nfc = $_POST['token_nfc'] ?? '';

        if (empty($id) || empty($uid_nfc)) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos incompletos'
            ]);
            return;
        }

        $resultado = $this->modelo->actualizarNFC($id, $uid_nfc, $token_nfc);

        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'NFC actualizado correctamente',
                'uid_nfc' => $uid_nfc
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar el NFC'
            ]);
        }
    }

    /**
     * Actualiza los datos generales via AJAX
     */
    public function actualizarDatosAjax()
    {
        header('Content-Type: application/json');
        
        $id = $_POST['id'] ?? 0;
        
        $datos = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellido_paterno' => $_POST['apellido_paterno'] ?? '',
            'apellido_materno' => $_POST['apellido_materno'] ?? '',
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
            'genero' => $_POST['genero'] ?? 'H',
            'nacionalidad' => $_POST['nacionalidad'] ?? 'Mexicana',
            'curp' => $_POST['curp'] ?? '',
            'rfc' => $_POST['rfc'] ?? '',
            'calle' => $_POST['calle'] ?? '',
            'num_exterior' => $_POST['num_exterior'] ?? '',
            'num_interior' => $_POST['num_interior'] ?? '',
            'colonia' => $_POST['colonia'] ?? '',
            'codigo_postal' => $_POST['codigo_postal'] ?? '',
            'municipio' => $_POST['municipio'] ?? '',
            'entidad' => $_POST['entidad'] ?? '',
            'entre_calles' => $_POST['entre_calles'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'telefono_fijo' => $_POST['telefono_fijo'] ?? '',
            'seccion_electoral' => $_POST['seccion_electoral'] ?? '',
            'clave_elector' => $_POST['clave_elector'] ?? ''
        ];

        // Validar campos obligatorios (excluyendo num_interior, entre_calles y telefono_fijo)
        $camposRequeridos = ['nombre', 'apellido_paterno', 'fecha_nacimiento', 'genero', 
                            'nacionalidad', 'curp', 'rfc', 'calle', 'num_exterior', 
                            'colonia', 'municipio', 'entidad', 'codigo_postal', 
                            'correo', 'telefono', 'seccion_electoral'];
        
        foreach ($camposRequeridos as $campo) {
            if (empty($datos[$campo])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Todos los campos son obligatorios (excepto Entre Calles y Teléfono Fijo)'
                ]);
                return;
            }
        }
        
        // Validar longitud de CURP (18 caracteres)
        if (strlen($datos['curp']) !== 18) {
            echo json_encode([
                'success' => false,
                'message' => 'El CURP debe tener exactamente 18 caracteres'
            ]);
            return;
        }
        
        // Validar longitud de RFC (13 caracteres)
        if (strlen($datos['rfc']) !== 13) {
            echo json_encode([
                'success' => false,
                'message' => 'El RFC debe tener exactamente 13 caracteres'
            ]);
            return;
        }

        // Validar CURP único
        if ($this->modelo->existeCurp($datos['curp'], $id)) {
            echo json_encode([
                'success' => false,
                'message' => 'El CURP ya está registrado en otro votante'
            ]);
            return;
        }

        // Validar RFC único
        if ($this->modelo->existeRfc($datos['rfc'], $id)) {
            echo json_encode([
                'success' => false,
                'message' => 'El RFC ya está registrado en otro votante'
            ]);
            return;
        }

        $resultado = $this->modelo->actualizarDatos($id, $datos);

        if ($resultado) {
            // Obtener el votante actualizado
            $votanteActualizado = $this->modelo->obtenerPorId($id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Datos actualizados correctamente',
                'votante' => $votanteActualizado
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar los datos'
            ]);
        }
    }

    /**
     * Cambia el estado del votante
     */
    public function cambiarEstado()
    {
        $id = $_POST['id'] ?? 0;
        $estado = $_POST['estado'] ?? 'activo';

        if (empty($id)) {
            $_SESSION["errores"] = ["ID de votante no válido"];
            header("Location: ../Vista/admin/Votantes.php");
            exit;
        }

        if ($this->modelo->cambiarEstado($id, $estado)) {
            $_SESSION["success"] = "Estado actualizado correctamente.";
        } else {
            $_SESSION["errores"] = ["Error al actualizar el estado."];
        }

        header("Location: ../Vista/admin/Votantes.php");
        exit;
    }
}

// Inicializar controlador
$controlador = new VotantesCtrl();

// Verificar si es una petición AJAX
$esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($esAjax) {
    // Peticiones AJAX
    if (isset($_POST['accion_ajax'])) {
        switch ($_POST['accion_ajax']) {
            case 'obtener_votante':
                $controlador->obtenerVotanteAjax();
                break;
            case 'actualizar_huella':
                $controlador->actualizarHuellaAjax();
                break;
            case 'actualizar_nfc':
                $controlador->actualizarNFCAjax();
                break;
            case 'actualizar_datos':
                $controlador->actualizarDatosAjax();
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        }
    }
    exit;
}

// Peticiones normales (POST)
if (isset($_POST["accion"])) {
    switch ($_POST["accion"]) {
        case "cambiarEstado":
            $controlador->cambiarEstado();
            break;
        default:
            header("Location: ../Vista/admin/Votantes.php");
            exit;
    }
}

