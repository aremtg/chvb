<?php
require_once __DIR__ . '/../../src/helpers/FileManager.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/DocumentoModel.php';

requireSuperAdmin();

$id = (int)($_GET['id'] ?? 0);
$doc = DocumentoModel::obtenerPorId($id);

if (!$doc) {
    http_response_code(404);
    exit('Documento no encontrado.');
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