<?php

declare(strict_types=1);

date_default_timezone_set('America/Bogota');

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/FormatoModel.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';

header('Content-Type: application/json; charset=utf-8');

requireSuperAdmin();

$rolesFormatos = [
    'superadmin_talento_humano',
    'auxiliar_talento_humano'
];

if (!in_array($_SESSION['superadmin_rol'] ?? '', $rolesFormatos, true)) {
    http_response_code(403);

    echo json_encode([
        'ok' => false,
        'error' => 'No tienes permiso para generar Otrosí.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

/*
|--------------------------------------------------------------------------
| CSRF
|--------------------------------------------------------------------------
*/
validarCSRF();

/*
|--------------------------------------------------------------------------
| FUNCIONES AUXILIARES
|--------------------------------------------------------------------------
*/

/**
 * Convierte un número entero a palabras en español.
 */
function numeroGrupo(int $n): string
{
    $unidades = [
        'cero',
        'uno',
        'dos',
        'tres',
        'cuatro',
        'cinco',
        'seis',
        'siete',
        'ocho',
        'nueve',
        'diez',
        'once',
        'doce',
        'trece',
        'catorce',
        'quince',
        'dieciséis',
        'diecisiete',
        'dieciocho',
        'diecinueve',
        'veinte',
        'veintiuno',
        'veintidós',
        'veintitrés',
        'veinticuatro',
        'veinticinco',
        'veintiséis',
        'veintisiete',
        'veintiocho',
        'veintinueve'
    ];

    $decenas = [
        30 => 'treinta',
        40 => 'cuarenta',
        50 => 'cincuenta',
        60 => 'sesenta',
        70 => 'setenta',
        80 => 'ochenta',
        90 => 'noventa'
    ];

    $centenas = [
        100 => 'cien',
        200 => 'doscientos',
        300 => 'trescientos',
        400 => 'cuatrocientos',
        500 => 'quinientos',
        600 => 'seiscientos',
        700 => 'setecientos',
        800 => 'ochocientos',
        900 => 'novecientos'
    ];

    if ($n < 30) {
        return $unidades[$n];
    }

    if ($n < 100) {
        $decena = intdiv($n, 10) * 10;
        $resto = $n % 10;

        return $decenas[$decena]
            . ($resto > 0 ? ' y ' . $unidades[$resto] : '');
    }

    if ($n === 100) {
        return 'cien';
    }

    $centena = intdiv($n, 100) * 100;
    $resto = $n % 100;

    $base = $centena === 100
        ? 'ciento'
        : $centenas[$centena];

    return $base
        . ($resto > 0 ? ' ' . numeroGrupo($resto) : '');
}


/**
 * Convierte números hasta miles de millones.
 */
function numeroEnPalabras(int $n): string
{
    if ($n === 0) {
        return 'cero';
    }

    if ($n < 0) {
        return 'menos ' . numeroEnPalabras(abs($n));
    }

    $partes = [];

    /*
     * Millones
     */
    $millones = intdiv($n, 1000000);
    $resto = $n % 1000000;

    if ($millones > 0) {
        if ($millones === 1) {
            $partes[] = 'un millón';
        } else {
            $millonesTexto = numeroGrupo($millones);

            /*
             * Casos como:
             * 21 millones -> veintiún millones
             * 31 millones -> treinta y un millones
             */
            $millonesTexto = preg_replace(
                '/\buno$/u',
                'un',
                $millonesTexto
            );

            $millonesTexto = preg_replace(
                '/\bveintiuno$/u',
                'veintiún',
                $millonesTexto
            );

            $partes[] = $millonesTexto . ' millones';
        }
    }

    /*
     * Miles
     */
    $miles = intdiv($resto, 1000);
    $resto = $resto % 1000;

    if ($miles > 0) {
        if ($miles === 1) {
            $partes[] = 'mil';
        } else {
            $milesTexto = numeroGrupo($miles);

            $milesTexto = preg_replace(
                '/\buno$/u',
                'un',
                $milesTexto
            );

            $milesTexto = preg_replace(
                '/\bveintiuno$/u',
                'veintiún',
                $milesTexto
            );

            $partes[] = $milesTexto . ' mil';
        }
    }

    /*
     * Centenas, decenas y unidades
     */
    if ($resto > 0) {
        $partes[] = numeroGrupo($resto);
    }

    return implode(' ', $partes);
}


/**
 * Formatea una cédula colombiana.
 *
 * Ejemplo:
 * 1123456789 -> 1.123.456.789
 */
function formatearCedulaColombia(string $cedula): string
{
    $cedula = preg_replace('/\D+/', '', $cedula);

    if ($cedula === '') {
        return '';
    }

    return number_format(
        (int)$cedula,
        0,
        '',
        '.'
    );
}


/**
 * Convierte una fecha YYYY-MM-DD
 * en sus componentes legales.
 */
function obtenerPartesFecha(string $fecha): array
{
    $dt = DateTimeImmutable::createFromFormat(
        '!Y-m-d',
        $fecha,
        new DateTimeZone('America/Bogota')
    );

    $errores = DateTimeImmutable::getLastErrors();

    if (
        !$dt ||
        (
            is_array($errores) &&
            (
                $errores['warning_count'] > 0 ||
                $errores['error_count'] > 0
            )
        )
    ) {
        throw new RuntimeException(
            'La fecha de remuneración no es válida.'
        );
    }

    $meses = [
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre'
    ];

    return [
        'dia' => $dt->format('j'),
        'mes' => $meses[(int)$dt->format('n')],
        'anio' => $dt->format('Y')
    ];
}


/**
 * Limpia caracteres no permitidos para nombre de archivo.
 */
function limpiarNombreArchivo(string $nombre): string
{
    $nombre = trim($nombre);

    /*
     * Windows no permite estos caracteres:
     * \ / : * ? " < > |
     */
    $nombre = preg_replace(
        '/[\\\\\/:*?"<>|]+/u',
        '',
        $nombre
    );

    /*
     * Evitar saltos de línea.
     */
    $nombre = preg_replace(
        '/[\r\n\t]+/u',
        ' ',
        $nombre
    );

    /*
     * Evitar múltiples espacios.
     */
    $nombre = preg_replace(
        '/\s+/u',
        ' ',
        $nombre
    );

    return trim($nombre);
}


/**
 * Valida que un DOCX realmente sea un ZIP válido.
 */
function validarDocx(string $archivo): bool
{
    if (!is_file($archivo) || filesize($archivo) < 100) {
        return false;
    }

    if (!class_exists('ZipArchive')) {
        return true;
    }

    $zip = new ZipArchive();

    $resultado = $zip->open($archivo);

    if ($resultado !== true) {
        return false;
    }

    $documentXml = $zip->locateName(
        'word/document.xml'
    );

    $zip->close();

    return $documentXml !== false;
}


/*
|--------------------------------------------------------------------------
| DATOS RECIBIDOS DESDE EL PANEL
|--------------------------------------------------------------------------
*/

$cedula = preg_replace(
    '/\D+/',
    '',
    trim((string)($_POST['cedula'] ?? ''))
);

$fechaRemuneracion = trim(
    (string)($_POST['fecha_remuneracion'] ?? '')
);

$salarioRaw = trim(
    (string)($_POST['salario'] ?? '')
);


/*
|--------------------------------------------------------------------------
| VALIDACIONES
|--------------------------------------------------------------------------
*/

if ($cedula === '') {
    echo json_encode([
        'ok' => false,
        'error' => 'No se recibió la cédula del empleado.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($fechaRemuneracion === '') {
    echo json_encode([
        'ok' => false,
        'error' => 'No se recibió la fecha de remuneración.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$salarioNumerico = preg_replace(
    '/\D+/',
    '',
    $salarioRaw
);

if (
    $salarioNumerico === '' ||
    !ctype_digit($salarioNumerico) ||
    (int)$salarioNumerico < 1
) {
    echo json_encode([
        'ok' => false,
        'error' => 'El salario ingresado no es válido.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$salario = (int)$salarioNumerico;


/*
|--------------------------------------------------------------------------
| OBTENER EMPLEADO
|--------------------------------------------------------------------------
*/

try {

    $empleado = EmpleadoModel::obtenerPorCedula($cedula);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'error' => 'No fue posible consultar el empleado.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


if (!$empleado) {

    echo json_encode([
        'ok' => false,
        'error' => 'No se encontró un empleado con esa cédula.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| DATOS DEL EMPLEADO
|--------------------------------------------------------------------------
*/

$nombreOriginal = trim(
    (string)($empleado['nombre'] ?? '')
);

if ($nombreOriginal === '') {

    echo json_encode([
        'ok' => false,
        'error' => 'El empleado no tiene un nombre registrado.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| FORMATO DE DATOS
|--------------------------------------------------------------------------
*/

/*
 * Nombre:
 * MAYÚSCULA + la plantilla ya tiene el placeholder en negrita.
 */
$nombreEmpleado = mb_strtoupper(
    $nombreOriginal,
    'UTF-8'
);

/*
 * Cédula:
 * 1123456789 -> 1.123.456.789
 */
$cedulaFormateada = formatearCedulaColombia(
    $cedula
);


/*
 * Fecha que el usuario seleccionó.
 */
try {

    $partesFecha = obtenerPartesFecha(
        $fechaRemuneracion
    );

} catch (Throwable $e) {

    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
 * Salario en número:
 *
 * 2500000 -> $ 2.500.000
 */
$salarioNumero = number_format(
    $salario,
    0,
    '',
    '.'
);


/*
 * Salario en letras:
 *
 * 2500000
 * ->
 * DOS MILLONES QUINIENTOS MIL PESOS M/CTE
 */
$salarioLetras = mb_strtoupper(
    numeroEnPalabras($salario),
    'UTF-8'
);

$salarioLetras .= ' PESOS M/CTE';


/*
|--------------------------------------------------------------------------
| PLANTILLA
|--------------------------------------------------------------------------
|
| Esta es la plantilla adaptada que te preparé.
|
*/

$plantilla = __DIR__
    . '/../../uploads/plantillas/'
    . 'GH-FT-24-OTROSI CAMBIO SALARIO PLANTILLA.docx';


if (!is_file($plantilla)) {

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'error' => 'No se encontró la plantilla de Otrosí cambio de salario.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| PHPWORD
|--------------------------------------------------------------------------
*/

$autoload = __DIR__ . '/../../vendor/autoload.php';

if (!is_file($autoload)) {

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'error' => 'No se encontró el autoload de Composer.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

require_once $autoload;

if (!class_exists(
    \PhpOffice\PhpWord\TemplateProcessor::class
)) {

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'error' => 'PHPWord no está instalado correctamente.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| CARPETA DE SALIDA
|--------------------------------------------------------------------------
*/

$directorioSalida = __DIR__
    . '/../../uploads/generados';

if (!is_dir($directorioSalida)) {

    if (!mkdir(
        $directorioSalida,
        0775,
        true
    )) {

        http_response_code(500);

        echo json_encode([
            'ok' => false,
            'error' => 'No se pudo crear la carpeta de archivos generados.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }
}

if (!is_writable($directorioSalida)) {

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'error' => 'La carpeta de archivos generados no tiene permisos de escritura.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| NOMBRE FINAL DEL ARCHIVO
|--------------------------------------------------------------------------
|
| EXACTAMENTE:
|
| GH-FT-24 OTROSI AL CONTRATO INDIVIDUAL DE TRABAJO NOMBRE.docx
|
*/

$nombreArchivoEmpleado = limpiarNombreArchivo(
    $nombreEmpleado
);

$archivo = sprintf(
    'GH-FT-24 OTROSI AL CONTRATO INDIVIDUAL DE TRABAJO %s.docx',
    $nombreArchivoEmpleado
);

$archivoSalida = $directorioSalida
    . DIRECTORY_SEPARATOR
    . $archivo;


/*
|--------------------------------------------------------------------------
| GENERAR WORD
|--------------------------------------------------------------------------
*/

try {

    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(
        $plantilla
    );


    /*
     * IMPORTANTE:
     *
     * NO modificamos todos los estilos del documento.
     * La plantilla ya tiene:
     *
     * - Nombre -> negrita
     * - Cédula -> negrita
     * - Fechas -> Arial Narrow 12
     *
     * TemplateProcessor reemplaza el contenido del placeholder
     * manteniendo el formato del run donde está ubicado.
     */


    /*
     * Datos del empleado
     */
    $templateProcessor->setValue(
        'nombre_empleado',
        htmlspecialchars(
            $nombreEmpleado,
            ENT_QUOTES | ENT_XML1,
            'UTF-8'
        )
    );

    $templateProcessor->setValue(
        'cedula',
        htmlspecialchars(
            $cedulaFormateada,
            ENT_QUOTES | ENT_XML1,
            'UTF-8'
        )
    );


    /*
     * Fecha de remuneración
     */
    $templateProcessor->setValue(
        'dia_actual',
        $partesFecha['dia']
    );

    $templateProcessor->setValue(
        'mes_actual',
        $partesFecha['mes']
    );

    $templateProcessor->setValue(
        'anio_actual',
        $partesFecha['anio']
    );


    /*
     * Salario
     */
    $templateProcessor->setValue(
        'salario_letras',
        htmlspecialchars(
            $salarioLetras,
            ENT_QUOTES | ENT_XML1,
            'UTF-8'
        )
    );

    $templateProcessor->setValue(
        'salario_numero',
        htmlspecialchars(
            $salarioNumero,
            ENT_QUOTES | ENT_XML1,
            'UTF-8'
        )
    );


    /*
     * Generar archivo.
     */
    $templateProcessor->saveAs(
        $archivoSalida
    );


} catch (Throwable $e) {

    if (is_file($archivoSalida)) {
        @unlink($archivoSalida);
    }

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'error' => 'No se pudo generar el documento Word.',
        'detalle' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| VALIDAR DOCX
|--------------------------------------------------------------------------
*/

if (!validarDocx($archivoSalida)) {

    @unlink($archivoSalida);

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'error' => 'El documento generado no es un archivo Word DOCX válido.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| RESPUESTA
|--------------------------------------------------------------------------
*/

$url = './api/formato_archivo.php?f='
    . rawurlencode($archivo)
    . '&accion=descargar';


echo json_encode([
    'ok' => true,
    'archivo' => $archivo,
    'url' => $url,
    'nombre_empleado' => $nombreEmpleado,
    'cedula' => $cedulaFormateada,
    'fecha_remuneracion' => $fechaRemuneracion,
    'salario' => $salarioNumero,
    'salario_letras' => $salarioLetras
], JSON_UNESCAPED_UNICODE);

exit;