<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/formatos_guard.php';
requireFormatosAccess();

$archivo = basename((string)($_GET['f'] ?? ''));
if ($archivo === '') {
    http_response_code(400);
    exit('Archivo no especificado.');
}

$permitidos = [
    '/^RENOVACION_[^\/\\\\]+\.docx$/iu',
    '/^OTROSI_[^\/\\\\]+\.docx$/iu',
    '/^GH-FT-24 OTROSI AL CONTRATO INDIVIDUAL DE TRABAJO [^\/\\\\]+\.docx$/iu',
    '/^GH-FT-25 OTROSI CAMBIO DE SALARIO [^\/\\\\]+\.docx$/iu',
    '/^GH-FT-03 REMISION EXAMENES [^\/\\\\]+\.docx$/iu',
];

$valido = false;
foreach ($permitidos as $regex) {
    if (preg_match($regex, $archivo)) {
        $valido = true;
        break;
    }
}
if (!$valido) {
    http_response_code(400);
    exit('Archivo no válido.');
}

$base = realpath(__DIR__ . '/../../uploads/generados');
if ($base === false || !is_dir($base)) {
    http_response_code(500);
    exit('No se encontró la carpeta de archivos generados.');
}

$path = realpath($base . DIRECTORY_SEPARATOR . $archivo);
if (!$path || !is_file($path) || dirname($path) !== $base) {
    http_response_code(404);
    exit('Archivo no encontrado.');
}

$tmpBase = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'chvb_preview_' . bin2hex(random_bytes(8));
if (!mkdir($tmpBase, 0700, true) && !is_dir($tmpBase)) {
    http_response_code(500);
    exit('No fue posible preparar la vista previa.');
}

$cleanup = static function () use ($tmpBase): void {
    if (!is_dir($tmpBase)) return;
    $items = glob($tmpBase . DIRECTORY_SEPARATOR . '*') ?: [];
    foreach ($items as $item) {
        if (is_file($item)) @unlink($item);
        elseif (is_dir($item)) {
            foreach (glob($item . DIRECTORY_SEPARATOR . '*') ?: [] as $child) {
                if (is_file($child)) @unlink($child);
            }
            @rmdir($item);
        }
    }
    @rmdir($tmpBase);
};
register_shutdown_function($cleanup);

$pdf = $tmpBase . DIRECTORY_SEPARATOR . pathinfo($archivo, PATHINFO_FILENAME) . '.pdf';
$converted = false;
$converterUsed = '';

/*
 * 1) LibreOffice / soffice
 *
 * No dependemos únicamente de `where`, porque shell_exec puede estar
 * deshabilitado en algunos XAMPP/PHP y LibreOffice puede no estar en PATH.
 */
$candidatos = [];

$envPath = getenv('PATH');
if (is_string($envPath) && $envPath !== '') {
    $sep = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? ';' : ':';
    foreach (explode($sep, $envPath) as $dir) {
        $dir = trim($dir, " \t\r\n\"");
        if ($dir === '') continue;
        $candidatos[] = $dir . DIRECTORY_SEPARATOR . (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'soffice.exe' : 'soffice');
        $candidatos[] = $dir . DIRECTORY_SEPARATOR . (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'libreoffice.exe' : 'libreoffice');
    }
}

$candidatos = array_merge($candidatos, [
    'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
    'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
    'C:\\Program Files\\LibreOffice\\program\\libreoffice.exe',
    'C:\\Program Files (x86)\\LibreOffice\\program\\libreoffice.exe',
    'C:\\Program Files\\LibreOffice\\program\\soffice.com',
    'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.com',
    'C:\\ProgramData\\chocolatey\\bin\\soffice.exe',
    'C:\\ProgramData\\chocolatey\\bin\\libreoffice.exe',
    'C:\\Program Files\\Scoop\\apps\\libreoffice\\current\\program\\soffice.exe',
    'libreoffice',
    'soffice',
]);

$soffice = null;
$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

