<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/models/BolsilloModel.php';
require_once __DIR__ . '/../src/models/PermisoModel.php';
requireSuperAdmin();

// ---------------------------------------------------------------------------
// ENCABEZADO: saludo y fecha de hoy
// ---------------------------------------------------------------------------
$stmtYo = getPDO()->prepare('SELECT username, nombre_completo FROM usuarios WHERE id = :id');
$stmtYo->execute(['id' => (int) ($_SESSION['superadmin_id'] ?? 0)]);
$yo = $stmtYo->fetch() ?: [];
$nombreBase = trim((string) ($yo['nombre_completo'] ?? '')) !== ''
    ? (string) $yo['nombre_completo']
    : (string) ($yo['username'] ?? ($_SESSION['superadmin_username'] ?? ''));
$partesNombre = preg_split('/[\s._-]+/', trim($nombreBase), -1, PREG_SPLIT_NO_EMPTY) ?: [];
$primerNombre = $partesNombre ? mb_convert_case($partesNombre[0], MB_CASE_TITLE, 'UTF-8') : '';

$diasSemanaTxt = [1 => 'lunes', 2 => 'martes', 3 => 'miércoles', 4 => 'jueves', 5 => 'viernes', 6 => 'sábado', 7 => 'domingo'];
$fechaHoyTxt = ucfirst($diasSemanaTxt[(int) date('N')]) . ', ' . (int) date('j') . ' de '
    . mb_strtolower(EmpleadoModel::mesEnEspanol((int) date('n')), 'UTF-8') . ' de ' . date('Y');

// ---------------------------------------------------------------------------
// PERÍODO DE LAS MÉTRICAS
// mm = mes a analizar (1-12) o "todos"; ma = año. Por defecto, el mes actual.
// El mes se filtra por la fecha de inicio del permiso.
// ---------------------------------------------------------------------------
$mmParam = (string) ($_GET['mm'] ?? date('n'));
$periodoTodos = ($mmParam === 'todos');
$mesMetricas = $periodoTodos ? (int) date('n') : (int) $mmParam;
$anioMetricas = (int) ($_GET['ma'] ?? date('Y'));
if ($mesMetricas < 1 || $mesMetricas > 12) {
    $mesMetricas = (int) date('n');
    $periodoTodos = false;
}
if ($anioMetricas < 2000 || $anioMetricas > 2100) {
    $anioMetricas = (int) date('Y');
}

$desdeMetricas = $periodoTodos ? null : sprintf('%04d-%02d-01', $anioMetricas, $mesMetricas);
$hastaMetricas = $periodoTodos ? null : (new DateTime($desdeMetricas))->format('Y-m-t');
$etiquetaPeriodo = $periodoTodos
    ? 'Todo el historial'
    : EmpleadoModel::mesEnEspanol($mesMetricas) . ' ' . $anioMetricas;

// ---------------------------------------------------------------------------
// CALENDARIO DE CUMPLEAÑOS
// Si no se navega el calendario a mano (anio/mes), sigue al período elegido arriba.
// ---------------------------------------------------------------------------
$anioCalendario = (int) ($_GET['anio'] ?? ($periodoTodos ? date('Y') : $anioMetricas));
$mesCalendario = (int) ($_GET['mes'] ?? ($periodoTodos ? date('n') : $mesMetricas));
if ($anioCalendario < 2000 || $anioCalendario > 2100) {
    $anioCalendario = (int) date('Y');
}
if ($mesCalendario < 1 || $mesCalendario > 12) {
    $mesCalendario = (int) date('n');
}

$cumpleanosProximos = EmpleadoModel::proximosCumpleanos(15);

$inicioMes = new DateTime(sprintf('%04d-%02d-01', $anioCalendario, $mesCalendario));
$diasEnMes = (int) $inicioMes->format('t');
$diaInicioSemana = (int) $inicioMes->format('N'); // 1=lunes ... 7=domingo

