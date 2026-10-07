<?php
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

function formatoSexo(string $sexo): string
{
    $sexo = mb_strtolower(trim($sexo), 'UTF-8');
    if (in_array($sexo, ['f', 'femenino', 'femenina', 'mujer'], true))
        return 'F';
    if (in_array($sexo, ['m', 'masculino', 'hombre'], true))
        return 'M';
    return '';
}

/**
 * Devuelve la duración acumulable del contrato inicial en meses.
 * Se trabaja con fecha fin + 1 día para respetar contratos inclusivos
 * (por ejemplo, 01/01 al 30/06 = 6 meses).
 */
function mesesContratoInicial(DateTime $inicio, DateTime $fin): int
{
    $finMasUnDia = (clone $fin)->modify('+1 day');
    $meses = ((int) $finMasUnDia->format('Y') - (int) $inicio->format('Y')) * 12
        + ((int) $finMasUnDia->format('m') - (int) $inicio->format('m'));

    if ($meses < 0)
        return 0;

    $candidato = (clone $inicio)->modify('+' . $meses . ' months');
    if ($candidato < $finMasUnDia)
        $meses++;

    return max(0, $meses);
}

function textoAcumuladoMeses(int $meses): string
{
    $anos = intdiv($meses, 12);
    $resto = $meses % 12;
    $partes = [];
    if ($anos > 0)
        $partes[] = $anos . ' ' . ($anos === 1 ? 'año' : 'años');
    if ($resto > 0)
        $partes[] = $resto . ' ' . ($resto === 1 ? 'mes' : 'meses');
    return $partes ? implode(' y ', $partes) : '0 meses';
}

function eliminarLineasRenovacionNoNecesarias(string $docx, int $renovacionActual): void
{
    $zip = new ZipArchive();
    if ($zip->open($docx) !== true)
        return;

    foreach (['word/document.xml'] as $nombre) {
        $xml = $zip->getFromName($nombre);
        if ($xml === false)
            continue;
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = false;
        if (!@$dom->loadXML($xml))
            continue;

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        foreach ($xpath->query('//w:body/w:p') as $p) {
            $texto = '';
            foreach ($xpath->query('.//w:t', $p) as $t)
                $texto .= $t->textContent;
            $texto = trim($texto);

            // Las líneas históricas pueden comenzar con un símbolo de check
            // (por ejemplo: "✓ Rnv3: ..."), así que no debemos exigir que
            // "RnvN:" esté al principio del texto. Además, la renovación
            // actual tampoco debe aparecer en el historial: se muestra abajo
            // como "Renovación No.N". Por eso eliminamos la actual y todas
            // las posteriores.
            if (preg_match('/\bRnv(\d+):/iu', $texto, $m)) {
                $numero = (int) $m[1];
                if ($numero >= $renovacionActual) {
                    $p->parentNode->removeChild($p);
                }
            }
        }
        $zip->addFromString($nombre, $dom->saveXML());
    }
    $zip->close();
}

