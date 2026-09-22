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
    echo json_encode(['ok' => false, 'error' => 'No tienes permiso para usar Formatos.'], JSON_UNESCAPED_UNICODE);
    exit;
}

validarCSRF();

function mesActualEspanol(int $numero): string
{
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    return $meses[$numero] ?? '';
}

/**
 * Fuerza TODO el texto del DOCX a Arial Narrow 12.
 * Se aplica a document.xml, tablas, headers, footers, notas, etc.
 */
function forzarArialNarrow12(string $docx): void
{
    $zip = new ZipArchive();

    if ($zip->open($docx) !== true) {
        throw new RuntimeException('No fue posible abrir el Word generado para aplicar Arial Narrow 12.');
    }

    $wNs = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $nombre = $zip->getNameIndex($i);

        if (!preg_match('#^word/.*\.xml$#i', $nombre)) {
            continue;
        }

        $xml = $zip->getFromName($nombre);
        if ($xml === false) {
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

        foreach ($xpath->query('//w:r') as $run) {
            $rPr = null;

            foreach ($run->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE && $child->localName === 'rPr') {
                    $rPr = $child;
                    break;
                }
            }

            if (!$rPr) {
                $rPr = $dom->createElementNS($wNs, 'w:rPr');
                $run->insertBefore($rPr, $run->firstChild);
            }

            foreach (iterator_to_array($rPr->childNodes) as $child) {
                if (
                    $child->nodeType === XML_ELEMENT_NODE &&
                    in_array($child->localName, ['rFonts', 'sz', 'szCs'], true)
                ) {
                    $rPr->removeChild($child);
                }
            }

            $fonts = $dom->createElementNS($wNs, 'w:rFonts');
            $fonts->setAttributeNS($wNs, 'w:ascii', 'Arial Narrow');
            $fonts->setAttributeNS($wNs, 'w:hAnsi', 'Arial Narrow');
            $fonts->setAttributeNS($wNs, 'w:eastAsia', 'Arial Narrow');
            $fonts->setAttributeNS($wNs, 'w:cs', 'Arial Narrow');
            $rPr->appendChild($fonts);

            // Word usa half-points: 12 pt = 24.
            $sz = $dom->createElementNS($wNs, 'w:sz');
            $sz->setAttributeNS($wNs, 'w:val', '24');
            $rPr->appendChild($sz);

            $szCs = $dom->createElementNS($wNs, 'w:szCs');
            $szCs->setAttributeNS($wNs, 'w:val', '24');
            $rPr->appendChild($szCs);
        }

        if ($nombre === 'word/styles.xml') {
            $rPr = $xpath->query('//w:docDefaults/w:rPrDefault/w:rPr')->item(0);

            if (!$rPr) {
                $docDefaults = $xpath->query('//w:docDefaults')->item(0);

                if (!$docDefaults) {
                    $docDefaults = $dom->createElementNS($wNs, 'w:docDefaults');
                    $dom->documentElement->insertBefore($docDefaults, $dom->documentElement->firstChild);
                }

                $rPrDefault = $dom->createElementNS($wNs, 'w:rPrDefault');
                $rPr = $dom->createElementNS($wNs, 'w:rPr');
                $rPrDefault->appendChild($rPr);
                $docDefaults->appendChild($rPrDefault);
            }

            foreach (iterator_to_array($rPr->childNodes) as $child) {
                if (
                    $child->nodeType === XML_ELEMENT_NODE &&
                    in_array($child->localName, ['rFonts', 'sz', 'szCs'], true)
                ) {
                    $rPr->removeChild($child);
                }
            }

            $fonts = $dom->createElementNS($wNs, 'w:rFonts');
            $fonts->setAttributeNS($wNs, 'w:ascii', 'Arial Narrow');
            $fonts->setAttributeNS($wNs, 'w:hAnsi', 'Arial Narrow');
            $fonts->setAttributeNS($wNs, 'w:eastAsia', 'Arial Narrow');
            $fonts->setAttributeNS($wNs, 'w:cs', 'Arial Narrow');
            $rPr->appendChild($fonts);

            $sz = $dom->createElementNS($wNs, 'w:sz');
            $sz->setAttributeNS($wNs, 'w:val', '24');
            $rPr->appendChild($sz);

            $szCs = $dom->createElementNS($wNs, 'w:szCs');
            $szCs->setAttributeNS($wNs, 'w:val', '24');
            $rPr->appendChild($szCs);
        }

        $zip->addFromString($nombre, $dom->saveXML());
    }

    $zip->close();
}

try {
    $cedula = preg_replace('/\D+/', '', trim($_POST['cedula'] ?? ''));

    if ($cedula === '') {
        throw new InvalidArgumentException('Escribe la cédula del empleado.');
    }

    $empleado = EmpleadoModel::obtenerPorCedula($cedula);

    if (!$empleado) {
        throw new InvalidArgumentException('No se encontró un empleado con esa cédula.');
    }

    $nombre = trim((string)($empleado['nombre'] ?? ''));
    $cedulaEmpleado = trim((string)($empleado['cedula'] ?? ''));

    if ($nombre === '') {
        throw new InvalidArgumentException('El empleado no tiene nombre registrado.');
    }

    if ($cedulaEmpleado === '') {
        throw new InvalidArgumentException('El empleado no tiene cédula registrada.');
    }

    $plantilla = __DIR__ . '/../../uploads/plantillas/GH-FT-24-OTROSI.docx';

    if (!is_file($plantilla)) {
        throw new RuntimeException(
            'No se encontró la plantilla GH-FT-24-OTROSI.docx en uploads/plantillas/.'
        );
    }

    $generados = __DIR__ . '/../../uploads/generados';

    if (!is_dir($generados) && !mkdir($generados, 0775, true) && !is_dir($generados)) {
        throw new RuntimeException('No fue posible crear uploads/generados/.');
    }

    if (!class_exists('PhpOffice\\PhpWord\\TemplateProcessor')) {
        throw new RuntimeException(
            'PHPWord no está disponible. Ejecuta composer install en el proyecto.'
        );
    }

    $hoy = new DateTimeImmutable('now', new DateTimeZone('America/Bogota'));

    $valores = [
        'nombre_empleado' => $nombre,
        'cedula' => FormatoModel::formatearCedula($cedulaEmpleado),
        'dia_actual' => $hoy->format('j'),
        'mes_actual' => mesActualEspanol((int)$hoy->format('n')),
        'anio_actual' => $hoy->format('Y'),
    ];

    $nombreSeguro = FormatoModel::nombreSeguro($nombre);

    $archivo = 'OTROSI_' . $cedulaEmpleado . '_' .
        $hoy->format('Ymd_His') . '_' . $nombreSeguro . '.docx';

    $salida = $generados . DIRECTORY_SEPARATOR . $archivo;

    $processor = new \PhpOffice\PhpWord\TemplateProcessor($plantilla);
    $processor->setValues($valores);
    $processor->saveAs($salida);

    forzarArialNarrow12($salida);

    echo json_encode([
        'ok' => true,
        'archivo' => $archivo,
        'nombre' => $archivo,
        'url' => './api/formato_archivo.php?f=' .
            rawurlencode($archivo) . '&accion=descargar',
        'empleado' => [
            'nombre' => $nombre,
            'cedula' => $cedulaEmpleado,
        ],
        'fecha' => [
            'dia' => $valores['dia_actual'],
            'mes' => $valores['mes_actual'],
            'anio' => $valores['anio_actual'],
        ],
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
