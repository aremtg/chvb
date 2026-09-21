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
UsuarioEmpleadoModel::revocar($cedula);
if (($_SESSION['superadmin_rol'] ?? '') === 'auxiliar_talento_humano') {
    $empleado = EmpleadoModel::obtenerPorCedula($cedula);
    $nombre = $empleado['nombre'] ?? $cedula;
    $actor = $_SESSION['superadmin_username'] ?? 'Auxiliar';
    NotificacionModel::crearParaSuperAdminsDesdeAuxiliar(
        $cedula,
        'usuario_empleado',
        "\"{$actor}\" revocó el acceso del empleado \"{$nombre}\"",
        "/chvb/public/usuarios_empleados.php?q=" . urlencode($cedula)
    );
}
echo json_encode(['ok' => true]);