try {
    $cedula = trim($_POST['cedula'] ?? '');
    $duraciones = $_POST['duraciones'] ?? [];
    $fechasInicio = $_POST['fechas_inicio'] ?? [];

    if ($cedula === '')
        throw new InvalidArgumentException('Selecciona un empleado.');
    if (!is_array($duraciones) || !$duraciones)
        throw new InvalidArgumentException('Agrega al menos una renovación.');
    if (!is_array($fechasInicio))
        $fechasInicio = [];

    // La plantilla actual maneja RNV1-RNV4. Se mantiene ese límite para que
    // el documento generado siempre tenga un historial coherente.
    $renovacionActual = count($duraciones);
    if ($renovacionActual < 1 || $renovacionActual > 4) {
        throw new InvalidArgumentException('La plantilla de Renovación de Contrato está configurada hasta RNV4.');
    }

    $empleado = EmpleadoModel::obtenerPorCedula($cedula);
    if (!$empleado)
        throw new InvalidArgumentException('El empleado no existe.');
    EmpleadoModel::exigirActivo($empleado);

    $faltantes = [];
    if (trim((string) ($empleado['nombre'] ?? '')) === '')
        $faltantes[] = 'Nombre';
    if (trim((string) ($empleado['cedula'] ?? '')) === '')
        $faltantes[] = 'Cédula';
    if (formatoSexo((string) ($empleado['sexo'] ?? '')) === '')
        $faltantes[] = 'Sexo';
    if (trim((string) ($empleado['cargo'] ?? '')) === '')
        $faltantes[] = 'Cargo';
    if (trim((string) ($empleado['lugar_expedicion'] ?? '')) === '')
        $faltantes[] = 'Lugar de expedición de la cédula';
    if (trim((string) ($empleado['tipo_de_personal'] ?? '')) === '')
        $faltantes[] = 'Tipo de personal';
    if (trim((string) ($empleado['fecha_inicio_contrato'] ?? '')) === '')
        $faltantes[] = 'Fecha de inicio del contrato';
    if (trim((string) ($empleado['fecha_fin_contrato'] ?? '')) === '')
        $faltantes[] = 'Fecha de fin del contrato';

    if ($faltantes) {
        throw new InvalidArgumentException('No se puede generar la renovación. Faltan en la hoja de vida: ' . implode(', ', $faltantes) . '.');
    }

    $sexo = formatoSexo((string) $empleado['sexo']);
    $tipoPersonal = mb_strtolower(trim((string) $empleado['tipo_de_personal']), 'UTF-8');
    $tratamiento = $sexo === 'F' ? 'Señora' : 'Señor';
    $saludo = $sexo === 'F' ? 'Estimada' : 'Estimado';
    $esBombero = $tipoPersonal !== 'civil' && ($tipoPersonal === 'bombero' || !empty($empleado['es_bombero_integral']));

    $fechaInicioContrato = DateTime::createFromFormat('Y-m-d', $empleado['fecha_inicio_contrato']);
    $fechaFinContrato = DateTime::createFromFormat('Y-m-d', $empleado['fecha_fin_contrato']);
    if (!$fechaInicioContrato || $fechaInicioContrato->format('Y-m-d') !== $empleado['fecha_inicio_contrato']) {
        throw new InvalidArgumentException('La fecha de inicio del contrato no es válida.');
    }
    if (!$fechaFinContrato || $fechaFinContrato->format('Y-m-d') !== $empleado['fecha_fin_contrato']) {
        throw new InvalidArgumentException('La fecha de fin del contrato no es válida.');
    }

    $renovaciones = [];
    $duracionAnterior = null;
    $sumaMeses = 0;

    for ($n = 1; $n <= $renovacionActual; $n++) {
        $meses = (int) ($duraciones[$n] ?? 0);
        if ($meses < 1 || !in_array($meses, [1, 2, 3, 6, 12, 24], true)) {
            throw new InvalidArgumentException("La duración de RNV{$n} no es válida.");
        }

        if ($n >= 4 && $meses < 12) {
            throw new InvalidArgumentException('La 4ta renovación debe ser igual o mayor a 1 año Art. 46 CST Ley 2466 de 2025');
        }
        if ($duracionAnterior !== null && $meses < $duracionAnterior) {
            throw new InvalidArgumentException(
                'Validación Legal de Renovación Corta: No es posible registrar una duración menor a la del periodo anterior mediante renovación automática. Reducir el tiempo del contrato solo es legal si se suscribe un Otrosí de mutuo acuerdo.'
            );
        }

        $fechaInicio = trim((string) ($fechasInicio[$n] ?? ''));
        if ($fechaInicio === '') {
            $fechaInicio = $n === 1
                ? (clone $fechaFinContrato)->modify('+1 day')->format('Y-m-d')
                : (new DateTime($renovaciones[$n - 1]['fin']))->modify('+1 day')->format('Y-m-d');
        }

        $fechaInicioDt = DateTime::createFromFormat('Y-m-d', $fechaInicio);
        if (!$fechaInicioDt || $fechaInicioDt->format('Y-m-d') !== $fechaInicio) {
            throw new InvalidArgumentException("La fecha de inicio de RNV{$n} no es válida.");
        }

        $fin = FormatoModel::calcularFin($fechaInicio, $meses);
        $renovaciones[$n] = ['inicio' => $fechaInicio, 'fin' => $fin, 'meses' => $meses];
        $sumaMeses += $meses;
        $duracionAnterior = $meses;
    }

    $mesesIniciales = mesesContratoInicial($fechaInicioContrato, $fechaFinContrato);
    $totalAcumulado = $mesesIniciales + $sumaMeses;
    if ($totalAcumulado > 48) {
        $faltanMeses = 0;
        throw new InvalidArgumentException(
            'ALERTA: Esta persona debe pasar a Contrato Indefinido, ya que supera los 4 años o la próxima renovación lo haría superar. Total actual '
            . textoAcumuladoMeses($totalAcumulado)
            . ', le faltan ' . $faltanMeses . ' meses. Ley 2466 de 2025'
        );
    }

    $plantilla = __DIR__ . '/../../uploads/plantillas/AF-FT-02-AF-RENOVACION DE CONTRATO.docx';
    if (!is_file($plantilla)) {
        throw new RuntimeException('No se encontró la plantilla AF-FT-02-AF-RENOVACION DE CONTRATO.docx en uploads/plantillas/.');
    }

    $generados = __DIR__ . '/../../uploads/generados';
    if (!is_dir($generados) && !mkdir($generados, 0775, true) && !is_dir($generados)) {
        throw new RuntimeException('No fue posible crear uploads/generados/.');
    }

    $nombreSeguro = FormatoModel::nombreSeguro($empleado['nombre']);
    $archivo = 'RENOVACION_' . $empleado['cedula'] . '_' . date('Ymd_His') . '_' . $nombreSeguro . '.docx';
    $salida = $generados . DIRECTORY_SEPARATOR . $archivo;

    if (!class_exists('PhpOffice\\PhpWord\\TemplateProcessor')) {
        throw new RuntimeException('PHPWord no está disponible. Ejecuta composer install en el proyecto.');
    }

    $processor = new \PhpOffice\PhpWord\TemplateProcessor($plantilla);
    $hoy = date('Y-m-d');
    $actual = $renovaciones[$renovacionActual];

    $valores = [
        'fecha_hoy' => FormatoModel::fechaLarga($hoy, true),
        'tratamiento' => $tratamiento,
        'prefijo_bombero' => $esBombero ? 'BRO. ' : '',
        'nombre_mayus' => mb_strtoupper(trim($empleado['nombre']), 'UTF-8'),
        'cedula_formateada' => FormatoModel::formatearCedula($empleado['cedula']),
        // Plantilla: "C.C. No. ${cedula_formateada} de ${lugar_expedicion}"
        'lugar_expedicion' => FormatoModel::municipioExpedicion((string) $empleado['lugar_expedicion']),
        'lugar_expedicion_completo' => trim((string) $empleado['lugar_expedicion']),
        'cargo' => $empleado['cargo'],
        'saludo' => $saludo,
        'primer_nombre' => FormatoModel::primerNombre($empleado['nombre']),
        'fecha_inicio_contrato_larga' => FormatoModel::fechaLarga($empleado['fecha_inicio_contrato'], true),
        'fecha_fin_contrato_larga' => FormatoModel::fechaLarga($empleado['fecha_fin_contrato'], true),
        'renovacion_actual' => (string) $renovacionActual,
        'renovacion_actual_inicio_corta' => FormatoModel::fechaCorta($actual['inicio']),
        'renovacion_actual_fin_corta' => FormatoModel::fechaCorta($actual['fin']),
        'renovacion_actual_inicio_larga' => FormatoModel::fechaLarga($actual['inicio'], true),
        'renovacion_actual_fin_larga' => FormatoModel::fechaLarga($actual['fin'], true),
        'duracion_texto' => FormatoModel::textoMeses($actual['meses']),
        'duracion_numero' => str_pad((string) $actual['meses'], 2, '0', STR_PAD_LEFT),
    ];

    for ($n = 1; $n <= 4; $n++) {
        $valores['rnv' . $n . '_inicio'] = '';
        $valores['rnv' . $n . '_fin'] = '';
        if (isset($renovaciones[$n]) && $n < $renovacionActual) {
            $valores['rnv' . $n . '_inicio'] = FormatoModel::fechaLarga($renovaciones[$n]['inicio'], true);
            $valores['rnv' . $n . '_fin'] = FormatoModel::fechaLarga($renovaciones[$n]['fin'], true);
        }
    }

    $processor->setValues($valores);
    $processor->saveAs($salida);

    eliminarLineasRenovacionNoNecesarias($salida, $renovacionActual);

    echo json_encode([
        'ok' => true,
        'archivo' => $archivo,
        'nombre' => $archivo,
        'url' => './api/formato_archivo.php?f=' . rawurlencode($archivo) . '&accion=descargar'
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('formatos_renovacion_generar: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo generar la renovación.'], JSON_UNESCAPED_UNICODE);
}
