<?php
require_once __DIR__ . '/errores.php';  // manejo central de errores (sin detalles al usuario, todo al log)
registrarManejoErrores();
// includes/session.php
require_once __DIR__ . '/rutas.php';   // CHVB_PUBLIC_URL: ruta URL de /public (APP_PUBLIC_PATH en .env)
require_once __DIR__ . '/reloj.php';   // hora única de la app (fija la zona horaria de PHP)
require_once __DIR__ . '/roles.php';   // roles de Talento Humano (única fuente)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');

    $secureCookie = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secureCookie,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

/**
 * Bloquea el acceso si no hay sesión activa de superadmin.
 * Inclúyelo al inicio de cada página protegida del panel.
 */
function requireSuperAdmin(): void {
    if (($_SESSION['auth_type'] ?? '') !== 'admin' || empty($_SESSION['superadmin_id'])) {
        header('Location: ' . CHVB_PUBLIC_URL . '/login.php');
        exit;
    }

    require_once __DIR__ . '/../config/database.php';
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT username, rol, activo, debe_cambiar_password, password_cambiada_at FROM usuarios WHERE id = :b1");
    $stmt->execute(['b1' => $_SESSION['superadmin_id']]);
    $usuario = $stmt->fetch();

    // La sesión queda invalidada de inmediato (sin importar cuánto llevara activa) si:
    //  - el usuario ya no existe, o el username no coincide (id reciclado por AUTO_INCREMENT);
    //  - la cuenta fue desactivada desde Configuración -> Usuarios;
    //  - la contraseña cambió DESPUÉS de que se inició esta sesión (cambio propio en otro equipo o reseteo del superadmin).
    $motivo = null;
    if (!$usuario || $usuario['username'] !== ($_SESSION['superadmin_username'] ?? null)) {
        $motivo = 'sesion_invalida';
    } elseif (!(int) $usuario['activo']) {
        $motivo = 'cuenta_desactivada';
    } elseif (!empty($usuario['password_cambiada_at'])
        && strtotime($usuario['password_cambiada_at']) > (int) ($_SESSION['login_at'] ?? 0)) {
        $motivo = 'password_cambiada';
    }
    if ($motivo !== null) {
        unset($_SESSION['superadmin_id'], $_SESSION['superadmin_username'], $_SESSION['superadmin_rol'], $_SESSION['auth_type'], $_SESSION['login_at'], $_SESSION['debe_cambiar_password']);
        header('Location: ' . CHVB_PUBLIC_URL . '/login.php?motivo=' . $motivo);
        exit;
    }

    // Autocorrección: si el rol cambió en BD (ej. Talento Humano reclasificó
    // a alguien de auxiliar a superadmin), la sesión lo refleja sin exigir un nuevo login.
    $_SESSION['superadmin_rol'] = $usuario['rol'];
    $_SESSION['debe_cambiar_password'] = (int) $usuario['debe_cambiar_password'];

    // Contraseña temporal (cuenta nueva o reseteada): mientras no la cambie, solo puede usar
    // la pantalla de cambio de contraseña y cerrar sesión.
    if ($_SESSION['debe_cambiar_password'] === 1) {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        if (!in_array(basename($script), ['cambiar_password.php', 'usuario_cambiar_password.php', 'logout.php'], true)) {
            if (str_contains($script, '/api/')) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                echo json_encode(['ok' => false, 'codigo' => 'cambiar_password', 'error' => 'Debes cambiar tu contraseña temporal antes de continuar.']);
            } else {
                header('Location: ' . CHVB_PUBLIC_URL . '/cambiar_password.php');
            }
            exit;
        }
    }
}
/**
 * Bloquea el acceso si no hay sesión activa de empleado.
 * Inclúyelo al inicio de las páginas del portal del empleado.
 */
function requireEmpleado(): void {
    if (($_SESSION['auth_type'] ?? '') !== 'empleado' || empty($_SESSION['empleado_cedula'])) {
        header('Location: ' . CHVB_PUBLIC_URL . '/login_empleado.php');
        exit;
    }

    require_once __DIR__ . '/../config/database.php';
    $pdo = getPDO();

    // Solo empleados ACTIVOS pueden mantener sesión en el portal.
    $stmt = $pdo->prepare("SELECT cedula FROM empleados WHERE cedula = :b1 AND estado = 'activo'");
    $stmt->execute(['b1' => $_SESSION['empleado_cedula']]);
    $empleadoExiste = (bool) $stmt->fetch();

    $stmt2 = $pdo->prepare("SELECT activo FROM usuarios_empleados WHERE cedula = :b1");
    $stmt2->execute(['b1' => $_SESSION['empleado_cedula']]);
    $acceso = $stmt2->fetch();

    if (!$empleadoExiste || !$acceso || !$acceso['activo']) {
        unset($_SESSION['empleado_cedula'], $_SESSION['auth_type']);
        header('Location: ' . CHVB_PUBLIC_URL . '/login_empleado.php?motivo=sesion_invalida');
        exit;
    }
}

/**
 * Bloquea cualquier acción de escritura (crear/editar/eliminar/subir) si el rol es 'teniente'.
 * Se llama DESPUÉS de requireSuperAdmin() en cada endpoint que modifica datos.
 */
function bloquearSiSoloLectura(): void {
    if (($_SESSION['superadmin_rol'] ?? '') === 'teniente') {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'Tu rol solo tiene permisos de lectura.']);
        exit;
    }
}
require_once __DIR__ . '/icon.php';

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfCampoHTML(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken()) . '">';
}

function validarCSRF(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $esperado = $_SESSION['csrf_token'] ?? '';
    // Sin token en la sesión o sin token en la petición NO es válido
    // (hash_equals('', '') devuelve true y dejaría pasar la petición).
    if ($esperado === '' || $token === '' || !hash_equals($esperado, $token)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Sesión no válida, recarga la página.']);
        exit;
    }
}

/**
 * Cierra la sesión por completo: vacía $_SESSION, borra la cookie y destruye la sesión.
 */
function cerrarSesionCompleta(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'domain'   => $p['domain'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => 'Lax',
        ]);
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/**
 * Para los logout: solo actúa si la petición es POST con token CSRF válido.
 * Devuelve true si se debe cerrar la sesión.
 */
function logoutSolicitadoPorPost(): bool {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') return false;
    $token = $_POST['csrf_token'] ?? '';
    return $token !== '' && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}
