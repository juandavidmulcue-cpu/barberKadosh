
<?php

require_once __DIR__ . '/../models/BarberoModel.php';

class ResenaController
{
    private BarberoModel $barberoModel;

    public function __construct()
    {
        $this->barberoModel = new BarberoModel();
    }

    /**
     * Consulta el promedio y todas las reseñas de un barbero.
     */
    public function obtenerResenasBarbero()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id'])) {
            http_response_code(401);

            echo json_encode([
                'success' => false,
                'mensaje' => 'Debes iniciar sesión.'
            ]);

            exit;
        }

        $idBarbero = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$idBarbero || $idBarbero <= 0) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'mensaje' => 'Identificador de barbero inválido.'
            ]);

            exit;
        }

        try {
            // Consultar información del barbero
            $barbero = $this->barberoModel->obtenerPorId($idBarbero);

            if (!$barbero) {
                http_response_code(404);

                echo json_encode([
                    'success' => false,
                    'mensaje' => 'No se encontró el barbero.'
                ]);

                exit;
            }

            // Consultar promedio y total de reseñas
            $resumen = $this->barberoModel
                ->obtenerResumenResenasBarbero($idBarbero);

            // Consultar todas las reseñas
            $resenas = $this->barberoModel
                ->obtenerTodasLasResenas($idBarbero);

            echo json_encode([
                'success' => true,
                'barbero' => [
                    'nombre' => trim($barbero['nombre'] . ' ' . $barbero['apellido']),
                    'foto' => !empty($barbero['foto'])
                        ? $barbero['foto']
                        : 'app/public/assets/img/avatar.png',
                    'promedio' => $resumen['promedio'],
                    'total' => $resumen['total']
                ],
                'resenas' => $resenas
            ], JSON_UNESCAPED_UNICODE);

            exit;

        } catch (Throwable $e) {
            error_log('Error al consultar reseñas: ' . $e->getMessage());

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'mensaje' => 'Ocurrió un error al consultar las reseñas.'
            ]);

            exit;
        }
    }
}
