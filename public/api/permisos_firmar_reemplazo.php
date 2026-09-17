<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/PermisoController.php';
require_once __DIR__ . '/../../src/controllers/FirmaController.php';
require_once __DIR__ . '/../../src/helpers/FileManager.php';

header('Content-Type: application/json');
requireEmpleado();
validarCSRF();

$id = (int)($_POST['id'] ?? 0);
$version = (int)($_POST['version'] ?? 0);
$cedula = $_SESSION['empleado_cedula'];

$rutaFirma = !empty($_POST['usar_firma_guardada'])
    ? null
    : (!empty($_POST['firma_base64']) ? FirmaController::guardarFirmaBase64($cedula, $_POST['firma_base64']) : null);

if (!empty($_POST['usar_firma_guardada'])) {
    require_once __DIR__ . '/../../src/models/FirmaModel.php';
    $existente = FirmaModel::obtenerPorCedula($cedula);
    if (!$existente) { echo json_encode(['ok' => false, 'error' => 'No tienes firma guardada.']); exit; }
    $rutaFirma = ['ok' => true, 'ruta' => $existente['ruta_imagen']];
}
if (!$rutaFirma || !$rutaFirma['ok']) { echo json_encode(['ok' => false, 'error' => 'Firma inválida.']); exit; }

$rutaFoto = null;
if (!empty($_POST['foto_base64'])) {
    $r = FileManager::guardarFotoPermisoBase64($cedula, $_POST['foto_base64'], 'permisos/fotos');
    if ($r['ok']) $rutaFoto = $r['ruta'];
}

echo json_encode(PermisoController::firmarReemplazo($id, $version, $rutaFirma['ruta'], $rutaFoto));