<?php
/**
 * Genera los avisos de vencimiento de contratos (módulo Control de Renovaciones).
 * Cada aviso se envía una sola vez, así que se puede ejecutar todas las veces que se quiera.
 *
 * SOLO se ejecuta desde la terminal (no por el navegador):
 *
 *     cd C:\xampp\htdocs\chvb
 *     C:\xampp\php\php.exe bin\renovaciones_avisos.php
 *
 * Para que corra solo cada día, créalo en el "Programador de tareas" de Windows apuntando a
 * C:\xampp\php\php.exe con el argumento C:\xampp\htdocs\chvb\bin\renovaciones_avisos.php
 * (si no lo programas, los avisos se generan igual cuando alguien entra al sistema).
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script solo se puede ejecutar desde la terminal.');
}

date_default_timezone_set('America/Bogota');
require_once __DIR__ . '/../src/helpers/RenovacionAvisos.php';

try {
    $n = RenovacionAvisos::generarPendientes();
    echo date('Y-m-d H:i') . ' - Avisos nuevos creados: ' . $n . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, 'No se pudieron generar los avisos: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
