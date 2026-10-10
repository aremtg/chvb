<?php
// src/helpers/PermisoPdf.php
//
// Genera el PDF de uno o varios permisos. El PDF se crea en memoria y se envía
// directo al navegador (descarga): NO se guarda en la BD ni en las carpetas del
// servidor. Cada permiso es VERTICAL y ocupa media hoja oficio (6.5 x 8.5 in).
//
//   - 1 permiso      -> página vertical de media hoja oficio (468 x 612 pt).
//   - 2 por hoja     -> hoja oficio (13 x 8.5 in) con los 2 permisos verticales
//                       lado a lado y una línea punteada para cortar por la mitad.
//
// Solo usa tablas y estilos simples (Dompdf soporta CSS 2.1, no flex ni grid, y
// su modelo de caja es "content-box": por eso las medidas ya descuentan el relleno).

use Dompdf\Dompdf;
use Dompdf\Options;

class PermisoPdf
{

    const ANCHO_MEDIA_PT = 468;    // 6.5 in  (media hoja oficio, vertical)
    const ALTO_PT = 612;           // 8.5 in
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
    public static function generar(array $permisos, bool $dosPorHoja, string $generadoPor = '', ?callable $resolverImagen = null): string
    {
        $html = self::construirHtml($permisos, $dosPorHoja, $generadoPor, $resolverImagen);

        $opciones = new Options();
        $opciones->set('isRemoteEnabled', false);
        $opciones->set('isHtml5ParserEnabled', true);
        $opciones->set('defaultFont', 'Helvetica');
        $opciones->set('chroot', realpath(__DIR__ . '/../../'));

        $dompdf = new Dompdf($opciones);
        $anchoPagina = $dosPorHoja
            ? self::ANCHO_MEDIA_PT * 2
            : 612; // 8,5 pulgadas: ancho carta/oficio

        $dompdf->setPaper([
            0,
            0,
            $anchoPagina,
            self::ALTO_PT
        ]);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        return $dompdf->output();
    }

