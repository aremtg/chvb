<?php
require_once __DIR__ . '/../../src/helpers/FileManager.php';
// public/api/empleado_ver_documento.php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/DocumentoModel.php';
require_once __DIR__ . '/../../src/models/BolsilloModel.php';

requireEmpleado();

$cedula = $_SESSION['empleado_cedula'];
$id = (int)($_GET['id'] ?? 0);
$doc = DocumentoModel::obtenerPorId($id);

if (!$doc) {
    http_response_code(404);
    exit('Documento no encontrado.');
}

$bolsillo = BolsilloModel::obtenerPorId((int)$doc['bolsillo_id']);
if (!$bolsillo || $bolsillo['cedula_empleado'] !== $cedula) {
    http_response_code(403);
    exit('No tienes permiso para ver este documento.');
}

$uploadsPath = FileManager::rutaUploads();
$ruta = $uploadsPath . '/' . $doc['ruta'];

if (!is_file($ruta)) {
    http_response_code(404);
    exit('Archivo no encontrado en disco.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($doc['ruta']) . '"');
readfile($ruta);