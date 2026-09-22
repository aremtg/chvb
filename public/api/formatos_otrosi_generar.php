<?php
declare(strict_types=1);

date_default_timezone_set('America/Bogota');

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/FormatoModel.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';

header('Content-Type: application/json; charset=utf-8');

requireSuperAdmin();

$rolesFormatos = ['superadmin_talento_humano', 'auxiliar_talento_humano'];

if (!in_array($_SESSION['superadmin_rol'] ?? '', $rolesFormatos, true)) {
    http_response_code(403);

    echo json_encode([
        'ok' => false,
        'error' => 'No tienes permiso para usar Formatos.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

validarCSRF();

/**
 * Devuelve el nombre del mes en español.
 */
function mesActualEspanol(int $numero): string
{
    $meses = [
        1  => 'Enero',
        2  => 'Febrero',
        3  => 'Marzo',
        4  => 'Abril',
        5  => 'Mayo',
        6  => 'Junio',
        7  => 'Julio',
        8  => 'Agosto',
        9  => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    ];

    return $meses[$numero] ?? '';
}

/**
 * Formatea una cédula colombiana con puntos cada 3 cifras.
 *
 * Ejemplos:
 * 1123456789  -> 1.123.456.789
 * 800195217   -> 800.195.217
 */
function formatearCedulaColombia(string $cedula): string
{
    $cedula = preg_replace('/\D+/', '', trim($cedula));

    if ($cedula === '') {
        return '';
    }

    return number_format((int)$cedula, 0, '', '.');
}

/**
 * Limpia caracteres que no son válidos para nombres de archivos de Windows.
 */
function limpiarNombreArchivo(string $nombre): string
{
    $nombre = trim($nombre);

    // Caracteres prohibidos por Windows:
    // \ / : * ? " < > |
    $nombre = preg_replace('/[\\\\\/:*?"<>|]/u', '', $nombre);

    // Evita espacios excesivos.
    $nombre = preg_replace('/\s+/u', ' ', $nombre);

    // Evita puntos o espacios al final del nombre.
    $nombre = rtrim($nombre, ". ");

    return $nombre;
}

/**
 * Aplica Arial Narrow 12 y negrita a los runs
 * que contienen alguno de los valores indicados.
 *
 * IMPORTANTE:
 * No modifica el resto del formato del documento.
 */
function aplicarFormatoEspecialDocx(
    string $docx,
    string $nombreEmpleado,
    string $cedulaFormateada,
    string $dia,
    string $mes,
    string $anio
): void {
    $zip = new ZipArchive();

    if ($zip->open($docx) !== true) {
        throw new RuntimeException(
            'No fue posible abrir el Word generado para aplicar el formato.'
        );
    }

    $wNs = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    /*
     * Solo procesamos XML que pueda contener texto visible.
     */
    for ($i = 0; $i < $zip->numFiles; $i++) {

        $archivoXml = $zip->getNameIndex($i);

        if (
            !preg_match(
                '#^word/(document|header\d+|footer\d+|footnotes|endnotes)\.xml$#i',
                $archivoXml
            )
        ) {
            continue;
        }

        $xml = $zip->getFromName($archivoXml);

        if ($xml === false || trim($xml) === '') {
            continue;
        }

        $dom = new DOMDocument();

        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = false;

        if (!@$dom->loadXML($xml)) {
            continue;
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', $wNs);

        /*
         * Recorremos todos los runs de Word.
         */
        foreach ($xpath->query('//w:r') as $run) {

            $textos = $xpath->query('./w:t', $run);

            if (!$textos || $textos->length === 0) {
                continue;
            }

            $textoCompleto = '';

            foreach ($textos as $textoNode) {
                $textoCompleto .= $textoNode->textContent;
            }

            if ($textoCompleto === '') {
                continue;
            }

            /*
             * -----------------------------------------
             * NOMBRE
             * -----------------------------------------
             *
             * El nombre siempre:
             * - MAYÚSCULAS
             * - NEGRITA
             */
            if (
                $nombreEmpleado !== '' &&
                mb_stripos($textoCompleto, $nombreEmpleado, 0, 'UTF-8') !== false
            ) {
                aplicarNegritaRun($dom, $run, $xpath, $wNs);
            }

            /*
             * -----------------------------------------
             * CÉDULA
             * -----------------------------------------
             *
             * La cédula:
             * - queda con puntos
             * - queda en negrita
             */
            if (
                $cedulaFormateada !== '' &&
                mb_stripos($textoCompleto, $cedulaFormateada, 0, 'UTF-8') !== false
            ) {
                aplicarNegritaRun($dom, $run, $xpath, $wNs);
            }

            /*
             * -----------------------------------------
             * FECHAS
             * -----------------------------------------
             *
             * Las fechas dinámicas quedan:
             * Arial Narrow
             * 12 pt
             *
             * No modificamos negrita, cursiva, subrayado,
             * alineación ni demás propiedades.
             */
            $esFecha =
                ($dia !== '' && $textoCompleto === $dia) ||
                ($mes !== '' && $textoCompleto === $mes) ||
                ($anio !== '' && $textoCompleto === $anio);

            if ($esFecha) {
                aplicarArialNarrow12Run($dom, $run, $xpath, $wNs);
            }
        }

        $zip->addFromString(
            $archivoXml,
            $dom->saveXML()
        );
    }

    /*
     * Verificamos que el ZIP pueda cerrarse correctamente.
     */
    if (!$zip->close()) {
        throw new RuntimeException(
            'No fue posible finalizar correctamente el archivo Word.'
        );
    }

    /*
     * Comprobación final de que el DOCX sigue siendo un ZIP válido.
     */
    $verificacion = new ZipArchive();

    if ($verificacion->open($docx) !== true) {
        throw new RuntimeException(
            'El Word generado quedó corrupto después de aplicar el formato.'
        );
    }

    $verificacion->close();
}

/**
 * Agrega negrita a un run sin eliminar los demás estilos.
 */
function aplicarNegritaRun(
    DOMDocument $dom,
    DOMElement $run,
    DOMXPath $xpath,
    string $wNs
): void {
    $rPr = $xpath->query('./w:rPr', $run)->item(0);

    if (!$rPr) {
        $rPr = $dom->createElementNS($wNs, 'w:rPr');

        if ($run->firstChild) {
            $run->insertBefore($rPr, $run->firstChild);
        } else {
            $run->appendChild($rPr);
        }
    }

    $bold = $xpath->query('./w:b', $rPr)->item(0);

    if (!$bold) {
        $bold = $dom->createElementNS($wNs, 'w:b');
        $rPr->appendChild($bold);
    }

    $bold->setAttributeNS($wNs, 'w:val', '1');
}

/**
 * Aplica Arial Narrow 12 a un run sin destruir
 * el resto de sus propiedades.
 */
function aplicarArialNarrow12Run(
    DOMDocument $dom,
    DOMElement $run,
    DOMXPath $xpath,
    string $wNs
): void {
    $rPr = $xpath->query('./w:rPr', $run)->item(0);

    if (!$rPr) {
        $rPr = $dom->createElementNS($wNs, 'w:rPr');

        if ($run->firstChild) {
            $run->insertBefore($rPr, $run->firstChild);
        } else {
            $run->appendChild($rPr);
        }
    }

    /*
     * Fuente.
     */
    $rFonts = $xpath->query('./w:rFonts', $rPr)->item(0);

    if (!$rFonts) {
        $rFonts = $dom->createElementNS($wNs, 'w:rFonts');
        $rPr->appendChild($rFonts);
    }

    $rFonts->setAttributeNS($wNs, 'w:ascii', 'Arial Narrow');
    $rFonts->setAttributeNS($wNs, 'w:hAnsi', 'Arial Narrow');
    $rFonts->setAttributeNS($wNs, 'w:eastAsia', 'Arial Narrow');
    $rFonts->setAttributeNS($wNs, 'w:cs', 'Arial Narrow');

    /*
     * Tamaño: Word utiliza half-points.
     * 12 pt = 24.
     */
    $sz = $xpath->query('./w:sz', $rPr)->item(0);

    if (!$sz) {
        $sz = $dom->createElementNS($wNs, 'w:sz');
        $rPr->appendChild($sz);
    }

    $sz->setAttributeNS($wNs, 'w:val', '24');

    /*
     * Tamaño para caracteres complejos.
     */
    $szCs = $xpath->query('./w:szCs', $rPr)->item(0);

    if (!$szCs) {
        $szCs = $dom->createElementNS($wNs, 'w:szCs');
        $rPr->appendChild($szCs);
    }

    $szCs->setAttributeNS($wNs, 'w:val', '24');
}

try {

    /*
     * -----------------------------------------
     * 1. OBTENER CÉDULA
     * -----------------------------------------
     */
    $cedula = preg_replace(
        '/\D+/',
        '',
        trim($_POST['cedula'] ?? '')
    );

    if ($cedula === '') {
        throw new InvalidArgumentException(
            'Escribe la cédula del empleado.'
        );
    }

    /*
     * -----------------------------------------
     * 2. BUSCAR EMPLEADO
     * -----------------------------------------
     */
    $empleado = EmpleadoModel::obtenerPorCedula($cedula);

    if (!$empleado) {
        throw new InvalidArgumentException(
            'No se encontró un empleado con esa cédula.'
        );
    }

    $nombreOriginal = trim(
        (string)($empleado['nombre'] ?? '')
    );

    $cedulaEmpleado = preg_replace(
        '/\D+/',
        '',
        trim((string)($empleado['cedula'] ?? ''))
    );

    if ($nombreOriginal === '') {
        throw new InvalidArgumentException(
            'El empleado no tiene nombre registrado.'
        );
    }

    if ($cedulaEmpleado === '') {
        throw new InvalidArgumentException(
            'El empleado no tiene cédula registrada.'
        );
    }

    /*
     * -----------------------------------------
     * 3. FORMATO DEL NOMBRE
     * -----------------------------------------
     *
     * mb_strtoupper permite manejar correctamente
     * tildes y Ñ.
     */
    $nombre = mb_strtoupper(
        $nombreOriginal,
        'UTF-8'
    );

    /*
     * -----------------------------------------
     * 4. FORMATO DE CÉDULA
     * -----------------------------------------
     */
    $cedulaFormateada = formatearCedulaColombia(
        $cedulaEmpleado
    );

    /*
     * -----------------------------------------
     * 5. PLANTILLA
     * -----------------------------------------
     */
    $plantilla = __DIR__ .
        '/../../uploads/plantillas/GH-FT-24-OTROSI.docx';

    if (!is_file($plantilla)) {
        throw new RuntimeException(
            'No se encontró la plantilla GH-FT-24-OTROSI.docx en uploads/plantillas/.'
        );
    }

    /*
     * -----------------------------------------
     * 6. CARPETA DE SALIDA
     * -----------------------------------------
     */
    $generados = __DIR__ .
        '/../../uploads/generados';

    if (
        !is_dir($generados) &&
        !mkdir($generados, 0775, true) &&
        !is_dir($generados)
    ) {
        throw new RuntimeException(
            'No fue posible crear uploads/generados/.'
        );
    }

    /*
     * -----------------------------------------
     * 7. PHPWORD
     * -----------------------------------------
     */
    if (
        !class_exists(
            'PhpOffice\\PhpWord\\TemplateProcessor'
        )
    ) {
        throw new RuntimeException(
            'PHPWord no está disponible. Ejecuta composer install en el proyecto.'
        );
    }

    /*
     * -----------------------------------------
     * 8. FECHA ACTUAL
     * -----------------------------------------
     */
    $hoy = new DateTimeImmutable(
        'now',
        new DateTimeZone('America/Bogota')
    );

    $diaActual = $hoy->format('j');

    $mesActual = mesActualEspanol(
        (int)$hoy->format('n')
    );

    $anioActual = $hoy->format('Y');

    /*
     * -----------------------------------------
     * 9. VALORES PARA EL WORD
     * -----------------------------------------
     */
    $valores = [
        'nombre_empleado' => $nombre,
        'cedula'          => $cedulaFormateada,
        'dia_actual'      => $diaActual,
        'mes_actual'      => $mesActual,
        'anio_actual'     => $anioActual,
    ];

    /*
     * -----------------------------------------
     * 10. NOMBRE DEL ARCHIVO
     * -----------------------------------------
     *
     * FORMATO EXACTO:
     *
     * GH-FT-24 OTROSI AL CONTRATO INDIVIDUAL
     * DE TRABAJO NOMBRE DEL EMPLEADO.docx
     */
    $nombreArchivoEmpleado = limpiarNombreArchivo(
        $nombre
    );

    $archivo =
        'GH-FT-24 OTROSI AL CONTRATO INDIVIDUAL DE TRABAJO ' .
        $nombreArchivoEmpleado .
        '.docx';

    $salida =
        $generados .
        DIRECTORY_SEPARATOR .
        $archivo;

    /*
     * -----------------------------------------
     * 11. GENERAR WORD
     * -----------------------------------------
     */
    $processor = new \PhpOffice\PhpWord\TemplateProcessor(
        $plantilla
    );

    /*
     * Reemplazo de variables.
     */
    $processor->setValues($valores);

    /*
     * Guardar documento.
     */
    $processor->saveAs($salida);

    /*
     * -----------------------------------------
     * 12. APLICAR FORMATO ESPECIAL
     * -----------------------------------------
     *
     * Nombre -> MAYÚSCULAS + NEGRITA
     * Cédula -> puntos + NEGRITA
     * Fechas -> Arial Narrow 12
     *
     * Sin destruir el resto del diseño.
     */
    aplicarFormatoEspecialDocx(
        $salida,
        $nombre,
        $cedulaFormateada,
        $diaActual,
        $mesActual,
        $anioActual
    );

    /*
     * -----------------------------------------
     * 13. VERIFICACIÓN FINAL
     * -----------------------------------------
     */
    if (!is_file($salida)) {
        throw new RuntimeException(
            'El archivo Word no fue generado correctamente.'
        );
    }

    if (filesize($salida) <= 0) {
        throw new RuntimeException(
            'El archivo Word generado está vacío.'
        );
    }

    /*
     * -----------------------------------------
     * 14. RESPUESTA JSON
     * -----------------------------------------
     */
    echo json_encode([
        'ok' => true,

        'archivo' => $archivo,

        'nombre' => $archivo,

        'url' =>
            './api/formato_archivo.php?f=' .
            rawurlencode($archivo) .
            '&accion=descargar',

        'empleado' => [
            'nombre' => $nombre,
            'cedula' => $cedulaFormateada,
        ],

        'fecha' => [
            'dia' => $diaActual,
            'mes' => $mesActual,
            'anio' => $anioActual,
        ],

    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {

    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}