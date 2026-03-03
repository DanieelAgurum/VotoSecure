<?php

class EleccionesMdl
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function crear($nombre, $descripcion, $id_tipo, $fecha_inicio, $fecha_fin)
    {
        $sql = "INSERT INTO elecciones 
                (nombre_eleccion, descripcion_eleccion, id_tipo, fecha_inicio, fecha_fin)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$nombre, $descripcion, $id_tipo, $fecha_inicio, $fecha_fin]);
    }

    public function actualizar($id, $nombre, $descripcion, $id_tipo, $fecha_inicio, $fecha_fin)
    {
        $sql = "UPDATE elecciones 
                SET nombre_eleccion = ?, 
                    descripcion_eleccion = ?, 
                    id_tipo = ?, 
                    fecha_inicio = ?, 
                    fecha_fin = ?
                WHERE id_eleccion = ?";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$nombre, $descripcion, $id_tipo, $fecha_inicio, $fecha_fin, $id]);
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM elecciones WHERE id_eleccion = ?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function cancelar($id)
    {
        $sql = "UPDATE elecciones SET estado = 3 WHERE id_eleccion = ?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM elecciones WHERE id_eleccion = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    public function obtenerTipos()
    {
        $sql = "SELECT * FROM tipos_eleccion";
        return $this->conexion->query($sql);
    }

    public function listar()
    {
        $sql = "SELECT e.*, t.nombre_tipo,
        CASE
            WHEN e.estado = 3 THEN 'Cancelada'
            WHEN NOW() < e.fecha_inicio THEN 'Programada'
            WHEN NOW() BETWEEN e.fecha_inicio AND e.fecha_fin THEN 'Activa'
            WHEN NOW() > e.fecha_fin THEN 'Finalizada'
        END AS estado_calculado
        FROM elecciones e
        INNER JOIN tipos_eleccion t ON e.id_tipo = t.id_tipo
        ORDER BY e.fecha_inicio DESC";

        return $this->conexion->query($sql);
    }

    public function existeTraslape($id_tipo, $fecha_inicio, $fecha_fin, $id_excluir = null)
    {
        $sql = "SELECT 1 
            FROM elecciones 
            WHERE id_tipo = ?
            AND estado != 3
            AND fecha_inicio < ?
            AND fecha_fin > ?";

        if ($id_excluir) {
            $sql .= " AND id_eleccion != ?";
        }

        $stmt = $this->conexion->prepare($sql);

        if ($id_excluir) {
            $stmt->bind_param("issi", $id_tipo, $fecha_fin, $fecha_inicio, $id_excluir);
        } else {
            $stmt->bind_param("iss", $id_tipo, $fecha_fin, $fecha_inicio);
        }

        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }
}
