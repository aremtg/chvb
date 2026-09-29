<?php
// Herramienta de mantenimiento: crea carpetas y bolsillos faltantes de cada empleado
// e importa los documentos que ya estén en disco.
// Solo Talento Humano (superadmin) y siempre con POST + CSRF, porque modifica archivos y datos.
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/helpers/FileManager.php';
require_once __DIR__ . '/../src/models/BolsilloModel.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/helpers/ReconciliadorArchivos.php';

requireSuperAdmin();
if (($_SESSION['superadmin_rol'] ?? '') !== 'superadmin_talento_humano') {
    http_response_code(403);
    exit('No autorizado.');
}

$esc = static fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$ejecutar = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
if ($ejecutar) {
    validarCSRF();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CHVB - Sincronizar carpetas</title>
</head>
<body style="font-family: sans-serif; max-width: 640px; margin: 2rem auto; padding: 0 1rem;">
<h1>Sincronizar carpetas de empleados</h1>

<?php if (!$ejecutar): ?>
    <p>Crea las carpetas y bolsillos que falten e importa los documentos existentes en disco.</p>
    <form method="post">
        <?= csrfCampoHTML() ?>
        <button type="submit">Ejecutar sincronización</button>
        <a href="./formatos.php" style="margin-left: 1rem;">Cancelar</a>
    </form>
<?php else:
    $pdo = getPDO();
    $empleados = $pdo->query("SELECT cedula FROM empleados")->fetchAll(PDO::FETCH_COLUMN);
    $existen = $pdo->prepare("SELECT COUNT(*) FROM bolsillos WHERE cedula_empleado = ?");

    foreach ($empleados as $cedula) {
        echo 'Revisando ' . $esc($cedula) . '...<br>';
        try {
            FileManager::crearEstructuraEmpleado($cedula);
            // Si ya tiene bolsillos (porque los importaste) no los duplica
            $existen->execute([$cedula]);
            if ($existen->fetchColumn() == 0) {
                BolsilloModel::crearBolsillosParaEmpleado($cedula);
                echo ' -&gt; Bolsillos creados<br>';
            }
            ReconciliadorArchivos::importarDocumentosExistentes($cedula);
            echo ' -&gt; Carpeta OK<br>';
        } catch (Throwable $e) {
            error_log('sync_carpetas (' . $cedula . '): ' . $e->getMessage());
            echo ' -&gt; ERROR (revisa el log del servidor)<br>';
        }
    }
    echo '<p><strong>Listo.</strong></p>';
endif; ?>
</body>
</html>
