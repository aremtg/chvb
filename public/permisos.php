<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/PermisoModel.php';
requireEmpleado();

$cedula = $_SESSION['empleado_cedula'];
$permisos = PermisoModel::listarPorEmpleadoConFiltros($cedula, []);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Permisos - CHVB</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <link rel="stylesheet" href="./assets/css/permisos_ui.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/permisos_ui.css') ?>">
</head>
<body class="pm-body min-h-screen">
<?php require __DIR__ . '/../includes/sidebar_empleado.php'; ?>

<div class="md:ml-64 pt-14 md:pt-0">
    <header class="pm-header">
        <div class="pm-header__in">
            <div>
                <h1 class="pm-title">Mis permisos</h1>
                <p class="pm-subtitle">Consulta el estado de tus solicitudes y las que requieren tu atención.</p>
            </div>
        </div>
    </header>

    <main class="pm-main">

        <!-- Resumen (se calcula con todos tus permisos, sin filtros) -->
        <section class="pm-stats" aria-label="Resumen de tus permisos">
            <div class="pm-stat pm-s-borrador">
                <span class="pm-stat__ico"><?= icon('file-text', 'pm-ico') ?></span>
                <div><div class="pm-stat__num" id="pmStatTotal">0</div><div class="pm-stat__lbl">Total</div></div>
            </div>
            <div class="pm-stat pm-s-revision">
                <span class="pm-stat__ico"><?= icon('alarm-clock', 'pm-ico') ?></span>
                <div><div class="pm-stat__num" id="pmStatRevision">0</div><div class="pm-stat__lbl">En revisión</div></div>
            </div>
            <div class="pm-stat pm-s-accion" id="pmStatAccionBox">
                <span class="pm-stat__ico"><?= icon('circle-alert', 'pm-ico') ?></span>
                <div><div class="pm-stat__num" id="pmStatAccion">0</div><div class="pm-stat__lbl">Requieren acción</div></div>
            </div>
            <div class="pm-stat pm-s-firmado">
                <span class="pm-stat__ico"><?= icon('check', 'pm-ico') ?></span>
                <div><div class="pm-stat__num" id="pmStatFirmados">0</div><div class="pm-stat__lbl">Firmados</div></div>
            </div>
        </section>

        <!-- Filtros -->
        <section class="pm-filters" aria-label="Filtros">
            <div class="pm-filters__bar">
                <div class="pm-filters__head">
                    <?= icon('search', 'pm-ico') ?> Filtros
                    <span class="pm-filters__badge" id="pmFiltrosActivos" hidden>0</span>
                </div>
                <div style="display:flex;gap:.5rem;align-items:center">
                    <button type="button" id="pmLimpiar" class="pm-btn pm-btn--text-danger pm-btn--sm" hidden>Limpiar</button>
                    <button type="button" id="pmToggleFiltros" class="pm-btn pm-btn--ghost pm-btn--sm pm-filters__toggle"
                            aria-expanded="false" aria-controls="pmFiltrosBody">
                        Mostrar <?= icon('chevron-down', 'pm-ico pm-ico--sm') ?>
                    </button>
                </div>
            </div>
            <div class="pm-filters__body pm-cols-4" id="pmFiltrosBody">
                <div class="pm-field">
                    <label for="filtroTipo">Tipo de permiso</label>
                    <select id="filtroTipo" class="pm-select">
                        <option value="">Todos</option>
                        <option value="Permiso">Permiso</option>
                        <option value="Vacaciones">Vacaciones</option>
                        <option value="Licencia">Licencia</option>
                        <option value="Mision institucional">Misión institucional</option>
                    </select>
                </div>
                <div class="pm-field">
                    <label for="filtroEstado">Estado</label>
                    <select id="filtroEstado" class="pm-select">
                        <option value="">Todos</option>
                        <option value="en_revision">En revisión</option>
                        <option value="devuelto">Devueltos (para editar)</option>
                        <option value="firmado">Firmados</option>
                        <option value="rechazado">Rechazados</option>
                        <option value="anulado">Anulados</option>
                        <option value="aprobado_pendiente_regreso">Regreso pendiente</option>
                        <option value="por_firmar_jefe_final">Pendientes de firma final</option>
                    </select>
                </div>
                <div class="pm-field">
                    <label for="filtroFechaDesde">Desde</label>
                    <input type="date" id="filtroFechaDesde" class="pm-input">
                </div>
                <div class="pm-field">
                    <label for="filtroFechaHasta">Hasta</label>
                    <input type="date" id="filtroFechaHasta" class="pm-input">
                </div>
            </div>
        </section>

        <div class="pm-count" aria-live="polite" id="pmContador"></div>

        <div id="listaPermisos" class="pm-list" data-permisos-iniciales='<?= htmlspecialchars(json_encode($permisos), ENT_QUOTES) ?>'></div>

    </main>
</div>

<script src="./assets/js/permisos.js"></script>
</body>
</html>
