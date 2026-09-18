<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';

header('Content-Type: application/json');
$esEmpleado = !empty($_SESSION['empleado_cedula']);
$esTH = !empty($_SESSION['superadmin_id']);
if (!$esEmpleado && !$esTH) { http_response_code(401); echo json_encode(['ok' => false]); exit; }

$id = (int)($_GET['id'] ?? 0);
$permiso = PermisoModel::obtenerPorId($id);
if (!$permiso) { echo json_encode(['ok' => false, 'error' => 'Permiso no encontrado.']); exit; }

if ($esEmpleado) {
    $cedula = $_SESSION['empleado_cedula'];
    $autorizado = in_array($cedula, [$permiso['cedula_empleado'], $permiso['cedula_reemplazo'], $permiso['cedula_jefe']], true);
    if (!$autorizado) {
        $pdoHist = getPDO();
        $stHist = $pdoHist->prepare("SELECT 1 FROM permisos_historial WHERE permiso_id=:b1 AND actor_tipo='reemplazo' AND actor_cedula_o_usuario=:b2 LIMIT 1");
        $stHist->execute(['b1'=>$id,'b2'=>$cedula]);
        $autorizado = (bool)$stHist->fetchColumn();
    }
    if (!$autorizado) { http_response_code(403); echo json_encode(['ok' => false, 'error' => 'No autorizado.']); exit; }
}

$permiso['dias'] = PermisoModel::obtenerDias($id);
$permiso['devoluciones'] = PermisoModel::obtenerDevoluciones($id);

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM permisos_historial WHERE permiso_id = :b1 ORDER BY created_at ASC");
$stmt->execute(['b1' => $id]);
$permiso['historial'] = $stmt->fetchAll();

echo json_encode(['ok' => true, 'permiso' => $permiso]);