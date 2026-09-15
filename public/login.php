<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';

// Si ya está logueado, mándalo directo al dashboard
if (!empty($_SESSION['superadmin_id'])) {
    header('Location: /chvb/public/dashboard.php');
    exit;
}

$error = '';

if (($_GET['motivo'] ?? '') === 'sesion_invalida') {
    $error = 'Tu sesión ya no es válida (el usuario fue eliminado o modificado). Inicia sesión de nuevo.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Debes completar usuario y contraseña.';
    } else {
        $resultado = AuthController::login($username, $password);
        if ($resultado['ok']) {
            header('Location: /chvb/public/dashboard.php');
            exit;
        } else {
            $error = $resultado['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Iniciar sesión</title>
    <link rel="stylesheet" href="/chvb/public/assets/css/tailwind.css">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-sm">
        <img src="/chvb/public/assets/img/logo_chv.png" alt="Logo">
        <p class="text-sm text-gray-500 mb-6 text-center">Control Hojas de Vida Bomberos Yopal</p>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 text-sm rounded p-3 mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                <input type="text" name="username" required
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 rounded transition">
                Ingresar
            </button>
        </form>
        <p class="text-xs text-gray-400 mt-4 text-center">
            <a href="/chvb/public/login_empleado.php" class="hover:underline">Acceso para empleados</a>
        </p>
    </div>
</body>

</html>