    public static function construirHtml(array $permisos, bool $dosPorHoja, string $generadoPor = '', ?callable $resolverImagen = null): string
    {
        $resolverImagen ??= [self::class, 'imagenDesdeUploads'];
        $logo = self::logo();
        $generado = date('d/m/Y H:i');
        $porPagina = $dosPorHoja ? 2 : 1;

        $paginas = array_chunk(array_values($permisos), $porPagina);
        $cuerpo = '';
        foreach ($paginas as $n => $grupo) {
            $esUltima = $n === count($paginas) - 1;
            $celdas = '';
            for ($k = 0; $k < $porPagina; $k++) {
                // Línea punteada de corte entre las dos mitades.
                $corte = ($dosPorHoja && $k === 0) ? ' corte' : '';
                $contenido = isset($grupo[$k]) ? self::bloquePermiso($grupo[$k], $logo, $generado, $generadoPor, $resolverImagen) : '';
                $celdas .= '<td class="mitad' . $corte . '"><div class="interno">' . $contenido . '</div></td>';
            }
            $cuerpo .= '<div class="pagina"' . ($esUltima ? '' : ' style="page-break-after:always"') . '><table class="hoja"><tr>' . $celdas . '</tr></table></div>';
        }

        $anchoMedia = $dosPorHoja ? self::ANCHO_MEDIA_PT : 612;
        $anchoHoja = $anchoMedia * $porPagina;

        $altoSeguro = self::ALTO_PT - 4;
        $relleno = 22;

        $anchoInterno = $anchoMedia - 2 * $relleno - ($dosPorHoja ? 2 : 0);
        $altoInterno = $altoSeguro - 10;      // 18 pt de margen superior
        return '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><style>
            @page { margin: 0; }
            body { margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif; font-size: 9pt; color: #1f2937; }
            .pagina { width: ' . $anchoHoja . 'pt; height: ' . $altoSeguro . 'pt; overflow: hidden; }
            table { border-collapse: collapse; }
            .hoja { width: ' . $anchoHoja . 'pt; }
            td { vertical-align: top; }
            td.mitad { width: ' . $anchoMedia . 'pt; height: ' . $altoSeguro . 'pt; padding: 0; }
            td.corte { border-right: 1px dashed #9ca3af; }
            .interno { width: ' . $anchoInterno . 'pt; height: ' . $altoInterno . 'pt; margin: 10pt ' . $relleno . 'pt 0 ' . $relleno . 'pt; overflow: hidden; }
            .t100 { width: 100%; }
            .enc td { vertical-align: middle; }
            .titulo { font-size: 12.5pt; font-weight: bold; color: #111827; }
            .sub { font-size: 7pt; color: #6b7280; }
            .consec { font-size: 10.5pt; font-weight: bold; color: #b91c1c; text-align: right; }
            .estado { font-size: 8pt; font-weight: bold; text-align: right; }
            .sec { font-size: 7pt; font-weight: bold; letter-spacing: .6pt; text-transform: uppercase; color: #6b7280; margin: 12pt 0 3pt 0; }
            .caja { border: 1px solid #d1d5db; }
            .caja td { padding: 3pt 6pt; }
            .lbl { font-size: 6.5pt; text-transform: uppercase; color: #6b7280; }
            .val { font-size: 9pt; font-weight: bold; color: #111827; }
            .motivo { font-size: 9pt; line-height: 1.35; }
            .dias td { padding: 2.5pt 3pt; border-bottom: 1px solid #e5e7eb; font-size: 8.5pt; }
            .tot { font-weight: bold; color: #b91c1c; }
            .firma-box { height: 58pt; border-bottom: 1px solid #374151; text-align: center; }
            .pend { font-size: 7.5pt; color: #9ca3af; font-style: italic; padding-top: 24pt; }
            .fcel { text-align: center; padding: 0 3pt; }
            .nombre { font-size: 8pt; font-weight: bold; margin-top: 2pt; }
            .rol { font-size: 6pt; text-transform: uppercase; color: #6b7280; }
            .aviso { border: 1px solid #fca5a5; background: #fef2f2; color: #991b1b; padding: 3pt 6pt; margin-top: 6pt; font-size: 8pt; }
            .pie { font-size: 6.5pt; color: #00000; margin-top: 8pt; }
        </style></head><body>' . $cuerpo . '</body></html>';
    }

    // ------------------------------------------------------------------ un permiso
    private static function bloquePermiso(array $p, ?string $logo, string $generado, string $generadoPor, callable $resolver): string
    {
        $e = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
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
                . ($uri ? self::imgAjustada($uri, 118, 54) : '<div class="pend">Pendiente de firma</div>')
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

        $logoHtml = $logo ? '<img src="' . $logo . '" style="width:60pt;height:60pt">' : '';
        $quien = $generadoPor !== '' ? ' por ' . $e($generadoPor) : '';

        return '<table class="enc t100"><tr>
                <td style="width:63pt">' . $logoHtml . '</td>
                <td><div class="titulo">SOLICITUD DE TIPO: ' . $e(self::tipo($p['tipo_permiso'] ?? 'Permiso')) . '</div>
                    <div class="sub">CHVB · Gestion de Talento Humano · Solicitado el ' . $e(self::fecha($p['fecha_solicitud'] ?? null)) . '</div></td>
                <td style="width:135pt"><div class="consec">' . $e($p['consecutivo'] ?? '') . '</div>
                    <div class="estado" style="color:' . $colorEstado . '">' . $e($estado) . '</div></td>
            </tr></table>

            <div class="sec">Solicitante</div>
            <table class="caja t100">
                <tr><td style="width:62%"><div class="lbl">Nombre</div><div class="val">' . $e($p['nombre_empleado_snapshot'] ?? '') . '</div></td>
                    <td><div class="lbl">Cédula</div><div class="val">' . $e($p['cedula_empleado'] ?? '') . '</div></td></tr>
                <tr><td><div class="lbl">Cargo</div><div class="val">' . $e($p['cargo_empleado_snapshot'] ?? '') . '</div></td>
                    <td><div class="lbl">Celular</div><div class="val">' . $e($p['celular_empleado_snapshot'] ?: '-') . '</div></td></tr>
            </table>

            <div class="sec">Motivo</div>
            <div class="motivo">' . nl2br($e(self::recortar((string) ($p['motivo'] ?? ''), 380))) . '</div>

            <div class="sec">Fechas y horas · ' . $totales . '</div>
            ' . self::tablaDias($p, $e) . '
            ' . $avisos . '

            <div class="sec">Firmas</div>
            <table class="t100"><tr>' . $celdasFirma . '</tr></table>

            <div class="pie">Generado el ' . $e($generado) . $quien . '</div>';
    }

    // ------------------------------------------------------------------ días
    /** Agrupa días consecutivos con el mismo horario para que quepan en media hoja. */
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

        $tramos = [];
        foreach ($dias as $d) {
            $llave = substr((string) $d['hora_inicio'], 0, 5) . '-' . substr((string) $d['hora_fin'], 0, 5) . '|' . (int) $d['incluido'] . '|' . (int) $d['es_festivo'];
            $ultimo = count($tramos) - 1;
            $fechaTs = strtotime((string) $d['fecha']);
            if ($ultimo >= 0 && $tramos[$ultimo]['llave'] === $llave && $fechaTs - $tramos[$ultimo]['fin_ts'] === 86400) {
                $tramos[$ultimo]['fin'] = $d['fecha'];
                $tramos[$ultimo]['fin_ts'] = $fechaTs;
                $tramos[$ultimo]['horas'] += (float) $d['horas_netas'];
                $tramos[$ultimo]['n']++;
            } else {
                $tramos[] = [
                    'llave' => $llave,
                    'ini' => $d['fecha'],
                    'fin' => $d['fecha'],
                    'fin_ts' => $fechaTs,
                    'horario' => substr((string) $d['hora_inicio'], 0, 5) . ' – ' . substr((string) $d['hora_fin'], 0, 5),
                    'horas' => (float) $d['horas_netas'],
                    'n' => 1,
                    'nota' => !empty($d['es_festivo']) ? ('festivo ' . ((int) $d['incluido'] === 1 ? 'contado' : 'no contado')) : '',
                ];
            }
        }

        $max = 7;
        $filas = '';
        foreach (array_slice($tramos, 0, $max) as $t) {
            $rango = $t['ini'] === $t['fin'] ? self::fecha($t['ini']) : self::fecha($t['ini']) . ' al ' . self::fecha($t['fin']);
            $filas .= '<tr><td>' . $e($rango) . ($t['n'] > 1 ? ' <span class="sub">(' . $t['n'] . ' días)</span>' : '') . '</td>'
                . '<td>' . $e($t['horario']) . '</td>'
                . '<td style="text-align:right">' . $e(self::horas($t['horas'])) . ($t['nota'] ? ' <span class="sub">· ' . $e($t['nota']) . '</span>' : '') . '</td></tr>';
        }
        if (count($tramos) > $max) {
            $filas .= '<tr><td colspan="3" class="sub">… y ' . (count($tramos) - $max) . ' tramo(s) más (ver detalle en el sistema)</td></tr>';
        }
        return '<table class="dias t100">' . $filas . '</table>';
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
