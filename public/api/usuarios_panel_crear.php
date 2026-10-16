<?php
// public/api/usuarios_panel_crear.php
// Crea una cuenta del panel con contraseña TEMPORAL (se muestra una sola vez).
require_once __DIR__ . '/../../includes/usuarios_api.php';

$actor = iniciarApiUsuarios();

$username = trim($_POST['username'] ?? '');
$nombre   = trim($_POST['nombre_completo'] ?? '');
$rol      = $_POST['rol'] ?? '';

if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
    responderJson(['ok' => false, 'campo' => 'username', 'error' => 'El usuario debe tener de 3 a 50 caracteres: letras, números, punto, guion o guion bajo.']);
}
if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
    responderJson(['ok' => false, 'campo' => 'nombre_completo', 'error' => 'Escribe el nombre completo de la persona (3 a 100 caracteres).']);
}
if (!in_array($rol, ROLES_PANEL, true)) {
    responderJson(['ok' => false, 'campo' => 'rol', 'error' => 'Rol no válido.']);
}
if (UsuarioModel::obtenerPorUsername($username)) {
    responderJson(['ok' => false, 'campo' => 'username', 'error' => 'Ese nombre de usuario ya existe.']);
}

$temporal = PasswordPolicy::generarTemporal();
try {
    $id = UsuarioModel::crear($username, $nombre, $rol, $temporal, $actor['id']);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {   // otro superadmin lo creó justo ahora
        responderJson(['ok' => false, 'campo' => 'username', 'error' => 'Ese nombre de usuario ya existe.']);
    }
    throw $e;
}

UsuarioAuditoriaModel::registrar($actor['id'], $actor['username'], $id, $username, 'crear_usuario', "rol=$rol");
responderJson(['ok' => true, 'username' => $username, 'password_temporal' => $temporal]);
