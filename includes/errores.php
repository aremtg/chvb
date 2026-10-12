<?php
// includes/errores.php
//
// Manejo central de errores. Se activa desde includes/session.php, que ya cargan
// las páginas y los endpoints de la API, así que no hace falta copiarlo en cada archivo.
//
// Qué hace:
//  - Nunca muestra detalles técnicos al usuario (rutas, SQL, nombres de variables).
//  - Registra TODO en el log del servidor con un código de referencia (ERR-XXXXXXXX).
//  - Excepciones y errores fatales: respuesta limpia (JSON en la API, HTML en las páginas).
//  - Avisos y warnings: se registran con su origen pero NO interrumpen la página.
//    Con APP_ERRORES_ESTRICTOS=1 se convierten en excepciones (solo para pruebas).
//  - APP_DEBUG=1 (solo en desarrollo) muestra el mensaje real en la respuesta.

if (!function_exists('registrarManejoErrores')) {

    function registrarManejoErrores(): void
    {
        static $registrado = false;
        if ($registrado) {
            return;
        }
        $registrado = true;

        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
        error_reporting(E_ALL);

        $estricto = (getenv('APP_ERRORES_ESTRICTOS') === '1');

        set_error_handler(function (int $nivel, string $mensaje, string $archivo, int $linea) use ($estricto): bool {
            // Respeta el operador @ (error_reporting() = 0 dentro de él).
            if (!(error_reporting() & $nivel)) {
                return false;
            }
            if ($estricto) {
                throw new ErrorException($mensaje, 0, $nivel, $archivo, $linea);
            }
            $ref = referenciaError();
            error_log("[$ref] aviso PHP ($nivel): $mensaje en $archivo:$linea");
            return true;   // ya registrado; PHP no lo imprime
        });

        set_exception_handler(function (Throwable $e): void {
            $ref = referenciaError();
            error_log("[$ref] excepción no capturada: " . get_class($e) . ': ' . $e->getMessage()
                . ' en ' . $e->getFile() . ':' . $e->getLine());
            responderErrorInterno($ref, $e->getMessage());
        });

        register_shutdown_function(function (): void {
            $fatal = error_get_last();
            if ($fatal === null || !in_array($fatal['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
                return;
            }
            $ref = referenciaError();
            error_log("[$ref] error fatal: {$fatal['message']} en {$fatal['file']}:{$fatal['line']}");
            responderErrorInterno($ref, $fatal['message']);
        });
    }

    /** Código corto para que el usuario lo diga a soporte y se encuentre el registro. */
    function referenciaError(): string
    {
        return 'ERR-' . strtoupper(bin2hex(random_bytes(4)));
    }

    function responderErrorInterno(string $ref, string $detalle): void
    {
        if (ob_get_level() > 0) {
            @ob_end_clean();
        }
        $debug = getenv('APP_DEBUG') === '1';
        $mensaje = 'Ocurrió un error inesperado. Intenta de nuevo; si continúa, informa a Talento Humano con el código ' . $ref . '.';
        if ($debug) {
            $mensaje .= ' Detalle (solo desarrollo): ' . $detalle;
        }

        $esApi = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/api/')
            || str_contains(strtolower($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');

        if ($esApi) {
            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: application/json; charset=utf-8');
            }
            echo json_encode(['ok' => false, 'error' => $mensaje, 'ref' => $ref], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Página HTML: si ya salió contenido no se puede cambiar el código, pero el aviso igual se muestra.
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
        }
        echo '<!DOCTYPE html><html lang="es"><meta charset="utf-8"><title>Error</title>'
            . '<body style="font-family:Arial,sans-serif;max-width:560px;margin:60px auto;color:#1f2937">'
            . '<h2>Algo salió mal</h2><p>' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p><a href="javascript:history.back()">Volver</a></p></body></html>';
    }
}
