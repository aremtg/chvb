<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/PermisoController.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';
require_once __DIR__ . '/../../src/helpers/FileManager.php';
requireEmpleado();
validarCSRF();
header('Content-Type: application/json');
ini_set('display_errors','0');
try {
    $id=(int)($_POST['id']??0); $version=(int)($_POST['version']??0);
    $fechaFin=trim($_POST['fecha_fin']??''); $horaFin=trim($_POST['hora_fin']??'');
    $rutaEvidencia=null;
    if (!empty($_FILES['evidencia']) && $_FILES['evidencia']['error']===UPLOAD_ERR_OK) {
        $ruta=FileManager::guardarEvidenciaPermiso($_SESSION['empleado_cedula'], $_FILES['evidencia']);
        if (!$ruta['ok']) { echo json_encode(['ok'=>false,'error'=>$ruta['error']]); exit; }
        $rutaEvidencia=$ruta['ruta'];
    }
    echo json_encode(PermisoController::registrarLlegada($id,$version,$fechaFin,$horaFin,$rutaEvidencia));
} catch(Throwable $e) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'Error interno: '.$e->getMessage()]); }
