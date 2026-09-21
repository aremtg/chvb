<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/UsuarioEmpleadoModel.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../../src/models/NotificacionModel.php';

header('Content-Type: application/json');
requireSuperAdmin();
validarCSRF();
bloquearSiSoloLectura();

$cedula = trim($_POST['cedula'] ?? '');
$pin = trim($_POST['pin'] ?? '');

if (!preg_match('/^[A-Za-z0-9]{5,10}$/', $cedula)) {
    echo json_encode(['ok' => false, 'error' => 'Cédula inválida.']);
    exit;
}

if (!preg_match('/^[0-9]{4}$/', $pin)) {
    echo json_encode(['ok' => false, 'error' => 'El PIN debe ser de exactamente 4 dígitos numéricos.']);
    exit;
}

UsuarioEmpleadoModel::crearAcceso($cedula, $pin);
if (($_SESSION['superadmin_rol'] ?? '') === 'auxiliar_talento_humano') {
    $empleado = EmpleadoModel::obtenerPorCedula($cedula);
    $nombre = $empleado['nombre'] ?? $cedula;
    $actor = $_SESSION['superadmin_username'] ?? 'Auxiliar';
    NotificacionModel::crearParaSuperAdminsDesdeAuxiliar(
        $cedula,
        'usuario_empleado',
        "\"{$actor}\" creó o cambió el PIN de acceso del empleado \"{$nombre}\"",
        "/chvb/public/usuarios_empleados.php?q=" . urlencode($cedula)
    );
}
echo json_encode(['ok' => true]);