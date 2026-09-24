<?php
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

$q = trim((string)($_GET['q'] ?? ''));
$qRespuesta = $q;
if ($q === '') {
    echo json_encode(['ok' => true, 'q' => $qRespuesta, 'empleados' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

$pdo = getPDO();

// La búsqueda por cédula solo debe agregarse cuando realmente hay dígitos.
// Antes, una búsqueda como "usu" producía LIKE '%%' y terminaba devolviendo
// empleados que no tenían ninguna relación con el texto buscado.
$cedulaQ = preg_replace('/\D+/', '', $q);
$likeNombre = '%' . $q . '%';

$where = ['nombre LIKE :nombre'];
$params = ['nombre' => $likeNombre];

if ($cedulaQ !== '') {
    $where[] = 'cedula LIKE :cedula';
    $params['cedula'] = '%' . $cedulaQ . '%';
}

$sql = 'SELECT cedula, nombre FROM empleados WHERE ' . implode(' OR ', $where) . ' ORDER BY nombre ASC LIMIT 12';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$empleados = $stmt->fetchAll();

$empleados = array_map(static fn($e) => [
    'cedula' => (string)$e['cedula'],
    'nombre' => (string)$e['nombre'],
], $empleados);

if (!$empleados) {
    echo json_encode([
        'ok' => true,
        'q' => $qRespuesta,
        'empleados' => [],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// La respuesta siempre usa "empleados", incluso cuando solo hay una coincidencia.
// Esto mantiene un único contrato entre PHP y JavaScript.
echo json_encode([
    'ok' => true,
    'q' => $qRespuesta,
    'empleados' => $empleados,
], JSON_UNESCAPED_UNICODE);
