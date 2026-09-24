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

function mesesEspanol(): array {
    return [1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',7=>'julio',8=>'agosto',9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre'];
}

function fechaValida(string $fecha): DateTimeImmutable {
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha, new DateTimeZone('America/Bogota'));
    $errores = DateTimeImmutable::getLastErrors();
    if (!$d || ($errores !== false && ($errores['warning_count'] || $errores['error_count'])) || $d->format('Y-m-d') !== $fecha) {
        throw new InvalidArgumentException('La fecha seleccionada no es válida.');
    }
    return $d;
}

function fechaLegal(DateTimeInterface $d): string {
    $dia = (int)$d->format('j');
    $mes = mesesEspanol()[(int)$d->format('n')];
    $anio = $d->format('Y');
    $diaTexto = $dia === 1 ? 'primer' : numeroGrupo($dia);
   
    return $diaTexto . ' (' . str_pad((string)$dia, 2, '0', STR_PAD_LEFT) . ') '. ' del mes de ' . $mes . ' de ' . $anio;
}

function numeroGrupo(int $n): string {
    $u = ['cero','uno','dos','tres','cuatro','cinco','seis','siete','ocho','nueve','diez','once','doce','trece','catorce','quince','dieciséis','diecisiete','dieciocho','diecinueve','veinte','veintiuno','veintidós','veintitrés','veinticuatro','veinticinco','veintiséis','veintisiete','veintiocho','veintinueve'];
    $d = [30=>'treinta',40=>'cuarenta',50=>'cincuenta',60=>'sesenta',70=>'setenta',80=>'ochenta',90=>'noventa'];
    $c = [100=>'cien',200=>'doscientos',300=>'trescientos',400=>'cuatrocientos',500=>'quinientos',600=>'seiscientos',700=>'setecientos',800=>'ochocientos',900=>'novecientos'];
    if ($n < 30) return $u[$n];
    if ($n < 100) {
        $base = $d[intdiv($n, 10) * 10];
        return $base . (($n % 10) ? ' y ' . $u[$n % 10] : '');
    }
    $centena = intdiv($n, 100) * 100;
    $resto = $n % 100;
    if ($n === 100) return 'cien';
    // Del 101 al 199 se usa "ciento", no "cien": ciento uno, ciento setenta y siete, etc.
    $base = $centena === 100 ? 'ciento' : $c[$centena];
    return $base . ($resto ? ' ' . numeroGrupo($resto) : '');
}

function numeroEnPalabras(int $numero): string {
    if ($numero === 0) return 'cero';
    if ($numero < 1000) return numeroGrupo($numero);

    $partes = [];
    $millones = intdiv($numero, 1000000);
    $resto = $numero % 1000000;
    if ($millones > 0) {
        $partes[] = $millones === 1 ? 'un millón' : convertirMiles($millones) . ' millones';
    }
    $miles = intdiv($resto, 1000);
    $resto %= 1000;
    if ($miles > 0) {
        $partes[] = $miles === 1 ? 'mil' : convertirMiles($miles) . ' mil';
    }
    if ($resto > 0) $partes[] = numeroGrupo($resto);
    return implode(' ', $partes);
}

function convertirMiles(int $numero): string {
    $texto = numeroGrupo($numero);
    $texto = preg_replace('/\buno$/u', 'un', $texto);
    $texto = preg_replace('/\bveintiuno$/u', 'veintiún', $texto);
    return $texto;
}

function formatearPesos(int $valor): string {
    return '$' . number_format($valor, 0, ',', '.');
}

function limpiarNombreArchivo(string $nombre): string {
    $nombre = trim($nombre);
    $nombre = preg_replace('/[\\\\\/:*?"<>|]/u', '', $nombre);
    $nombre = preg_replace('/\s+/u', ' ', $nombre);
    return rtrim($nombre, '. ');
}

