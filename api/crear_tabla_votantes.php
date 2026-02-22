<?php
/**
 * Script para crear la tabla de votantes
 * Ejecutar una sola vez: php crear_tabla_votantes.php
 */

require "conexion.php";

try {
    // Crear la tabla de votantes
    $sql = "
    CREATE TABLE IF NOT EXISTS voters (
        id INT AUTO_INCREMENT PRIMARY KEY,
        
        -- Datos Personales
        nombre VARCHAR(100) NOT NULL,
        apellido_paterno VARCHAR(100) NOT NULL,
        apellido_materno VARCHAR(100) DEFAULT NULL,
        fecha_nacimiento DATE NOT NULL,
        genero ENUM('M', 'F', 'O') NOT NULL,
        nacionalidad VARCHAR(50) DEFAULT 'Mexicana',
        
        -- Identificadores Oficiales
        curp VARCHAR(18) NOT NULL UNIQUE,
        rfc VARCHAR(13) NOT NULL,
        
        -- Domicilio
        calle VARCHAR(200) NOT NULL,
        num_exterior VARCHAR(20) NOT NULL,
        num_interior VARCHAR(20) DEFAULT NULL,
        colonia VARCHAR(200) NOT NULL,
        codigo_postal VARCHAR(5) NOT NULL,
        municipio VARCHAR(200) NOT NULL,
        entidad VARCHAR(50) NOT NULL,
        entre_calles VARCHAR(200) DEFAULT NULL,
        
        -- Información de Contacto
        correo VARCHAR(255) NOT NULL,
        telefono VARCHAR(20) NOT NULL,
        telefono_fijo VARCHAR(20) DEFAULT NULL,
        
        -- Información Electoral
        seccion_electoral VARCHAR(4) NOT NULL,
        clave_elector VARCHAR(18) DEFAULT NULL,
        
        -- Datos del ESP32 (NFC + Huella)
        uid_nfc VARCHAR(50) DEFAULT NULL,
        token_nfc VARCHAR(100) DEFAULT NULL,
        finger_id INT DEFAULT NULL,
        
        -- Foto (ruta o datos base64)
        foto TEXT DEFAULT NULL,
        
        -- Estado y timestamps
        estado ENUM('activo', 'inactivo', 'votado') DEFAULT 'activo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        -- Índices para búsquedas rápidas
        INDEX idx_curp (curp),
        INDEX idx_rfc (rfc),
        INDEX idx_uid_nfc (uid_nfc),
        INDEX idx_finger_id (finger_id),
        INDEX idx_correo (correo),
        INDEX idx_seccion (seccion_electoral)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($sql);
    echo "✅ Tabla 'voters' creada correctamente\n";
    
    // Verificar que se creó
    $stmt = $pdo->query("SHOW TABLES LIKE 'voters'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla verificada\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

