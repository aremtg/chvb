<?php
// public/renovaciones.php
// Control de Renovaciones. Acceso idéntico para superadmin_talento_humano y
// auxiliar_talento_humano (validado en backend por requireRenovacionesAccess()).
require_once __DIR__ . '/../includes/renovaciones_guard.php';
require_once __DIR__ . '/../src/models/RenovacionAuditoriaModel.php';
requireRenovacionesAccess();

$porPagina = 50;
$pagina    = max(1, (int) ($_GET['pagina'] ?? 1));
$cedulaQ   = trim((string) ($_GET['cedula'] ?? ''));
$accionQ   = trim((string) ($_GET['accion'] ?? ''));

$auditoria = RenovacionAuditoriaModel::listar(
    ['cedula' => $cedulaQ, 'accion' => $accionQ],
    $porPagina,
    ($pagina - 1) * $porPagina
);
$totalPaginas = max(1, (int) ceil($auditoria['total'] / $porPagina));

function renUrlPagina(int $p, string $cedula, string $accion): string
{
    return './renovaciones.php?' . http_build_query(['pagina' => $p, 'cedula' => $cedula, 'accion' => $accion]);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Control de Renovaciones</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white shadow px-6 py-4">
            <h1 class="text-lg font-bold text-gray-800">Control de Renovaciones</h1>
            <p class="text-xs text-gray-600">Superadmin y Auxiliar de Talento Humano tienen las mismas funciones; cada acción queda registrada a nombre de quien la realizó.</p>
        </header>

        <main class="p-4 sm:p-6 max-w-6xl space-y-4">

            <!-- Aviso: el panel de vencimientos se construye en los siguientes pasos -->
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-gray-700">
                Panel de vencimientos, registro de renovaciones y checklist: en construcción.
                Esta pantalla ya aplica el control de acceso y muestra el historial de acciones del módulo.
            </div>

            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-100">
                    <h2 class="font-bold text-gray-800">Historial de acciones</h2>
                    <p class="text-xs text-gray-600">Quién hizo qué y cuándo dentro del módulo.</p>

                    <form method="get" class="mt-3 flex flex-col sm:flex-row gap-2">
                        <input type="text" name="cedula" value="<?= htmlspecialchars($cedulaQ) ?>"
                            placeholder="Filtrar por cédula del empleado" autocomplete="off"
                            class="h-10 border border-gray-200 bg-white rounded-lg px-3.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                        <select name="accion"
                            class="h-10 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700">
                            <option value="">Todas las acciones</option>
                            <?php foreach (RenovacionAuditoriaModel::ACCIONES as $clave => $texto): ?>
                                <option value="<?= htmlspecialchars($clave) ?>" <?= $clave === $accionQ ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($texto) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit"
                            class="h-10 px-4 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">Filtrar</button>
                    </form>
                </div>

                <?php if (empty($auditoria['filas'])): ?>
                    <p class="p-8 text-center text-xs text-gray-600">Aún no hay acciones registradas<?= ($cedulaQ !== '' || $accionQ !== '') ? ' con ese filtro' : '' ?>.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[520px] text-sm text-left">
                            <thead class="border-b border-gray-100 bg-gray-50/60">
                                <tr>
                                    <th class="px-4 sm:px-5 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Fecha</th>
                                    <th class="px-4 sm:px-5 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Acción</th>
                                    <th class="px-4 sm:px-5 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Empleado</th>
                                    <th class="px-4 sm:px-5 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Realizada por</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($auditoria['filas'] as $f): ?>
                                    <tr class="bg-white hover:bg-gray-50/70 transition">
                                        <td class="px-4 sm:px-5 py-3.5 whitespace-nowrap text-gray-600">
                                            <?= htmlspecialchars(date('d/m/Y H:i', strtotime($f['created_at']))) ?>
                                        </td>
                                        <td class="px-4 sm:px-5 py-3.5">
                                            <div class="font-semibold text-gray-800"><?= htmlspecialchars($f['accion_etiqueta']) ?></div>
                                            <?php if (!empty($f['detalle']['resumen'])): ?>
                                                <div class="text-xs text-gray-600"><?= htmlspecialchars((string) $f['detalle']['resumen']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 sm:px-5 py-3.5">
                                            <?php if ($f['cedula_empleado'] !== null): ?>
                                                <div class="text-gray-800"><?= htmlspecialchars($f['nombre_empleado'] ?? 'Empleado eliminado') ?></div>
                                                <div class="text-xs text-gray-600 font-mono"><?= htmlspecialchars($f['cedula_empleado']) ?></div>
                                            <?php else: ?>
                                                <span class="text-gray-600">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 sm:px-5 py-3.5">
                                            <div class="text-gray-800"><?= htmlspecialchars($f['usuario_nombre']) ?></div>
                                            <div class="text-xs text-gray-600"><?= htmlspecialchars($f['usuario_rol_etiqueta']) ?></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 sm:p-5 border-t border-gray-100 flex items-center justify-between text-xs text-gray-600">
                        <span><?= (int) $auditoria['total'] ?> registro(s) · página <?= $pagina ?> de <?= $totalPaginas ?></span>
                        <span class="flex gap-2">
                            <?php if ($pagina > 1): ?>
                                <a href="<?= htmlspecialchars(renUrlPagina($pagina - 1, $cedulaQ, $accionQ)) ?>"
                                    class="px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50">Anterior</a>
                            <?php endif; ?>
                            <?php if ($pagina < $totalPaginas): ?>
                                <a href="<?= htmlspecialchars(renUrlPagina($pagina + 1, $cedulaQ, $accionQ)) ?>"
                                    class="px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50">Siguiente</a>
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>

</html>
