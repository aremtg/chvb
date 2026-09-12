<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/UsuarioEmpleadoModel.php';

header('Content-Type: application/json');
requireSuperAdmin();
bloquearSiSoloLectura();

$cedula = trim($_POST['cedula'] ?? '');
UsuarioEmpleadoModel::reactivar($cedula);
echo json_encode(['ok' => true]);