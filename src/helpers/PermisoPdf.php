<?php
// src/helpers/PermisoPdf.php
//
// Genera el PDF de uno o varios permisos. El PDF se crea en memoria y se envía
// directo al navegador (descarga): NO se guarda en la BD ni en las carpetas del
// servidor. Cada permiso es VERTICAL y ocupa media hoja oficio (6.5 x 8.5 in).
//
//   - 1 permiso      -> página de 612 x 612 pt.
//   - 2 por hoja     -> hoja oficio VERTICAL (8.5 x 13 in = 612 x 936 pt) con un permiso
//                       arriba y otro abajo (612 x 468 c/u) y una línea punteada para cortar
//                       por la mitad. En este modo se usa un diseño compacto.
//
// Evidencias (permisos.evidencia_archivo):
//   - Foto: va en la mitad libre de la hoja si el grupo tiene un solo permiso; si no, en la
//           página siguiente a la hoja, con el título "Evidencia del permiso ...".
//   - PDF:  se une con FPDI justo después de la hoja de su permiso (con franja de título).
//   - Sin librería FPDI o archivo ilegible: se deja un aviso visible, nunca se omite en silencio.
//
// Solo usa tablas y estilos simples (Dompdf soporta CSS 2.1, no flex ni grid, y
// su modelo de caja es "content-box": por eso las medidas ya descuentan el relleno).

use Dompdf\Dompdf;
use Dompdf\Options;

class PermisoPdf
{

    const ANCHO_MEDIA_PT = 468;    // 6.5 in  (media hoja oficio, vertical)
    const ALTO_PT = 612;           // 8.5 in
    const ANCHO_PAGINA_PT = 612;   // 8.5 in
    const ALTO_OFICIO_PT = 936;    // 13 in (oficio vertical)
    const MAX_PERMISOS = 300;      // tope por descarga, para no agotar memoria

    private const ESTADOS = [
        'en_proceso' => 'Borrador',
        'por_firmar_reemplazo' => 'Por firmar (reemplazo)',
        'por_firmar_jefe' => 'Por firmar (jefe)',
        'por_firmar_jefe_final' => 'Firma final pendiente',
        'aprobado_pendiente_regreso' => 'Regreso pendiente',
        'firmado' => 'Firmado',
        'devuelto' => 'Devuelto',
        'devuelto_regreso' => 'Llegada devuelta',
        'rechazado' => 'Rechazado',
        'anulado' => 'Anulado',
    ];

    private const MESES = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

    /**
     * Devuelve los bytes del PDF.
     * $resolverImagen(string $rutaRelativa): ?string  -> data URI de la imagen
     * (si se omite se leen los archivos de uploads con FileManager).
     */
    public static function generar(array $permisos, bool $dosPorHoja, string $generadoPor = '', ?callable $resolverImagen = null, ?array $formato = null): string
    {
        $pdfsDespues = [];   // nº de página (1-based) => evidencias PDF que van justo después
        $html = self::construirHtml($permisos, $dosPorHoja, $generadoPor, $resolverImagen, $pdfsDespues, $formato);

        $opciones = new Options();
        $opciones->set('isRemoteEnabled', false);
        $opciones->set('isHtml5ParserEnabled', true);
        $opciones->set('defaultFont', 'Helvetica');
        $opciones->set('chroot', realpath(__DIR__ . '/../../'));

        $dompdf = new Dompdf($opciones);
        // 1 permiso: 612 x 612. 2 por hoja: oficio vertical 612 x 936 (uno arriba, otro abajo).
        $dompdf->setPaper([0, 0, self::ANCHO_PAGINA_PT, $dosPorHoja ? self::ALTO_OFICIO_PT : self::ALTO_PT]);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $base = $dompdf->output();

        // Evidencias en PDF: Dompdf no puede incrustar páginas de otro PDF, se unen con FPDI.
        return $pdfsDespues ? self::unirEvidenciasPdf($base, $pdfsDespues) : $base;
    }

