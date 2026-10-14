<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';

header('Content-Type: application/json');
requireSuperAdmin();

// Empleado y jefe: se envía el texto tal cual (cédula con o sin puntos, o nombre). El modelo lo interpreta.
$textoFiltro = static fn($valor): string => trim((string)$valor);


$filtros = [
    'permiso_id' => (int)($_GET['id'] ?? 0),
    'cedula_jefe' => $textoFiltro($_GET['cedula_jefe'] ?? ''),
    'cedula_empleado' => $textoFiltro($_GET['cedula_empleado'] ?? ''),
    'tipo_permiso' => trim((string)($_GET['tipo_permiso'] ?? '')),
    'estado' => trim((string)($_GET['estado'] ?? '')),
];

echo json_encode(['ok' => true, 'permisos' => PermisoModel::listarParaTalentoHumano($filtros)]);