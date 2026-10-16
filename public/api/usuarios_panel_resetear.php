<?php
// public/api/usuarios_panel_resetear.php
// Pone una contraseña TEMPORAL nueva (se muestra una sola vez). La persona deberá cambiarla al entrar
// y sus sesiones abiertas se cierran.
require_once __DIR__ . '/../../includes/usuarios_api.php';

$actor    = iniciarApiUsuarios();
$objetivo = obtenerObjetivo($actor, 'Para cambiar tu propia contraseña usa "Mi contraseña" en el menú.');

$temporal = PasswordPolicy::generarTemporal();
UsuarioModel::resetearPassword((int) $objetivo['id'], $temporal);

UsuarioAuditoriaModel::registrar($actor['id'], $actor['username'], (int) $objetivo['id'], $objetivo['username'], 'resetear_password');
responderJson(['ok' => true, 'username' => $objetivo['username'], 'password_temporal' => $temporal]);
