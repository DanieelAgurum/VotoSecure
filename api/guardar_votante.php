<?php

/**
 * API para guardar votante
 * Método: POST
 * 
 * Recibe datos del formulario y datos del ESP32 (NFC + Huella)
 */

header('Content-Type: application/json');

require "conexion.php";
require "crypto.php";

// ================== ENTRADAS ==================

// Datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$apellido_paterno = trim($_POST['apellido_paterno'] ?? '');
$apellido_materno = trim($_POST['apellido_materno'] ?? '');
$fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
$genero = trim($_POST['genero'] ?? '');
$nacionalidad = trim($_POST['nacionalidad'] ?? 'Mexicana');

// Identificadores oficiales
$curp = strtoupper(trim($_POST['curp'] ?? ''));
$rfc = strtoupper(trim($_POST['rfc'] ?? ''));

// Domicilio
$calle = trim($_POST['calle'] ?? '');
$num_exterior = trim($_POST['num_exterior'] ?? '');
$num_interior = trim($_POST['num_interior'] ?? '');
$colonia = trim($_POST['colonia'] ?? '');
$codigo_postal = trim($_POST['codigo_postal'] ?? '');
$municipio = trim($_POST['municipio'] ?? '');
$entidad = trim($_POST['entidad'] ?? '');
$entre_calles = trim($_POST['entre_calles'] ?? '');

// Contacto
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$telefono_fijo = trim($_POST['telefono_fijo'] ?? '');

// Información electoral
$seccion_electoral = trim($_POST['seccion'] ?? '');
$clave_elector = strtoupper(trim($_POST['clave_elector'] ?? ''));

// Datos del ESP32 (NFC + Huella)
$uid = trim($_POST['uid'] ?? '');
$token = trim($_POST['token'] ?? '');
$finger_id = trim($_POST['finger_id'] ?? '');

// Debug: registrar lo que se recibe
error_log("=== DEBUG guardar_votante.php ===");
error_log("UID recibido: " . ($uid ? "SI [" . strlen($uid) . " chars]" : "VACIO"));
error_log("Token recibido: " . ($token ? "SI [" . strlen($token) . " chars]" : "VACIO"));
error_log("Finger ID recibido: " . ($finger_id ? "SI [" . strlen($finger_id) . " chars]" : "VACIO"));
error_log("=====================================");

// Foto (base64)
$foto = $_POST['foto'] ?? '';

// ================== VALIDACIÓN ==================

$errores = [];

if (empty($nombre)) {
    $errores[] = "El nombre es requerido";
}
if (empty($apellido_paterno)) {
    $errores[] = "El apellido paterno es requerido";
}
if (empty($fecha_nacimiento)) {
    $errores[] = "La fecha de nacimiento es requerida";
}
if (empty($genero)) {
    $errores[] = "El género es requerido";
}
if (empty($curp) || strlen($curp) !== 18) {
    $errores[] = "La CURP debe tener 18 caracteres";
}
if (empty($rfc) || strlen($rfc) < 10 || strlen($rfc) > 13) {
    $errores[] = "El RFC debe tener entre 10 y 13 caracteres";
}
if (empty($calle)) {
    $errores[] = "La calle es requerida";
}
if (empty($num_exterior)) {
    $errores[] = "El número exterior es requerido";
}
if (empty($colonia)) {
    $errores[] = "La colonia es requerida";
}
if (empty($codigo_postal)) {
    $errores[] = "El código postal es requerido";
}
if (empty($municipio)) {
    $errores[] = "El municipio es requerido";
}
if (empty($entidad)) {
    $errores[] = "La entidad federativa es requerida";
}
if (empty($correo)) {
    $errores[] = "El correo electrónico es requerido";
}
if (empty($telefono)) {
    $errores[] = "El teléfono celular es requerido";
}
if (empty($seccion_electoral)) {
    $errores[] = "La sección electoral es requerida";
}

// Validar CURP formato
if (!preg_match('/^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]{2}$/', $curp)) {
    $errores[] = "El formato de CURP no es válido";
}

// Validar RFC formato
if (!preg_match('/^[A-Z]{4}\d{6}[A-Z0-9]{3}$/', $rfc)) {
    $errores[] = "El formato de RFC no es válido";
}

if (!empty($errores)) {
    echo json_encode([
        "status" => "ERROR",
        "message" => "Datos inválidos",
        "errors" => $errores
    ]);
    exit;
}

// ================== GUARDAR EN BASE DE DATOS ==================

