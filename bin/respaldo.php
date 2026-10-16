<?php
/**
 * Respaldo de CHVB: copia de la base de datos (y, si se pide, de la carpeta uploads/) en un .zip con fecha.
 *
 * SOLO se ejecuta desde la terminal (no por el navegador):
 *
 *     C:\xampp\php\php.exe C:\xampp\htdocs\chvb\bin\respaldo.php
 *     C:\xampp\php\php.exe C:\xampp\htdocs\chvb\bin\respaldo.php --con-uploads
 *
 * Opciones (todas opcionales; si no se pasan se usa el .env):
 *     --con-uploads        incluye también uploads/ (hojas de vida, firmas, evidencias)
 *     --dias=30            borra respaldos con más de 30 días (siempre conserva los 3 más recientes)
 *     --destino=D:\respaldos\chvb
 *
 * Variables del .env que usa:
 *     DB_HOST, DB_NAME, DB_USER, DB_PASS   (las mismas de la aplicación)
 *     BACKUP_PATH            carpeta destino. Ponla en OTRO disco o en una carpeta de red.
 *     BACKUP_RETENTION_DAYS  días que se conservan los respaldos (por defecto 30)
 *     MYSQLDUMP_PATH         ruta de mysqldump (por defecto C:\xampp\mysql\bin\mysqldump.exe en Windows)
 *     UPLOADS_PATH           la misma de la aplicación (vacía = carpeta uploads/ del proyecto)
 *
 * Código de salida: 0 = respaldo correcto, 1 = falló (para que el Programador de tareas lo muestre).
 * No depende de vendor/ (funciona aunque Composer no esté instalado).
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script solo se puede ejecutar desde la terminal.');
}

date_default_timezone_set('America/Bogota');

/** Lee un .env sencillo (CLAVE=valor, comentarios con #, comillas opcionales). */
function leerEnv(string $ruta): array
{
    $datos = [];
    if (!is_file($ruta)) return $datos;
    foreach (file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $linea) {
        $linea = trim($linea);
        if ($linea === '' || $linea[0] === '#' || !str_contains($linea, '=')) continue;
        [$clave, $valor] = explode('=', $linea, 2);
        $valor = trim($valor);
        if (strlen($valor) >= 2 && ($valor[0] === '"' || $valor[0] === "'") && substr($valor, -1) === $valor[0]) {
            $valor = substr($valor, 1, -1);
        }
        $datos[trim($clave)] = $valor;
    }
    return $datos;
}

$raiz = dirname(__DIR__);
$env = leerEnv($raiz . DIRECTORY_SEPARATOR . '.env');
$cfg = static function (string $clave, string $porDefecto = '') use ($env): string {
    $v = getenv($clave);
    if ($v !== false && $v !== '') return $v;
    return $env[$clave] ?? $porDefecto;
};

// ---- opciones de línea de comandos ----
$conUploads = false;
$dias = (int) $cfg('BACKUP_RETENTION_DAYS', '30');
$destino = $cfg('BACKUP_PATH', '');
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--con-uploads') $conUploads = true;
    elseif (str_starts_with($arg, '--dias=')) $dias = (int) substr($arg, 7);
    elseif (str_starts_with($arg, '--destino=')) $destino = substr($arg, 10);
    else { fwrite(STDERR, "Opción desconocida: $arg\n"); exit(1); }
}
if ($dias < 1) $dias = 30;
$destinoPorDefecto = $destino === '';
if ($destinoPorDefecto) $destino = $raiz . DIRECTORY_SEPARATOR . 'respaldos';
$destino = rtrim($destino, '/\\');

// ---- registro ----
function registrar(string $nivel, string $mensaje): void
{
    global $destino;
    $linea = '[' . date('Y-m-d H:i:s') . "] $nivel $mensaje";
    echo $linea . PHP_EOL;
    if (is_dir($destino)) @file_put_contents($destino . DIRECTORY_SEPARATOR . 'respaldo.log', $linea . PHP_EOL, FILE_APPEND);
}

