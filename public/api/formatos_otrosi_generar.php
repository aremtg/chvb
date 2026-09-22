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

function mesActualEspanol(int $numero): string {
    $meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
    return $meses[$numero] ?? '';
}
function formatearCedulaColombia(string $cedula): string {
    $cedula = preg_replace('/\D+/', '', trim($cedula));
    if ($cedula === '') return '';
    return number_format((int)$cedula, 0, '', '.');
}
function limpiarNombreArchivo(string $nombre): string {
    $nombre = trim($nombre);
    $nombre = preg_replace('/[\\\\\/:*?"<>|]/u', '', $nombre);
    $nombre = preg_replace('/\s+/u', ' ', $nombre);
    return rtrim($nombre, ". ");
}

/**
 * CORREGIDO: Solo nombre y cedula en negrita, partiendo el run
 */
function aplicarFormatoEspecialDocx(string $docx, string $nombreEmpleado, string $cedulaFormateada, string $dia, string $mes, string $anio): void {
    $zip = new ZipArchive();
    if ($zip->open($docx) !== true) throw new RuntimeException('No fue posible abrir el Word');
    $wNs = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $archivoXml = $zip->getNameIndex($i);
        if (!preg_match('#^word/(document|header\d+|footer\d+|footnotes|endnotes)\.xml$#i', $archivoXml)) continue;
        $xml = $zip->getFromName($archivoXml);
        if ($xml === false || trim($xml) === '') continue;
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = false;
        if (!@$dom->loadXML($xml)) continue;
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', $wNs);

        $runs = [];
        foreach ($xpath->query('//w:r') as $r) $runs[] = $r;

        foreach ($runs as $run) {
            if (!$run->parentNode) continue;
            $textos = $xpath->query('./w:t', $run);
            if (!$textos || $textos->length === 0) continue;
            $textoCompleto = '';
            foreach ($textos as $tn) $textoCompleto .= $tn->textContent;
            if ($textoCompleto === '') continue;

            // Fechas -> Arial Narrow 12
            $esFecha = ($dia !== '' && trim($textoCompleto) === $dia) || ($mes !== '' && trim($textoCompleto) === $mes) || ($anio !== '' && trim($textoCompleto) === $anio);
            if ($esFecha) {
                aplicarArialNarrow12Run($dom, $run, $xpath, $wNs);
                continue;
            }

            // NOMBRE y CEDULA - solo ese trozo en negrita
            $encontro = false;
            if ($nombreEmpleado !== '' && mb_stripos($textoCompleto, $nombreEmpleado, 0, 'UTF-8') !== false) {
                dividirRunEnPartes($dom, $run, $xpath, $wNs, $nombreEmpleado, true);
                $encontro = true;
            } else if ($cedulaFormateada !== '' && mb_stripos($textoCompleto, $cedulaFormateada, 0, 'UTF-8') !== false) {
                dividirRunEnPartes($dom, $run, $xpath, $wNs, $cedulaFormateada, true);
                $encontro = true;
            }
        }
        $zip->addFromString($archivoXml, $dom->saveXML());
    }
    $zip->close();
}

function dividirRunEnPartes(DOMDocument $dom, DOMElement $runOriginal, DOMXPath $xpath, string $wNs, string $objetivo, bool $negritaObjetivo): void {
    $parent = $runOriginal->parentNode;
    if (!$parent) return;
    $textoCompleto = '';
    foreach ($xpath->query('./w:t', $runOriginal) as $t) $textoCompleto .= $t->textContent;
    $pos = mb_stripos($textoCompleto, $objetivo, 0, 'UTF-8');
    if ($pos === false) return;
    
    $antes = mb_substr($textoCompleto, 0, $pos, 'UTF-8');
    $match = mb_substr($textoCompleto, $pos, mb_strlen($objetivo, 'UTF-8'), 'UTF-8');
    $despues = mb_substr($textoCompleto, $pos + mb_strlen($objetivo, 'UTF-8'), null, 'UTF-8');
    
    $rPrOriginal = $xpath->query('./w:rPr', $runOriginal)->item(0);
    
    $crearRun = function($texto, $enNegrita) use ($dom, $wNs, $rPrOriginal) {
        if ($texto === '') return null;
        $r = $dom->createElementNS($wNs, 'w:r');
        if ($rPrOriginal) {
            $rPrNuevo = $rPrOriginal->cloneNode(true);
            // Quitar o poner negrita según corresponda
            $xpTmp = new DOMXPath($dom);
            $xpTmp->registerNamespace('w', $wNs);
            // Eliminar b existente
            foreach ($xpTmp->query('.//w:b', $rPrNuevo) as $b) $b->parentNode->removeChild($b);
            foreach ($xpTmp->query('.//w:bCs', $rPrNuevo) as $b) $b->parentNode->removeChild($b);
            if ($enNegrita) {
                $rPrNuevo->appendChild($dom->createElementNS($wNs, 'w:b'));
                $rPrNuevo->appendChild($dom->createElementNS($wNs, 'w:bCs'));
            }
            $r->appendChild($rPrNuevo);
        } else if ($enNegrita) {
            $rPrNuevo = $dom->createElementNS($wNs, 'w:rPr');
            $rPrNuevo->appendChild($dom->createElementNS($wNs, 'w:b'));
            $rPrNuevo->appendChild($dom->createElementNS($wNs, 'w:bCs'));
            $r->appendChild($rPrNuevo);
        }
        $t = $dom->createElementNS($wNs, 'w:t', $texto);
        $t->setAttribute('xml:space', 'preserve');
        $r->appendChild($t);
        return $r;
    };
    
    $runsNuevos = [];
    if ($antes !== '') $runsNuevos[] = $crearRun($antes, false);
    $runsNuevos[] = $crearRun($match, $negritaObjetivo);
    if ($despues !== '') $runsNuevos[] = $crearRun($despues, false);
    
    foreach ($runsNuevos as $nr) {
        if ($nr) $parent->insertBefore($nr, $runOriginal);
    }
    $parent->removeChild($runOriginal);
}

