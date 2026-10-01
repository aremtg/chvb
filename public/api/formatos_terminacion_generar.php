<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/FormatoModel.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';

header('Content-Type: application/json; charset=utf-8');

requireSuperAdmin();

if (!in_array($_SESSION['superadmin_rol'] ?? '', ['superadmin_talento_humano', 'auxiliar_talento_humano'], true)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No tienes permiso para usar Formatos.'], JSON_UNESCAPED_UNICODE);
    exit;
}

validarCSRF();

function af02JsonError(string $message, int $status = 400): never
{
    http_response_code($status);
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function af02Fecha(string $value, string $campo): DateTimeImmutable
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    $errors = DateTimeImmutable::getLastErrors();
    $hasErrors = is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0);

    if (!$date || $hasErrors || $date->format('Y-m-d') !== $value) {
        throw new InvalidArgumentException("Fecha inválida en {$campo}.");
    }

    return $date;
}

function af02Sexo(string $sexo): string
{
    $sexo = mb_strtolower(trim($sexo), 'UTF-8');
    return match (true) {
        in_array($sexo, ['f', 'femenino', 'femenina', 'mujer'], true) => 'Señora',
        in_array($sexo, ['m', 'masculino', 'hombre'], true) => 'Señor',
        default => '',
    };
}

function af02TipoContrato(string $tipo): string
{
    $tipo = trim(preg_replace('/\s+/u', ' ', $tipo));
    return $tipo !== '' ? mb_strtolower($tipo, 'UTF-8') : '';
}

function af02NombreArchivo(string $nombre, string $cedula): string
{
    $nombre = mb_strtoupper(trim($nombre), 'UTF-8');
    $nombre = preg_replace('/[\\\/:*?"<>|]/u', '', $nombre);
    $nombre = trim(preg_replace('/\s+/u', ' ', $nombre));
    $cedula = preg_replace('/[^0-9A-Za-z.-]/', '', $cedula);
    return "AF-FT-02 NOTIFICACION DE TERMINACION CONTRATO {$nombre}_{$cedula}.docx";
}

function af02FinPorMeses(string $inicio, int $meses): string
{
    if ($meses < 1 || $meses > 120) {
        throw new InvalidArgumentException('La duración debe estar entre 1 y 120 meses.');
    }

    $base = af02Fecha($inicio, 'inicio de renovación');
    $dia = (int)$base->format('d');
    $objetivo = $base->modify('first day of this month')->modify("+{$meses} months");
    $ultimoDia = (int)$objetivo->format('t');
    $objetivo = $objetivo->setDate(
        (int)$objetivo->format('Y'),
        (int)$objetivo->format('m'),
        min($dia, $ultimoDia)
    );

    return $objetivo->modify('-1 day')->format('Y-m-d');
}

/**
 * Duración real entre dos fechas como [meses completos, días sobrantes].
 * Es la inversa exacta de af02FinPorMeses(); permite comparar periodos aunque el fin se haya editado a mano.
 */
function af02Duracion(string $inicio, string $fin): array
{
    $m = 0;
    while ($m < 120 && af02FinPorMeses($inicio, $m + 1) <= $fin) $m++;

    $base = $m === 0
        ? (new DateTimeImmutable($inicio))->modify('-1 day')
        : new DateTimeImmutable(af02FinPorMeses($inicio, $m));

    return [$m, (int)$base->diff(new DateTimeImmutable($fin))->days];
}

function af02CompararDuracion(array $a, array $b): int
{
    return ($a[0] <=> $b[0]) ?: ($a[1] <=> $b[1]);
}

function af02FechaLarga(string $fecha): string
{
    return FormatoModel::fechaLarga($fecha);
}

/**
 * Reemplaza texto dentro de párrafos Word conservando los estilos de los runs.
 * Word puede dividir un marcador entre varios runs; esta función lo maneja.
 */
