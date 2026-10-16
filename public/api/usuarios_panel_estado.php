<?php
// public/api/usuarios_panel_estado.php
// Desactiva o reactiva una cuenta. No se borran cuentas: así el historial queda intacto.
require_once __DIR__ . '/../../includes/usuarios_api.php';

$actor    = iniciarApiUsuarios();
$objetivo = obtenerObjetivo($actor, 'No puedes desactivar tu propia cuenta.');

$activo = ($_POST['activo'] ?? '') === '1';
if ((int) $objetivo['activo'] === (int) $activo) {
    responderJson(['ok' => false, 'error' => $activo ? 'La cuenta ya está activa.' : 'La cuenta ya está desactivada.']);
}

if ($error = UsuarioModel::establecerActivo((int) $objetivo['id'], $activo)) {
    responderJson(['ok' => false, 'error' => $error]);
}

UsuarioAuditoriaModel::registrar($actor['id'], $actor['username'], (int) $objetivo['id'], $objetivo['username'], $activo ? 'reactivar' : 'desactivar');
responderJson(['ok' => true]);
