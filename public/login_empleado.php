<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/controllers/AuthEmpleadoController.php';

if (!empty($_SESSION['empleado_cedula'])) {
    header('Location: /chvb/public/mi_hoja_de_vida.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = trim($_POST['cedula'] ?? '');
    $pin = trim($_POST['pin'] ?? '');

    if ($cedula === '' || $pin === '') {
        $error = 'Debes completar cédula y PIN.';
    } else {
        $resultado = AuthEmpleadoController::login($cedula, $pin);
        if ($resultado['ok']) {
            header('Location: /chvb/public/mi_hoja_de_vida.php');
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
    <title>CHVB - Acceso Empleado</title>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                <input type="text" name="cedula" maxlength="10" required
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">PIN (4 dígitos)</label>
                <input type="password" name="pin" maxlength="4" pattern="[0-9]{4}" required
                    class="w-full border border-gray-300 rounded px-3 py-2 tracking-widest text-center text-lg focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 rounded transition">
                Ingresar
            </button>
        </form>

        <p class="text-xs text-gray-400 mt-4 text-center">
            <a href="/chvb/public/login.php" class="hover:underline">Acceso administrativo</a>
        </p>
    </div>
</body>
</html>