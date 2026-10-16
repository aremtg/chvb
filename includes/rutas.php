<?php
// includes/rutas.php
// ÚNICA fuente de la ruta URL donde se sirve la carpeta /public.
//
// Se configura en el .env con APP_PUBLIC_PATH:
//   APP_PUBLIC_PATH=/chvb/public    -> proyecto en C:\xampp\htdocs\chvb (valor por defecto)
//   APP_PUBLIC_PATH=/hojas/public   -> proyecto en C:\xampp\htdocs\hojas
//   APP_PUBLIC_PATH=                -> vacío: el VirtualHost apunta directo a la carpeta /public
//
// Uso en PHP:   header('Location: ' . CHVB_PUBLIC_URL . '/login.php');
//               ruta('api/notificaciones_contar.php')
// Uso en HTML:  echo CHVB_PUBLIC_URL dentro de href/src/action, seguido de /dashboard.php
if (!defined('CHVB_PUBLIC_URL')) {
    $leer = static function (): ?string {
        foreach ([$_SERVER, $_ENV] as $origen) {
            if (array_key_exists('APP_PUBLIC_PATH', $origen) && $origen['APP_PUBLIC_PATH'] !== false) {
                return (string) $origen['APP_PUBLIC_PATH'];
            }
        }
        return null;
    };

    $valor = $leer();
    if ($valor === null) {
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (is_file($autoload)) {
            require_once $autoload;
            \Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();
            $valor = $leer();
        }
    }
    if ($valor === null) {
        $valor = '/chvb/public';   // compatibilidad: comportamiento anterior si el .env no define la variable
    }

    // Normaliza: sin barra final y con barra inicial (o cadena vacía).
    $valor = trim(str_replace('\\', '/', trim($valor)), '/');
    define('CHVB_PUBLIC_URL', $valor === '' ? '' : '/' . $valor);
}

if (!function_exists('ruta')) {
    /** URL absoluta (desde la raíz del sitio) de un archivo dentro de /public. */
    function ruta(string $archivo = ''): string
    {
        return CHVB_PUBLIC_URL . '/' . ltrim($archivo, '/');
    }
}