$cumpleanosMes = EmpleadoModel::cumpleanosDelMes($anioCalendario, $mesCalendario);
$cumpleanosPorDia = [];
foreach ($cumpleanosMes as $emp) {
    $cumpleanosPorDia[$emp['dia_cumpleanos']][] = $emp;
}

$mesAnterior = (clone $inicioMes)->modify('-1 month');
$mesSiguiente = (clone $inicioMes)->modify('+1 month');
$nombreMesCalendario = EmpleadoModel::mesEnEspanol($mesCalendario);

// Enlaces del calendario: conservan el período de las métricas y vuelven a la tarjeta de cumpleaños.
$urlCalendario = function (int $anio, int $mes) use ($periodoTodos, $mesMetricas, $anioMetricas): string {
    return 'dashboard.php?' . htmlspecialchars(http_build_query([
        'anio' => $anio,
        'mes' => $mes,
        'mm' => $periodoTodos ? 'todos' : $mesMetricas,
        'ma' => $anioMetricas,
    ]), ENT_QUOTES) . '#cumpleanos';
};

// ---------------------------------------------------------------------------
// MÉTRICAS DE AUSENTISMO
// ---------------------------------------------------------------------------
$resPeriodo = PermisoModel::resumenAusentismo($desdeMetricas, $hastaMetricas);
$resTotal = $periodoTodos ? $resPeriodo : PermisoModel::resumenAusentismo();
$tiposPeriodo = PermisoModel::ausentismoPorTipo($desdeMetricas, $hastaMetricas);
$tiposTotal = $periodoTodos ? $tiposPeriodo : PermisoModel::ausentismoPorTipo();
$topEmpleados = PermisoModel::topEmpleadosAusentismo($desdeMetricas, $hastaMetricas, 5);
$jefesPendientes = PermisoModel::jefesConFirmasPendientes(5);
$estadosActuales = PermisoModel::resumenEstadosActuales();
$tendencia = PermisoModel::tendenciaMensual($anioMetricas, $mesMetricas, 6);

$pct = fn(int $parte, int $todo): int => $todo > 0 ? (int) round($parte * 100 / $todo) : 0;
$fmtHoras = function (float $h): string {
    $min = (int) round($h * 60);
    $hh = intdiv($min, 60);
    $mm = $min % 60;
    if ($hh > 0 && $mm > 0) return "{$hh} h {$mm} min";
    if ($hh > 0) return "{$hh} h";
    if ($mm > 0) return "{$mm} min";
    return '0 h';
};

$pendFirmasPeriodo = $resPeriodo['pend_reemplazo'] + $resPeriodo['pend_jefe'];
$pendFirmasActuales = $estadosActuales['firmas_pendientes'];
$decididos = $resPeriodo['firmados'] + $resPeriodo['rechazados'];
$tasaAprobacion = $decididos > 0 ? $pct($resPeriodo['firmados'], $decididos) : null;
$promedioHoras = $resPeriodo['firmados'] > 0 ? $resPeriodo['horas_firmadas'] / $resPeriodo['firmados'] : 0.0;

$coloresTipo = [
    'Permiso' => '#dc2626',
    'Vacaciones' => '#2563eb',
    'Licencia' => '#d97706',
    'Mision institucional' => '#7c3aed',
];
$etiquetasTipo = ['Mision institucional' => 'Misión institucional'];

$maxTendencia = max(1, ...array_column($tendencia, 'total'));
$aniosSelector = range((int) date('Y') + 1, (int) date('Y') - 4);

// Tarjetas de "Requiere atención": lo primero que Talento Humano necesita ver.
// [título, cantidad, descripción, tono si hay pendientes, ícono]
$tarjetasAtencion = [
    ['Firma del reemplazo', (int) $estadosActuales['pend_reemplazo'], 'Esperan que el reemplazo firme', 'warn', 'user'],
    ['Firma del jefe', (int) $estadosActuales['pend_jefe'], 'Esperan la firma del jefe inmediato', 'warn', 'file-signature'],
    ['Esperando regreso', (int) $estadosActuales['esperando_regreso'], 'Falta la llegada y la firma final', 'info', 'alarm-clock'],
    ['Devueltos al empleado', (int) $estadosActuales['devueltos'], 'Deben corregir y reenviar', 'alert', 'circle-alert'],
];

