<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';

header('Content-Type: application/json');
requireSuperAdmin();
if (($_SESSION['superadmin_rol'] ?? '') === 'teniente') {
    echo json_encode(['ok' => true, 'permisos' => []]);
    exit;
}

$desde = $_GET['desde'] ?? date('Y-m-d H:i:s', strtotime('-1 minute'));
echo json_encode(['ok' => true, 'permisos' => PermisoModel::listarActualizadosDesde($desde), 'servidor_ahora' => date('Y-m-d H:i:s')]);