function af02ReemplazarTextoEnParrafos(DOMDocument $dom, string $xml, array $reemplazos): string
{
    $dom->preserveWhiteSpace = true;
    if (!@$dom->loadXML($xml)) {
        throw new RuntimeException('La plantilla Word contiene XML inválido.');
    }

    $xp = new DOMXPath($dom);
    $xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

    foreach ($xp->query('//w:p') as $p) {
        $textNodes = [];
        $full = '';

        foreach ($xp->query('.//w:t', $p) as $node) {
            $value = $node->textContent;
            $textNodes[] = [
                'node' => $node,
                'start' => mb_strlen($full, 'UTF-8'),
                'length' => mb_strlen($value, 'UTF-8'),
            ];
            $full .= $value;
        }

        if ($full === '') continue;

        foreach ($reemplazos as $buscar => $reemplazo) {
            $pos = mb_strpos($full, $buscar, 0, 'UTF-8');
            if ($pos === false) continue;

            $fin = $pos + mb_strlen($buscar, 'UTF-8');
            $first = null;
            $last = null;

            foreach ($textNodes as $item) {
                $nodeStart = $item['start'];
                $nodeEnd = $nodeStart + $item['length'];
                if ($nodeEnd > $pos && $nodeStart < $fin) {
                    if ($first === null) $first = $item;
                    $last = $item;
                }
            }

            if ($first === null || $last === null) continue;

            $firstText = $first['node']->textContent;
            $lastText = $last['node']->textContent;
            $firstOffset = max(0, $pos - $first['start']);
            $lastOffset = max(0, $fin - $last['start']);

            if ($first['node'] === $last['node']) {
                $newValue = mb_substr($firstText, 0, $firstOffset, 'UTF-8')
                    . $reemplazo
                    . mb_substr($firstText, $lastOffset, null, 'UTF-8');
                $first['node']->nodeValue = $newValue;
            } else {
                $prefix = mb_substr($firstText, 0, $firstOffset, 'UTF-8');
                $suffix = mb_substr($lastText, $lastOffset, null, 'UTF-8');
                $first['node']->nodeValue = $prefix . $reemplazo;
                $between = false;
                foreach ($textNodes as $item) {
                    if ($item['node'] === $first['node']) {
                        $between = true;
                        continue;
                    }
                    if ($item['node'] === $last['node']) break;
                    if ($between) $item['node']->nodeValue = '';
                }
                $last['node']->nodeValue = $suffix;
            }

            // Recalcular el texto del párrafo para permitir varios marcadores en el mismo párrafo.
            $full = '';
            foreach ($xp->query('.//w:t', $p) as $node) $full .= $node->textContent;
            $textNodes = [];
            $cursor = 0;
            foreach ($xp->query('.//w:t', $p) as $node) {
                $value = $node->textContent;
                $length = mb_strlen($value, 'UTF-8');
                $textNodes[] = [
                    'node' => $node,
                    'start' => $cursor,
                    'length' => $length,
                ];
                $cursor += $length;
            }
        }
    }

    return $dom->saveXML();
}

/** Reemplaza todo el texto visible de un párrafo usando el estilo del primer run. */
function af02EscribirParrafo(DOMDocument $dom, DOMXPath $xp, DOMElement $p, string $texto, bool $negrita = false): void
{
    $ns = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    $runs = $xp->query('./w:r', $p);
    $prototype = $runs->item(0);
    $rPr = $prototype ? $xp->query('./w:rPr', $prototype)->item(0) : null;

    foreach (iterator_to_array($xp->query('./w:r', $p)) as $r) $p->removeChild($r);

    $run = $dom->createElementNS($ns, 'w:r');
    if ($rPr) $run->appendChild($rPr->cloneNode(true));

    if ($negrita) {
        $runPr = $xp->query('./w:rPr', $run)->item(0);
        if (!$runPr) {
            $runPr = $dom->createElementNS($ns, 'w:rPr');
            $run->insertBefore($runPr, $run->firstChild);
        }
        if ($xp->query('./w:b', $runPr)->length === 0) $runPr->appendChild($dom->createElementNS($ns, 'w:b'));
    }

    $t = $dom->createElementNS($ns, 'w:t');
    $t->setAttribute('xml:space', 'preserve');
    $t->appendChild($dom->createTextNode($texto));
    $run->appendChild($t);
    $p->appendChild($run);
}

/**
 * Reemplaza el párrafo de renovaciones por uno por renovación.
 */
