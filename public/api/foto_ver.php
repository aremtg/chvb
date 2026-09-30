<?php
require_once __DIR__ . '/../../src/helpers/FileManager.php';
// limpia cualquier espacio basura
if (ob_get_length()) ob_end_clean();
ob_start();

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';

if (!isset($_SERVER['UPLOADS_PATH']) && !isset($_ENV['UPLOADS_PATH'])) {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
    $dotenv->load();
}

$cedula = trim($_GET['cedula'] ?? '');
$esSuperAdmin = !empty($_SESSION['superadmin_id']) || !empty($_SESSION['superadmin_rol']);
$esElMismo = !empty($_SESSION['empleado_cedula']) && $_SESSION['empleado_cedula'] === $cedula;

if (!$esSuperAdmin && !$esElMismo) {
    ob_end_clean();
    http_response_code(403);
    exit('No autorizado');
}

$emp = EmpleadoModel::obtenerPorCedula($cedula);
if (!$emp || empty($emp['foto'])) {
    ob_end_clean();
    http_response_code(404);
    exit('Sin foto');
}

$base = FileManager::rutaUploads();
$ruta = $base . '/' . ltrim($emp['foto'], '/\\');
$ruta = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ruta);

if (!is_file($ruta)) {
    ob_end_clean();
    http_response_code(404);
    exit('No existe: '.$ruta);
}

ob_end_clean();

$ext = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));

$mime = [
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'webp' => 'image/webp'
][$ext] ?? mime_content_type($ruta);

$size = filesize($ruta);
$mtime = filemtime($ruta);

$etag = '"' . md5($ruta . '|' . $size . '|' . $mtime) . '"';

header('Content-Type: ' . $mime);
header('Content-Length: ' . $size);
header('Content-Disposition: inline; filename="foto.' . $ext . '"');

header('Cache-Control: private, max-age=0, must-revalidate');
header('ETag: ' . $etag);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');

if (
    isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
    trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag
) {
    http_response_code(304);
    exit;
}

readfile($ruta);
exit;