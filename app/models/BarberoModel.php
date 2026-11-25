<?php

require_once __DIR__ . '/../config/conexion.php';

class BarberoModel
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::conectar();
    }

    /**
     * Obtiene todos los barberos activos.
     */
    public function obtenerBarberosDisponibles()
    {
        $sql = "SELECT
                    id_usuario,
                    nombre,
                    apellido,
                    foto
                FROM usuarios
                WHERE id_rol = 2
                  AND estado = 'activo'
                ORDER BY nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un barbero por su ID.
     */
    public function obtenerPorId($id)
    {
        $sql = "SELECT
                    id_usuario,
                    nombre,
                    apellido,
                    telefono,
                    correo
                FROM usuarios
                WHERE id_usuario = :id
                  AND id_rol = 2";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los 2 barberos con mejor promedio de calificaciones,
     * total de reseñas y su reseña más reciente.
     */
    public function obtenerTopBarberos(int $limite = 2): array
    {
        $sql = "
            SELECT
                u.id_usuario AS barbero_id,
                CONCAT(u.nombre, ' ', u.apellido) AS nombre_barbero,
                u.foto AS foto_barbero,
                ROUND(AVG(r.calificacion), 1) AS promedio_estrellas,
                COUNT(r.id_resena) AS total_resenas,
                (
                    SELECT r2.comentario
                    FROM resenas r2
                    WHERE r2.id_barbero COLLATE utf8mb4_general_ci
                    = u.id_usuario COLLATE utf8mb4_general_ci
                      AND r2.comentario IS NOT NULL
                      AND TRIM(r2.comentario) <> ''
                    ORDER BY r2.fecha DESC, r2.id_resena DESC
                    LIMIT 1
                ) AS ultima_resena,
                (
                    SELECT CONCAT(c.nombre, ' ', c.apellido)
                    FROM resenas r2
                    INNER JOIN usuarios c ON c.id_usuario = r2.id_cliente
                    WHERE r2.id_barbero COLLATE utf8mb4_general_ci
                    = u.id_usuario COLLATE utf8mb4_general_ci
                      AND r2.comentario IS NOT NULL
                      AND TRIM(r2.comentario) <> ''
                    ORDER BY r2.fecha DESC, r2.id_resena DESC
                    LIMIT 1
                ) AS nombre_cliente_resena
            FROM usuarios u
            INNER JOIN resenas r
            ON r.id_barbero COLLATE utf8mb4_general_ci
            = u.id_usuario COLLATE utf8mb4_general_ci
            WHERE u.id_rol = 2
              AND u.estado = 'activo'
            GROUP BY u.id_usuario, u.nombre, u.apellido, u.foto
            HAVING COUNT(r.id_resena) > 0
            ORDER BY promedio_estrellas DESC, total_resenas DESC, u.id_usuario ASC
            LIMIT :limite
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        $barberos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($barberos as &$barbero) {
            $promedio = (float) ($barbero['promedio_estrellas'] ?? 0);
            $barbero['promedio_estrellas'] = number_format($promedio, 1, '.', '');
            $barbero['estrellas_html'] = $this->renderEstrellas($promedio);
            $barbero['total_resenas'] = (int) ($barbero['total_resenas'] ?? 0);
            $barbero['foto_url'] = $this->resolverFoto($barbero['foto_barbero'] ?? '');
        }
        unset($barbero);

        return $barberos;
    }

    /**
     * Genera 5 estrellas visuales según el promedio (ej. 4.8 -> 5 llenas o redondeo).
     */
    public function renderEstrellas(float $promedio): string
    {
        $llenas = (int) round($promedio);
        $llenas = max(0, min(5, $llenas));
        $vacias = 5 - $llenas;

        return str_repeat('★', $llenas) . str_repeat('☆', $vacias);
    }

    private function resolverFoto(?string $foto): string
    {
        if (!empty($foto)) {
            $ruta = ltrim(trim($foto), '/');

            // Si en BD ya empieza con app/public/... (ej: app/public/uploads/foto.jpg), la dejamos tal cual
            if (strpos($ruta, 'app/public/') === 0) {
                return htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8');
            }

            // Si empieza con uploads/... (ej: uploads/default.png), le anteponemos app/public/
            return htmlspecialchars('app/public/' . $ruta, ENT_QUOTES, 'UTF-8');
        }

        return 'app/public/assets/img/logo1.jpeg';
    }

    /**
     * Obtiene el promedio y total de reseñas de un barbero.
     */
    public function obtenerResumenResenasBarbero($idBarbero): array
    {
        $sql = "
        SELECT
            ROUND(AVG(calificacion), 1) AS promedio,
            COUNT(id_resena) AS total
        FROM resenas
        WHERE id_barbero = :id_barbero
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_barbero' => $idBarbero
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'promedio' => (float) ($resultado['promedio'] ?? 0),
            'total' => (int) ($resultado['total'] ?? 0)
        ];
    }


    /**
     * Obtiene TODAS las reseñas de un barbero.
     */
    public function obtenerTodasLasResenas($idBarbero): array
    {
        $sql = "
        SELECT
            r.id_resena,
            r.calificacion,
            r.comentario,
            r.fecha,
            CONCAT(c.nombre, ' ', c.apellido) AS nombre_cliente
        FROM resenas r
        LEFT JOIN usuarios c
            ON c.id_usuario COLLATE utf8mb4_general_ci
            = r.id_cliente COLLATE utf8mb4_general_ci
        WHERE r.id_barbero = :id_barbero
        ORDER BY r.fecha DESC, r.id_resena DESC
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_barbero' => $idBarbero
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
