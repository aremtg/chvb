<?php
// public/api/empleado_bolsillo_obtener.php
// Devuelve un bolsillo del empleado en sesión (con sus documentos) para repintar el modal sin recargar la página.
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/BolsilloModel.php';
require_once __DIR__ . '/../../src/models/DocumentoModel.php';

header('Content-Type: application/json');
requireEmpleado();

$cedula = $_SESSION['empleado_cedula']; // nunca confiar en un valor enviado por el cliente
$id = (int) ($_GET['id'] ?? 0);

// Solo se aceptan bolsillos que pertenezcan a ESTE empleado.
$encontrado = null;
foreach (BolsilloModel::listarPorEmpleado($cedula) as $b) {
    if ((int) $b['id'] === $id) {
        $encontrado = $b;
        break;
    }
}

if (!$encontrado) {
    echo json_encode(['ok' => false, 'error' => 'Bolsillo no encontrado.']);
    exit;
}

$documentos = [];
foreach (DocumentoModel::listarPorBolsillo($id) as $d) {
    $documentos[] = [
        'id'                      => (int) $d['id'],
        'nombre_archivo'          => (string) $d['nombre_archivo'],
        'puede_eliminar_empleado' => DocumentoModel::puedeEliminarEmpleado($d, $cedula),
    ];
}

echo json_encode([
    'ok' => true,
    'bolsillo' => [
        'id'              => $id,
        'nombre'          => $encontrado['nombre'] ?? '',
        'nombre_completo' => $encontrado['nombre_completo'] ?? '',
        'documentos'      => $documentos,
    ],
], JSON_UNESCAPED_UNICODE);
