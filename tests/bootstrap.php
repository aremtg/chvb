<?php
// tests/bootstrap.php
// Carga el autoload de Composer (si existe) y las clases puras que se prueban.
// Las pruebas NO usan base de datos.

date_default_timezone_set('America/Bogota');

$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}

require_once dirname(__DIR__) . '/src/helpers/JornadaHelper.php';
