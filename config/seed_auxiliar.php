<?php
// config/seed_auxiliar.php
require_once __DIR__ . '/database.php';

$username = 'auxiliar1';
$passwordPlano = 'Auxiliarprimero123';

$hash = password_hash($passwordPlano, PASSWORD_DEFAULT);
$pdo = getPDO();

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = :username");
$stmt->execute(['username' => $username]);

if ($stmt->fetch()) {
    echo "Ya existe un usuario con ese username.";
} else {
    $stmt = $pdo->prepare(
        "INSERT INTO usuarios (username, password_hash, rol) VALUES (:username, :hash, 'auxiliar_talento_humano')"
    );
    $stmt->execute(['username' => $username, 'hash' => $hash]);
    echo "Usuario Auxiliar de Talento Humano creado con éxito. Username: $username";
}
//http://localhost/chvb/config/seed_auxiliar.php