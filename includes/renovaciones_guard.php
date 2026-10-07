<?php
// includes/renovaciones_guard.php
// ÚNICO lugar donde se define quién puede usar el módulo "Control de Renovaciones".
// (Mismo patrón que includes/formatos_guard.php.)
//
// REGLA DE NEGOCIO: superadmin_talento_humano y auxiliar_talento_humano tienen
// EXACTAMENTE las mismas capacidades en este módulo. No existe aquí ningún
// equivalente a requireFormatosAdmin(). La única diferencia entre ambos roles es
// la identidad del actor, que se conserva para auditoría (ver renovacionesActor()
// y RenovacionAuditoriaModel).
//
// SEGURIDAD: se usa una LISTA BLANCA de roles. A diferencia de
// bloquearSiSoloLectura() (lista negra: solo bloquea 'teniente'), un rol nuevo
// que se agregue algún día a la tabla usuarios NO tendrá acceso por omisión.
//
// Uso en una página:
//     require_once __DIR__ . '/../includes/renovaciones_guard.php';
//     requireRenovacionesAccess();
//
// Uso en un endpoint de LECTURA (GET → JSON):
//     require_once __DIR__ . '/../../includes/renovaciones_guard.php';
//     requireRenovacionesAccess(true);
//
// Uso en un endpoint de ESCRITURA (POST → JSON). Valida rol + método POST + CSRF
// y devuelve la identidad del actor, lista para guardar en las columnas *_por_*:
//     $actor = requireRenovacionesEscritura();
require_once __DIR__ . '/session.php';

const ROLES_RENOVACIONES = ['superadmin_talento_humano', 'auxiliar_talento_humano'];

const ETIQUETAS_ROL_TALENTO_HUMANO = [
    'superadmin_talento_humano' => 'Superadmin Talento Humano',
    'auxiliar_talento_humano'   => 'Auxiliar Talento Humano',
];

/** Texto legible de un rol de Talento Humano (para mostrar "registrada por ..."). */
function etiquetaRolTalentoHumano(string $rol): string
{
    return ETIQUETAS_ROL_TALENTO_HUMANO[$rol] ?? $rol;
}

/** Corta la petición con el código HTTP dado (JSON para APIs, texto plano para páginas). */
function renovacionesDenegar(int $codigo, string $mensaje, bool $json): void
{
    http_response_code($codigo);
    if ($json) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => $mensaje], JSON_UNESCAPED_UNICODE);
    } else {
        echo 'No autorizado.';
    }
    exit;
}

/**
 * Exige sesión de usuario admin + rol de Talento Humano (superadmin o auxiliar).
 * Bloquea a 'teniente' y a cualquier otro rol, incluso para consultar.
 *
 * requireSuperAdmin() valida que el usuario siga existiendo en BD y refresca
 * $_SESSION['superadmin_rol'] desde la tabla usuarios en cada petición; por eso
 * el chequeo de rol de abajo usa el rol REAL y no uno desactualizado de sesión.
 *
 * $json = true para endpoints de API (responde JSON en vez de redirigir/texto plano).
 */
function requireRenovacionesAccess(bool $json = false): void
{
    // En APIs, una sesión ausente debe ser un 401 JSON y no una redirección a login.php
    // (que es lo que hace requireSuperAdmin() y que un fetch() recibiría como HTML).
    if ($json && (($_SESSION['auth_type'] ?? '') !== 'admin' || empty($_SESSION['superadmin_id']))) {
        renovacionesDenegar(401, 'Tu sesión expiró. Inicia sesión de nuevo.', true);
    }

    requireSuperAdmin();

    if (!in_array($_SESSION['superadmin_rol'] ?? '', ROLES_RENOVACIONES, true)) {
        renovacionesDenegar(403, 'No tienes permiso para usar Control de Renovaciones.', $json);
    }
}

/**
 * Para endpoints que ESCRIBEN. Un solo llamado valida, en este orden:
 *   sesión + rol (backend) → método POST → token CSRF.
 * Devuelve la identidad del actor (ver renovacionesActor()).
 * Superadmin y auxiliar pasan exactamente la misma validación.
 */
function requireRenovacionesEscritura(): array
{
    requireRenovacionesAccess(true);

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        renovacionesDenegar(405, 'Método no permitido.', true);
    }

    validarCSRF();

    return renovacionesActor();
}

/**
 * Identidad de quien está actuando, tomada de la sesión ya validada contra la BD.
 * Se guarda como SNAPSHOT (id + nombre + rol) en cada registro/acción: así la
 * trazabilidad sobrevive aunque el usuario se elimine, cambie de rol, o su id
 * sea reciclado por AUTO_INCREMENT (caso ya documentado en includes/session.php).
 *
 * Solo debe llamarse DESPUÉS de requireRenovacionesAccess()/Escritura().
 *
 * @return array{id:int, nombre:string, rol:string, rol_etiqueta:string}
 */
function renovacionesActor(): array
{
    $id     = (int) ($_SESSION['superadmin_id'] ?? 0);
    $nombre = trim((string) ($_SESSION['superadmin_username'] ?? ''));
    $rol    = (string) ($_SESSION['superadmin_rol'] ?? '');

    if ($id <= 0 || $nombre === '' || !in_array($rol, ROLES_RENOVACIONES, true)) {
        // Falla cerrada: jamás se registra una acción sin un actor identificable.
        throw new RuntimeException('Actor no válido para el módulo de renovaciones.');
    }

    return [
        'id'           => $id,
        'nombre'       => $nombre,
        'rol'          => $rol,
        'rol_etiqueta' => etiquetaRolTalentoHumano($rol),
    ];
}