try {
    // ================== VALIDACIÓN DE DUPLICADOS ==================
    // Verificar si CURP, correo o RFC ya existen
    $stmt = $pdo->prepare("SELECT id, curp, rfc, correo FROM votantes WHERE curp = ? OR rfc = ? OR correo = ?");
    $stmt->execute([$curp, $rfc, $correo]);
    $duplicado = $stmt->fetch();

    if ($duplicado) {
        if (!empty($finger_id)) {
            // Esto depende de tu conexión: ejemplo si lo mandas vía frontend:
            // echo json_encode(["command" => "DELETE_FINGER:" . $finger_id]);
            // O tu sistema debe mandar DELETE_FINGER:{finger_id} al ESP32
        }

        echo json_encode([
            "status" => "ERROR",
            "message" => "Ya existe un votante registrado con esta información",
            "duplicado" => [
                "curp" => $duplicado['curp'],
                "rfc" => $duplicado['rfc'],
                "correo" => $duplicado['correo']
            ]
        ]);
        exit;
    }

    if ($stmt->fetch()) {
        echo json_encode([
            "status" => "ERROR",
            "message" => "Ya existe un votante registrado con esta CURP"
        ]);
        exit;
    }

    // Preparar datos para guardar
    // Ciframos los datos sensibles: UID NFC, Token NFC y Finger ID
    $uid_nfc = null;
    $token_nfc = null;
    $finger_id_cifrado = null;

    if (!empty($uid)) {
        $uid_nfc = encrypt_data($uid);  // Ciframos el UID
    }

    if (!empty($token)) {
        $token_nfc = encrypt_data($token);  // Ciframos el token
    }

    if (!empty($finger_id)) {
        $finger_id_cifrado = encrypt_data($finger_id);  // Ciframos el finger_id
    }

    // Procesar foto (guardar como texto/base64 o null)
    $foto_guardar = null;
    if (!empty($foto) && strpos($foto, 'data:image') === 0) {
        // La foto ya viene en base64 desde el cliente
        // Validar tamaño máximo (500KB en base64 = ~666KB raw)
        $fotoSize = strlen($foto);
        if ($fotoSize > 666000) {
            echo json_encode([
                "status" => "ERROR",
                "message" => "La imagen es muy grande. Máximo 500KB permitidos."
            ]);
            exit;
        }
        $foto_guardar = $foto;
    }

    // Insertar voter
    $sql = "
    INSERT INTO votantes (
        nombre, apellido_paterno, apellido_materno, fecha_nacimiento,
        genero, nacionalidad, curp, rfc, calle, num_exterior, num_interior,
        colonia, codigo_postal, municipio, entidad, entre_calles,
        correo, telefono, telefono_fijo, seccion_electoral, clave_elector,
        uid_nfc, token_nfc, finger_id, foto, estado
    ) VALUES (
        :nombre, :apellido_paterno, :apellido_materno, :fecha_nacimiento,
        :genero, :nacionalidad, :curp, :rfc, :calle, :num_exterior, :num_interior,
        :colonia, :codigo_postal, :municipio, :entidad, :entre_calles,
        :correo, :telefono, :telefono_fijo, :seccion_electoral, :clave_elector,
        :uid_nfc, :token_nfc, :finger_id, :foto, 'activo'
    )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nombre' => $nombre,
        ':apellido_paterno' => $apellido_paterno,
        ':apellido_materno' => $apellido_materno ?: null,
        ':fecha_nacimiento' => $fecha_nacimiento,
        ':genero' => $genero,
        ':nacionalidad' => $nacionalidad,
        ':curp' => $curp,
        ':rfc' => $rfc,
        ':calle' => $calle,
        ':num_exterior' => $num_exterior,
        ':num_interior' => $num_interior ?: null,
        ':colonia' => $colonia,
        ':codigo_postal' => $codigo_postal,
        ':municipio' => $municipio,
        ':entidad' => $entidad,
        ':entre_calles' => $entre_calles ?: null,
        ':correo' => $correo,
        ':telefono' => $telefono,
        ':telefono_fijo' => $telefono_fijo ?: null,
        ':seccion_electoral' => $seccion_electoral,
        ':clave_elector' => $clave_elector ?: null,
        ':uid_nfc' => $uid_nfc,
        ':token_nfc' => $token_nfc,
        ':finger_id' => $finger_id_cifrado,
        ':foto' => $foto_guardar
    ]);

    $voter_id = $pdo->lastInsertId();

    echo json_encode([
        "status" => "OK",
        "message" => "Votante registrado correctamente",
        "data" => [
            "id" => $voter_id,
            "nombre" => $nombre . " " . $apellido_paterno,
            "curp" => $curp
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "ERROR",
        "message" => "Error al guardar en la base de datos",
        "error" => $e->getMessage()
    ]);
}
