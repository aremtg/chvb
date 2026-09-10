<?php
// src/helpers/Crypto.php

class Crypto {
    private static function key(): string {
        $key = $_ENV['APP_KEY'] ?? '';
        if (strlen($key) < 32) {
            throw new Exception('APP_KEY no está configurada correctamente en el .env.');
        }
        return substr(hash('sha256', $key, true), 0, 32);
    }

    public static function encriptar(string $texto): string {
        $iv = random_bytes(16);
        $cifrado = openssl_encrypt($texto, 'aes-256-cbc', self::key(), OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $cifrado);
    }

    public static function desencriptar(string $textoCifrado): ?string {
        $datos = base64_decode($textoCifrado);
        if ($datos === false || strlen($datos) < 17) {
            return null;
        }
        $iv = substr($datos, 0, 16);
        $cifrado = substr($datos, 16);
        $resultado = openssl_decrypt($cifrado, 'aes-256-cbc', self::key(), OPENSSL_RAW_DATA, $iv);
        return $resultado !== false ? $resultado : null;
    }
}