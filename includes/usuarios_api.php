<?php
// includes/usuarios_api.php
// Preámbulo común de los endpoints de administración de usuarios (public/api/usuarios_panel_*.php).
// Centraliza lo que TODOS deben hacer, en el mismo orden, para que ninguno se olvide de nada:
//   solo POST -> sesión + rol superadmin -> CSRF -> confirmación de contraseña del superadmin.
require_once __DIR__ . '/usuarios_guard.php';
require_once __DIR__ . '/../src/models/UsuarioModel.php';
require_once __DIR__ . '/../src/models/UsuarioAuditoriaModel.php';
require_once __DIR__ . '/../src/helpers/PasswordPolicy.php';

function responderJson(array $datos, int $codigo = 200): never
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * @return array{id:int, username:string} el superadmin que realiza la acción
 */
function iniciarApiUsuarios(): array
{
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');   // las respuestas pueden traer una contraseña temporal

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        responderJson(['ok' => false, 'error' => 'Método no permitido.'], 405);
    }
    requireGestionUsuarios(true);
    validarCSRF();

    $actor = ['id' => (int) $_SESSION['superadmin_id'], 'username' => (string) $_SESSION['superadmin_username']];

    // Acción sensible: se vuelve a pedir la contraseña (protege si alguien deja la sesión abierta).
    $r = UsuarioModel::verificarPasswordActual($actor['id'], (string) ($_POST['password_actual'] ?? ''));
    if (!$r['ok']) {
        responderJson(['ok' => false, 'error' => $r['error'], 'campo' => 'password_actual']);
    }
    return $actor;
}

/** Valida el id del usuario objetivo: debe existir y NO ser el propio actor. */
function obtenerObjetivo(array $actor, string $mensajeSiEsUnoMismo): array
{
    $id = (int) ($_POST['id'] ?? 0);
    if ($id === $actor['id']) {
        responderJson(['ok' => false, 'error' => $mensajeSiEsUnoMismo]);
    }
    $objetivo = $id > 0 ? UsuarioModel::obtener($id) : null;
    if (!$objetivo) {
        responderJson(['ok' => false, 'error' => 'El usuario no existe.']);
    }
    return $objetivo;
}
