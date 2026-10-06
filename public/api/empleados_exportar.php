<?php
// public/api/empleados_exportar.php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../../src/controllers/EmpleadoController.php';
require_once __DIR__ . '/../../src/helpers/JornadaHelper.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

requireSuperAdmin();

$busqueda  = trim($_GET['q'] ?? '');
$filtros   = EmpleadoController::filtrosDesdeRequest($_GET);
$empleados = EmpleadoModel::listarParaExportar($busqueda, $filtros);

// El teniente es de solo lectura y no necesita ver salarios.
$verSalario = ($_SESSION['superadmin_rol'] ?? '') !== 'teniente';

// [encabezado, extractor, tipo]  tipo: texto | fecha | dinero
$columnas = [
    ['Nombre',              fn($e) => $e['nombre'],                                'texto'],
    ['Cédula',              fn($e) => $e['cedula'],                                'texto'],
    ['Lugar de expedición', fn($e) => $e['lugar_expedicion'],                      'texto'],
    ['Sexo',                fn($e) => $e['sexo'],                                  'texto'],
    ['Cargo',               fn($e) => JornadaHelper::cargoDetalle($e),             'texto'],
    ['Tipo de personal',    fn($e) => $e['tipo_de_personal'],                      'texto'],
    ['Bombero integral',    fn($e) => $e['es_bombero_integral'] ? 'Sí' : 'No',     'texto'],
    ['Jornada',             fn($e) => $e['tipo_jornada'],                          'texto'],
    ['Tipo de contrato',    fn($e) => $e['tipo_de_contrato'],                      'texto'],
    ['Inicio contrato',     fn($e) => $e['fecha_inicio_contrato'],                 'fecha'],
    ['Fin contrato',        fn($e) => $e['fecha_fin_contrato'],                    'fecha'],
    ['Estado',              fn($e) => $e['estado'],                                'texto'],
];
if ($verSalario) {
    $columnas[] = ['Salario básico', fn($e) => $e['salario_basico'], 'dinero'];
}
array_push($columnas,
    ['EPS',              fn($e) => $e['eps'],              'texto'],
    ['Pensión',          fn($e) => $e['pension'],          'texto'],
    ['ARL',              fn($e) => $e['arl'],              'texto'],
    ['Celular',          fn($e) => $e['celular'],          'texto'],
    ['Correo',           fn($e) => $e['correo'],           'texto'],
    ['Fecha nacimiento', fn($e) => $e['fecha_nacimiento'], 'fecha'],
);

$libro = new Spreadsheet();
$hoja  = $libro->getActiveSheet();
$hoja->setTitle('Empleados');

$totalCols = count($columnas);
$ultimaCol = Coordinate::stringFromColumnIndex($totalCols);

// Encabezados
foreach ($columnas as $i => [$titulo]) {
    $hoja->setCellValueExplicit(Coordinate::stringFromColumnIndex($i + 1) . '1', $titulo, DataType::TYPE_STRING);
}

// Datos (texto explícito: evita perder ceros a la izquierda o notación científica en cédula/celular)
$fila = 2;
foreach ($empleados as $emp) {
    foreach ($columnas as $i => [, $extraer, $tipo]) {
        $coord = Coordinate::stringFromColumnIndex($i + 1) . $fila;
        $valor = $extraer($emp);

        if ($valor === null || $valor === '') {
            continue;
        }
        if ($tipo === 'fecha') {
            $hoja->setCellValue($coord, Date::stringToExcel($valor));
            $hoja->getStyle($coord)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        } elseif ($tipo === 'dinero') {
            $hoja->setCellValue($coord, (float) $valor);
            $hoja->getStyle($coord)->getNumberFormat()->setFormatCode('"$"#,##0');
        } else {
            $hoja->setCellValueExplicit($coord, (string) $valor, DataType::TYPE_STRING);
        }
    }
    $fila++;
}
$ultimaFila = max(1, $fila - 1);

// Estilos
$hoja->getStyle("A1:{$ultimaCol}1")->applyFromArray([
    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => 'B91C1C']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);
$hoja->getRowDimension(1)->setRowHeight(24);
$hoja->getStyle("A1:{$ultimaCol}{$ultimaFila}")->getBorders()->getAllBorders()
     ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D1D5DB');

$hoja->freezePane('A2');
$hoja->setAutoFilter("A1:{$ultimaCol}{$ultimaFila}");
for ($c = 1; $c <= $totalCols; $c++) {
    $hoja->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
}

// Nombre de archivo: incluye los filtros aplicados
$partes = ['empleados'];
foreach ($filtros as $v) {
    if ($v !== '') {
        $partes[] = preg_replace('/[^a-z0-9]+/', '_', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $v)));
    }
}
$partes[] = date('Ymd_His');
$nombreArchivo = implode('_', $partes) . '.xlsx';

if (ob_get_level()) {
    ob_end_clean();
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Cache-Control: max-age=0');

(new Xlsx($libro))->save('php://output');
exit;