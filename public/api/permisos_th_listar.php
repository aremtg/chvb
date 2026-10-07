<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';

header('Content-Type: application/json');
requireSuperAdmin();

$normalizarCedulaFiltro = static function ($valor): string {
    // Los campos de cédula del panel son numéricos y la interfaz puede recibir
    // una cédula copiada con separadores (ej. 1.118.569.829). La BD almacena
    // la cédula sin puntos, por lo que el filtro debe comparar el valor limpio.
    return preg_replace('/\D+/', '', (string)$valor) ?? '';
};

$filtros = [
    'permiso_id' => (int)($_GET['id'] ?? 0),
    'fecha_desde' => trim((string)($_GET['fecha_desde'] ?? '')),
    'fecha_hasta' => trim((string)($_GET['fecha_hasta'] ?? '')),
    'cedula_jefe' => $normalizarCedulaFiltro($_GET['cedula_jefe'] ?? ''),
    'cedula_empleado' => $normalizarCedulaFiltro($_GET['cedula_empleado'] ?? ''),
    'tipo_permiso' => trim((string)($_GET['tipo_permiso'] ?? '')),
    'estado' => trim((string)($_GET['estado'] ?? '')),
    'orden_horas' => trim((string)($_GET['orden_horas'] ?? '')),
];

echo json_encode(['ok' => true, 'permisos' => PermisoModel::listarParaTalentoHumano($filtros)]);