function af02InsertarHistorial(DOMDocument $dom, DOMXPath $xp, array $renovaciones): void
{
    foreach ($xp->query('//w:body/w:p') as $p) {
        $texto = '';
        foreach ($xp->query('.//w:t', $p) as $t) $texto .= $t->textContent;
        if (mb_stripos($texto, 'Renovación N°${}:', 0, 'UTF-8') === false) continue;

        $parent = $p->parentNode;
        if (!$renovaciones) {
            $parent->removeChild($p);
            return;
        }

        $prototipo = $p->cloneNode(true);
        $referencia = $p;

        foreach ($renovaciones as $index => $r) {
            $linea = 'Renovación N°' . ($index + 1) . ': del ' . af02FechaLarga($r['inicio']) . ' al ' . af02FechaLarga($r['fin']) . '.';
            $destino = $index === 0 ? $p : $prototipo->cloneNode(true);
            af02EscribirParrafo($dom, $xp, $destino, $linea);
            if ($index > 0) {
                $parent->insertBefore($destino, $referencia->nextSibling);
                $referencia = $destino;
            }
        }
        return;
    }
}

/**
 * Aplica negrita únicamente al texto exacto del nombre y la cédula, sin cambiar fuente/tamaño.
 */
function af02NegritaExacta(DOMDocument $dom, DOMXPath $xp, array $objetivos): void
{
    $ns = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    foreach ($xp->query('//w:r') as $r) {
        $texts = $xp->query('./w:t', $r);
        if ($texts->length !== 1) continue;
        $textNode = $texts->item(0);
        $text = $textNode->textContent;

        foreach ($objetivos as $objetivo) {
            if ($objetivo === '' || mb_strpos($text, $objetivo, 0, 'UTF-8') === false) continue;

            $pos = mb_strpos($text, $objetivo, 0, 'UTF-8');
            $antes = mb_substr($text, 0, $pos, 'UTF-8');
            $despues = mb_substr($text, $pos + mb_strlen($objetivo, 'UTF-8'), null, 'UTF-8');
            $rPr = $xp->query('./w:rPr', $r)->item(0);

            $crearRun = static function (string $value, bool $bold) use ($dom, $rPr, $ns): DOMElement {
                $nuevo = $dom->createElementNS($ns, 'w:r');
                if ($rPr) $nuevo->appendChild($rPr->cloneNode(true));
                if ($bold) {
                    $nuevoPr = null;
                    foreach ($nuevo->childNodes as $child) {
                        if ($child instanceof DOMElement && $child->localName === 'rPr') { $nuevoPr = $child; break; }
                    }
                    if (!$nuevoPr) {
                        $nuevoPr = $dom->createElementNS($ns, 'w:rPr');
                        $nuevo->insertBefore($nuevoPr, $nuevo->firstChild);
                    }
                    if ($nuevoPr->getElementsByTagNameNS($ns, 'b')->length === 0) $nuevoPr->appendChild($dom->createElementNS($ns, 'w:b'));
                }
                $t = $dom->createElementNS($ns, 'w:t');
                $t->setAttribute('xml:space', 'preserve');
                $t->appendChild($dom->createTextNode($value));
                $nuevo->appendChild($t);
                return $nuevo;
            };

            $parent = $r->parentNode;
            if ($antes !== '') $parent->insertBefore($crearRun($antes, false), $r);
            $parent->insertBefore($crearRun($objetivo, true), $r);
            if ($despues !== '') $parent->insertBefore($crearRun($despues, false), $r);
            $parent->removeChild($r);
            break;
        }
    }
}

