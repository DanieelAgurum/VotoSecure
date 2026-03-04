
<?php

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/../api/crypto.php';

class VotantesMdl
{
    private $conexion;
    private $tabla = 'votantes';

    public function __construct()
    {
        $con = new Conexion();
        $this->conexion = $con->conectar();
    }

    /**
     * Obtiene todos los votantes con los campos desencriptados
     */
    public function obtenerTodos()
    {
        $query = "SELECT * FROM {$this->tabla} ORDER BY id DESC";
        $stmt = $this->conexion->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $votantes = [];
        foreach ($rows as $row) {
            // Desencriptar campos sensibles
            $row['uid_nfc'] = !empty($row['uid_nfc']) ? decrypt_data($row['uid_nfc']) : '';
            $row['token_nfc'] = !empty($row['token_nfc']) ? decrypt_data($row['token_nfc']) : '';
            $row['finger_id'] = !empty($row['finger_id']) ? decrypt_data($row['finger_id']) : '';
            
            // Crear dirección completa
            $row['direccion_completa'] = $this->formatearDireccion($row);
            
            $votantes[] = $row;
        }
        return $votantes;
    }

    /**
     * Obtiene un votante por ID
     */
    public function obtenerPorId($id)
    {
        $query = "SELECT * FROM {$this->tabla} WHERE id = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Desencriptar campos sensibles
            $row['uid_nfc'] = !empty($row['uid_nfc']) ? decrypt_data($row['uid_nfc']) : '';
            $row['token_nfc'] = !empty($row['token_nfc']) ? decrypt_data($row['token_nfc']) : '';
            $row['finger_id'] = !empty($row['finger_id']) ? decrypt_data($row['finger_id']) : '';
            $row['direccion_completa'] = $this->formatearDireccion($row);
        }

        return $row;
    }

    /**
     * Actualiza la huella digital del votante
     */
    public function actualizarHuella($id, $finger_id)
    {
        $finger_id_encriptado = encrypt_data($finger_id);
        
        $query = "UPDATE {$this->tabla} SET finger_id = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($query);
        return $stmt->execute([$finger_id_encriptado, $id]);
    }

    /**
     * Actualiza los datos NFC del votante
     */
    public function actualizarNFC($id, $uid_nfc, $token_nfc)
    {
        $uid_nfc_encriptado = encrypt_data($uid_nfc);
        $token_nfc_encriptado = encrypt_data($token_nfc);
        
        $query = "UPDATE {$this->tabla} SET uid_nfc = ?, token_nfc = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($query);
        return $stmt->execute([$uid_nfc_encriptado, $token_nfc_encriptado, $id]);
    }

    /**
     * Actualiza los datos generales del votante
     */
    public function actualizarDatos($id, $datos)
    {
        $query = "UPDATE {$this->tabla} SET 
            nombre = ?,
            apellido_paterno = ?,
            apellido_materno = ?,
            fecha_nacimiento = ?,
            genero = ?,
            nacionalidad = ?,
            curp = ?,
            rfc = ?,
            calle = ?,
            num_exterior = ?,
            num_interior = ?,
            colonia = ?,
            codigo_postal = ?,
            municipio = ?,
            entidad = ?,
            entre_calles = ?,
            correo = ?,
            telefono = ?,
            telefono_fijo = ?,
            seccion_electoral = ?,
            clave_elector = ?
            WHERE id = ?";

        $stmt = $this->conexion->prepare($query);
        return $stmt->execute([
            $datos['nombre'],
            $datos['apellido_paterno'],
            $datos['apellido_materno'],
            $datos['fecha_nacimiento'],
            $datos['genero'],
            $datos['nacionalidad'],
            $datos['curp'],
            $datos['rfc'],
            $datos['calle'],
            $datos['num_exterior'],
            $datos['num_interior'],
            $datos['colonia'],
            $datos['codigo_postal'],
            $datos['municipio'],
            $datos['entidad'],
            $datos['entre_calles'],
            $datos['correo'],
            $datos['telefono'],
            $datos['telefono_fijo'],
            $datos['seccion_electoral'],
            $datos['clave_elector'],
            $id
        ]);
    }

    /**
     * Cambia el estado del votante
     */
    public function cambiarEstado($id, $estado)
    {
        $query = "UPDATE {$this->tabla} SET estado = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($query);
        return $stmt->execute([$estado, $id]);
    }

    /**
     * Verifica si existe un CURP
     */
    public function existeCurp($curp, $idExcluir = null)
    {
        if ($idExcluir) {
            $query = "SELECT id FROM {$this->tabla} WHERE curp = ? AND id != ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([$curp, $idExcluir]);
        } else {
            $query = "SELECT id FROM {$this->tabla} WHERE curp = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([$curp]);
        }

        return $stmt->fetch() !== false;
    }

    /**
     * Verifica si existe un RFC
     */
    public function existeRfc($rfc, $idExcluir = null)
    {
        if ($idExcluir) {
            $query = "SELECT id FROM {$this->tabla} WHERE rfc = ? AND id != ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([$rfc, $idExcluir]);
        } else {
            $query = "SELECT id FROM {$this->tabla} WHERE rfc = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([$rfc]);
        }

        return $stmt->fetch() !== false;
    }

    /**
     * Formatea la dirección completa
     */
    private function formatearDireccion($row)
    {
        $partes = [];
        
        if (!empty($row['calle'])) {
            $partes[] = $row['calle'];
        }
        if (!empty($row['num_exterior'])) {
            $partes[] = '#' . $row['num_exterior'];
        }
        if (!empty($row['num_interior'])) {
            $partes[] = 'Int. ' . $row['num_interior'];
        }
        if (!empty($row['colonia'])) {
            $partes[] = $row['colonia'];
        }
        if (!empty($row['municipio'])) {
            $partes[] = $row['municipio'];
        }
        if (!empty($row['entidad'])) {
            $partes[] = $row['entidad'];
        }
        if (!empty($row['codigo_postal'])) {
            $partes[] = 'CP ' . $row['codigo_postal'];
        }

        return implode(', ', $partes);
    }
}