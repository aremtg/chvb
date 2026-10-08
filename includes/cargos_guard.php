<?php
// includes/cargos_guard.php
// Único lugar donde se define quién puede gestionar el catálogo de cargos.
//   - Super admin de Talento Humano: crear, leer, editar y eliminar.
//   - Auxiliar de Talento Humano:    crear, leer y editar (NO eliminar).
//   - Teniente: no gestiona cargos.
require_once __DIR__ . '/session.php';

const ROLES_CARGOS = ['superadmin_talento_humano', 'auxiliar_talento_humano'];
const ROL_ELIMINA_CARGOS = 'superadmin_talento_humano';

function puedeGestionarCargos(): bool
{
    return in_array($_SESSION['superadmin_rol'] ?? '', ROLES_CARGOS, true);
}

function puedeEliminarCargos(): bool
{
    return ($_SESSION['superadmin_rol'] ?? '') === ROL_ELIMINA_CARGOS;
}

function cargosRechazar(string $mensaje): void
{
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $mensaje], JSON_UNESCAPED_UNICODE);
    exit;
}

/** Para endpoints de leer / crear / editar cargos (super admin y auxiliar). */
function requireCargosAccess(): void
{
    requireSuperAdmin();
    if (!puedeGestionarCargos()) {
        cargosRechazar('No tienes permiso para gestionar cargos.');
    }
}

/** Para el endpoint de eliminar (solo super admin). */
function requireCargosEliminar(): void
{
    requireCargosAccess();
    if (!puedeEliminarCargos()) {
        cargosRechazar('Solo el super administrador puede eliminar cargos.');
    }
}
