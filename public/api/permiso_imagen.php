<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';

$id = (int)($_GET['id'] ?? 0);
$campo = $_GET['campo'] ?? ''; // foto_solicitante | firma_solicitante | foto_reemplazo | firma_reemplazo | foto_jefe | firma_jefe | evidencia_archivo

$camposValidos = ['foto_solicitante','firma_solicitante','foto_reemplazo','firma_reemplazo','foto_jefe','firma_jefe','evidencia_archivo'];
if (!in_array($campo, $camposValidos, true)) { http_response_code(400); exit('Campo inválido.'); }

$permiso = PermisoModel::obtenerPorId($id);
if (!$permiso) { http_response_code(404); exit('Permiso no encontrado.'); }

$esEmpleado = !empty($_SESSION['empleado_cedula']);
$esTH = !empty($_SESSION['superadmin_id']);

if ($esEmpleado) {
    $cedula = $_SESSION['empleado_cedula'];
    $autorizado = in_array($cedula, [$permiso['cedula_empleado'], $permiso['cedula_reemplazo'], $permiso['cedula_jefe']], true);
    if (!$autorizado) { http_response_code(403); exit('No autorizado.'); }
} elseif (!$esTH) {
    http_response_code(401); exit('No autenticado.');
}

$rutaRelativa = $permiso[$campo] ?? null;
if (!$rutaRelativa) { http_response_code(404); exit('No hay imagen para este campo.'); }

$uploadsPath = rtrim($_ENV['UPLOADS_PATH'] ?? $_SERVER['UPLOADS_PATH'], '/');
$rutaCompleta = $uploadsPath . '/' . $rutaRelativa;

if (!is_file($rutaCompleta)) { http_response_code(404); exit('Archivo no encontrado en disco.'); }

$extension = strtolower(pathinfo($rutaCompleta, PATHINFO_EXTENSION));
$mimeMap = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'pdf' => 'application/pdf'];

if (ob_get_level()) ob_end_clean(); // evita corrupción de imagen si algo emitió salida antes
header('Content-Type: ' . ($mimeMap[$extension] ?? 'application/octet-stream'));
readfile($rutaCompleta);