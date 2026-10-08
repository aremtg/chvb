<?php
require_once __DIR__ . '/../../includes/formatos_guard.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';

header('Content-Type: application/json; charset=utf-8');
requireFormatosAccess(true);

try {
    $q = trim((string)($_GET['q'] ?? ''));
    $qRespuesta = $q;
    if (mb_strlen($q, 'UTF-8') < 2) {
        echo json_encode(['ok' => true, 'q' => $qRespuesta, 'empleados' => []], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPDO();
    $valor = '%' . $q . '%';
    $stmt = $pdo->prepare(
        "SELECT cedula, nombre, sexo, cargo, tipo_de_personal, es_bombero_integral,
                tipo_de_contrato, fecha_inicio_contrato, fecha_fin_contrato
         FROM empleados
         WHERE cedula LIKE :q_cedula
            OR nombre LIKE :q_nombre
         ORDER BY nombre ASC
         LIMIT 12"
    );
    $stmt->execute([
        'q_cedula' => $valor,
        'q_nombre' => $valor,
    ]);

    $empleados = [];
    foreach ($stmt->fetchAll() as $e) {
        $empleados[] = [
            'cedula' => $e['cedula'],
            'nombre' => $e['nombre'],
            'sexo' => $e['sexo'],
            'cargo' => $e['cargo'],
            'tipo_de_personal' => $e['tipo_de_personal'],
            'es_bombero_integral' => (int)$e['es_bombero_integral'],
            'tipo_de_contrato' => $e['tipo_de_contrato'],
            'fecha_inicio_contrato' => $e['fecha_inicio_contrato'],
            'fecha_fin_contrato' => $e['fecha_fin_contrato'],
        ];
    }

    echo json_encode(['ok' => true, 'q' => $qRespuesta, 'empleados' => $empleados], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No fue posible buscar los empleados.'], JSON_UNESCAPED_UNICODE);
}
