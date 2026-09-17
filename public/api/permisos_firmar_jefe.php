<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/PermisoController.php';
require_once __DIR__ . '/../../src/controllers/FirmaController.php';
require_once __DIR__ . '/../../src/helpers/FileManager.php';
require_once __DIR__ . '/../../src/models/FirmaModel.php';

header('Content-Type: application/json');
requireEmpleado();

$id = (int)($_POST['id'] ?? 0);
$version = (int)($_POST['version'] ?? 0);
$cedula = $_SESSION['empleado_cedula'];

if (!empty($_POST['usar_firma_guardada'])) {
    $existente = FirmaModel::obtenerPorCedula($cedula);
    if (!$existente) { echo json_encode(['ok' => false, 'error' => 'No tienes firma guardada.']); exit; }
    $rutaFirma = ['ok' => true, 'ruta' => $existente['ruta_imagen']];
} elseif (!empty($_POST['firma_base64'])) {
    $rutaFirma = FirmaController::guardarFirmaBase64($cedula, $_POST['firma_base64']);
} else {
    echo json_encode(['ok' => false, 'error' => 'Firma requerida.']); exit;
}
if (!$rutaFirma['ok']) { echo json_encode(['ok' => false, 'error' => $rutaFirma['error']]); exit; }

$rutaFoto = null;
if (!empty($_POST['foto_base64'])) {
    $r = FileManager::guardarFotoPermisoBase64($cedula, $_POST['foto_base64'], 'permisos/fotos');
    if ($r['ok']) $rutaFoto = $r['ruta'];
}

echo json_encode(PermisoController::firmarJefe($id, $version, $rutaFirma['ruta'], $rutaFoto));