try {
    $cedula = preg_replace('/\D+/', '', trim((string)($_POST['cedula'] ?? '')));
    $fechaRemuneracion = trim((string)($_POST['fecha_remuneracion'] ?? ''));
    $salarioRaw = preg_replace('/\D+/', '', (string)($_POST['salario'] ?? ''));

    if ($cedula === '') throw new InvalidArgumentException('Selecciona un empleado.');
    if ($fechaRemuneracion === '') throw new InvalidArgumentException('Selecciona la fecha a partir de la cual aplica la nueva remuneración.');
    if ($salarioRaw === '' || !ctype_digit($salarioRaw)) throw new InvalidArgumentException('Escribe un salario válido.');

    $salario = (int)$salarioRaw;
    if ($salario < 1 || $salario > 999999999999) throw new InvalidArgumentException('El salario debe estar entre $1 y $999.999.999.999.');

    $fechaRem = fechaValida($fechaRemuneracion);
    $empleado = EmpleadoModel::obtenerPorCedula($cedula);
    if (!$empleado) throw new InvalidArgumentException('No se encontró el empleado.');

    $nombre = mb_strtoupper(trim((string)$empleado['nombre']), 'UTF-8');
    $cedulaEmpleado = preg_replace('/\D+/', '', trim((string)$empleado['cedula']));
    if ($nombre === '' || $cedulaEmpleado === '') throw new InvalidArgumentException('El empleado no tiene nombre o cédula registrados.');

    $fechaInicio = $empleado['fecha_inicio_contrato'] ? fechaValida((string)$empleado['fecha_inicio_contrato']) : null;
    $hoy = new DateTimeImmutable('today', new DateTimeZone('America/Bogota'));

    $plantilla = __DIR__ . '/../../uploads/plantillas/GH-FT-25-OTROSI-CAMBIO-SALARIO.docx';
    if (!is_file($plantilla)) {
        throw new RuntimeException('No se encontró la plantilla GH-FT-25-OTROSI-CAMBIO-SALARIO.docx en uploads/plantillas/.');
    }
    if (!class_exists('PhpOffice\\PhpWord\\TemplateProcessor')) {
        throw new RuntimeException('PHPWord no está disponible. Ejecuta composer install en el proyecto.');
    }

    $generados = __DIR__ . '/../../uploads/generados';
    if (!is_dir($generados) && !mkdir($generados, 0775, true) && !is_dir($generados)) {
        throw new RuntimeException('No fue posible crear uploads/generados/.');
    }

    $cedulaFormateada = FormatoModel::formatearCedula($cedulaEmpleado);
    $salarioTexto = mb_strtoupper(numeroEnPalabras($salario) . ' PESOS M/CTE', 'UTF-8');
    $salarioCop = formatearPesos($salario);

    $valores = [
        'nombre_empleado' => $nombre,
        'cedula' => $cedulaFormateada,
        'fecha_inicio_contrato' => $fechaInicio ? fechaLegal($fechaInicio) : 'NO REGISTRADA',
        'fecha_actual' => fechaLegal($hoy),
        'fecha_remuneracion' => fechaLegal($fechaRem),
        'salario_texto' => $salarioTexto,
        'salario_cop' => $salarioCop,
    ];

    $archivo = 'GH-FT-25 OTROSI CAMBIO DE SALARIO ' . limpiarNombreArchivo($nombre) . '.docx';
    $salida = $generados . DIRECTORY_SEPARATOR . $archivo;

    $processor = new \PhpOffice\PhpWord\TemplateProcessor($plantilla);
    $processor->setValues($valores);
    $processor->saveAs($salida);

    if (!is_file($salida) || filesize($salida) <= 0) throw new RuntimeException('El archivo Word no fue generado correctamente.');

    echo json_encode([
        'ok' => true,
        'archivo' => $archivo,
        'url' => './api/formato_archivo.php?f=' . rawurlencode($archivo) . '&accion=descargar',
        'empleado' => ['nombre' => $nombre, 'cedula' => $cedulaFormateada],
        'fecha_inicio_contrato' => $fechaInicio ? $fechaInicio->format('Y-m-d') : null,
        'fecha_actual' => $hoy->format('Y-m-d'),
        'fecha_remuneracion' => $fechaRem->format('Y-m-d'),
        'salario' => ['texto' => $salarioTexto, 'cop' => $salarioCop],
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
