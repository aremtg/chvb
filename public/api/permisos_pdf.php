<?php
// public/api/permisos_pdf.php
// Descarga en PDF de permisos (media hoja oficio cada uno). Solo lectura:
// el PDF se arma en memoria y se envía al navegador; no se guarda en BD ni en disco.
//
// POST:
//   csrf_token
//   id            (opcional) un solo permiso
//   ids[]         (opcional) selección manual de permisos (varios ids); tiene prioridad sobre los filtros
//   cedula_empleado, cedula_jefe (nombre o cédula), tipo_permiso, estado
//                 (opcionales) los MISMOS filtros de la pantalla; sin filtros = todos
//   dos_por_hoja  (opcional) 1 = dos permisos por hoja oficio
//
// Roles: los mismos que pueden ver Permisos (superadmin, auxiliar de TH y teniente).
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';
require_once __DIR__ . '/../../src/helpers/PermisoPdf.php';
require_once __DIR__ . '/../../src/models/FormatoPermisoModel.php';

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

// Solicitante y jefe: texto tal cual (nombre con varias palabras o cédula con/sin puntos); lo interpreta Busqueda en el modelo.
$textoFiltro = static fn($v): string => trim((string)$v);

$idsSeleccionados = array_values(array_unique(array_filter(
    array_map('intval', (array)($_POST['ids'] ?? [])),
    static fn(int $v): bool => $v > 0
)));
// Tope antes de consultar: evita armar un IN (...) gigante con miles de ids.
if (count($idsSeleccionados) > PermisoPdf::MAX_PERMISOS) {
    pdfError(422, 'Marcaste ' . count($idsSeleccionados) . ' permisos; el máximo por descarga es ' . PermisoPdf::MAX_PERMISOS . '.');
}

$filtros = [
    'permiso_id'      => (int)($_POST['id'] ?? 0),
    'permiso_ids'     => $idsSeleccionados,
    'cedula_jefe'     => $textoFiltro($_POST['cedula_jefe'] ?? ''),
    'cedula_empleado' => $textoFiltro($_POST['cedula_empleado'] ?? ''),
    'tipo_permiso'    => trim((string)($_POST['tipo_permiso'] ?? '')),
    'estado'          => trim((string)($_POST['estado'] ?? '')),
];
$dosPorHoja = ($_POST['dos_por_hoja'] ?? '') === '1';

$permisos = PermisoModel::listarParaTalentoHumano($filtros);
if (!$permisos) pdfError(404, 'No hay permisos que coincidan para exportar.');
if (count($permisos) > PermisoPdf::MAX_PERMISOS) {
    pdfError(422, 'Son ' . count($permisos) . ' permisos; el máximo por descarga es ' . PermisoPdf::MAX_PERMISOS . '. Acota con los filtros (solicitante, jefe, tipo o estado) o marca menos permisos e inténtalo de nuevo.');
}
$permisos = PermisoModel::enriquecerParaPdf($permisos);

@set_time_limit(300);
@ini_set('memory_limit', '512M');

try {
    $pdf = PermisoPdf::generar($permisos, $dosPorHoja, (string)($_SESSION['superadmin_username'] ?? ''), null, FormatoPermisoModel::obtener());
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
