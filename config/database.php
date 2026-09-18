

<?php
// config/database.php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

function getPDO(): PDO {
    static $pdo = null;

    if ($pdo === null) {
    $host = $_SERVER['DB_HOST'] ?? $_ENV['DB_HOST'] ?? '127.0.0.1';
    $db   = $_SERVER['DB_NAME'] ?? $_ENV['DB_NAME'] ?? 'chvb';
    $user = $_SERVER['DB_USER'] ?? $_ENV['DB_USER'] ?? 'root';
    $pass = $_SERVER['DB_PASS'] ?? $_ENV['DB_PASS'] ?? '';

        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // prepared statements reales
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $opciones);
                } catch (PDOException $e) {
            error_log('Error de conexión a la base de datos: ' . $e->getMessage());
            die('No se pudo conectar a la base de datos. Contacta al administrador.');
        }
    }

    return $pdo;
}