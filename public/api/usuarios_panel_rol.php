<?php
// public/api/usuarios_panel_rol.php
// Cambia el rol de una cuenta (la sesión abierta de esa persona se actualiza sola, ver requireSuperAdmin).
require_once __DIR__ . '/../../includes/usuarios_api.php';

$actor    = iniciarApiUsuarios();
$objetivo = obtenerObjetivo($actor, 'No puedes cambiar tu propio rol.');

$rol = $_POST['rol'] ?? '';
if (!in_array($rol, ROLES_PANEL, true)) {
    responderJson(['ok' => false, 'error' => 'Rol no válido.']);
}
if ($objetivo['rol'] === $rol) {
    responderJson(['ok' => false, 'error' => 'La cuenta ya tiene ese rol.']);
}

if ($error = UsuarioModel::cambiarRol((int) $objetivo['id'], $rol)) {
    responderJson(['ok' => false, 'error' => $error]);
}

UsuarioAuditoriaModel::registrar($actor['id'], $actor['username'], (int) $objetivo['id'], $objetivo['username'], 'cambiar_rol', $objetivo['rol'] . ' -> ' . $rol);
responderJson(['ok' => true]);