try {
    $cedula = trim((string)($_POST['cedula'] ?? ''));
    $fechaFin = trim((string)($_POST['fecha_fin'] ?? ''));
    $rawRenovaciones = (string)($_POST['renovaciones'] ?? '[]');
    $renovaciones = json_decode($rawRenovaciones, true, 512, JSON_THROW_ON_ERROR);

    if ($cedula === '') throw new InvalidArgumentException('Selecciona un empleado.');
    if (!is_array($renovaciones)) throw new InvalidArgumentException('La información de renovaciones no es válida.');
    if (count($renovaciones) > 20) throw new InvalidArgumentException('Puedes registrar máximo 20 renovaciones.');

    $empleado = EmpleadoModel::obtenerPorCedula($cedula);
    if (!$empleado) throw new InvalidArgumentException('No se encontró el empleado seleccionado.');

    foreach (['nombre', 'cedula', 'sexo', 'cargo', 'tipo_de_contrato', 'fecha_inicio_contrato', 'fecha_fin_contrato'] as $campo) {
        if (trim((string)($empleado[$campo] ?? '')) === '') {
            throw new InvalidArgumentException("Falta el dato obligatorio en la hoja de vida: {$campo}.");
        }
    }

    $tratamiento = af02Sexo((string)$empleado['sexo']);
    if ($tratamiento === '') throw new InvalidArgumentException('El sexo del empleado no está registrado correctamente.');

    $inicioInicial = trim((string)$empleado['fecha_inicio_contrato']);
    $finInicial = trim((string)$empleado['fecha_fin_contrato']);
    af02Fecha($inicioInicial, 'inicio del contrato inicial');
    af02Fecha($finInicial, 'fin del contrato inicial');
    af02Fecha($fechaFin, 'fecha de terminación');

    $lineasHistorial = [];
    $prevFin = $finInicial;   // RN1 también debe empezar el día posterior al fin del contrato inicial
    $prevDur = null;

    foreach (array_values($renovaciones) as $i => $r) {
        if (!is_array($r)) throw new InvalidArgumentException('La información de RN' . ($i + 1) . ' no es válida.');

        $n = $i + 1;
        $inicio = trim((string)($r['inicio'] ?? ''));
        $fin = trim((string)($r['fin'] ?? ''));
        $meses = filter_var($r['meses'] ?? null, FILTER_VALIDATE_INT);

        if ($meses === false || $meses === null) throw new InvalidArgumentException("La duración de RN{$n} no es válida.");
        af02Fecha($inicio, "inicio de RN{$n}");
        af02Fecha($fin, "fin de RN{$n}");

        if ($meses < 1 || $meses > 120) throw new InvalidArgumentException("La duración de RN{$n} debe estar entre 1 y 120 meses.");
        if (new DateTimeImmutable($fin) < new DateTimeImmutable($inicio)) throw new InvalidArgumentException("La fecha fin de RN{$n} no puede ser anterior a su inicio.");

        $esperado = (new DateTimeImmutable($prevFin))->modify('+1 day')->format('Y-m-d');
        if ($inicio !== $esperado) {
            $origen = $n === 1 ? 'el contrato inicial' : 'RN' . ($n - 1);
            throw new InvalidArgumentException("RN{$n} debe iniciar el día {$esperado}, inmediatamente después de {$origen}.");
        }

        // Reglas de duración, evaluadas sobre las fechas reales (no solo sobre el campo "meses").
        $dur = af02Duracion($inicio, $fin);
        if ($dur[0] < 1) throw new InvalidArgumentException("La duración de RN{$n} debe ser de mínimo 1 mes.");
        if ($n >= 4 && af02CompararDuracion($dur, [12, 0]) < 0) throw new InvalidArgumentException("La renovación RN{$n} debe ser de mínimo 12 meses.");
        if ($prevDur !== null && af02CompararDuracion($dur, $prevDur) < 0) {
            throw new InvalidArgumentException("La duración de RN{$n} no puede ser menor a la de RN" . ($n - 1) . '.');
        }

        $prevFin = $fin;
        $prevDur = $dur;
        $lineasHistorial[] = [
            'inicio' => $inicio,
            'fin' => $fin,
            'meses' => $meses,
        ];
    }

    if ($fechaFin !== $prevFin) {
        throw new InvalidArgumentException("La fecha de terminación debe coincidir con el fin del último contrato ({$prevFin}).");
    }

    $plantilla = __DIR__ . '/../../uploads/plantillas/AF-FT-02 NOTIFICACION TERMINACION CONTRATO.docx';
    if (!is_file($plantilla)) throw new RuntimeException('No se encontró la plantilla AF-FT-02 en uploads/plantillas/.');
    if (!class_exists('ZipArchive')) throw new RuntimeException('La extensión PHP ZipArchive no está habilitada.');
    if (!class_exists('DOMDocument')) throw new RuntimeException('La extensión PHP DOM no está habilitada.');

    $generados = __DIR__ . '/../../uploads/generados';
    if (!is_dir($generados) && !mkdir($generados, 0775, true) && !is_dir($generados)) {
        throw new RuntimeException('No se pudo crear la carpeta de formatos generados.');
    }
    if (!is_writable($generados)) throw new RuntimeException('La carpeta de formatos generados no tiene permisos de escritura.');

    $nombre = mb_strtoupper(trim((string)$empleado['nombre']), 'UTF-8');
    $cedulaFormateada = FormatoModel::formatearCedula((string)$empleado['cedula']);
    $tipoContrato = af02TipoContrato((string)$empleado['tipo_de_contrato']);
    $archivo = af02NombreArchivo($nombre, $cedulaFormateada);
    $salida = $generados . DIRECTORY_SEPARATOR . $archivo;

    // Nunca escribimos directamente sobre la plantilla. Primero copiamos un archivo independiente.
    if (!copy($plantilla, $salida)) throw new RuntimeException('No fue posible crear una copia de la plantilla.');

    $zip = new ZipArchive();
    if ($zip->open($salida) !== true) {
        @unlink($salida);
        throw new RuntimeException('No fue posible abrir el documento Word generado.');
    }

    $documentXml = $zip->getFromName('word/document.xml');
    if ($documentXml === false) {
        $zip->close();
        @unlink($salida);
        throw new RuntimeException('La plantilla no contiene word/document.xml.');
    }

    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = true;
    $dom->loadXML($documentXml);
    $xp = new DOMXPath($dom);
    $xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

    // Párrafos estructurales que dependen de la información variable.
    foreach ($xp->query('//w:body/w:p') as $p) {
        $texto = '';
        foreach ($xp->query('.//w:t', $p) as $t) $texto .= $t->textContent;

        if (str_contains($texto, 'Contrato inicial:')) {
            af02EscribirParrafo($dom, $xp, $p, 'Contrato inicial: del ' . af02FechaLarga($inicioInicial) . ' al ' . af02FechaLarga($finInicial) . '.');
        }
    }

    $reemplazos = [
        '{fecha actual día mes año}' => af02FechaLarga(date('Y-m-d')),
        'Señor o señora {SEGÚN SEXO}' => $tratamiento,
        '{Nombre cmlpeto mayuscua}' => $nombre,
        '{Cargo}' => trim((string)$empleado['cargo']),
        'Ciudad' => 'Yopal',
        '{nombre}' => $nombre,
        '{cedula}' => $cedulaFormateada,
        '${tipo contrato}' => $tipoContrato,
        '{tipo contrato}' => $tipoContrato,
        '{fecha fin}' => af02FechaLarga($fechaFin),
        'de 2026' => 'de ' . (new DateTimeImmutable($fechaFin))->format('Y'),
        'NOMBRE Y APELLIDOS' => $nombre,
    ];

    $documentXml = af02ReemplazarTextoEnParrafos($dom, $documentXml, $reemplazos);

    // Volvemos a cargar el XML ya reemplazado para trabajar sobre la estructura final.
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = true;
    if (!@$dom->loadXML($documentXml)) throw new RuntimeException('No fue posible preparar el contenido del documento Word.');
    $xp = new DOMXPath($dom);
    $xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

    af02InsertarHistorial($dom, $xp, $lineasHistorial);
    af02NegritaExacta($dom, $xp, [$nombre, $cedulaFormateada]);

    $zip->addFromString('word/document.xml', $dom->saveXML());
    if (!$zip->close()) {
        @unlink($salida);
        throw new RuntimeException('No fue posible finalizar el archivo Word.');
    }

    if (!is_file($salida) || filesize($salida) < 1000) {
        @unlink($salida);
        throw new RuntimeException('El Word generado quedó incompleto.');
    }

    echo json_encode([
        'ok' => true,
        'archivo' => $archivo,
        'url' => './api/formato_archivo.php?f=' . rawurlencode($archivo) . '&accion=descargar'
    ], JSON_UNESCAPED_UNICODE);
} catch (JsonException $e) {
    af02JsonError('Los datos de renovaciones no tienen un formato JSON válido.');
} catch (Throwable $e) {
    error_log('AF-FT-02: ' . $e->getMessage());
    af02JsonError($e->getMessage(), 400);
}