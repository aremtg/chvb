<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/LibroController.php';
header('Content-Type: application/json');
requireEmpleado();
validarCSRF();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Método no permitido.']); exit; }
$documentoId=(int)($_POST['documento_id']??0);
$cedula=$_SESSION['empleado_cedula'];
echo json_encode(LibroController::eliminarDocumento($documentoId,$cedula,true));
