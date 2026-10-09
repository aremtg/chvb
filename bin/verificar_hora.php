<?php
/**
 * Comprueba que PHP y MySQL dan la MISMA hora (la de includes/reloj.php).
 * SOLO se ejecuta desde la terminal:
 *
 *     cd C:\xampp\htdocs\chvb
 *     C:\xampp\php\php.exe bin\verificar_hora.php
 *
 * Termina con código 0 si todo coincide y con código 1 si hay una diferencia de más de 2 segundos.
 * Si falla, lo más probable es que el reloj de Windows esté desajustado
 * (Configuración > Hora e idioma > Fecha y hora > "Sincronizar ahora").
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script solo se puede ejecutar desde la terminal.');
}

require_once __DIR__ . '/../config/database.php';

try {
    $fila = getPDO()->query(
        "SELECT NOW() AS ahora, @@session.time_zone AS zona_sesion, @@global.time_zone AS zona_global, @@system_time_zone AS zona_sistema"
    )->fetch();
} catch (Throwable $e) {
    fwrite(STDERR, 'No se pudo consultar la base de datos: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

$php = Reloj::ahora();
$mysql = new DateTimeImmutable($fila['ahora'], Reloj::zona());
$diferencia = abs($php->getTimestamp() - $mysql->getTimestamp());

echo 'Zona de la app (PHP)     : ' . CHVB_ZONA_HORARIA . PHP_EOL;
echo 'Hora según PHP           : ' . $php->format('Y-m-d H:i:s') . PHP_EOL;
echo 'Hora según MySQL (NOW()) : ' . $fila['ahora'] . PHP_EOL;
echo 'Zona de esta conexión    : ' . $fila['zona_sesion'] . ' (la app fuerza ' . CHVB_OFFSET_MYSQL . ')' . PHP_EOL;
echo 'Zona global del servidor : ' . $fila['zona_global'] . ' / sistema: ' . $fila['zona_sistema'] . PHP_EOL;
echo 'Diferencia               : ' . $diferencia . ' s' . PHP_EOL;

if ($diferencia > 2) {
    echo 'RESULTADO: HAY DIFERENCIA. Revisa el reloj del equipo.' . PHP_EOL;
    exit(1);
}
echo 'RESULTADO: OK, PHP y MySQL coinciden.' . PHP_EOL;
