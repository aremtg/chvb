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

try {
    $cedula = trim((string)($_POST['cedula'] ?? ''));
    $tipo = mb_strtolower(trim((string)($_POST['tipo_examen'] ?? '')), 'UTF-8');

    $tipos = [
        'ingreso' => [
            'texto' => 'INGRESO',
            'x' => ['ingreso_x' => 'X', 'periodicos_x' => '', 'egreso_x' => ''],
        ],
        'periodicos' => [
            'texto' => 'PERIÓDICOS',
            'x' => ['ingreso_x' => '', 'periodicos_x' => 'X', 'egreso_x' => ''],
        ],
        'egreso' => [
            'texto' => 'EGRESO',
            'x' => ['ingreso_x' => '', 'periodicos_x' => '', 'egreso_x' => 'X'],
        ],
    ];

    if ($cedula === '') {
        throw new InvalidArgumentException('Debes seleccionar un empleado.');
    }
    if (!isset($tipos[$tipo])) {
        throw new InvalidArgumentException('El tipo de examen seleccionado no es válido.');
    }

    $pdo = getPDO();
    $stmt = $pdo->prepare(
        "SELECT cedula, nombre, cargo
         FROM empleados
         WHERE cedula = :cedula
         LIMIT 1"
    );
    $stmt->execute(['cedula' => $cedula]);
    $empleado = $stmt->fetch();

    if (!$empleado) {
        throw new InvalidArgumentException('No se encontró el empleado seleccionado.');
    }

    $plantilla = __DIR__ . '/../../uploads/plantillas/GH-FT-03 REMISION EXAMENES MEDICOS OCUPACIONALES.docx';
    if (!is_file($plantilla)) {
        throw new RuntimeException('No se encontró la plantilla GH-FT-03 en uploads/plantillas.');
    }

    $directorio = __DIR__ . '/../../uploads/generados';
    if (!is_dir($directorio) && !mkdir($directorio, 0775, true) && !is_dir($directorio)) {
        throw new RuntimeException('No fue posible crear la carpeta de archivos generados.');
    }

    // Conserva el nombre completo y la cédula en el nombre del archivo,
    // eliminando únicamente caracteres no permitidos por Windows.
    $nombreArchivo = preg_replace('/[\\\\\/:*?"<>|]+/u', '', trim((string)$empleado['nombre'])) ?: 'EMPLEADO';
    $nombreArchivo = preg_replace('/\s+/u', ' ', $nombreArchivo);
    $cedulaArchivo = preg_replace('/[\\\\\/:*?"<>|]+/u', '', $cedula);

    $archivo = 'GH-FT-03 REMISION EXAMENES ' . $nombreArchivo . ' ' . $cedulaArchivo . '.docx';
    $salida = $directorio . DIRECTORY_SEPARATOR . $archivo;

    $processor = new \PhpOffice\PhpWord\TemplateProcessor($plantilla);

    $meses = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];
    $hoy = new DateTimeImmutable('now', new DateTimeZone('America/Bogota'));
    // Día siempre con dos dígitos: 09 de octubre de 2026
    $fechaHoy = $hoy->format('d') . ' de ' .
        $meses[(int)$hoy->format('m')] . ' de ' . $hoy->format('Y');

    $valores = [
        'fecha_hoy' => $fechaHoy,
        'nombre_completo' => (string)$empleado['nombre'],
        'cedula' => (string)$empleado['cedula'],
        'cargo' => (string)($empleado['cargo'] ?? ''),
        'tipo_examen' => $tipos[$tipo]['texto'],
    ];

    foreach ($tipos[$tipo]['x'] as $clave => $valor) {
        $valores[$clave] = $valor;
    }

    $processor->setValues($valores);
    $processor->saveAs($salida);

    echo json_encode([
        'ok' => true,
        'archivo' => $archivo,
        'nombre' => $archivo,
        'url' => './api/formato_archivo.php?f=' . rawurlencode($archivo) . '&accion=descargar'
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}