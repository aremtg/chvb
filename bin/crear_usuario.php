<?php
/**
 * Crea un usuario del panel (superadmin, auxiliar o teniente) o cambia su contraseña.
 *
 * Es la vía para: (1) crear el PRIMER superadmin y (2) recuperar el acceso de emergencia si se olvida la
 * contraseña del superadmin. Para el día a día usa el panel Configuración -> Usuarios.
 * Al resetear por aquí la cuenta se REACTIVA, se quita la contraseña temporal pendiente y se cierran las
 * sesiones que tuviera abiertas. Todo queda en la auditoría.
 *
 * SOLO se ejecuta desde la terminal (no por el navegador):
 *
 *     cd C:\xampp\htdocs\chvb
 *     C:\xampp\php\php.exe bin\crear_usuario.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script solo se puede ejecutar desde la terminal.');
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../src/helpers/PasswordPolicy.php';
require_once __DIR__ . '/../src/models/UsuarioAuditoriaModel.php';

const ROLES = [
    '1' => ['superadmin_talento_humano', 'Superadmin de Talento Humano (acceso total)'],
    '2' => ['auxiliar_talento_humano',   'Auxiliar de Talento Humano'],
    '3' => ['teniente',                  'Teniente (solo lectura)'],
];

function preguntar(string $texto): string
{
    echo $texto;
    $linea = fgets(STDIN);
    return $linea === false ? '' : trim($linea);
}

function fallar(string $mensaje): never
{
    fwrite(STDERR, "\n✗ $mensaje\n");
    exit(1);
}

echo "\n=== Usuarios del panel CHVB ===\n\n";

// --- Usuario ---
$username = preguntar('Nombre de usuario: ');
if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
    fallar('El usuario debe tener de 3 a 50 caracteres: letras, números, punto, guion o guion bajo.');
}

$pdo = getPDO();
$stmt = $pdo->prepare('SELECT id, rol, activo FROM usuarios WHERE username = :u');
$stmt->execute(['u' => $username]);
$existente = $stmt->fetch();

// --- Rol (solo si es nuevo) ---
$rol = null;
if ($existente) {
    echo "\nEl usuario \"$username\" ya existe (rol: {$existente['rol']}).\n";
    if (strtolower(preguntar('¿Quieres cambiarle la contraseña? (s/n): ')) !== 's') {
        exit(0);
    }
} else {
    echo "\nRoles disponibles:\n";
    foreach (ROLES as $n => [$clave, $desc]) {
        echo "  $n) $desc\n";
    }
    $opcion = preguntar("\nElige el rol (1-3): ");
    if (!isset(ROLES[$opcion])) {
        fallar('Rol no válido.');
    }
    $rol = ROLES[$opcion][0];
}

// --- Contraseña ---
echo "\n(La contraseña se verá mientras la escribes; cierra la terminal al terminar.)\n";
$password = preguntar('Contraseña (mínimo ' . PasswordPolicy::MIN_CARACTERES . ' caracteres): ');
if ($error = PasswordPolicy::validar($password, $username)) {
    fallar($error);
}
if ($password !== preguntar('Repite la contraseña: ')) {
    fallar('Las contraseñas no coinciden.');
}

$hash = password_hash($password, PASSWORD_DEFAULT);

if ($existente) {
    $pdo->prepare(
        'UPDATE usuarios SET password_hash = :h, activo = 1, debe_cambiar_password = 0, password_cambiada_at = NOW(),
                intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = :id'
    )->execute(['h' => $hash, 'id' => $existente['id']]);
    UsuarioAuditoriaModel::registrar(null, 'CLI', (int) $existente['id'], $username, 'cli_resetear_password',
        (int) $existente['activo'] === 0 ? 'la cuenta estaba desactivada y se reactivó' : null);
    echo "\n✓ Contraseña de \"$username\" actualizada, cuenta activa y bloqueo reiniciado.\n";
    if ((int) $existente['activo'] === 0) {
        echo "  (La cuenta estaba desactivada: quedó reactivada.)\n";
    }
} else {
    $pdo->prepare('INSERT INTO usuarios (username, password_hash, rol, password_cambiada_at) VALUES (:u, :h, :r, NOW())')
        ->execute(['u' => $username, 'h' => $hash, 'r' => $rol]);
    UsuarioAuditoriaModel::registrar(null, 'CLI', (int) $pdo->lastInsertId(), $username, 'cli_crear_usuario', "rol=$rol");
    echo "\n✓ Usuario \"$username\" creado con el rol $rol.\n";
}
