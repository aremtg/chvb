<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';

header('Content-Type: application/json');
requireEmpleado();

$cedula = $_SESSION['empleado_cedula'];

$filtros = [
    'tipo_permiso' => $_GET['tipo_permiso'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'fecha_desde' => $_GET['fecha_desde'] ?? '',
    'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
];

$permisos = PermisoModel::listarPorEmpleadoConFiltros($cedula, $filtros);
echo json_encode(['ok' => true, 'permisos' => $permisos]);