<?php
// config/database.php

require_once __DIR__ . '/../includes/reloj.php';   // hora única de la app
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
            // Misma hora que PHP en cada conexión: NOW(), CURDATE() y current_timestamp() dejan de depender
            // de la zona horaria del servidor MySQL (ver includes/reloj.php).
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '" . CHVB_OFFSET_MYSQL . "'",
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $opciones);
                } catch (PDOException $e) {
            error_log('Error de conexión a la base de datos: ' . $e->getMessage());
            $mensaje = 'No se pudo conectar a la base de datos. Contacta al administrador.';
            // En los endpoints de /api/ se responde JSON (el JS espera JSON, no una página HTML).
            if (str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/api/')) {
                http_response_code(500);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'error' => $mensaje], JSON_UNESCAPED_UNICODE);
                exit;
            }
            die($mensaje);
        }
    }

    return $pdo;
}