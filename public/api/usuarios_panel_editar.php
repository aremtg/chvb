<?php
// public/api/usuarios_panel_editar.php
// Corrige el nombre completo y/o el nombre de usuario de una cuenta (p. ej. un error de digitación).
// El historial ya registrado conserva el usuario con el que se guardó; el cambio queda en la auditoría.
require_once __DIR__ . '/../../includes/usuarios_api.php';

$actor = iniciarApiUsuarios();

$id       = (int) ($_POST['id'] ?? 0);
$objetivo = $id > 0 ? UsuarioModel::obtener($id) : null;
if (!$objetivo) {
    responderJson(['ok' => false, 'error' => 'El usuario no existe.']);
}

$username = trim($_POST['username'] ?? '');
$nombre   = trim($_POST['nombre_completo'] ?? '');

if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
    responderJson(['ok' => false, 'campo' => 'username', 'error' => 'El usuario debe tener de 3 a 50 caracteres: letras, números, punto, guion o guion bajo.']);
}
if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
    responderJson(['ok' => false, 'campo' => 'nombre_completo', 'error' => 'Escribe el nombre completo de la persona (3 a 100 caracteres).']);
}

// Cambiarse el usuario a uno mismo cerraría la propia sesión (ver requireSuperAdmin): el nombre sí se puede corregir.
if ($id === $actor['id'] && $username !== $objetivo['username']) {
    responderJson(['ok' => false, 'campo' => 'username', 'error' => 'No puedes cambiar tu propio nombre de usuario; pídeselo a otro superadmin.']);
}

$cambios = [];
if ($username !== $objetivo['username']) {
    $cambios[] = 'usuario: ' . $objetivo['username'] . ' -> ' . $username;
}
if ($nombre !== (string) $objetivo['nombre_completo']) {
    $cambios[] = 'nombre: ' . ($objetivo['nombre_completo'] ?: '(vacío)') . ' -> ' . $nombre;
}
if (!$cambios) {
    responderJson(['ok' => false, 'error' => 'No hay cambios para guardar.']);
}

$otro = UsuarioModel::obtenerPorUsername($username);
if ($otro && (int) $otro['id'] !== $id) {
    responderJson(['ok' => false, 'campo' => 'username', 'error' => 'Ese nombre de usuario ya existe.']);
}

if ($error = UsuarioModel::actualizarDatos($id, $username, $nombre)) {
    responderJson(['ok' => false, 'campo' => 'username', 'error' => $error]);
}

UsuarioAuditoriaModel::registrar($actor['id'], $actor['username'], $id, $username, 'editar_datos', mb_substr(implode('; ', $cambios), 0, 255));
responderJson(['ok' => true]);