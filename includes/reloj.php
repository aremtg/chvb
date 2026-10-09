<?php
// includes/reloj.php
// ÚNICA fuente de la hora de la aplicación. Se carga desde includes/session.php y config/database.php,
// así que cualquier página, API o script que use la sesión o la base de datos queda con la misma hora.
//
//   - PHP: fija la zona horaria por defecto UNA vez, y con eso date(), strtotime() y new DateTime() de todo el
//     proyecto dan la hora de Colombia. Para pedir la hora explícitamente usa Reloj::ahora(), Reloj::hoy(), etc.
//   - MySQL: config/database.php le fija a cada conexión este mismo desfase (CHVB_OFFSET_MYSQL), de modo que
//     NOW(), CURDATE() y los DEFAULT current_timestamp() coinciden con PHP sin importar la zona del servidor.
//
// Para cambiar de zona horaria SOLO se tocan las dos constantes de abajo.
if (!defined('CHVB_ZONA_HORARIA')) {
    define('CHVB_ZONA_HORARIA', 'America/Bogota');
    // Colombia no usa horario de verano: el desfase es fijo (UTC-5) y no depende de las tablas de zonas de MySQL.
    define('CHVB_OFFSET_MYSQL', '-05:00');
}

date_default_timezone_set(CHVB_ZONA_HORARIA);

if (!class_exists('Reloj', false)) {
    final class Reloj
    {
        public static function zona(): DateTimeZone
        {
            return new DateTimeZone(CHVB_ZONA_HORARIA);
        }

        /** Fecha y hora actuales de Colombia. */
        public static function ahora(): DateTimeImmutable
        {
            return new DateTimeImmutable('now', self::zona());
        }

        /** Hoy a las 00:00 (hora de Colombia). */
        public static function hoy(): DateTimeImmutable
        {
            return new DateTimeImmutable('today', self::zona());
        }

        /** Fecha de hoy como 'Y-m-d'. */
        public static function hoyIso(): string
        {
            return self::ahora()->format('Y-m-d');
        }

        /** Fecha y hora actuales como 'Y-m-d H:i:s', listas para guardar en la base de datos. */
        public static function ahoraSql(): string
        {
            return self::ahora()->format('Y-m-d H:i:s');
        }
    }
}
