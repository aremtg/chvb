<?php
// src/helpers/PasswordPolicy.php
// ÚNICO lugar donde se define qué es una contraseña aceptable y cómo se generan las temporales.
// Lo usan el panel de usuarios, "Cambiar mi contraseña" y bin/crear_usuario.php.
// Clase pura (sin base de datos): se prueba en tests/Unit/PasswordPolicyTest.php.

final class PasswordPolicy
{
    public const MIN_CARACTERES = 10;
    /** bcrypt (PASSWORD_DEFAULT) ignora en silencio lo que pase de 72 bytes: mejor rechazarlo. */
    public const MAX_BYTES = 72;

    /** Contraseñas típicas que no se aceptan aunque cumplan el largo. */
    private const COMUNES = [
        '1234567890', '0123456789', '12345678910', 'qwertyuiop', 'contrasena1', 'contrasena12',
        'contrasena123', 'password12', 'password123', 'bomberos123', 'bomberos1234', 'bomberosyopal',
        'admin12345', 'administrador',
    ];

    // Alfabeto sin caracteres que se confunden al leerlos o dictarlos (0/O, 1/l/I).
    private const MAYUS = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    private const MINUS = 'abcdefghijkmnpqrstuvwxyz';
    private const DIGITOS = '23456789';

    /**
     * Devuelve el mensaje de error si la contraseña NO es aceptable, o null si está bien.
     */
    public static function validar(string $password, string $username = ''): ?string
    {
        if (mb_strlen($password) < self::MIN_CARACTERES) {
            return 'La contraseña debe tener al menos ' . self::MIN_CARACTERES . ' caracteres.';
        }
        if (strlen($password) > self::MAX_BYTES) {
            return 'La contraseña es demasiado larga (máximo ' . self::MAX_BYTES . ' caracteres).';
        }
        if (count(array_unique(mb_str_split($password))) < 4) {
            return 'La contraseña es demasiado simple: usa más caracteres distintos.';
        }
        $minus = mb_strtolower($password);
        if ($username !== '' && mb_strlen($username) >= 3 && str_contains($minus, mb_strtolower($username))) {
            return 'La contraseña no puede contener tu nombre de usuario.';
        }
        if (in_array($minus, self::COMUNES, true)) {
            return 'Esa contraseña es demasiado común. Elige otra.';
        }
        return null;
    }

    /**
     * Contraseña temporal aleatoria con el formato "Xk7m-Pq4w-Zt9r" (12 caracteres + 2 guiones).
     * Usa random_int (CSPRNG). Garantiza al menos una mayúscula, una minúscula y un dígito.
     */
    public static function generarTemporal(): string
    {
        $todo = self::MAYUS . self::MINUS . self::DIGITOS;
        $max = strlen($todo) - 1;
        do {
            $c = '';
            for ($i = 0; $i < 12; $i++) {
                $c .= $todo[random_int(0, $max)];
            }
            $ok = preg_match('/[A-Z]/', $c) && preg_match('/[a-z]/', $c) && preg_match('/[0-9]/', $c);
        } while (!$ok);

        return implode('-', str_split($c, 4));
    }
}
