<?php
declare(strict_types=1);


require_once __DIR__ . '/../includes/formatos_guard.php';
require_once __DIR__ . '/../includes/formatos_ui.php';
requireFormatosAccess();


$csrf = csrfToken();

$generadosDir = __DIR__ . '/../uploads/generados';
$generados = [];

if (is_dir($generadosDir)) {
    $archivos = [];
    // CORREGIDO: Buscar ambos formatos de nombre
    // 1. Formato antiguo OTROSI_*.docx
    // 2. Formato nuevo GH-FT-24 OTROSI...
    $patrones = [
        $generadosDir . '/OTROSI_*.docx',
        $generadosDir . '/GH-FT-24 OTROSI*.docx',
        $generadosDir . '/GH-FT-24*.docx',
    ];
    
    foreach ($patrones as $patron) {
        foreach (glob($patron) ?: [] as $ruta) {
            // Evitar duplicados
            $archivos[basename($ruta)] = $ruta;
        }
    }

    foreach ($archivos as $ruta) {
        $generados[] = [
            'archivo' => basename($ruta),
            'fecha' => filemtime($ruta) ?: time(),
        ];
    }

    usort($generados, static fn(array $a, array $b): int => $b['fecha'] <=> $a['fecha']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Otrosí</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <style>
        * { box-sizing: border-box; }
        body { background:#f8fafc; color:#1f2937; }

        .otrosi-card {
            min-width:0;
            transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .otrosi-card:hover {
            transform:translateY(-2px);
            border-color:#e5e7eb;
            box-shadow:0 10px 28px rgba(15,23,42,.06);
        }

        .otrosi-generado {
            display:grid;
            grid-template-columns:minmax(0,1fr) auto;
            align-items:center;
            gap:12px;
            min-width:0;
        }
        .otrosi-generado-contenido { min-width:0; }
        .otrosi-acciones {
            display:flex;
            flex-wrap:wrap;
            justify-content:flex-end;
            gap:7px;
        }
        .otrosi-acciones a,
        .otrosi-acciones button {
            min-height:34px;
            white-space:nowrap;
        }

        #otrosiEmpleado {
            transition:all .2s ease;
        }

        @media (max-width: 1023px) {
            .otrosi-generado {
                grid-template-columns:minmax(0,1fr);
            }
            .otrosi-acciones { justify-content:flex-start; }
        }

        @media (max-width:640px) {
            .otrosi-acciones {
                display:grid;
                grid-template-columns:repeat(2,minmax(0,1fr));
                width:100%;
            }
            .otrosi-acciones a,
            .otrosi-acciones button {
                width:100%;
                justify-content:center;
            }
            .otrosi-acciones .btn-eliminar-otrosi {
                grid-column:1 / -1;
            }
        }

        @media (max-width:420px) {
            .otrosi-acciones { grid-template-columns:1fr; }
            .otrosi-acciones .btn-eliminar-otrosi { grid-column:auto; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen text-gray-800">

<?php require __DIR__ . '/../includes/sidebar.php'; ?>

<div class="md:ml-64 pt-14 md:pt-0">
    <?= formatosHeader('Otrosí', 'Otrosí al Contrato Individual de Trabajo · GH-FT-24') ?>

    <main class="p-3 sm:p-5 lg:p-6 max-w-5xl mx-auto space-y-4 sm:space-y-5">

        <section class="otrosi-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
            <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-100">
                <div class="flex items-start gap-3">
                    <span class="w-10 h-10 shrink-0 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                        <?= icon('file-signature','w-5 h-5') ?>
                    </span>
                    <div>
                        <h2 class="font-bold text-gray-800">Generar Otrosí</h2>
                        <p class="text-xs text-gray-400">Busca al empleado por nombre o cédula. Los datos se cargan automáticamente.</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-5 lg:p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar empleado</label>
                    <input id="otrosiCedulaBuscar"
                            type="text"
                            
                            autocomplete="off"
                            placeholder="Escribe nombre o cédula..."
                            class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition">
                    <div id="otrosiResultados" class="mt-2 space-y-1"></div>
                    <p class="text-[11px] text-gray-400 mt-1.5">Puedes buscar por nombre completo, parte del nombre o número de cédula.</p>
                </div>

                <div id="otrosiError" class="hidden rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm p-3"></div>

                <div id="otrosiEmpleado" class="hidden rounded-xl bg-gray-50 border border-gray-100 p-4 sm:p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-sm">
                        <div>
                            <span class="block text-xs text-gray-400">Nombre</span>
                            <strong id="otrosiNombre" class="block text-gray-800"></strong>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400">Cédula</span>
                            <strong id="otrosiCedula" class="block text-gray-800"></strong>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400">Día</span>
                            <span id="otrosiDia" class="block text-gray-700"></span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400">Mes</span>
                            <span id="otrosiMes" class="block text-gray-700"></span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400">Año</span>
                            <span id="otrosiAnio" class="block text-gray-700"></span>
                        </div>
                    </div>
                </div>
                <?= formatosBarraAcciones('btnGenerarOtrosi', 'limpiarOtrosi()', 'button', 'generarOtrosi()') ?>
            </div>
        </section>

        <section class="otrosi-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
            <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">Generados</h2>
                <p class="text-xs text-gray-400">Archivos generados recientemente</p>
            </div>

            <div class="p-4 sm:p-5 lg:p-6">
                <?php if ($generados): ?>
                    <div class="space-y-2">
                        <?php foreach ($generados as $g): ?>
                            <div class="otrosi-generado border border-gray-100 rounded-xl p-3 hover:bg-gray-50/70 transition">
                                <div class="otrosi-generado-contenido">
                                    <p class="text-sm font-medium text-gray-700 break-words" title="<?= htmlspecialchars($g['archivo']) ?>">
                                        <?= htmlspecialchars($g['archivo']) ?>
                                    </p>
                                    <p class="text-xs text-gray-400"><?= date('d/m/Y H:i', $g['fecha']) ?></p>
                                </div>

                                <div class="otrosi-acciones">
                                    <a href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=ver"
                                       target="_blank"
                                       class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-lg transition">
                                        <?= icon('eye','w-4 h-4') ?> Ver
                                    </a>

                                    <a href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=descargar"
                                       class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-lg transition">
                                        <?= icon('download','w-4 h-4') ?> Descargar
                                    </a>

                                    <button type="button"
                                        onclick="eliminarOtrosi(<?= htmlspecialchars(json_encode($g['archivo'], JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT)) ?>)"
                                        class="btn-eliminar-otrosi inline-flex items-center gap-2 px-3 py-2 text-sm text-red-600 border border-red-200 hover:bg-red-50 rounded-lg transition">
                                        <?= icon('trash-2','w-4 h-4') ?> Eliminar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-xs text-gray-400 text-center py-8">Todavía no hay Otrosí generados.</p>
                <?php endif; ?>
            </div>
        </section>

    </main>
</div>

<script src="./assets/js/empleado_buscador.js"></script>
<script>
const csrfToken = <?= json_encode($csrf) ?>;
let empleadoOtrosi = null;

function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>'"]/g, c => ({
        '&':'&amp;', '<':'&lt;', '>':'&gt;', "'":'&#039;', '"':'&quot;'
    }[c]));
}

function mostrarErrorOtrosi(mensaje) {
    const box = document.getElementById('otrosiError');
    if (!mensaje) {
        box.classList.add('hidden');
        box.textContent = '';
        return;
    }
    box.textContent = mensaje;
    box.classList.remove('hidden');
}

function normalizarCedula(value) {
    return String(value ?? '').replace(/\D/g, '');
}

function mostrarEmpleadoOtrosi(emp) {
    empleadoOtrosi = emp;
    mostrarErrorOtrosi('');
    document.getElementById('otrosiNombre').textContent = emp.nombre;
    document.getElementById('otrosiCedula').textContent = emp.cedula;
    const hoy = new Date();
    document.getElementById('otrosiDia').textContent = hoy.toLocaleDateString('es-CO', {day: 'numeric'});
    document.getElementById('otrosiMes').textContent = hoy.toLocaleDateString('es-CO', {month: 'long'});
    document.getElementById('otrosiAnio').textContent = hoy.toLocaleDateString('es-CO', {year: 'numeric'});
    document.getElementById('otrosiEmpleado').classList.remove('hidden');
    formatosSetBoton(document.getElementById('btnGenerarOtrosi'), true);
}

function limpiarSeleccionOtrosi() {
    empleadoOtrosi = null;
    document.getElementById('otrosiEmpleado').classList.add('hidden');
    formatosSetBoton(document.getElementById('btnGenerarOtrosi'), false);
    mostrarErrorOtrosi('');
}

const buscadorOtrosi = crearBuscadorEmpleado({
    input: document.getElementById('otrosiCedulaBuscar'),
    lista: document.getElementById('otrosiResultados'),
    onSelect: mostrarEmpleadoOtrosi,
    onClear: limpiarSeleccionOtrosi,
    onError: mostrarErrorOtrosi
});

async function generarOtrosi() {
    if (!empleadoOtrosi) {
        mostrarErrorOtrosi('Primero busca y selecciona un empleado.');
        return;
    }

    mostrarErrorOtrosi('');

    const btn = document.getElementById('btnGenerarOtrosi');
    const fd = new FormData();
    fd.append('csrf_token', csrfToken);
    fd.append('cedula', empleadoOtrosi.cedula);

    btn.dataset.html = btn.innerHTML;
    btn.disabled = true;
    btn.textContent = 'Generando...';

    try {
        const r = await fetch('./api/formatos_otrosi_generar.php', {
            method: 'POST',
            body: fd
        });

        const data = await r.json();

        if (!data.ok) {
            throw new Error(data.error || 'No se pudo generar el Word.');
        }

        window.location.href = data.url;
        setTimeout(() => location.reload(), 1000);

    } catch (e) {
        mostrarErrorOtrosi(e.message || 'No se pudo generar el Word.');
        btn.innerHTML = btn.dataset.html;
        formatosSetBoton(btn, true);
    }
}

function limpiarOtrosi() {
    buscadorOtrosi.reset();
}

async function eliminarOtrosi(archivo) {
    if (!confirm('¿Eliminar este Otrosí generado?')) return;

    const fd = new FormData();
    fd.append('csrf_token', csrfToken);

    const url = './api/formato_archivo.php?f=' +
        encodeURIComponent(archivo) + '&accion=eliminar';

    try {
        const r = await fetch(url, {
            method: 'POST',
            body: fd
        });

        const data = await r.json();

        if (data.ok) {
            location.reload();
        } else {
            alert(data.error || 'No se pudo eliminar.');
        }
    } catch (e) {
        alert('No se pudo eliminar el archivo.');
    }
}
</script>
</body>
</html>
