<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/PermisoController.php';
requireSuperAdmin();
validarCSRF();
header('Content-Type: application/json');
$id=(int)($_POST['id']??0); $version=(int)($_POST['version']??0); $motivo=trim($_POST['motivo']??'');
echo json_encode(PermisoController::anular($id,$version,$motivo));
