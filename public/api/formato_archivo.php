<?php
require_once __DIR__ . '/../../includes/session.php';
requireSuperAdmin();
if (!in_array($_SESSION['superadmin_rol'] ?? '', ['superadmin_talento_humano', 'auxiliar_talento_humano'], true)) {
    http_response_code(403); exit('No autorizado.');
}

$archivo = basename((string)($_GET['f'] ?? ''));
if (!preg_match('/^RENOVACION_[A-Za-z0-9._-]+\.docx$/i', $archivo)) {
    http_response_code(400); exit('Archivo no válido.');
}
$base = realpath(__DIR__ . '/../../uploads/generados');
$path = $base ? realpath($base . DIRECTORY_SEPARATOR . $archivo) : false;
if (!$base || !$path || !is_file($path) || dirname($path) !== $base) {
    http_response_code(404); exit('Archivo no encontrado.');
}

$accion = $_GET['accion'] ?? 'descargar';
if ($accion === 'eliminar') {
    validarCSRF();
    if (!unlink($path)) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'No se pudo eliminar el archivo.']); exit; }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok'=>true], JSON_UNESCAPED_UNICODE); exit;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . rawurlencode($archivo) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
