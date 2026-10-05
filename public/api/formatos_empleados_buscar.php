<?php
declare(strict_types=1);
// Endpoint ÚNICO de búsqueda de empleados para todos los formatos.
// Busca por nombre y/o cédula. Varias palabras = todas deben coincidir
// ("juan dominguez" encuentra "Juan Fernando Dominguez Ibarguen").
require_once __DIR__ . '/../../includes/formatos_guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/helpers/JornadaHelper.php';

header('Content-Type: application/json; charset=utf-8');
requireFormatosAccess(true);

const MIN_CARACTERES = 2;
const MAX_RESULTADOS = 15;

try {
    $q = trim(preg_replace('/\s+/u', ' ', (string)($_GET['q'] ?? '')));

    if (mb_strlen($q, 'UTF-8') < MIN_CARACTERES) {
        echo json_encode(['ok' => true, 'q' => $q, 'empleados' => []], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Cada palabra debe aparecer en el nombre o en la cédula (AND entre palabras).
    $tokens = array_slice(explode(' ', $q), 0, 5);
    $where = [];
    $params = [];
    foreach ($tokens as $i => $t) {
        // Escapa comodines LIKE para que "%" o "_" escritos por el usuario no rompan la búsqueda.
        $like = '%' . addcslashes($t, '\\%_') . '%';
        $where[] = "(nombre LIKE :n$i OR cedula LIKE :c$i)";
        $params["n$i"] = $like;
        $params["c$i"] = $like;
    }
    $params['exacta']  = $q;
    $params['prefijo'] = addcslashes($q, '\\%_') . '%';

    $sql = 'SELECT cedula, nombre, sexo, cargo, tipo_de_personal, es_bombero_integral,
                   tipo_de_contrato, fecha_inicio_contrato, fecha_fin_contrato, estado
            FROM empleados
            WHERE ' . implode(' AND ', $where) . '
            ORDER BY (cedula = :exacta) DESC, (cedula LIKE :prefijo) DESC, nombre ASC
            LIMIT ' . MAX_RESULTADOS;

    $stmt = getPDO()->prepare($sql);
    $stmt->execute($params);

    $empleados = array_map(static fn(array $e): array => [
        'cedula'                => (string)$e['cedula'],
        'nombre'                => (string)$e['nombre'],
        'sexo'                  => $e['sexo'],
        'cargo'                 => $e['cargo'],
        'cargo_detalle'         => JornadaHelper::cargoDetalle($e),
        'tipo_de_personal'      => $e['tipo_de_personal'],
        'es_bombero_integral'   => (int)$e['es_bombero_integral'],
        'tipo_de_contrato'      => $e['tipo_de_contrato'],
        'fecha_inicio_contrato' => $e['fecha_inicio_contrato'],
        'fecha_fin_contrato'    => $e['fecha_fin_contrato'],
        'estado'                => $e['estado'],
    ], $stmt->fetchAll());

    echo json_encode(['ok' => true, 'q' => $q, 'empleados' => $empleados], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('formatos_empleados_buscar: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No fue posible buscar los empleados.'], JSON_UNESCAPED_UNICODE);
}