foreach (array_unique($candidatos) as $candidato) {
    if (is_file($candidato)) {
        $soffice = $candidato;
        break;
    }

    // Solo intentamos resolver comandos simples si PHP permite ejecutar procesos.
    if (in_array($candidato, ['libreoffice', 'soffice'], true) && function_exists('shell_exec')) {
        $probeCommand = $isWindows
            ? 'where ' . escapeshellarg($candidato) . ' 2>NUL'
            : 'command -v ' . escapeshellarg($candidato) . ' 2>/dev/null';
        $probe = @shell_exec($probeCommand);
        if (is_string($probe) && trim($probe) !== '') {
            $resolved = trim(strtok($probe, "\r\n"));
            if ($resolved !== '') {
                $soffice = $resolved;
                break;
            }
        }
    }
}

if ($soffice !== null) {
    $profileDir = $tmpBase . DIRECTORY_SEPARATOR . 'lo_profile';
    @mkdir($profileDir, 0700, true);

    $input = escapeshellarg($path);
    $outdir = escapeshellarg($tmpBase);
    $profileUri = 'file:///' . str_replace('\\', '/', $profileDir);
    $profile = escapeshellarg($profileUri);

    $cmd = escapeshellarg($soffice) .
        ' --headless --nologo --nodefault --nofirststartwizard' .
        ' -env:UserInstallation=' . $profile .
        ' --convert-to pdf --outdir ' . $outdir .
        ' ' . $input;

    $output = [];
    $exitCode = 0;
    if (function_exists('exec')) {
        @exec($cmd . ($isWindows ? ' 2>&1' : ' 2>&1'), $output, $exitCode);
    }

    clearstatcache(true, $pdf);
    if ($exitCode === 0 && is_file($pdf) && filesize($pdf) > 0) {
        $converted = true;
        $converterUsed = 'LibreOffice';
    }
}

/*
 * 2) Fallback para Windows con Microsoft Word instalado.
 *
 * Esto permite usar el visor incluso cuando LibreOffice no está instalado,
 * siempre que PHP tenga habilitado COM (com_dotnet) y exista Word de escritorio.
 */
if (!$converted && $isWindows && class_exists('COM')) {
    $word = null;
    $document = null;
    try {
        $word = new COM('Word.Application');
        $word->Visible = false;
        $word->DisplayAlerts = 0;
        $document = $word->Documents->Open($path, false, true, false);
        // 17 = wdExportFormatPDF
        $document->ExportAsFixedFormat($pdf, 17);
        $document->Close(false);
        $document = null;
        $word->Quit(false);
        $word = null;

        clearstatcache(true, $pdf);
        if (is_file($pdf) && filesize($pdf) > 0) {
            $converted = true;
            $converterUsed = 'Microsoft Word';
        }
    } catch (Throwable $e) {
        try {
            if ($document !== null) $document->Close(false);
        } catch (Throwable $ignored) {}
        try {
            if ($word !== null) $word->Quit(false);
        } catch (Throwable $ignored) {}
    }
}

if (!$converted) {
    http_response_code(503);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html lang="es"><meta charset="utf-8">';
    echo '<style>body{font-family:Arial,sans-serif;background:#f8fafc;padding:32px;color:#1f2937}.box{max-width:720px;margin:auto;background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;box-shadow:0 4px 18px rgba(0,0,0,.06)}h2{margin:0 0 12px;color:#b91c1c}li{margin:8px 0}</style>';
    echo '<div class="box"><h2>No se pudo generar la vista previa</h2>';
    echo '<p>El servidor pudo abrir el archivo, pero no encontró un conversor disponible para mostrarlo dentro del visor.</p>';
    echo '<p>En Windows puedes usar cualquiera de estas opciones:</p><ul>';
    echo '<li>Instalar <strong>LibreOffice</strong> en el servidor local.</li>';
    echo '<li>O tener <strong>Microsoft Word de escritorio</strong> instalado y habilitar la extensión <strong>COM (com_dotnet)</strong> de PHP.</li>';
    echo '</ul><p>El archivo original sigue disponible mediante <strong>Descargar</strong>.</p></div></html>';
    exit;
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . rawurlencode(pathinfo($archivo, PATHINFO_FILENAME) . '.pdf') . '"');
header('Content-Length: ' . filesize($pdf));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store, max-age=0');
readfile($pdf);