function fallar(string $mensaje): never
{
    registrar('ERROR', $mensaje);
    exit(1);
}

if (!is_dir($destino) && !@mkdir($destino, 0775, true) && !is_dir($destino)) {
    fwrite(STDERR, "No se pudo crear la carpeta de destino: $destino\n");
    exit(1);
}
if (!is_writable($destino)) fallar("La carpeta de destino no tiene permiso de escritura: $destino");
if ($destinoPorDefecto) {
    registrar('AVISO', 'No hay BACKUP_PATH en el .env: el respaldo se guarda dentro del proyecto. Configúralo en OTRO disco.');
}
if (!class_exists('ZipArchive')) fallar('Falta la extensión zip de PHP (actívala en php.ini: extension=zip).');

$bd = $cfg('DB_NAME', 'chvb');
$host = $cfg('DB_HOST', '127.0.0.1');
$usuario = $cfg('DB_USER', 'root');
$clave = $cfg('DB_PASS', '');

// ---- localizar mysqldump ----
$mysqldump = $cfg('MYSQLDUMP_PATH', '');
if ($mysqldump === '') {
    $mysqldump = (PHP_OS_FAMILY === 'Windows' && is_file('C:\\xampp\\mysql\\bin\\mysqldump.exe'))
        ? 'C:\\xampp\\mysql\\bin\\mysqldump.exe'
        : 'mysqldump';
}

$sello = date('Y-m-d_His');
$base = "chvb_respaldo_$sello";
$carpetaTmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "chvb_resp_$sello";
if (!@mkdir($carpetaTmp, 0700, true) && !is_dir($carpetaTmp)) fallar('No se pudo crear la carpeta temporal.');
$archivoSql = $carpetaTmp . DIRECTORY_SEPARATOR . 'base_de_datos.sql';
$archivoCfg = $carpetaTmp . DIRECTORY_SEPARATOR . 'cliente.cnf';
$zipParcial = $destino . DIRECTORY_SEPARATOR . $base . '.zip.part';
$zipFinal = $destino . DIRECTORY_SEPARATOR . $base . '.zip';

function limpiarTemporales(): void
{
    global $carpetaTmp;
    foreach (glob($carpetaTmp . DIRECTORY_SEPARATOR . '*') ?: [] as $f) @unlink($f);
    @rmdir($carpetaTmp);
}
register_shutdown_function('limpiarTemporales');

