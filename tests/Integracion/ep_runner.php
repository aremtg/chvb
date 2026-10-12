<?php
// tests/Integracion/ep_runner.php
// Ejecuta UN endpoint de la API como si fuera una petición HTTP, en un proceso aparte
// (los endpoints terminan con exit()). Lo llama EndpointsPermisoTest.php.
// Uso: php ep_runner.php ruta_al_json_con_la_peticion

$peticion = json_decode((string) file_get_contents($argv[1]), true);

$_SERVER['REQUEST_METHOD'] = $peticion['method'] ?? 'POST';
$_GET = $peticion['get'] ?? [];
$_SERVER['SCRIPT_NAME'] = $peticion['script_name'];
$_SERVER['HTTP_ACCEPT'] = 'application/json';
$_POST = $peticion['post'] ?? [];

session_start();
$_SESSION = $peticion['session'] ?? [];

register_shutdown_function(function () {
    // Deja el código HTTP y las cabeceras en stderr para que la prueba las lea.
    fwrite(STDERR, 'HTTP=' . http_response_code() . PHP_EOL);
    foreach (headers_list() as $h) {
        fwrite(STDERR, 'HDR=' . $h . PHP_EOL);
    }
});

include $peticion['endpoint'];
