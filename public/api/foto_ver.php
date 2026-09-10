<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';

$cedula = trim($_GET['cedula'] ?? '');

$esSuperAdmin = !empty($_SESSION['superadmin_id']);
$esElMismoEmpleado = !empty($_SESSION['empleado_cedula']) && $_SESSION['empleado_cedula'] === $cedula;

if (!$esSuperAdmin && !$esElMismoEmpleado) {
    http_response_code(403);
    exit('No autorizado.');
}

$empleado = EmpleadoModel::obtenerPorCedula($cedula);
if (!$empleado || empty($empleado['foto'])) {
    http_response_code(404);
    exit('Sin foto.');
}

$uploadsPath = rtrim($_ENV['UPLOADS_PATH'], '/');
$ruta = $uploadsPath . '/' . $empleado['foto'];

if (!is_file($ruta)) {
    http_response_code(404);
    exit('Archivo no encontrado.');
}

$extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
$mimeMap = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
header('Content-Type: ' . ($mimeMap[$extension] ?? 'application/octet-stream'));
readfile($ruta);