// El botón de menú se imprime dentro del encabezado (sin saltos de diseño al cargar).
$chvBotonEnCabecera = true;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#dc2626">
    <title>CHVB - Inicio</title>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <link rel="stylesheet" href="./assets/css/dashboard.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/dashboard.css') ?>">
</head>

<body class="bg-gray-100 min-h-screen">
    <a href="#contenido" class="app-skip">Saltar al contenido</a>
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="app-topbar">
            <?= chvBotonMenu() ?>
            <div class="app-topbar__txt">
                <h1>Inicio</h1>
                <p><?= $primerNombre !== '' ? 'Hola, ' . htmlspecialchars($primerNombre) . ' · ' : '' ?><?= htmlspecialchars($fechaHoyTxt) ?></p>
            </div>

            <!-- Período: mueve los indicadores y, al elegir un mes, también el calendario de cumpleaños -->
            <form method="get" action="dashboard.php" class="dash-filtro" aria-label="Período del tablero">
                <label class="sr-only" for="filtroMes">Mes</label>
                <select id="filtroMes" name="mm" onchange="this.form.submit()">
                    <option value="todos" <?= $periodoTodos ? 'selected' : '' ?>>Todo el historial</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= (!$periodoTodos && $m === $mesMetricas) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(EmpleadoModel::mesEnEspanol($m)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <label class="sr-only" for="filtroAnio">Año</label>
                <select id="filtroAnio" name="ma" onchange="this.form.submit()">
                    <?php foreach ($aniosSelector as $a): ?>
                        <option value="<?= $a ?>" <?= $a === $anioMetricas ? 'selected' : '' ?>><?= $a ?></option>
                    <?php endforeach; ?>
                </select>
                <noscript><button type="submit" class="dash-btn">Aplicar</button></noscript>
                <a href="dashboard.php" class="dash-btn">Mes actual</a>
            </form>
        </header>

        <main id="contenido" class="dash-main" tabindex="-1">
            <div class="dash-layout">

                <!-- ============ COLUMNA PRINCIPAL: operación y métricas ============ -->
                <div class="dash-col">

                    <!-- 1. REQUIERE ATENCIÓN -->
                    <section class="dash-atencion" aria-labelledby="tAtencion">
                        <div class="dash-sec-head">
                            <span class="dash-ico <?= $pendFirmasActuales > 0 ? 'ambar' : 'verde' ?>">
                                <?= icon($pendFirmasActuales > 0 ? 'alarm-clock' : 'check', 'w-5 h-5') ?>
                            </span>
                            <div>
                                <h2 id="tAtencion">Requiere atención</h2>
                                <p>
                                    <?= $pendFirmasActuales > 0
                                        ? '<strong>' . (int) $pendFirmasActuales . '</strong> permiso(s) esperando firma ahora · estado actual, sin importar el mes'
                                        : 'Nada esperando firma ahora · estado actual, sin importar el mes' ?>
                                </p>
                            </div>
                        </div>

                        <div class="dash-atencion__grid">
                            <?php foreach ($tarjetasAtencion as [$titulo, $cantidad, $descripcion, $tono, $iconoTarjeta]): ?>
                                <a href="permisos_th.php" class="dash-tile <?= $cantidad > 0 ? 'is-' . $tono : 'is-ok' ?>">
                                    <span class="dash-ico <?= $cantidad > 0 ? ['warn' => 'ambar', 'info' => 'azul', 'alert' => 'naranja'][$tono] : 'verde' ?>">
                                        <?= icon($cantidad > 0 ? $iconoTarjeta : 'check', 'w-5 h-5') ?>
                                    </span>
                                    <div>
                                        <div class="dash-tile__num"><?= $cantidad ?></div>
                                        <div class="dash-tile__lbl"><?= htmlspecialchars($titulo) ?></div>
                                        <div class="dash-tile__sub"><?= $cantidad > 0 ? htmlspecialchars($descripcion) : 'Todo al día' ?></div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- 2 a 4. AUSENTISMO Y PERMISOS -->
                    <section id="ausentismo" class="dash-col" aria-labelledby="tAusentismo">
                        <div class="dash-sec-head">
                            <span class="dash-ico rojo"><?= icon('clipboard-list', 'w-5 h-5') ?></span>
                            <div>
                                <h2 id="tAusentismo">Ausentismo y permisos</h2>
                                <p>Mostrando <strong><?= htmlspecialchars($etiquetaPeriodo) ?></strong> · según la fecha de inicio del permiso, sin contar borradores</p>
                            </div>
                        </div>

                        <?php if ($resTotal['total'] === 0): ?>
                            <div class="dash-card"><div class="dash-vacio" style="margin-top:0">Aún no hay permisos registrados para mostrar métricas.</div></div>
                        <?php else: ?>

                            <!-- 2. Indicadores -->
                            <div class="dash-kpis">
                                <div class="dash-card dash-kpi">
                                    <div class="dash-kpi__top">
                                        <span class="dash-ico rojo"><?= icon('user', 'w-5 h-5') ?></span>
                                        <div class="dash-label">Personas que pidieron permiso</div>
                                    </div>
                                    <div class="dash-num"><?= $resPeriodo['personas'] ?></div>
                                    <div class="dash-sub">
                                        <?= $periodoTodos ? 'Empleados distintos' : 'Histórico: ' . $resTotal['personas'] . ' persona(s)' ?>
                                    </div>
                                </div>

                                <div class="dash-card dash-kpi">
                                    <div class="dash-kpi__top">
                                        <span class="dash-ico azul"><?= icon('file-text', 'w-5 h-5') ?></span>
                                        <div class="dash-label">Permisos solicitados</div>
                                    </div>
                                    <div class="dash-num"><?= $resPeriodo['total'] ?></div>
                                    <div class="dash-sub">
                                        <?= $periodoTodos ? 'Todos los registros' : 'Total en la base de datos: ' . $resTotal['total'] ?>
                                    </div>
                                </div>

                                <div class="dash-card dash-kpi">
                                    <div class="dash-kpi__top">
                                        <span class="dash-ico ambar"><?= icon('alarm-clock', 'w-5 h-5') ?></span>
                                        <div class="dash-label">Tiempo de ausencia aprobado</div>
                                    </div>
                                    <div class="dash-num dash-num--txt"><?= htmlspecialchars($fmtHoras($resPeriodo['horas_firmadas'])) ?></div>
                                    <div class="dash-sub">
                                        <?= $resPeriodo['firmados'] > 0
                                            ? 'Promedio ' . htmlspecialchars($fmtHoras($promedioHoras)) . ' por permiso firmado'
                                            : 'Solo permisos ya firmados' ?>
                                    </div>
                                </div>

                                <div class="dash-card dash-kpi">
                                    <div class="dash-kpi__top">
                                        <span class="dash-ico verde"><?= icon('check', 'w-5 h-5') ?></span>
                                        <div class="dash-label">Tasa de aprobación</div>
                                    </div>
                                    <div class="dash-num"><?= $tasaAprobacion === null ? '—' : $tasaAprobacion . '%' ?></div>
                                    <div class="dash-sub"><?= $resPeriodo['firmados'] ?> firmado(s) · <?= $resPeriodo['rechazados'] ?> rechazado(s)</div>
                                </div>
                            </div>

                            <!-- 3. Gráficos: tendencia + estado del período -->
                            <?php
                            $enTramite = $pendFirmasPeriodo + $resPeriodo['esperando_regreso'];
                            $segmentos = [
                                ['Firmados', $resPeriodo['firmados'], '#16a34a'],
                                ['En trámite de firma', $enTramite, '#d97706'],
                                ['Devueltos', $resPeriodo['devueltos'], '#ea580c'],
                                ['Rechazados', $resPeriodo['rechazados'], '#dc2626'],
                                ['Anulados', $resPeriodo['anulados'], '#6b7280'],
                            ];
                            $resumenSegmentos = implode(', ', array_map(
                                fn($s) => $s[0] . ': ' . $s[1],
                                $segmentos
                            ));
                            $resumenTendencia = implode(', ', array_map(
                                fn($p) => EmpleadoModel::mesEnEspanol($p['mes']) . ': ' . $p['total'],
                                $tendencia
                            ));
                            ?>
                            <div class="dash-split">
                                <div class="dash-card">
                                    <h3>Tendencia de los últimos 6 meses</h3>
                                    <p class="dash-hint">Permisos solicitados por mes (termina en <?= $periodoTodos ? 'el mes actual' : htmlspecialchars($etiquetaPeriodo) ?>)</p>
                                    <div class="dash-barras" role="img" aria-label="Permisos por mes. <?= htmlspecialchars($resumenTendencia) ?>">
                                        <?php foreach ($tendencia as $i => $punto): ?>
                                            <?php
                                            $esUltimo = ($i === count($tendencia) - 1);
                                            $alto = $punto['total'] > 0 ? max(6, (int) round($punto['total'] * 100 / $maxTendencia)) : 3;
                                            ?>
                                            <div class="dash-col-b" aria-hidden="true">
                                                <span class="v"><?= $punto['total'] ?></span>
                                                <div class="b" style="height:<?= $alto ?>%;background:<?= $esUltimo ? '#dc2626' : '#fca5a5' ?>"></div>
                                                <span class="m"><?= htmlspecialchars(mb_substr(EmpleadoModel::mesEnEspanol($punto['mes']), 0, 3, 'UTF-8')) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="dash-card">
                                    <h3>Estado de las solicitudes del período</h3>
                                    <p class="dash-hint"><?= htmlspecialchars($etiquetaPeriodo) ?> · <?= $resPeriodo['total'] ?> permiso(s) según fecha de inicio</p>
                                    <div class="dash-apilada" role="img" aria-label="Estado de las solicitudes. <?= htmlspecialchars($resumenSegmentos) ?>">
                                        <?php foreach ($segmentos as [$nombre, $valor, $color]): ?>
                                            <?php if ($valor > 0): ?>
                                                <div title="<?= htmlspecialchars($nombre) ?>: <?= $valor ?>"
                                                    style="width:<?= ($valor * 100) / max(1, $resPeriodo['total']) ?>%;background:<?= $color ?>"></div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="dash-leyenda">
                                        <?php foreach ($segmentos as [$nombre, $valor, $color]): ?>
                                            <div><span class="pto" style="background:<?= $color ?>"></span><?= htmlspecialchars($nombre) ?>: <b><?= $valor ?></b></div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Listas: tipo, jefes pendientes, top empleados -->
                            <div class="dash-trio">
                                <div class="dash-card">
                                    <h3>Permisos por tipo</h3>
                                    <p class="dash-hint"><?= htmlspecialchars($etiquetaPeriodo) ?><?= $periodoTodos ? '' : ' · entre paréntesis, el total histórico' ?></p>
                                    <?php foreach ($tiposPeriodo as $tipo => $cantidad): ?>
                                        <?php $ancho = $pct($cantidad, $resPeriodo['total']); ?>
                                        <div class="dash-fila">
                                            <div class="dash-fila-top">
                                                <span><?= htmlspecialchars($etiquetasTipo[$tipo] ?? $tipo) ?></span>
                                                <span><b><?= $cantidad ?></b> <small><?= $ancho ?>%<?= $periodoTodos ? '' : ' (' . $tiposTotal[$tipo] . ')' ?></small></span>
                                            </div>
                                            <div class="dash-track"><div class="dash-fill" style="width:<?= $ancho ?>%;background:<?= $coloresTipo[$tipo] ?? '#6b7280' ?>"></div></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="dash-card">
                                    <h3>Jefes con firmas por dar</h3>
                                    <p class="dash-hint">Estado actual, sin importar el mes seleccionado</p>
                                    <?php if (empty($jefesPendientes)): ?>
                                        <div class="dash-vacio">Ningún jefe tiene permisos pendientes. ¡Todo al día!</div>
                                    <?php else: ?>
                                        <ul class="dash-lista">
                                            <?php foreach ($jefesPendientes as $i => $jefe): ?>
                                                <?php
                                                $diasEspera = (int) (new DateTime('now'))->diff(new DateTime($jefe['mas_antiguo']))->days;
                                                $textoEspera = $diasEspera === 0 ? 'desde hoy' : 'hace ' . $diasEspera . ' día' . ($diasEspera === 1 ? '' : 's');
                                                ?>
                                                <li>
                                                    <span class="dash-pos"><?= $i + 1 ?></span>
                                                    <div class="n">
                                                        <p><?= htmlspecialchars($jefe['nombre']) ?></p>
                                                        <small>El más antiguo espera <?= $textoEspera ?></small>
                                                    </div>
                                                    <span class="dash-pill <?= $diasEspera >= 3 ? 'alerta' : '' ?>"><?= (int) $jefe['pendientes'] ?> pendiente(s)</span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>

                                <div class="dash-card">
                                    <h3>Quién pide más permisos</h3>
                                    <p class="dash-hint">Top 5 · <?= htmlspecialchars($etiquetaPeriodo) ?></p>
                                    <?php if (empty($topEmpleados)): ?>
                                        <div class="dash-vacio">No hay permisos en este periodo.</div>
                                    <?php else: ?>
                                        <ul class="dash-lista">
                                            <?php foreach ($topEmpleados as $i => $emp): ?>
                                                <li>
                                                    <span class="dash-pos"><?= $i + 1 ?></span>
                                                    <div class="n">
                                                        <p><?= htmlspecialchars($emp['nombre']) ?></p>
                                                        <small>CC <?= htmlspecialchars($emp['cedula_empleado']) ?> · <?= htmlspecialchars($fmtHoras((float) $emp['horas'])) ?> aprobadas</small>
                                                    </div>
                                                    <span class="dash-pill"><?= (int) $emp['total'] ?> permiso(s)</span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>

                <!-- ============ 5. CUMPLEAÑOS (a la derecha en pantallas muy anchas) ============ -->
                <aside class="dash-aside" id="cumpleanos" aria-labelledby="tCumple">
                    <section class="dash-card" style="padding:0;overflow:hidden">
                        <div class="dash-cumple-head">
                            <div class="dash-sec-head">
                                <span class="dash-ico rojo"><?= icon('cake', 'w-5 h-5') ?></span>
                                <div>
                                    <h2 id="tCumple">Cumpleaños</h2>
                                    <p>Recordatorio con 15 días de anticipación</p>
                                </div>
                            </div>

                            <nav class="dash-cal-nav" aria-label="Navegar meses del calendario">
                                <a href="<?= $urlCalendario((int) date('Y'), (int) date('n')) ?>" class="dash-btn">Este mes</a>
                                <a href="<?= $urlCalendario((int) $mesAnterior->format('Y'), (int) $mesAnterior->format('n')) ?>"
                                    class="dash-nav-btn" aria-label="Mes anterior">
                                    <?= icon('chevron-left', 'w-5 h-5') ?>
                                </a>
                                <div class="mes" aria-live="polite"><?= htmlspecialchars($nombreMesCalendario . ' ' . $anioCalendario) ?></div>
                                <a href="<?= $urlCalendario((int) $mesSiguiente->format('Y'), (int) $mesSiguiente->format('n')) ?>"
                                    class="dash-nav-btn" aria-label="Mes siguiente">
                                    <?= icon('chevron-right', 'w-5 h-5') ?>
                                </a>
                            </nav>
                        </div>

                        <div class="dash-cumple-body">
                            <!-- Calendario compacto -->
                            <div class="dash-cal">
                                <div class="dash-cal-grid" aria-hidden="true">
                                    <?php foreach (['L', 'M', 'X', 'J', 'V', 'S', 'D'] as $diaSemana): ?>
                                        <div class="dash-cal-dow"><?= $diaSemana ?></div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="dash-cal-grid">
                                    <?php for ($espacio = 1; $espacio < $diaInicioSemana; $espacio++): ?>
                                        <div aria-hidden="true"></div>
                                    <?php endfor; ?>

                                    <?php for ($dia = 1; $dia <= $diasEnMes; $dia++): ?>
                                        <?php
                                        $tieneCumpleanos = !empty($cumpleanosPorDia[$dia]);
                                        $esHoy = ((int) date('Y') === $anioCalendario
                                            && (int) date('n') === $mesCalendario
                                            && (int) date('j') === $dia);
                                        $nombresDia = $tieneCumpleanos
                                            ? implode(', ', array_map(fn($e) => (string) $e['nombre'], $cumpleanosPorDia[$dia]))
                                            : '';
                                        ?>
                                        <div class="dash-dia <?= $tieneCumpleanos ? 'cumple' : '' ?> <?= $esHoy ? 'hoy' : '' ?>"
                                            <?= $tieneCumpleanos ? 'title="' . htmlspecialchars($nombresDia, ENT_QUOTES) . '"' : '' ?>>
                                            <span><?= $dia ?></span>
                                            <?php if ($tieneCumpleanos): ?>
                                                <span class="pto" aria-hidden="true"></span>
                                                <span class="sr-only">Cumpleaños: <?= htmlspecialchars($nombresDia) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>

                                <div class="dash-cal-pie"><span class="pto" aria-hidden="true"></span> Hay cumpleaños</div>
                            </div>

                            <!-- Lista del mes -->
                            <div class="dash-cumple-lista">
                                <h3>Personas que cumplen <span><?= count($cumpleanosMes) ?> persona(s)</span></h3>

                                <div class="dash-personas">
                                    <?php if (empty($cumpleanosMes)): ?>
                                        <div class="dash-vacio" style="margin-top:0">No hay cumpleaños registrados este mes.</div>
                                    <?php else: ?>
                                        <?php foreach ($cumpleanosMes as $emp): ?>
                                            <?php
                                            $infoCumple = EmpleadoModel::infoCumpleanos($emp['fecha_nacimiento'], 15);
                                            // Edad que va a cumplir en el año del calendario que se está viendo.
                                            $edadVaCumplir = $anioCalendario - (int) date('Y', strtotime($emp['fecha_nacimiento']));
                                            $proximidad = '';
                                            if ($infoCumple['cumple']) {
                                                $proximidad = $infoCumple['dias_faltantes'] === 0
                                                    ? 'Hoy'
                                                    : 'En ' . $infoCumple['dias_faltantes'] . ' día' . ($infoCumple['dias_faltantes'] === 1 ? '' : 's');
                                            }
                                            ?>
                                            <div class="dash-persona">
                                                <div class="dia"><?= str_pad((string) $emp['dia_cumpleanos'], 2, '0', STR_PAD_LEFT) ?></div>
                                                <div class="n">
                                                    <p><?= htmlspecialchars($emp['nombre']) ?></p>
                                                    <small>
                                                        CC <?= htmlspecialchars($emp['cedula']) ?>
                                                        <?php if ($proximidad !== ''): ?> · <span class="dash-prox"><?= $proximidad ?></span><?php endif; ?>
                                                    </small>
                                                </div>
                                                <span class="dash-edad"><?= $edadVaCumplir ?> años</span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($cumpleanosProximos)): ?>
                            <div class="dash-cumple-pie">
                                <b>Próximos 15 días:</b> <?= count($cumpleanosProximos) ?> cumpleañero(s).
                            </div>
                        <?php endif; ?>
                    </section>
                </aside>

            </div>
        </main>
    </div>
</body>

</html>