try {
    // La contraseña va en un archivo temporal (no en la línea de comandos, donde otros procesos la verían).
    $esc = static fn(string $v): string => str_replace(['\\', '"'], ['\\\\', '\\"'], $v);
    file_put_contents($archivoCfg, "[client]\nhost=\"{$esc($host)}\"\nuser=\"{$esc($usuario)}\"\npassword=\"{$esc($clave)}\"\n");
    @chmod($archivoCfg, 0600);

    registrar('INFO', "Respaldando la base de datos '$bd'...");
    $comando = [
        $mysqldump, '--defaults-extra-file=' . $archivoCfg,
        '--single-transaction', '--routines', '--triggers', '--events',
        '--default-character-set=utf8mb4', '--result-file=' . $archivoSql, $bd,
    ];
    $proceso = @proc_open($comando, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $tuberias);
    if (!is_resource($proceso)) fallar("No se pudo ejecutar mysqldump ($mysqldump). Revisa MYSQLDUMP_PATH en el .env.");
    stream_get_contents($tuberias[1]);
    $errores = trim((string) stream_get_contents($tuberias[2]));
    fclose($tuberias[1]);
    fclose($tuberias[2]);
    $codigo = proc_close($proceso);
    if ($codigo !== 0) fallar("mysqldump terminó con error ($codigo): $errores");

    // mysqldump escribe "-- Dump completed" al final: si falta, el volcado quedó cortado.
    if (!is_file($archivoSql) || filesize($archivoSql) < 100) fallar('El volcado de la base de datos quedó vacío.');
    $fp = fopen($archivoSql, 'rb');
    fseek($fp, -min(512, filesize($archivoSql)), SEEK_END);
    $cola = (string) fread($fp, 512);
    fclose($fp);
    if (!str_contains($cola, 'Dump completed')) fallar('El volcado de la base de datos quedó incompleto (no termina con "Dump completed").');
    registrar('INFO', 'Base de datos respaldada (' . number_format(filesize($archivoSql) / 1048576, 2) . ' MB sin comprimir).');

    // ---- armar el .zip (primero como .part; se renombra solo cuando está completo) ----
    $zip = new ZipArchive();
    if ($zip->open($zipParcial, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) fallar('No se pudo crear el archivo zip.');
    $zip->addFile($archivoSql, 'base_de_datos.sql');

    $archivosUploads = 0;
    if ($conUploads) {
        $uploads = trim($cfg('UPLOADS_PATH', ''));
        $uploads = $uploads !== '' ? rtrim($uploads, '/\\') : $raiz . DIRECTORY_SEPARATOR . 'uploads';
        if (!is_dir($uploads)) fallar("No existe la carpeta de uploads: $uploads");
        $baseLen = strlen($uploads) + 1;
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploads, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $archivo) {
            if (!$archivo->isFile()) continue;
            $ruta = $archivo->getPathname();
            $relativa = str_replace('\\', '/', substr($ruta, $baseLen));
            if ($relativa === '.gitkeep') continue;
            $zip->addFile($ruta, 'uploads/' . $relativa);
            $archivosUploads++;
        }
        registrar('INFO', "Agregando uploads/: $archivosUploads archivos.");
    }
    $zip->addFromString('LEEME.txt',
        "Respaldo de CHVB\nFecha: " . date('Y-m-d H:i:s') . "\nBase de datos: $bd\n" .
        'Incluye uploads: ' . ($conUploads ? "sí ($archivosUploads archivos)" : 'no') . "\n\n" .
        "Restaurar: ver deploy/xampp/LEEME_RESPALDOS.md del proyecto.\n");
    if (!$zip->close()) fallar('No se pudo terminar de escribir el archivo zip (¿falta espacio en disco?).');

    // ---- verificar que el zip abre y trae lo esperado ----
    $verif = new ZipArchive();
    if ($verif->open($zipParcial, ZipArchive::CHECKCONS) !== true || $verif->locateName('base_de_datos.sql') === false) {
        @unlink($zipParcial);
        fallar('El zip generado no pasó la verificación; se descartó.');
    }
    $entradas = $verif->numFiles;
    $verif->close();
    if (!@rename($zipParcial, $zipFinal)) fallar('No se pudo dejar el zip en su nombre final.');
    registrar('OK', "Respaldo listo: $zipFinal (" . number_format(filesize($zipFinal) / 1048576, 2) . " MB, $entradas entradas).");

    // ---- retención: borra los viejos, pero siempre deja los 3 más recientes ----
    $lista = glob($destino . DIRECTORY_SEPARATOR . 'chvb_respaldo_*.zip') ?: [];
    rsort($lista); // el nombre lleva fecha y hora: orden alfabético inverso = más nuevo primero
    $limite = time() - $dias * 86400;
    foreach (array_slice($lista, 3) as $viejo) {
        if (filemtime($viejo) < $limite && @unlink($viejo)) registrar('INFO', 'Respaldo antiguo eliminado: ' . basename($viejo));
    }
    foreach (glob($destino . DIRECTORY_SEPARATOR . '*.zip.part') ?: [] as $sobra) {
        if (filemtime($sobra) < time() - 86400) @unlink($sobra); // restos de un respaldo interrumpido
    }
    exit(0);
} catch (Throwable $e) {
    fallar('Error inesperado: ' . $e->getMessage());
}
