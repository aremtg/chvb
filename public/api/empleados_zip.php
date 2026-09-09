<?php
// public/api/empleados_zip.php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../../src/helpers/FileManager.php';

requireSuperAdmin();

$cedula = trim($_GET['cedula'] ?? '');
$empleado = EmpleadoModel::obtenerPorCedula($cedula);

if (!$empleado) {
    http_response_code(404);
    exit('Empleado no encontrado.');
}

$rutaBase = FileManager::rutaBase($cedula);

if (!is_dir($rutaBase)) {
    http_response_code(404);
    exit('La carpeta física del empleado no existe.');
}

// Nombre del archivo ZIP temporal
$nombreZip = 'hv_' . $cedula . '_' . date('Ymd_His') . '.zip';
$rutaZipTemporal = sys_get_temp_dir() . '/' . $nombreZip;

$zip = new ZipArchive();

if ($zip->open($rutaZipTemporal, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    exit('No se pudo crear el archivo ZIP.');
}

// Recorremos recursivamente la carpeta del empleado y la agregamos al ZIP
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rutaBase, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($iterator as $archivo) {
    if (!$archivo->isFile()) {
        continue;
    }

    $rutaCompleta = $archivo->getPathname();
    // Ruta relativa dentro del ZIP, empezando desde "hv_{cedula}/..."
    $rutaRelativa = 'hv_' . $cedula . '/' . substr($rutaCompleta, strlen($rutaBase) + 1);
    $rutaRelativa = str_replace('\\', '/', $rutaRelativa);

    $zip->addFile($rutaCompleta, $rutaRelativa);
}

$zip->close();

// Enviar el ZIP al navegador para descarga
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $nombreZip . '"');
header('Content-Length: ' . filesize($rutaZipTemporal));

readfile($rutaZipTemporal);

// Limpieza: borramos el ZIP temporal después de enviarlo
unlink($rutaZipTemporal);
exit;