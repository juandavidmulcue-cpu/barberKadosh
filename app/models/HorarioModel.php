<?php
class HorarioModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    // Obtiene la lista de barberos para el <select>
    public function obtenerBarberos()
    {
        $sql = "SELECT id_usuario, nombre, apellido
            FROM usuarios
            WHERE id_rol = 2
            AND estado = 'activo'
            ORDER BY nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene la lista de horarios realizando INNER JOIN con usuarios para traer el nombre
    public function obtenerHorarios()
    {
        $sql = "SELECT h.id_horario, h.fecha, h.hora_inicio, h.hora_fin, u.nombre, u.apellido 
                FROM horarios h 
                INNER JOIN usuarios u ON h.id_barbero = u.id_usuario 
                ORDER BY h.fecha DESC, h.hora_inicio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserta un día individual
    public function insertarHorario($id_barbero, $fecha, $hora_inicio, $hora_fin)
    {
        // Verificar si el barbero ya tiene horario ese día
        $check = "SELECT id_horario 
              FROM horarios 
              WHERE id_barbero = ? 
              AND fecha = ?";

        $stmtCheck = $this->db->prepare($check);
        $stmtCheck->execute([$id_barbero, $fecha]);

        if ($stmtCheck->rowCount() > 0) {

            // Ya existe: actualizamos las horas
            $sql = "UPDATE horarios
                SET hora_inicio = ?, 
                    hora_fin = ?
                WHERE id_barbero = ?
                AND fecha = ?";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                $hora_inicio,
                $hora_fin,
                $id_barbero,
                $fecha
            ]);
        } else {

            // Generar nuevo ID
            $sqlId = "SELECT id_horario
                  FROM horarios
                  ORDER BY id_horario DESC
                  LIMIT 1";

            $stmtId = $this->db->prepare($sqlId);
            $stmtId->execute();

            $ultimo = $stmtId->fetch(PDO::FETCH_ASSOC);

            if ($ultimo && !empty($ultimo['id_horario'])) {

                // Ejemplo: HOR005 -> 5
                $numero = intval(substr($ultimo['id_horario'], 3));

                $numero++;
            } else {

                $numero = 1;
            }

            // HOR001, HOR002, HOR003...
            $id_horario = 'HOR' . str_pad($numero, 3, '0', STR_PAD_LEFT);


            // Insertar
            $sql = "INSERT INTO horarios
                (
                    id_horario,
                    id_barbero,
                    fecha,
                    hora_inicio,
                    hora_fin
                )
                VALUES (?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                $id_horario,
                $id_barbero,
                $fecha,
                $hora_inicio,
                $hora_fin
            ]);
        }
    }

    // Elimina un horario puntual por su ID
    public function eliminarHorario($id_horario)
    {
        $sql = "DELETE FROM horarios WHERE id_horario = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_horario]);
    }
}
