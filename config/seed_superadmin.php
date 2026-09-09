<?php
// config/seed_superadmin.php
require_once __DIR__ . '/database.php';

$username = 'admin';
$passwordPlano = 'CambiaEsto123'; // CAMBIA esto antes de correr el script

$hash = password_hash($passwordPlano, PASSWORD_DEFAULT);

$pdo = getPDO();

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = :username");
$stmt->execute(['username' => $username]);

if ($stmt->fetch()) {
    echo "Ya existe un usuario con ese username.";
} else {
    $stmt = $pdo->prepare(
        "INSERT INTO usuarios (username, password_hash, rol) 
         VALUES (:username, :hash, 'superadmin_talento_humano')"
    );
    $stmt->execute([
        'username' => $username,
        'hash' => $hash,
    ]);
    echo "Usuario superadmin creado con éxito. Username: $username";
}