    public static function construirHtml(array $permisos, bool $dosPorHoja, string $generadoPor = '', ?callable $resolverImagen = null, array &$pdfsDespues = [], ?array $formato = null): string
    {
        $resolverImagen ??= [self::class, 'imagenDesdeUploads'];
        // Versión y fecha del formato GH-FT-10 (las pone el endpoint desde la base de datos).
        $formato ??= ['version' => 1, 'fecha' => date('Y-m-d')];
        $logo = self::logo();
        $generado = date('d/m/Y H:i');
        $porPagina = $dosPorHoja ? 2 : 1;

        $anchoHoja = self::ANCHO_PAGINA_PT;
        $altoPagina = $dosPorHoja ? self::ALTO_OFICIO_PT : self::ALTO_PT;
        $altoSeguro = $altoPagina - 4;
        $altoCelda = $dosPorHoja ? intdiv($altoSeguro, 2) : $altoSeguro;
        $relleno = $dosPorHoja ? 20 : 22;
        $margenSup = $dosPorHoja ? 8 : 10;

        $anchoInterno = $anchoHoja - 2 * $relleno;
        $altoInterno = $altoCelda - $margenSup - ($dosPorHoja ? 2 : 0);

        // Espacio para la foto de evidencia dentro de una mitad (descontando el título).
        $imgMaxW = $anchoInterno;
        $imgMaxH = $altoInterno - 46;

        // Si falta la librería FPDI, las evidencias PDF no se pueden unir: se avisa en la hoja.
        $puedeUnirPdf = class_exists(\setasign\Fpdi\Fpdi::class);

        $celda = static function (int $k, string $contenido) use ($dosPorHoja): string {
            // Línea punteada de corte entre el permiso de arriba y el de abajo.
            $corte = ($dosPorHoja && $k === 0) ? ' corte' : '';
            return '<tr><td class="mitad' . $corte . '"><div class="interno">' . $contenido . '</div></td></tr>';
        };

        $paginasHtml = [];   // cada elemento = una página del PDF base
        $pdfsDespues = [];
        foreach (array_chunk(array_values($permisos), $porPagina) as $grupo) {
            // Evidencias de los permisos de esta hoja.
            $imagenes = [];
            $pdfs = [];
            foreach ($grupo as $p) {
                $ev = self::evidenciaDe($p, $puedeUnirPdf);
                if ($ev === null)
                    continue;
                if ($ev['tipo'] === 'pdf')
                    $pdfs[] = $ev;
                else
                    $imagenes[] = $ev;
            }

            // 1) Hoja de permisos. Si queda una mitad libre, ahí va la primera foto de evidencia.
            $filas = '';
            for ($k = 0; $k < $porPagina; $k++) {
                if (isset($grupo[$k])) {
                    $contenido = self::bloquePermiso($grupo[$k], $logo, $generado, $generadoPor, $resolverImagen, $dosPorHoja, $formato);
                } elseif ($imagenes) {
                    $contenido = self::bloqueEvidencia(array_shift($imagenes), $imgMaxW, $imgMaxH);
                } else {
                    $contenido = '';
                }
                $filas .= $celda($k, $contenido);
            }
            $paginasHtml[] = '<table class="hoja">' . $filas . '</table>';

            // 2) Fotos restantes: página(s) de evidencia justo después de la hoja.
            foreach (array_chunk($imagenes, $porPagina) as $lote) {
                $filas = '';
                for ($k = 0; $k < $porPagina; $k++) {
                    $filas .= $celda($k, isset($lote[$k]) ? self::bloqueEvidencia($lote[$k], $imgMaxW, $imgMaxH) : '');
                }
                $paginasHtml[] = '<table class="hoja">' . $filas . '</table>';
            }

            // 3) Evidencias en PDF: se insertan después de la última página de este grupo.
            if ($pdfs)
                $pdfsDespues[count($paginasHtml)] = $pdfs;
        }

        $cuerpo = '';
        $ultima = count($paginasHtml) - 1;
        foreach ($paginasHtml as $i => $tabla) {
            $cuerpo .= '<div class="pagina"' . ($i === $ultima ? '' : ' style="page-break-after:always"') . '>' . $tabla . '</div>';
        }

        // Modo compacto (2 por hoja): cada permiso tiene solo 468 pt de alto.
        $secMargen = $dosPorHoja ? 7 : 12;
        $filaPad = $dosPorHoja ? 1.5 : 2.5;
        $firmaAlto = $dosPorHoja ? 40 : 58;
        $pendPad = $dosPorHoja ? 14 : 24;
        $tamBase = $dosPorHoja ? 8.5 : 9;
        $tamMotivo = $dosPorHoja ? 8.5 : 9;
        return '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><style>
            @page { margin: 0; }
            body { margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif; font-size: ' . $tamBase . 'pt; color: #1f2937; }
            .pagina { width: ' . $anchoHoja . 'pt; height: ' . $altoSeguro . 'pt; overflow: hidden; }
            table { border-collapse: collapse; }
            .hoja { width: ' . $anchoHoja . 'pt; }
            td { vertical-align: top; }
            td.mitad { width: ' . $anchoHoja . 'pt; height: ' . $altoCelda . 'pt; padding: 0; }
            td.corte { border-bottom: 1px dashed #9ca3af; }
            .interno { width: ' . $anchoInterno . 'pt; height: ' . $altoInterno . 'pt; margin: ' . $margenSup . 'pt ' . $relleno . 'pt 0 ' . $relleno . 'pt; overflow: hidden; }
            .t100 { width: 100%; }
            .enc td { vertical-align: middle; }
            .titulo { font-size: 12.5pt; font-weight: bold; color: #111827; }
            .sub { font-size: 7pt; color: #6b7280; }
            .consec { font-size: 10.5pt; font-weight: bold; color: #b91c1c; text-align: right; }
            .estado { font-size: 8pt; font-weight: bold; text-align: right; }
            .sec { font-size: 7pt; font-weight: bold; letter-spacing: .6pt; text-transform: uppercase; color: #6b7280; margin: ' . $secMargen . 'pt 0 3pt 0; }
            .caja { border: 1px solid #d1d5db; }
            .caja td { padding: 3pt 6pt; }
            .val-sm { font-size: 8pt; font-weight: bold; margin-top: 1pt; }
            .lbl { font-size: 6.5pt; text-transform: uppercase; color: #6b7280; }
            .val { font-size: 9pt; font-weight: bold; color: #111827; }
            .motivo { font-size: ' . $tamMotivo . 'pt; line-height: 1.3; }
            .dias td { padding: ' . $filaPad . 'pt 3pt; border-bottom: 1px solid #e5e7eb; font-size: 8.5pt; }
            .tot { font-weight: bold; color: #b91c1c; }
            .firma-box { height: ' . $firmaAlto . 'pt; border-bottom: 1px solid #374151; text-align: center; }
            .pend { font-size: 7.5pt; color: #9ca3af; font-style: italic; padding-top: ' . $pendPad . 'pt; }
            .fcel { text-align: center; padding: 0 3pt; }
            .nombre { font-size: 8pt; font-weight: bold; margin-top: 2pt; }
            .rol { font-size: 6pt; text-transform: uppercase; color: #6b7280; }
            .aviso { border: 1px solid #fca5a5; background: #fef2f2; color: #991b1b; padding: 3pt 6pt; margin-top: 6pt; font-size: 8pt; }
            .evi-t { font-size: 10pt; font-weight: bold; color: #111827; }
            .evi-c { text-align: center; margin-top: 6pt; }
            .evi-nota { border: 1px dashed #9ca3af; color: #6b7280; padding: 14pt; margin-top: 8pt; font-size: 8.5pt; text-align: center; }
            .pie { font-size: 6.5pt; color: #00000; margin-top: 8pt; }
            .formato-info { font-size: 6.5pt; color: #6b7280; margin-top: 1pt; }
            .nota-legal { font-size: 6pt; color: #4b5563; margin-top: 4pt; padding-top: 3pt; border-top: 0.5pt solid #d1d5db; line-height: 1.25; }
        </style></head><body>' . $cuerpo . '</body></html>';
    }

    // ------------------------------------------------------------------ un permiso
    private static function bloquePermiso(array $p, ?string $logo, string $generado, string $generadoPor, callable $resolver, bool $compacto = false, ?array $formato = null): string
    {
        $e = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
        // Formato con el que se creó el permiso (trazabilidad). Si no lo tiene, se usa el recibido.
        if (!empty($p['formato_version'])) {
            $formato = ['codigo' => $p['formato_codigo'] ?? null, 'version' => $p['formato_version'], 'fecha' => $p['formato_fecha'] ?? null];
        }
        $estado = self::ESTADOS[$p['estado'] ?? ''] ?? (string) ($p['estado'] ?? '');
        $colorEstado = match ($p['estado'] ?? '') {
            'firmado' => '#15803d',
            'rechazado', 'anulado' => '#b91c1c',
            'devuelto', 'devuelto_regreso' => '#c2410c',
            default => '#a16207',
        };

        $img = static function (?string $ruta) use ($resolver): ?string {
            return $ruta ? $resolver($ruta) : null;
        };

        // El jefe puede haber pre-firmado (permiso con regreso pendiente).
        $firmaJefe = $p['firma_jefe'] ?: ($p['firma_jefe_prefirmado'] ?? null);

        $firmantes = [
            ['Solicitante', $p['nombre_empleado_snapshot'] ?? '', $p['cedula_empleado'] ?? '', $p['firma_solicitante'] ?? null],
        ];
        if (!empty($p['cedula_reemplazo'])) {
            $firmantes[] = ['Reemplazo', $p['nombre_reemplazo'] ?? '', $p['cedula_reemplazo'], $p['firma_reemplazo'] ?? null];
        }
        $firmantes[] = ['Jefe inmediato', $p['nombre_jefe'] ?? '', $p['cedula_jefe'] ?? '', $firmaJefe];

        $celdasFirma = '';
        foreach ($firmantes as [$rol, $nombre, $cedula, $ruta]) {
            $uri = $img($ruta);
            $celdasFirma .= '<td class="fcel" style="width:' . round(100 / count($firmantes), 2) . '%"><div class="firma-box">'
                . ($uri ? self::imgAjustada($uri, 118, $compacto ? 36 : 54) : '<div class="pend">Pendiente de firma</div>')
                . '</div><div class="nombre">' . $e($nombre !== '' ? $nombre : 'Sin nombre') . '</div>'
                . '<div class="rol">' . $e($rol) . ' · C.C. ' . $e($cedula ?: '-') . '</div></td>';
        }

        $avisos = '';
        foreach ([['motivo_anulacion', 'Anulado'], ['motivo_rechazo', 'Rechazado'], ['motivo_devolucion', 'Devuelto']] as [$campo, $tit]) {
            $estadoCoincide = ($campo === 'motivo_anulacion' && ($p['estado'] ?? '') === 'anulado')
                || ($campo === 'motivo_rechazo' && ($p['estado'] ?? '') === 'rechazado')
                || ($campo === 'motivo_devolucion' && in_array($p['estado'] ?? '', ['devuelto', 'devuelto_regreso'], true));
            if ($estadoCoincide && !empty($p[$campo])) {
                $avisos .= '<div class="aviso"><strong>' . $tit . ':</strong> ' . $e(self::recortar((string) $p[$campo], 220)) . '</div>';
            }
        }

        $totalDias = $p['total_dias'] ?? null;
        $totales = ($totalDias !== null ? '<span class="tot">' . $e(self::dias((int) $totalDias)) . '</span> · ' : '')
            . '<span class="tot">' . $e(self::horas($p['total_horas'] ?? null)) . '</span>';

        $logoHtml = $logo ? '<img src="' . $logo . '" style="width:' . ($compacto ? 46 : 60) . 'pt;height:' . ($compacto ? 46 : 60) . 'pt">' : '';
        $quien = $generadoPor !== '' ? ' por ' . $e($generadoPor) : '';

        return '<table class="enc t100"><tr>
                <td style="width:63pt">' . $logoHtml . '</td>
                <td><div class="titulo">SOLICITUD DE TIPO: ' . $e(self::tipo($p['tipo_permiso'] ?? 'Permiso')) . '</div>
                    <div class="sub">CHVB · Gestion de Talento Humano</div></td>
                <td style="width:135pt"><div class="consec">' . $e($p['consecutivo'] ?? '') . '</div>
                    <div class="formato-info">Versión-' . $e((string) ($formato['version'] ?? 1)) . '</div>
                    <div class="formato-info">Fecha-' . $e(self::fecha($formato['fecha'] ?? null)) . '</div></td>
            </tr></table>

            <div class="sec">Solicitante</div>
            <table class="t100"><tr>
                <td style="width:76%;vertical-align:top">
                    <table class="caja t100">
                        <tr><td style="width:62%"><div class="lbl">Nombre</div><div class="val">' . $e($p['nombre_empleado_snapshot'] ?? '') . '</div></td>
                            <td><div class="lbl">Cédula</div><div class="val">' . $e($p['cedula_empleado'] ?? '') . '</div></td></tr>
                        <tr><td><div class="lbl">Cargo</div><div class="val">' . $e($p['cargo_empleado_snapshot'] ?? '') . '</div></td>
                            <td><div class="lbl">Celular</div><div class="val">' . $e($p['celular_empleado_snapshot'] ?: '-') . '</div></td></tr>
                    </table>
                </td>
                <td style="width:24%;vertical-align:top;padding-left:8pt">
                    <table class="caja t100">
                        <tr><td><div class="lbl">Estado</div><div class="val-sm" style="color:' . $colorEstado . '">' . $e($estado) . '</div></td></tr>
                        <tr><td><div class="lbl">Fecha de solicitud</div><div class="val-sm">' . $e(self::fecha($p['fecha_solicitud'] ?? null)) . '</div></td></tr>
                    </table>
                </td>
            </tr></table>

            <div class="sec">Motivo</div>
            <div class="motivo">' . nl2br($e(self::recortar((string) ($p['motivo'] ?? ''), 380))) . '</div>

            <div class="sec">Condiciones</div>
            <div class="motivo">Remunerado: <strong>' . (!empty($p['remunerado']) ? 'Sí' : 'No') . '</strong> &nbsp;·&nbsp; Compensatorio: <strong>' . (!empty($p['es_compensatorio']) ? 'Sí' : 'No') . '</strong> &nbsp;·&nbsp; Devolución de tiempo: <strong>' . (!empty($p['es_devolucion']) ? 'Sí' : 'No') . '</strong></div>

            <div class="sec">Fechas y horas · ' . $totales . '</div>
            ' . self::tablaDias($p, $e) . '
            ' . $avisos . '

            <div class="sec">Firmas</div>
            <table class="t100"><tr>' . $celdasFirma . '</tr></table>

            <div class="pie">Generado el ' . $e($generado) . $quien . '</div>
            <div class="nota-legal">
                <div><strong>Nota importante:</strong> Las vacaciones deben solicitarse con 2 meses de anticipación y los permisos personales con 2 días de anticipación, según instructivo GH-FT-10.</div>
                <div>La compensación debe realizarse dentro del mismo mes del permiso.</div>
                <div>No se considerará accidente de trabajo si ocurre durante permisos que no sean misión institucional ordenada por el empleador.</div>
            </div>';
    }

    // ------------------------------------------------------------------ días
    /** Lista cada día en dos columnas (máx. 8 filas c/u) para que quepa en media hoja. */
    private static function tablaDias(array $p, callable $e): string
    {
        $dias = $p['dias'] ?? [];
        if (!$dias) {
            if (!empty($p['es_salida_pendiente_regreso'])) {
                return '<div class="motivo">Salida: ' . $e(self::fecha($p['fecha_inicio'] ?? null)) . ' desde las '
                    . $e(substr((string) ($p['hora_inicio'] ?? ''), 0, 5)) . ' · llegada por confirmar</div>';
            }
            return '<div class="sub">Sin desglose disponible.</div>';
        }

        // Un renglón por día, repartido en dos columnas de máximo 8 filas cada una
        // (hasta 16 días). Se balancean: con 4 días quedan 2 + 2, con 9 quedan 5 + 4.
        $porColumna = 8;
        $maxDias = $porColumna * 2;
        $visibles = array_slice($dias, 0, $maxDias);
        $ocultos = count($dias) - count($visibles);
        $filasCol1 = min($porColumna, (int) ceil(count($visibles) / 2));
        $columnas = [array_slice($visibles, 0, $filasCol1), array_slice($visibles, $filasCol1)];

        $celdas = '';
        foreach ($columnas as $i => $col) {
            $filas = '<tr><td class="sub">Fecha</td><td class="sub">Horario</td><td class="sub" style="text-align:right">Horas</td></tr>';
            foreach ($col as $d) {
                $filas .= '<tr><td>' . $e(self::fecha($d['fecha'] ?? null)) . '</td>'
                    . '<td>' . $e(substr((string) ($d['hora_inicio'] ?? ''), 0, 5) . ' – ' . substr((string) ($d['hora_fin'] ?? ''), 0, 5)) . '</td>'
                    . '<td style="text-align:right">' . $e(self::horas($d['horas_netas'] ?? null)) . '</td></tr>';
            }
            $contenido = $col ? '<table class="dias t100">' . $filas . '</table>' : '';
            $celdas .= '<td style="width:50%;' . ($i === 1 ? 'padding-left:10pt;' : 'padding-right:10pt;') . '">' . $contenido . '</td>';
        }

        $aviso = $ocultos > 0
            ? '<div class="sub" style="margin-top:2pt">… y ' . $ocultos . ' día(s) más (ver detalle en el sistema)</div>'
            : '';
        return '<table class="t100"><tr>' . $celdas . '</tr></table>' . $aviso;
    }


    // ------------------------------------------------------------------ evidencias
    /**
     * Datos de la evidencia del permiso, o null si no tiene.
     * tipo: 'img' (foto), 'pdf' (se une con FPDI) o 'nota' (hay evidencia pero no se puede mostrar).
     */
    private static function evidenciaDe(array $p, bool $puedeUnirPdf): ?array
    {
        $rel = trim((string) ($p['evidencia_archivo'] ?? ''));
        if ($rel === '')
            return null;

        require_once __DIR__ . '/FileManager.php';
        $abs = FileManager::rutaAbsolutaDesdeRelativa($rel);
        $base = ['p' => $p, 'abs' => $abs];

        if ($abs === null || !is_file($abs))
            return $base + ['tipo' => 'nota', 'msg' => 'La evidencia está registrada, pero el archivo no está disponible en el servidor.'];

        $ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            return $puedeUnirPdf
                ? $base + ['tipo' => 'pdf']
                : $base + ['tipo' => 'nota', 'msg' => 'La evidencia es un PDF que no se pudo unir a este documento (falta instalar setasign/fpdi). Consúltala en el sistema.'];
        }
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true))
            return $base + ['tipo' => 'img'];

        return $base + ['tipo' => 'nota', 'msg' => 'Formato de evidencia no soportado para el PDF.'];
    }

    private static function tituloEvidencia(array $p): string
    {
        return 'Evidencia del permiso ' . ($p['consecutivo'] ?? '');
    }

    private static function subtituloEvidencia(array $p): string
    {
        return trim(($p['nombre_empleado_snapshot'] ?? '') . ' · ' . ($p['tipo_permiso'] ?? ''), ' ·');
    }

    /** Mitad de hoja con la foto de evidencia y su título. */
    private static function bloqueEvidencia(array $ev, int $maxW, int $maxH): string
    {
        $e = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
        $p = $ev['p'];
        $html = '<div class="evi-t">' . $e(mb_strtoupper(self::tituloEvidencia($p), 'UTF-8')) . '</div>'
            . '<div class="sub">' . $e(self::subtituloEvidencia($p)) . '</div>';

        $uri = $ev['tipo'] === 'img' ? self::fotoEvidencia((string) $ev['abs'], 1400) : null;
        if ($uri !== null)
            return $html . '<div class="evi-c">' . self::imgAjustada($uri, $maxW, $maxH) . '</div>';

        $msg = $ev['msg'] ?? 'No se pudo leer la imagen de la evidencia.';
        return $html . '<div class="evi-nota">' . $e($msg) . '</div>';
    }

    /** Foto de evidencia: corrige la rotación EXIF del celular y la reduce a JPEG para que el PDF pese poco. */
    private static function fotoEvidencia(string $ruta, int $maxLado): ?string
    {
        if (!function_exists('imagecreatefromstring'))
            return null;
        $datos = @file_get_contents($ruta);
        if ($datos === false)
            return null;
        $src = @imagecreatefromstring($datos);
        unset($datos);
        if (!$src)
            return null;

        if (function_exists('exif_read_data') && preg_match('/\.jpe?g$/i', $ruta)) {
            $exif = @exif_read_data($ruta);
            $giro = match ((int) ($exif['Orientation'] ?? 1)) {
                3 => 180, 6 => -90, 8 => 90, default => 0,
            };
            if ($giro !== 0) {
                $rot = imagerotate($src, $giro, 0);
                if ($rot) {
                    $src = $rot;
                }
            }
        }

        $w = imagesx($src);
        $h = imagesy($src);
        $escala = min(1, $maxLado / max($w, $h));
        $nw = max(1, (int) round($w * $escala));
        $nh = max(1, (int) round($h * $escala));
        $dst = imagecreatetruecolor($nw, $nh);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));   // aplana PNG/WebP transparentes
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        ob_start();
        imagejpeg($dst, null, 80);
        $jpg = (string) ob_get_clean();
        return 'data:image/jpeg;base64,' . base64_encode($jpg);
    }

    /**
     * Inserta cada evidencia PDF justo después de la página indicada del PDF base.
     * Cada página de la evidencia se conserva intacta, con una franja arriba que dice a qué permiso pertenece.
     * Si un PDF no se puede leer (cifrado, formato muy nuevo), se deja una página con el aviso: nunca se rompe la descarga.
     */
    private static function unirEvidenciasPdf(string $base, array $pdfsDespues): string
    {
        $pdf = new \setasign\Fpdi\Fpdi('P', 'pt');
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);

        $total = $pdf->setSourceFile(\setasign\Fpdi\PdfParser\StreamReader::createByString($base));
        $plantillas = [];
        for ($i = 1; $i <= $total; $i++)
            $plantillas[$i] = $pdf->importPage($i);

        $txt = static fn(string $t): string => (string) @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $t);

        for ($i = 1; $i <= $total; $i++) {
            $t = $pdf->getTemplateSize($plantillas[$i]);
            $pdf->AddPage($t['orientation'], [$t['width'], $t['height']]);
            $pdf->useTemplate($plantillas[$i]);

            foreach ($pdfsDespues[$i] ?? [] as $ev) {
                $titulo = $txt(mb_strtoupper(self::tituloEvidencia($ev['p']), 'UTF-8'));
                $sub = $txt(self::subtituloEvidencia($ev['p']));
                $temporal = null;
                try {
                    try {
                        $paginas = $pdf->setSourceFile($ev['abs']);
                    } catch (\Throwable $primero) {
                        // PDF con compresión moderna (celulares, escáneres): se reescribe y se reintenta.
                        $temporal = self::normalizarPdf($ev['abs']);
                        if ($temporal === null)
                            throw $primero;
                        $paginas = $pdf->setSourceFile($temporal);
                    }
                    $tpls = [];
                    for ($n = 1; $n <= min($paginas, 30); $n++)
                        $tpls[] = $pdf->importPage($n);
                } catch (\Throwable $ex) {
                    error_log('PermisoPdf evidencia PDF: ' . $ex->getMessage());
                    if ($temporal !== null)
                        @unlink($temporal);
                    $pdf->AddPage('P', [612, 612]);
                    self::franjaEvidencia($pdf, 612, $titulo, $sub);
                    $pdf->SetFont('Helvetica', '', 10);
                    $pdf->SetTextColor(107, 114, 128);
                    $pdf->SetXY(40, 120);
                    $pdf->MultiCell(532, 14, $txt('La evidencia es un PDF que no se pudo incorporar (archivo cifrado o con un formato no compatible). Consúltala en el sistema, en el detalle del permiso.'), 0, 'C');
                    continue;
                }
                if ($temporal !== null)
                    @unlink($temporal);   // las plantillas ya quedaron en memoria
                foreach ($tpls as $tpl) {
                    $t = $pdf->getTemplateSize($tpl);
                    $banda = 26;
                    $pdf->AddPage($t['orientation'], [$t['width'], $t['height'] + $banda]);
                    $pdf->useTemplate($tpl, 0, $banda, $t['width'], $t['height']);
                    self::franjaEvidencia($pdf, $t['width'], $titulo, $sub);
                }
            }
        }
        return $pdf->Output('S');
    }

    /**
     * Reescribe un PDF sin object-streams para que el lector gratuito de FPDI pueda leerlo.
     * Requiere qpdf o Ghostscript en el servidor; si no hay (o exec está deshabilitado) devuelve null.
     */
    private static function normalizarPdf(string $ruta): ?string
    {
        $deshabilitadas = array_map('trim', explode(',', (string) ini_get('disable_functions')));
        if (!function_exists('exec') || in_array('exec', $deshabilitadas, true))
            return null;

        $salida = tempnam(sys_get_temp_dir(), 'evi_');
        if ($salida === false)
            return null;

        $intentos = [
            'qpdf --object-streams=disable ' . escapeshellarg($ruta) . ' ' . escapeshellarg($salida),
            'gs -q -dNOPAUSE -dBATCH -dSAFER -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -sOutputFile=' . escapeshellarg($salida) . ' ' . escapeshellarg($ruta),
        ];
        foreach ($intentos as $cmd) {
            $codigo = 1;
            @exec('timeout 20 ' . $cmd . ' 2>/dev/null', $o, $codigo);
            if (in_array($codigo, [0, 3], true) && is_file($salida) && filesize($salida) > 0)   // qpdf devuelve 3 si solo hubo advertencias
                return $salida;
        }
        @unlink($salida);
        return null;
    }

    private static function franjaEvidencia($pdf, float $ancho, string $titulo, string $sub): void
    {
        $pdf->SetTextColor(17, 24, 39);
        $pdf->SetFont('Helvetica', 'B', 9.5);
        $pdf->SetXY(20, 5);
        $pdf->Cell($ancho - 40, 10, $titulo, 0, 0, 'L');
        $pdf->SetTextColor(107, 114, 128);
        $pdf->SetFont('Helvetica', '', 7.5);
        $pdf->SetXY(20, 15);
        $pdf->Cell($ancho - 40, 8, $sub, 0, 0, 'L');
        $pdf->SetDrawColor(209, 213, 219);
        $pdf->Line(20, 25, $ancho - 20, 25);
    }

    // ------------------------------------------------------------------ utilidades
    private static function tipo(string $t): string
    {
        return mb_strtoupper($t, 'UTF-8');
    }

    private static function fecha(?string $f): string
    {
        if (!$f)
            return '-';
        [$y, $m, $d] = array_map('intval', explode('-', substr($f, 0, 10)) + [0, 0, 0]);
        return ($y && $m && $d) ? sprintf('%d %s %d', $d, self::MESES[$m - 1], $y) : $f;
    }

    private static function horas($h): string
    {
        if ($h === null || $h === '')
            return 'Pendiente';
        $t = (int) round((float) $h * 60);
        $hh = intdiv($t, 60);
        $mm = $t % 60;
        if ($hh > 0 && $mm > 0)
            return "{$hh} h {$mm} min";
        return $hh > 0 ? "{$hh} h" : "{$mm} min";
    }

    private static function dias(int $n): string
    {
        return $n . ($n === 1 ? ' día' : ' días');
    }

    private static function recortar(string $t, int $max): string
    {
        $t = trim($t);
        return mb_strlen($t, 'UTF-8') > $max ? rtrim(mb_substr($t, 0, $max - 1, 'UTF-8')) . '…' : $t;
    }

    // ------------------------------------------------------------------ imágenes
    /** <img> con ancho y alto explícitos (en pt) que cabe en $maxW x $maxH sin deformarse. */
    private static function imgAjustada(string $uri, int $maxW, int $maxH): string
    {
        $w = $h = 0;
        $coma = strpos($uri, ',');
        $info = $coma !== false ? @getimagesizefromstring((string) base64_decode(substr($uri, $coma + 1))) : false;
        if ($info) {
            $w = (int) $info[0];
            $h = (int) $info[1];
        }
        if ($w <= 0 || $h <= 0)
            return '<img src="' . $uri . '" style="height:' . $maxH . 'pt">';
        $escala = min($maxW / $w, $maxH / $h);
        return '<img src="' . $uri . '" style="width:' . round($w * $escala, 1) . 'pt;height:' . round($h * $escala, 1) . 'pt">';
    }

    /** Lee una imagen de uploads (firmas) y la reduce para que el PDF pese poco. */
    public static function imagenDesdeUploads(string $rutaRelativa): ?string
    {
        require_once __DIR__ . '/FileManager.php';
        $ruta = FileManager::rutaAbsolutaDesdeRelativa($rutaRelativa);
        if ($ruta === null || !is_file($ruta))
            return null;
        if (!in_array(strtolower(pathinfo($ruta, PATHINFO_EXTENSION)), ['png', 'jpg', 'jpeg', 'webp'], true))
            return null;
        return self::reducir($ruta, 420);
    }

    private static function logo(): ?string
    {
        static $uri = false;
        if ($uri === false) {
            $ruta = __DIR__ . '/../../public/assets/img/logo-bomberos.png';
            $uri = is_file($ruta) ? self::reducir($ruta, 160) : null;
        }
        return $uri;
    }

    /** Reduce a $anchoMax px, aplana sobre blanco y devuelve data URI PNG. */
    private static function reducir(string $ruta, int $anchoMax): ?string
    {
        $datos = @file_get_contents($ruta);
        if ($datos === false)
            return null;
        if (!function_exists('imagecreatefromstring'))
            return 'data:image/png;base64,' . base64_encode($datos);

        $src = @imagecreatefromstring($datos);
        if (!$src)
            return null;
        $w = imagesx($src);
        $h = imagesy($src);
        $nw = min($w, $anchoMax);
        $nh = (int) round($h * ($nw / $w));
        $dst = imagecreatetruecolor($nw, $nh);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        ob_start();
        imagepng($dst, null, 6);
        $png = ob_get_clean();
        return 'data:image/png;base64,' . base64_encode($png);
    }
}
