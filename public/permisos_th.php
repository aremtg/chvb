<?php
require_once __DIR__ . '/../includes/session.php';
requireSuperAdmin();

// El teniente es solo lectura: no debe ver el botón de anular.
$puedeAnular = ($_SESSION['superadmin_rol'] ?? '') !== 'teniente';
$enfocadoEnUno = !empty($_GET['id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permisos - Talento Humano</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <link rel="stylesheet" href="./assets/css/permisos_ui.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/permisos_ui.css') ?>">
</head>
<body class="pm-body min-h-screen" style="--pm-max:80rem">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<div class="md:ml-64 pt-14 md:pt-0">
    <header class="pm-header">
        <div class="pm-header__in">
            <div>
                <h1 class="pm-title">Permisos</h1>
                <p class="pm-subtitle">Seguimiento de las solicitudes de permisos, vacaciones y licencias del personal.</p>
            </div>
            <div class="pm-header__actions">
                <span class="pm-live" title="La lista se actualiza sola cada pocos segundos"><i></i> En vivo</span>
            </div>
        </div>
    </header>

    <input type="hidden" id="csrfToken" value="<?= htmlspecialchars(csrfToken()) ?>">
    <main class="pm-main">

        <?php if ($enfocadoEnUno): ?>
            <div class="pm-banner">
                <span>Estás viendo un permiso específico.</span>
                <a href="./permisos_th.php">Ver todos los permisos</a>
            </div>
        <?php endif; ?>

        <!-- Resumen de la vista actual -->
        <section class="pm-stats" aria-label="Resumen de la vista actual">
            <div class="pm-stat pm-s-borrador">
                <span class="pm-stat__ico"><?= icon('file-text', 'pm-ico') ?></span>
                <div><div class="pm-stat__num" id="pmStatTotal">0</div><div class="pm-stat__lbl">Permisos en esta vista</div></div>
            </div>
            <div class="pm-stat pm-s-revision">
                <span class="pm-stat__ico"><?= icon('alarm-clock', 'pm-ico') ?></span>
                <div><div class="pm-stat__num" id="pmStatRevision">0</div><div class="pm-stat__lbl">Por firmar</div></div>
            </div>
            <div class="pm-stat pm-s-regreso">
                <span class="pm-stat__ico"><?= icon('clipboard-list', 'pm-ico') ?></span>
                <div><div class="pm-stat__num" id="pmStatRegreso">0</div><div class="pm-stat__lbl">Regreso pendiente</div></div>
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
                    <button type="button" id="pmLimpiar" class="pm-btn pm-btn--text-danger pm-btn--sm" hidden>Limpiar filtros</button>
                    <button type="button" id="pmToggleFiltros" class="pm-btn pm-btn--ghost pm-btn--sm pm-filters__toggle"
                            aria-expanded="false" aria-controls="pmFiltrosBody">
                        Mostrar <?= icon('chevron-down', 'pm-ico pm-ico--sm') ?>
                    </button>
                </div>
            </div>
            <div class="pm-filters__body pm-cols-4" id="pmFiltrosBody">
                <div class="pm-field">
                    <label for="fEmpleado">Empleado (creador)</label>
                    <input type="text" id="fEmpleado" placeholder="Cédula" inputmode="numeric" autocomplete="off" class="pm-input">
                </div>
                <div class="pm-field">
                    <label for="fJefe">Jefe inmediato</label>
                    <input type="text" id="fJefe" placeholder="Cédula" inputmode="numeric" autocomplete="off" class="pm-input">
                </div>
                <div class="pm-field">
                    <label for="fTipo">Tipo</label>
                    <select id="fTipo" class="pm-select">
                        <option value="">Todos</option>
                        <option value="Permiso">Permiso</option>
                        <option value="Vacaciones">Vacaciones</option>
                        <option value="Licencia">Licencia</option>
                        <option value="Mision institucional">Misión institucional</option>
                    </select>
                </div>
                <div class="pm-field">
                    <label for="fEstado">Estado</label>
                    <select id="fEstado" class="pm-select">
                        <option value="">Todos</option>
                        <option value="en_proceso">En proceso</option>
                        <option value="firmado">Firmados/Aprobados</option>
                        <option value="rechazado">Rechazados</option>
                        <option value="devuelto">Devueltos</option>
                        <option value="anulado">Anulados</option>
                    </select>
                </div>
                <div class="pm-field">
                    <label for="fDesde">Desde</label>
                    <input type="date" id="fDesde" class="pm-input">
                </div>
                <div class="pm-field">
                    <label for="fHasta">Hasta</label>
                    <input type="date" id="fHasta" class="pm-input">
                </div>
                <div class="pm-field">
                    <label for="fOrdenHoras">Ordenar por horas</label>
                    <select id="fOrdenHoras" class="pm-select">
                        <option value="">Sin orden</option>
                        <option value="desc">Mayor a menor</option>
                        <option value="asc">Menor a mayor</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- Exportar a PDF (admin, auxiliar y teniente). Se descarga en el dispositivo; no se guarda en la BD. -->
        <div class="pm-pdfbar" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.5rem .75rem;margin:.25rem 0 .5rem">
            <div class="pm-count" aria-live="polite" id="pmContador" style="margin:0"></div>
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.5rem .9rem">
                <label id="pmPdfDosWrap" hidden style="display:none;align-items:center;gap:.4rem;font-size:.85rem;cursor:pointer">
                    <input type="checkbox" id="pmPdfDos" checked>
                    2 permisos por hoja oficio
                </label>
                <button type="button" id="pmPdfTodos" class="pm-btn pm-btn--outline-brand pm-btn--sm" disabled>
                    <?= icon('download', 'pm-ico pm-ico--sm') ?> <span id="pmPdfTodosTxt">Descargar PDF</span>
                </button>
            </div>
        </div>

        <!-- Cabecera de columnas (solo en pantallas anchas) -->
        <div class="pm-thead" aria-hidden="true">
            <span>Empleado</span><span>Permiso</span><span>Jefe inmediato</span>
            <span>Duración</span><span>Solicitado</span><span>Estado</span><span></span>
        </div>

        <div id="listaPermisosTH" class="pm-list" data-puede-anular="<?= $puedeAnular ? '1' : '0' ?>"></div>
    </main>
</div>
<script src="./assets/js/permisos_th.js"></script>
</body>
</html>