function aplicarNegritaRun(DOMDocument $dom, DOMElement $run, DOMXPath $xpath, string $wNs): void {
    // Obsoleta, mantenida por compatibilidad
    $rPrList = $xpath->query('./w:rPr', $run);
    $rPr = $rPrList->length ? $rPrList->item(0) : null;
    if (!$rPr) {
        $rPr = $dom->createElementNS($wNs, 'w:rPr');
        $run->insertBefore($rPr, $run->firstChild);
    }
    if (!$xpath->query('./w:b', $rPr)->length) {
        $rPr->appendChild($dom->createElementNS($wNs, 'w:b'));
        $rPr->appendChild($dom->createElementNS($wNs, 'w:bCs'));
    }
}

function aplicarArialNarrow12Run(DOMDocument $dom, DOMElement $run, DOMXPath $xpath, string $wNs): void {
    $rPrList = $xpath->query('./w:rPr', $run);
    $rPr = $rPrList->length ? $rPrList->item(0) : null;
    if (!$rPr) {
        $rPr = $dom->createElementNS($wNs, 'w:rPr');
        $run->insertBefore($rPr, $run->firstChild);
    }
    $rFonts = $xpath->query('./w:rFonts', $rPr)->item(0);
    if (!$rFonts) { $rFonts = $dom->createElementNS($wNs, 'w:rFonts'); $rPr->appendChild($rFonts); }
    $rFonts->setAttribute('w:ascii', 'Arial Narrow'); $rFonts->setAttribute('w:hAnsi', 'Arial Narrow'); $rFonts->setAttribute('w:cs', 'Arial Narrow');
    $sz = $xpath->query('./w:sz', $rPr)->item(0); if (!$sz) { $sz = $dom->createElementNS($wNs, 'w:sz'); $rPr->appendChild($sz); } $sz->setAttribute('w:val', '24');
    $szCs = $xpath->query('./w:szCs', $rPr)->item(0); if (!$szCs) { $szCs = $dom->createElementNS($wNs, 'w:szCs'); $rPr->appendChild($szCs); } $szCs->setAttribute('w:val', '24');
}

try {
    $cedulaRaw = $_POST['cedula'] ?? '';
    $cedula = preg_replace('/\D+/', '', trim($cedulaRaw));
    if ($cedula === '') throw new InvalidArgumentException('Cédula no válida.');
    $empleado = EmpleadoModel::obtenerPorCedula($cedula);
    if (!$empleado) throw new InvalidArgumentException('No se encontró un empleado con esa cédula.');
    $nombreOriginal = trim((string)($empleado['nombre'] ?? ''));
    $cedulaEmpleado = preg_replace('/\D+/', '', trim((string)($empleado['cedula'] ?? '')));
    if ($nombreOriginal === '') throw new InvalidArgumentException('El empleado no tiene nombre registrado.');
    if ($cedulaEmpleado === '') throw new InvalidArgumentException('El empleado no tiene cédula registrada.');
    $nombre = mb_strtoupper($nombreOriginal, 'UTF-8');
    $cedulaFormateada = formatearCedulaColombia($cedulaEmpleado);
    $plantilla = __DIR__ . '/../../uploads/plantillas/GH-FT-24-OTROSI.docx';
    if (!is_file($plantilla)) throw new RuntimeException('No se encontró la plantilla GH-FT-24-OTROSI.docx en uploads/plantillas/.');
    $generados = __DIR__ . '/../../uploads/generados';
    if (!is_dir($generados) && !mkdir($generados, 0775, true) && !is_dir($generados)) throw new RuntimeException('No fue posible crear uploads/generados/.');
    if (!class_exists('PhpOffice\\PhpWord\\TemplateProcessor')) throw new RuntimeException('PHPWord no está disponible. Ejecuta composer install en el proyecto.');
    $hoy = new DateTimeImmutable('now', new DateTimeZone('America/Bogota'));
    $diaActual = $hoy->format('j');
    $mesActual = mesActualEspanol((int)$hoy->format('n'));
    $anioActual = $hoy->format('Y');
    $valores = ['nombre_empleado' => $nombre, 'cedula' => $cedulaFormateada, 'dia_actual' => $diaActual, 'mes_actual' => $mesActual, 'anio_actual' => $anioActual];
    $nombreArchivoEmpleado = limpiarNombreArchivo($nombre);
    $archivo = 'GH-FT-24 OTROSI AL CONTRATO INDIVIDUAL DE TRABAJO ' . $nombreArchivoEmpleado . '.docx';
    $salida = $generados . DIRECTORY_SEPARATOR . $archivo;
    $processor = new \PhpOffice\PhpWord\TemplateProcessor($plantilla);
    $processor->setValues($valores);
    $processor->saveAs($salida);
    aplicarFormatoEspecialDocx($salida, $nombre, $cedulaFormateada, $diaActual, $mesActual, $anioActual);
    if (!is_file($salida) || filesize($salida) <= 0) throw new RuntimeException('El archivo Word no fue generado correctamente.');
    echo json_encode(['ok' => true, 'archivo' => $archivo, 'nombre' => $archivo, 'url' => './api/formato_archivo.php?f=' . rawurlencode($archivo) . '&accion=descargar', 'empleado' => ['nombre' => $nombre, 'cedula' => $cedulaFormateada], 'fecha' => ['dia' => $diaActual, 'mes' => $mesActual, 'anio' => $anioActual]], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
