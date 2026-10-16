<?php
// public/api/usuario_cambiar_password.php
// Cada usuario del panel (cualquier rol, incluido el teniente) cambia SU PROPIA contraseña.
// Sigue permitido aunque la cuenta tenga la contraseña temporal pendiente (ver requireSuperAdmin).
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/UsuarioModel.php';
require_once __DIR__ . '/../../src/models/UsuarioAuditoriaModel.php';
require_once __DIR__ . '/../../src/helpers/PasswordPolicy.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function responder(array $d, int $c = 200): never { http_response_code($c); echo json_encode($d, JSON_UNESCAPED_UNICODE); exit; }

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    responder(['ok' => false, 'error' => 'Método no permitido.'], 405);
}
requireSuperAdmin();
// Sin bloquearSiSoloLectura(): el teniente también debe poder cambiar su contraseña.
validarCSRF();

$id       = (int) $_SESSION['superadmin_id'];
$username = (string) $_SESSION['superadmin_username'];
$actual   = (string) ($_POST['password_actual'] ?? '');
$nueva    = (string) ($_POST['password_nueva'] ?? '');
$confirma = (string) ($_POST['password_confirmacion'] ?? '');

$r = UsuarioModel::verificarPasswordActual($id, $actual);
if (!$r['ok']) {
    responder(['ok' => false, 'campo' => 'password_actual', 'error' => $r['error']]);
}
if ($nueva !== $confirma) {
    responder(['ok' => false, 'campo' => 'password_confirmacion', 'error' => 'La confirmación no coincide con la nueva contraseña.']);
}
if ($nueva === $actual) {
    responder(['ok' => false, 'campo' => 'password_nueva', 'error' => 'La nueva contraseña debe ser distinta de la actual.']);
}
if ($error = PasswordPolicy::validar($nueva, $username)) {
    responder(['ok' => false, 'campo' => 'password_nueva', 'error' => $error]);
}

$momentoCambio = UsuarioModel::cambiarPasswordPropia($id, $nueva);
UsuarioAuditoriaModel::registrar($id, $username, $id, $username, 'cambiar_password_propio');

// Esta sesión sigue válida con ID nuevo; las demás sesiones de la cuenta quedan invalidadas
// porque password_cambiada_at ahora es posterior a su login_at.
session_regenerate_id(true);
$_SESSION['login_at'] = $momentoCambio;
$_SESSION['debe_cambiar_password'] = 0;

responder(['ok' => true, 'redirigir' => CHVB_PUBLIC_URL . '/dashboard.php']);
