<?php

require_once 'app/config/conexion.php';

class PromocionModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    // ==========================================
    // MÉTODOS PARA EL ADMINISTRADOR
    // ==========================================

    public function obtenerTodas()
    {
        $sql = "SELECT * FROM promociones ORDER BY id_promocion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_promocion)
    {
        $sql = "SELECT * FROM promociones WHERE id_promocion = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_promocion]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombre, $descuento_porcentaje, $fecha_inicio, $fecha_fin)
    {
        $sql = "INSERT INTO promociones (nombre, descuento_porcentaje, fecha_inicio, fecha_fin, estado) 
                VALUES (:nombre, :descuento, :fecha_inicio, :fecha_fin, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':descuento' => $descuento_porcentaje,
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ]);
        return $this->db->lastInsertId();
    }

    public function actualizar($id_promocion, $nombre, $descuento_porcentaje, $fecha_inicio, $fecha_fin)
    {
        $sql = "UPDATE promociones 
                SET nombre = :nombre, descuento_porcentaje = :descuento, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin 
                WHERE id_promocion = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id_promocion,
            ':nombre' => $nombre,
            ':descuento' => $descuento_porcentaje,
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ]);
    }

    public function cambiarEstado($id_promocion)
    {
        // Invierte el valor actual: si es 1 pasa a 0, si es 0 pasa a 1
        $sql = "UPDATE promociones SET estado = IF(estado = 1, 0, 1) WHERE id_promocion = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id_promocion]);
    }

    public function limpiarRelaciones($id_promocion)
    {
        $sqlProd = "DELETE FROM promociones_productos WHERE id_promocion = :id";
        $sqlServ = "DELETE FROM promociones_servicios WHERE id_promocion = :id";

        $stmt1 = $this->db->prepare($sqlProd);
        $stmt1->execute([':id' => $id_promocion]);

        $stmt2 = $this->db->prepare($sqlServ);
        $stmt2->execute([':id' => $id_promocion]);
    }

    public function asociarServicios($id_promocion, array $ids_servicios)
    {
        if (empty($ids_servicios)) return;
        $sql = "INSERT INTO promociones_servicios (id_promocion, id_servicio) VALUES (:id_promocion, :id_servicio)";
        $stmt = $this->db->prepare($sql);
        foreach ($ids_servicios as $id_servicio) {
            $stmt->execute([':id_promocion' => $id_promocion, ':id_servicio' => $id_servicio]);
        }
    }

    public function asociarProductos($id_promocion, array $ids_productos)
    {
        if (empty($ids_productos)) return;
        $sql = "INSERT INTO promociones_productos (id_promocion, id_producto) VALUES (:id_promocion, :id_producto)";
        $stmt = $this->db->prepare($sql);
        foreach ($ids_productos as $id_producto) {
            $stmt->execute([':id_promocion' => $id_promocion, ':id_producto' => $id_producto]);
        }
    }

    // ==========================================
    // MÉTODOS PARA EL AGENDAMIENTO (CLIENTE)
    // ==========================================

    public function obtenerPromocionesServiciosActivas()
    {
        $sql = "SELECT ps.id_servicio, p.descuento_porcentaje, p.nombre AS promo_nombre 
                FROM promociones_servicios ps 
                JOIN promociones p ON ps.id_promocion = p.id_promocion 
                WHERE p.estado = 1 AND CURDATE() BETWEEN p.fecha_inicio AND p.fecha_fin";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $promociones = [];
        foreach ($resultados as $row) {
            $promociones[$row['id_servicio']] = [
                'descuento' => $row['descuento_porcentaje'],
                'nombre' => $row['promo_nombre']
            ];
        }
        return $promociones;
    }

    public function obtenerPromocionesProductosActivas()
    {
        $sql = "SELECT pp.id_producto, p.descuento_porcentaje, p.nombre AS promo_nombre 
                FROM promociones_productos pp 
                JOIN promociones p ON pp.id_promocion = p.id_promocion 
                WHERE p.estado = 1 AND CURDATE() BETWEEN p.fecha_inicio AND p.fecha_fin";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $promociones = [];
        foreach ($resultados as $row) {
            $promociones[$row['id_producto']] = [
                'descuento' => $row['descuento_porcentaje'],
                'nombre' => $row['promo_nombre']
            ];
        }
        return $promociones;
    }

    public function actualizarPromocion($id, $nombre, $descuento, $fecha_inicio, $fecha_fin, $estado)
    {
        $sql = "UPDATE promociones 
            SET nombre = :nombre, 
                descuento_porcentaje = :descuento, 
                fecha_inicio = :fecha_inicio, 
                fecha_fin = :fecha_fin, 
                estado = :estado 
            WHERE id_promocion = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nombre'       => $nombre,
            ':descuento'    => $descuento,
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin'    => $fecha_fin,
            ':estado'       => $estado,
            ':id'           => $id
        ]);
    }
}
