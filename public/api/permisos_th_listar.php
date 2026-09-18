<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';

header('Content-Type: application/json');
requireSuperAdmin();

$filtros = [
    'fecha_desde' => $_GET['fecha_desde'] ?? '',
    'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
    'cedula_jefe' => $_GET['cedula_jefe'] ?? '',
    'cedula_empleado' => $_GET['cedula_empleado'] ?? '',
    'tipo_permiso' => $_GET['tipo_permiso'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'orden_horas' => $_GET['orden_horas'] ?? '',
];

echo json_encode(['ok' => true, 'permisos' => PermisoModel::listarParaTalentoHumano($filtros)]);