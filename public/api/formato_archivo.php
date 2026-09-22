<?php

require_once __DIR__ . '/../../includes/session.php';

requireSuperAdmin();

if (!in_array(
    $_SESSION['superadmin_rol'] ?? '',
    ['superadmin_talento_humano', 'auxiliar_talento_humano'],
    true
)) {
    http_response_code(403);
    exit('No autorizado.');
}

/*
 * ============================================================
 * 1. OBTENER NOMBRE DEL ARCHIVO
 * ============================================================
 *
 * basename() evita que puedan intentar acceder a rutas
 * externas mediante ../
 */
$archivo = basename((string)($_GET['f'] ?? ''));

if ($archivo === '') {
    http_response_code(400);
    exit('Archivo no especificado.');
}

/*
 * ============================================================
 * 2. VALIDAR NOMBRE DEL ARCHIVO
 * ============================================================
 *
 * Permitimos los archivos generados por el sistema:
 *
 * RENOVACION_....docx
 * OTROSI_....docx
 *
 * Y el nuevo formato de Otrosí:
 *
 * GH-FT-24 OTROSI AL CONTRATO INDIVIDUAL DE TRABAJO NOMBRE.docx
 *
 * Se permite:
 * - letras
 * - números
 * - espacios
 * - puntos
 * - guiones
 * - guion bajo
 * - tildes
 * - Ñ
 */
$esRenovacion = preg_match(
    '/^RENOVACION_[^\/\\\\]+\.docx$/iu',
    $archivo
);

$esOtrosiAnterior = preg_match(
    '/^OTROSI_[^\/\\\\]+\.docx$/iu',
    $archivo
);

$esOtrosiNuevo = preg_match(
    '/^GH-FT-24 OTROSI AL CONTRATO INDIVIDUAL DE TRABAJO [^\/\\\\]+\.docx$/iu',
    $archivo
);

if (!$esRenovacion && !$esOtrosiAnterior && !$esOtrosiNuevo) {
    http_response_code(400);
    exit('Archivo no válido.');
}

/*
 * ============================================================
 * 3. CARPETA DE ARCHIVOS GENERADOS
 * ============================================================
 */
$base = realpath(__DIR__ . '/../../uploads/generados');

if ($base === false || !is_dir($base)) {
    http_response_code(500);
    exit('No se encontró la carpeta de archivos generados.');
}

/*
 * ============================================================
 * 4. RESOLVER ARCHIVO REAL
 * ============================================================
 */
$path = realpath(
    $base . DIRECTORY_SEPARATOR . $archivo
);

if (
    !$path ||
    !is_file($path) ||
    dirname($path) !== $base
) {
    http_response_code(404);
    exit('Archivo no encontrado.');
}

/*
 * ============================================================
 * 5. ACCIÓN
 * ============================================================
 */
$accion = $_GET['accion'] ?? 'descargar';

/*
 * ============================================================
 * 6. ELIMINAR
 * ============================================================
 */
if ($accion === 'eliminar') {

    validarCSRF();

    if (!unlink($path)) {

        http_response_code(500);

        header(
            'Content-Type: application/json; charset=utf-8'
        );

        echo json_encode([
            'ok' => false,
            'error' => 'No se pudo eliminar el archivo.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    header(
        'Content-Type: application/json; charset=utf-8'
    );

    echo json_encode([
        'ok' => true
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

/*
 * ============================================================
 * 7. VER EN EL NAVEGADOR
 * ============================================================
 */
if ($accion === 'ver') {

    header(
        'Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    );

    /*
     * filename:
     * Compatible con navegadores antiguos.
     *
     * filename*:
     * Compatible con UTF-8 y permite tildes/Ñ.
     */
    header(
        'Content-Disposition: inline; filename="' .
        basename($archivo) .
        '"; filename*=UTF-8\'\'' .
        rawurlencode($archivo)
    );

    header(
        'Content-Length: ' . filesize($path)
    );

    header('X-Content-Type-Options: nosniff');

    readfile($path);

    exit;
}

/*
 * ============================================================
 * 8. DESCARGAR
 * ============================================================
 */
if ($accion === 'descargar') {

    header(
        'Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    );

    /*
     * Fuerza la descarga y conserva correctamente:
     * - espacios
     * - tildes
     * - Ñ
     * - nombre completo
     */
    header(
        'Content-Disposition: attachment; filename="' .
        basename($archivo) .
        '"; filename*=UTF-8\'\'' .
        rawurlencode($archivo)
    );

    header(
        'Content-Length: ' . filesize($path)
    );

    header('X-Content-Type-Options: nosniff');

    readfile($path);

    exit;
}

/*
 * ============================================================
 * 9. ACCIÓN NO VÁLIDA
 * ============================================================
 */
http_response_code(400);
exit('Acción no válida.');