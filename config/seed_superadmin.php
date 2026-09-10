<?php
// config/seed_superadmin.php
require_once __DIR__ . '/database.php';

$username = 'Talento Humano';
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
//http://localhost/chvb/config/seed_superadmin.php
//npm run build:css
//B) .env y .env.example — agregar una llave de cifrado

//Genera una llave aleatoria corriendo esto en tu terminal, parado en C:\xampp\htdocs\chvb:

//bash
//php -r "echo bin2hex(random_bytes(16));"

//Copia el resultado (32 caracteres hexadecimales) y agrégalo a tu .env: