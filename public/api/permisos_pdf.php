<?php
// public/api/permisos_pdf.php
// Descarga en PDF de permisos (media hoja oficio cada uno). Solo lectura:
// el PDF se arma en memoria y se envía al navegador; no se guarda en BD ni en disco.
//
// POST:
//   csrf_token
//   id            (opcional) un solo permiso
//   cedula_empleado, cedula_jefe, tipo_permiso, estado, fecha_desde, fecha_hasta, orden_horas
//                 (opcionales) los MISMOS filtros de la pantalla; sin filtros = todos
//   dos_por_hoja  (opcional) 1 = dos permisos por hoja oficio
//
// Roles: los mismos que pueden ver Permisos (superadmin, auxiliar de TH y teniente).
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';
require_once __DIR__ . '/../../src/helpers/PermisoPdf.php';

requireSuperAdmin();
validarCSRF();

function pdfError(int $codigo, string $mensaje): never {
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $mensaje], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!class_exists(\Dompdf\Dompdf::class)) {
    pdfError(500, 'Falta instalar la librería de PDF (composer require dompdf/dompdf).');
}

$soloDigitos = static fn($v): string => preg_replace('/\D+/', '', (string)$v) ?? '';

$filtros = [
    'permiso_id'      => (int)($_POST['id'] ?? 0),
    'fecha_desde'     => trim((string)($_POST['fecha_desde'] ?? '')),
    'fecha_hasta'     => trim((string)($_POST['fecha_hasta'] ?? '')),
    'cedula_jefe'     => $soloDigitos($_POST['cedula_jefe'] ?? ''),
    'cedula_empleado' => $soloDigitos($_POST['cedula_empleado'] ?? ''),
    'tipo_permiso'    => trim((string)($_POST['tipo_permiso'] ?? '')),
    'estado'          => trim((string)($_POST['estado'] ?? '')),
    'orden_horas'     => trim((string)($_POST['orden_horas'] ?? '')),
];
$dosPorHoja = ($_POST['dos_por_hoja'] ?? '') === '1';

$permisos = PermisoModel::listarParaTalentoHumano($filtros);
if (!$permisos) pdfError(404, 'No hay permisos que coincidan para exportar.');
if (count($permisos) > PermisoPdf::MAX_PERMISOS) {
    pdfError(422, 'Son ' . count($permisos) . ' permisos; el máximo por descarga es ' . PermisoPdf::MAX_PERMISOS . '. Acota el rango de fechas u otro filtro e inténtalo de nuevo.');
}
$permisos = PermisoModel::enriquecerParaPdf($permisos);

@set_time_limit(300);
@ini_set('memory_limit', '512M');

try {
    $pdf = PermisoPdf::generar($permisos, $dosPorHoja, (string)($_SESSION['superadmin_username'] ?? ''));
} catch (Throwable $ex) {
    error_log('permisos_pdf: ' . $ex->getMessage());
    pdfError(500, 'No se pudo generar el PDF.');
}

$nombre = count($permisos) === 1 && !empty($permisos[0]['consecutivo'])
    ? 'permiso_' . preg_replace('/[^A-Za-z0-9_-]+/', '-', $permisos[0]['consecutivo'])
    : 'permisos_' . date('Y-m-d_Hi');

if (ob_get_level()) ob_end_clean();
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $nombre . '.pdf"');
header('Content-Length: ' . strlen($pdf));
header('Cache-Control: private, no-store');
header('X-Content-Type-Options: nosniff');
echo $pdf;
