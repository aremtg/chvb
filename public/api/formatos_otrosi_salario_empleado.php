<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';

header('Content-Type: application/json; charset=utf-8');
requireSuperAdmin();

$rolesFormatos = ['superadmin_talento_humano', 'auxiliar_talento_humano'];
if (!in_array($_SESSION['superadmin_rol'] ?? '', $rolesFormatos, true)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No tienes permiso para usar Formatos.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $q = trim((string)($_GET['q'] ?? ''));
    if (mb_strlen($q, 'UTF-8') < 2) {
        echo json_encode(['ok' => true, 'empleados' => []], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPDO();
    $valor = '%' . $q . '%';
    $stmt = $pdo->prepare(
        "SELECT cedula, nombre, fecha_inicio_contrato
         FROM empleados
         WHERE cedula LIKE :cedula
            OR nombre LIKE :nombre
         ORDER BY nombre ASC
         LIMIT 12"
    );
    $stmt->execute(['cedula' => $valor, 'nombre' => $valor]);

    $empleados = [];
    foreach ($stmt->fetchAll() as $e) {
        $empleados[] = [
            'cedula' => (string)$e['cedula'],
            'nombre' => (string)$e['nombre'],
            'fecha_inicio_contrato' => $e['fecha_inicio_contrato'] ? (string)$e['fecha_inicio_contrato'] : null,
        ];
    }

    echo json_encode(['ok' => true, 'empleados' => $empleados, 'q' => $q], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No fue posible buscar los empleados.'], JSON_UNESCAPED_UNICODE);
}
