<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/models/FormatoModel.php';
requireSuperAdmin();

$rolesFormatos = ['superadmin_talento_humano', 'auxiliar_talento_humano'];
if (!in_array($_SESSION['superadmin_rol'] ?? '', $rolesFormatos, true)) {
    http_response_code(403);
    exit('No autorizado.');
}

$generados = FormatoModel::listarRenovacionesGeneradas(__DIR__ . '/../uploads/generados');
$csrf = csrfToken();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Formatos</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <style>
        /* Las acciones de los archivos nunca deben quedar recortadas. */
        .formato-renovaciones-card,
        .formato-generados-list,
        .formato-generado {
            overflow: visible !important;
        }

        .formato-generado {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .formato-generado-contenido {
            min-width: 0;
            flex: 1 1 240px;
        }

        /* Solo las acciones de los archivos: se adaptan al ancho disponible. */
        .formato-generado-acciones {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            width: 100%;
            min-width: 0;
            position: relative;
            z-index: 10;
        }

        .formato-generado-acciones a,
        .formato-generado-acciones button {
            width: 100%;
            min-width: 0;
            justify-content: center;
            position: relative;
            z-index: 10;
            white-space: nowrap;
        }

        .formato-generado-acciones .btn-eliminar-formato {
            grid-column: 1 / -1;
        }

        @media (min-width: 641px) {
            .formato-generado-acciones {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 380px) {
            .formato-generado-acciones {
                grid-template-columns: 1fr;
            }

            .formato-generado-acciones .btn-eliminar-formato {
                grid-column: auto;
            }
        }

        .ren-alerta {
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #b91c1c;
        }

        .ren-resumen-superior {
            border: 1px solid #f3f4f6;
            background: #f9fafb;
        }

        .ren-row {
            overflow: visible;
            border: 1px solid #f3f4f6;
            background: #fff;
        }

        .ren-row:hover {
            background: #f9fafb;
            border-color: #e5e7eb;
        }

        .ren-row-error {
            border-color: #fecaca !important;
            background: #fffafa;
        }

        .ren-field-readonly {
            background: #f9fafb;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen text-gray-800">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white border-b border-gray-100 px-4 sm:px-6 py-4">
            <div class="flex items-center gap-3"><span
                    class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center"><?= icon('file-text', 'w-5 h-5') ?></span>
                <div>
                    <h1 class="text-base font-bold text-gray-800">Formatos</h1>
                    <p class="text-xs text-gray-400">Gestión de formatos y renovaciones</p>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 max-w-7xl mx-auto space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <section
                    class="formato-renovaciones-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">

                                <div>
                                    <h2 class="font-bold text-gray-800">Renovaciones</h2>
                                    <p class="text-xs text-gray-400">Renovación de contrato</p>
                                </div>
                            </div>
                            <button type="button" onclick="abrirModalRenovacion()"
                                class="w-9 h-9 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-500 flex items-center justify-center transition"
                                title="Abrir renovaciones">
                                <?= icon('more-vertical', 'w-5 h-5') ?>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-3">
                            <p class="text-sm font-semibold text-gray-700 truncate">AF-FT-02 · Renovación de Contrato
                            </p>
                            <p class="text-xs text-gray-400">Plantilla oficial</p>
                        </div>

                        <?php if ($generados): ?>
                            <div class="formato-generados-list mt-4 space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Generados</p>
                                <?php foreach ($generados as $g): ?>
                                    <div
                                        class="formato-generado border border-gray-100 rounded-xl p-3 hover:bg-gray-50/70 transition">
                                        <div class="formato-generado-contenido">
                                            <p class="text-sm font-medium text-gray-700 break-words"
                                                title="<?= htmlspecialchars($g['archivo']) ?>">
                                                <?= htmlspecialchars($g['archivo']) ?>
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1"><?= date('d/m/Y H:i', $g['fecha']) ?></p>
                                        </div>
                                        <div class="formato-generado-acciones">
                                            <a href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=ver"
                                                target="_blank" rel="noopener"
                                                class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-lg transition">
                                                <?= icon('eye', 'w-4 h-4') ?> Ver
                                            </a>
                                            <a href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=descargar"
                                                class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-lg transition">
                                                <?= icon('download', 'w-4 h-4') ?> Descargar
                                            </a>
                                            <button type="button"
                                                onclick="eliminarFormato(<?= htmlspecialchars(json_encode($g['archivo'], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>)"
                                                class="btn-eliminar-formato inline-flex items-center gap-2 px-3 py-2 text-sm text-red-600 border border-red-200 hover:bg-red-50 rounded-lg transition">
                                                <?= icon('trash-2', 'w-4 h-4') ?> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="mt-4 text-xs text-gray-400 text-center py-3">Todavía no hay renovaciones generadas.
                            </p>
                        <?php endif; ?>
                    </div>
                </section>

                <?php foreach ([
                    ['Terminación de contrato', 'file-minus'],
                    ['Otro Sí', 'file-signature'],
                    ['Requisición', 'clipboard-list']
                ] as $card): ?>
                    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
                        <div class="p-5">
                            <div class="flex items-center gap-3">

                                <div>
                                    <h2 class="font-bold text-gray-800"><?= htmlspecialchars($card[0]) ?></h2>
                                    <p class="text-xs text-gray-400">Próximamente</p>
                                </div>
                            </div>
                            <div class="mt-5 rounded-xl border border-gray-100 bg-gray-50/60 p-3 text-xs text-gray-400">Aquí
                                estarán los formatos de esta categoría.</div>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <div id="modalRenovacion" class="hidden fixed inset-0 z-50 bg-black/40 p-3 sm:p-6 items-center justify-center">
        <div
            class="bg-white rounded-2xl shadow-sm border border-gray-100 w-full max-w-3xl max-h-[94vh] overflow-y-auto">
            <div
                class="px-5 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-20">
                <div>
                    <h2 class="font-bold text-gray-800">Renovación de Contrato</h2>
                    <p class="text-xs text-gray-400">Busca el empleado por cédula y agrega las renovaciones con +.</p>
                </div>
                <button type="button" onclick="cerrarModalRenovacion()"
                    class="w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-400 hover:bg-gray-50 hover:text-gray-700 flex items-center justify-center transition">
                    <?= icon('x', 'w-5 h-5') ?>
                </button>
            </div>

            <form id="formRenovacion" class="p-5 space-y-5">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="cedula" id="renCedula">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cédula del empleado</label>
                    <input id="renBuscar" type="text" inputmode="numeric" autocomplete="off"
                        placeholder="Escribe la cédula..."
                        class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition">
                    <div id="renResultados" class="mt-2 space-y-1"></div>
                </div>

                <div id="renEmpleado"
                    class="hidden rounded-xl bg-gray-50/70 border border-gray-100 p-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                </div>
                <div id="renDatosFaltantes"
                    class="hidden rounded-xl bg-red-50 border border-red-200 text-red-700 p-4 text-sm"></div>

                <div id="renCampos" class="hidden space-y-4">
                    <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-4">
                        <div class="text-xs text-gray-400">Fecha fin del contrato inicial</div>
                        <div id="renFinContrato" class="mt-1 text-base font-semibold text-gray-800">-</div>
                    </div>

                    <div id="renAlerta" class="hidden ren-alerta rounded-xl p-4 text-sm font-semibold"></div>

                    <div id="renAcumulado"
                        class="ren-resumen-superior rounded-xl p-4 text-sm text-gray-700 bg-gray-50/70"></div>

                    <div>
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Historial de Renovaciones</p>
                                <p class="text-xs text-gray-400">Agrega una renovación y solo selecciona su duración.
                                </p>
                            </div>
                            <button id="btnAgregarRen" type="button" onclick="agregarRenovacion()"
                                class="shrink-0 inline-flex items-center gap-2 px-3.5 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">
                                <?= icon('plus', 'w-4 h-4') ?> Agregar Renovación
                            </button>
                        </div>
                        <div id="renHistorial" class="space-y-3"></div>
                        <div id="renHistorialVacio"
                            class="rounded-xl border border-dashed border-gray-200 bg-gray-50/40 text-center text-sm text-gray-400 py-8">
                            Aún no has agregado renovaciones.
                        </div>
                    </div>
                </div>

                <div id="renError" class="hidden rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm p-3">
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <button type="button" onclick="cerrarModalRenovacion()"
                        class="px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition">Cancelar</button>
                    <button id="btnGenerarRen" type="submit" disabled
                        class="px-4 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium opacity-50 cursor-not-allowed transition">
                        Generar Word
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const csrfToken = <?= json_encode($csrf) ?>;
        const opcionesDuracion = [
            { meses: 1, texto: '1 mes' },
            { meses: 2, texto: '2 meses' },
            { meses: 3, texto: '3 meses' },
            { meses: 6, texto: '6 meses' },
            { meses: 12, texto: '12 meses' },
            { meses: 24, texto: '2 años' }
        ];
        let empleadoRen = null;
        let renovacionesRen = [];

        function abrirModalRenovacion() {
            const m = document.getElementById('modalRenovacion');
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.getElementById('renBuscar').focus();
        }

        function cerrarModalRenovacion() {
            const m = document.getElementById('modalRenovacion');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>'"]/g, c => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
            }[c]));
        }

        function fmtDate(s) {
            if (!s) return '-';
            const [y, m, d] = s.split('-');
            return `${d}/${m}/${y}`;
        }

        function dateObj(s) {
            const [y, m, d] = String(s).split('-').map(Number);
            return new Date(y, m - 1, d);
        }

        function iso(d) {
            return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        }

        function addDay(s) {
            const d = dateObj(s);
            d.setDate(d.getDate() + 1);
            return iso(d);
        }

        function finPorMeses(inicio, meses) {
            const d = dateObj(inicio);
            const day = d.getDate();
            d.setMonth(d.getMonth() + Number(meses));
            if (d.getDate() !== day) d.setDate(0);
            d.setDate(d.getDate() - 1);
            return iso(d);
        }

        function mesesContratoInicial(inicio, fin) {
            if (!inicio || !fin) return 0;
            const a = dateObj(inicio);
            const finMasUnDia = dateObj(addDay(fin));
            let meses = (finMasUnDia.getFullYear() - a.getFullYear()) * 12 + (finMasUnDia.getMonth() - a.getMonth());
            if (meses < 0) return 0;
            const candidato = new Date(a.getFullYear(), a.getMonth() + meses, a.getDate());
            if (candidato < finMasUnDia) meses++;
            return Math.max(0, meses);
        }

        function textoAcumulado(meses) {
            const anos = Math.floor(meses / 12);
            const resto = meses % 12;
            const partes = [];
            if (anos) partes.push(`${anos} ${anos === 1 ? 'año' : 'años'}`);
            if (resto) partes.push(`${resto} ${resto === 1 ? 'mes' : 'meses'}`);
            return partes.length ? partes.join(' ') : '0 meses';
        }

        function sexoNormalizado(sexo) {
            const s = String(sexo ?? '').trim().toLowerCase();
            if (['f', 'femenino', 'femenina', 'mujer'].includes(s)) return 'F';
            if (['m', 'masculino', 'hombre'].includes(s)) return 'M';
            return '';
        }

        function camposFaltantes(emp) {
            const faltan = [];
            if (!String(emp.nombre ?? '').trim()) faltan.push('Nombre');
            if (!String(emp.cedula ?? '').trim()) faltan.push('Cédula');
            if (!sexoNormalizado(emp.sexo)) faltan.push('Sexo');
            if (!String(emp.cargo ?? '').trim()) faltan.push('Cargo');
            if (!String(emp.tipo_de_personal ?? '').trim()) faltan.push('Tipo de personal');
            if (!String(emp.fecha_inicio_contrato ?? '').trim()) faltan.push('Fecha de inicio del contrato');
            if (!String(emp.fecha_fin_contrato ?? '').trim()) faltan.push('Fecha de fin del contrato');
            return faltan;
        }

        function mostrarError(mensaje) {
            const box = document.getElementById('renError');
            if (!mensaje) {
                box.classList.add('hidden');
                box.textContent = '';
                return;
            }
            box.textContent = mensaje;
            box.classList.remove('hidden');
        }

        function setAlerta(mensaje) {
            const box = document.getElementById('renAlerta');
            if (!mensaje) {
                box.classList.add('hidden');
                box.textContent = '';
                return;
            }
            box.textContent = mensaje;
            box.classList.remove('hidden');
        }

        function actualizarResumen() {
            if (!empleadoRen) return;

            const inicial = mesesContratoInicial(empleadoRen.fecha_inicio_contrato, empleadoRen.fecha_fin_contrato);
            const renovaciones = renovacionesRen.reduce((total, r) => total + Number(r.meses || 0), 0);
            const total = inicial + renovaciones;
            const faltan = Math.max(0, 48 - total);
            const ultima = renovacionesRen.length ? Number(renovacionesRen[renovacionesRen.length - 1].meses || 0) : 0;
            const siguiente = renovacionesRen.length ? ultima : 1;
            const proximaMinima = renovacionesRen.length ? ultima : 1;

            document.getElementById('renAcumulado').innerHTML = `
        <div class="font-semibold text-gray-800 text-sm">Estado de la vigencia</div>
        <div class="mt-1 text-sm">Lleva acumulado: <strong>${textoAcumulado(total)}</strong> | Le quedan: <strong>${textoAcumulado(faltan)}</strong> para los 4 años | Próxima renovación mínima permitida: <strong>${proximaMinima} ${proximaMinima === 1 ? 'mes' : 'meses'}</strong></div>
    `;

            if (total > 48) {
                setAlerta(`ALERTA: Esta persona debe pasar a Contrato Indefinido, ya que supera los 4 años o la próxima renovación lo haría superar. Total actual ${textoAcumulado(total)}, le faltan 0 meses. Ley 2466 de 2025`);
            } else {
                setAlerta('');
            }

            return { inicial, renovaciones, total, faltan, siguiente, proximaMinima };
        }

        function opcionesHtml(minimo) {
            return opcionesDuracion
                .filter(o => o.meses >= minimo)
                .map(o => `<option value="${o.meses}">${o.texto}</option>`)
                .join('');
        }

        function fechaInicioEsperada(index) {
            if (!index) return addDay(empleadoRen.fecha_fin_contrato);
            return addDay(renovacionesRen[index - 1].fin);
        }

        function normalizarFila(index) {
            const r = renovacionesRen[index];
            r.inicio = fechaInicioEsperada(index);
            r.fin = finPorMeses(r.inicio, r.meses);
        }

        function renderHistorial() {
            const cont = document.getElementById('renHistorial');
            const vacio = document.getElementById('renHistorialVacio');
            vacio.classList.toggle('hidden', renovacionesRen.length > 0);

            cont.innerHTML = renovacionesRen.map((r, index) => {
                const minimo = index >= 3 ? 12 : (index > 0 ? Number(renovacionesRen[index - 1].meses) : 1);
                const minimoReal = Math.max(minimo, index >= 3 ? 12 : 1);
                return `
            <div class="ren-row rounded-xl p-3 sm:p-4 hover:border-gray-200 transition" data-index="${index}">
                <div class="grid grid-cols-1 md:grid-cols-[72px_1fr_1fr_1fr_auto] gap-3 items-end">
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Renovación</div>
                        <div class="font-bold text-gray-800 py-2.5">RNV${index + 1}</div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Fecha Inicio</label>
                        <input type="date" value="${r.inicio}" data-index="${index}"
                            class="renFechaInicio w-full h-10 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Duración</label>
                        <select class="renDuracion w-full h-10 border border-gray-200 bg-white rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300" data-index="${index}">
                            ${opcionesHtml(minimoReal)}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Fecha Fin</label>
                        <input type="date" value="${r.fin}" readonly
                            class="w-full h-10 border border-gray-200 rounded-lg px-3 text-sm ren-field-readonly text-gray-700">
                    </div>
                    <button type="button" onclick="eliminarRenovacion(${index})"
                        class="w-full md:w-10 h-10 rounded-lg border border-gray-200 text-gray-500 hover:text-red-600 hover:border-red-200 hover:bg-red-50 flex items-center justify-center transition"
                        title="Eliminar RNV${index + 1}">
                        <?= icon('x', 'w-4 h-4') ?>
                    </button>
                </div>
                <div class="renFilaError hidden mt-2 text-xs text-red-700"></div>
            </div>
        `;
            }).join('');

            renovacionesRen.forEach((r, index) => {
                const select = cont.querySelector(`.renDuracion[data-index="${index}"]`);
                if (select) select.value = String(r.meses);
            });

            cont.querySelectorAll('.renDuracion').forEach(select => {
                select.addEventListener('change', () => cambiarDuracion(Number(select.dataset.index), Number(select.value)));
            });
            cont.querySelectorAll('.renFechaInicio').forEach(input => {
                input.addEventListener('change', () => cambiarFechaInicio(Number(input.dataset.index), input.value));
            });
        }

        function agregarRenovacion() {
            if (!empleadoRen) return;
            if (renovacionesRen.length >= 4) {
                mostrarError('La plantilla de Renovación de Contrato está configurada hasta RNV4.');
                return;
            }

            const estado = actualizarResumen();
            const index = renovacionesRen.length;
            const minimo = index >= 3 ? 12 : (index > 0 ? Number(renovacionesRen[index - 1].meses) : 1);
            const minimoReal = Math.max(minimo, index >= 3 ? 12 : 1);

            if (estado && estado.faltan < minimoReal) {
                setAlerta(`ALERTA: Esta persona debe pasar a Contrato Indefinido, ya que supera los 4 años o la próxima renovación lo haría superar. Total actual ${textoAcumulado(estado.total)}, le faltan ${estado.faltan} ${estado.faltan === 1 ? 'mes' : 'meses'}. Ley 2466 de 2025`);
                mostrarError(`No se puede agregar RNV${index + 1}: la próxima renovación mínima sería de ${minimoReal} meses y supera los 4 años permitidos.`);
                return;
            }

            const r = { meses: minimoReal, inicio: '', fin: '' };
            renovacionesRen.push(r);
            normalizarFila(index);
            renderHistorial();
            actualizarResumen();
            validarFormulario();
        }

        function cambiarDuracion(index, meses) {
            const r = renovacionesRen[index];
            if (!r) return;

            const anterior = index > 0 ? Number(renovacionesRen[index - 1].meses) : 1;
            const minimo = Math.max(anterior, index >= 3 ? 12 : 1);
            const row = document.querySelector(`.ren-row[data-index="${index}"]`);
            const err = row?.querySelector('.renFilaError');

            if (index >= 3 && meses < 12) {
                if (err) {
                    err.textContent = 'La 4ta renovación debe ser igual o mayor a 1 año Art. 46 CST Ley 2466 de 2025';
                    err.classList.remove('hidden');
                }
                setAlerta('La 4ta renovación debe ser igual o mayor a 1 año Art. 46 CST Ley 2466 de 2025');
                renderHistorial();
                validarFormulario();
                setAlerta('La 4ta renovación debe ser igual o mayor a 1 año Art. 46 CST Ley 2466 de 2025');
                mostrarError('La 4ta renovación debe ser igual o mayor a 1 año Art. 46 CST Ley 2466 de 2025');
                return;
            }
            if (meses < anterior) {
                const mensajeLegal =
                    'Validación Legal de Renovación Corta: ' +
                    'No es posible registrar una duración menor a la del periodo anterior ' +
                    '(DURACION_CONTRATO_VIGENTE) mediante renovación automática. ' +
                    'Reducir el tiempo del contrato solo es legal si se suscribe un Otrosí de mutuo acuerdo.';

                if (err) {
                    err.textContent = mensajeLegal;
                    err.classList.remove('hidden');
                }

                setAlerta(mensajeLegal);
                mostrarError(mensajeLegal);

                renderHistorial();
                validarFormulario();

                return;
            }
            const estadoAntes = actualizarResumen();
            const totalConNueva = estadoAntes.inicial + estadoAntes.renovaciones - Number(r.meses) + meses;
            if (totalConNueva > 48) {
                const faltanActual = Math.max(0, 48 - (estadoAntes.inicial + estadoAntes.renovaciones - Number(r.meses)));
                const alerta = `ALERTA: Esta persona debe pasar a Contrato Indefinido, ya que supera los 4 años o la próxima renovación lo haría superar. Total actual ${textoAcumulado(estadoAntes.inicial + estadoAntes.renovaciones - Number(r.meses))}, le faltan ${faltanActual} ${faltanActual === 1 ? 'mes' : 'meses'}. Ley 2466 de 2025`;
                setAlerta(alerta);
                if (err) {
                    err.textContent = alerta;
                    err.classList.remove('hidden');
                }
                renderHistorial();
                validarFormulario();
                setAlerta(alerta);
                mostrarError(alerta);
                return;
            }

            r.meses = meses;
            for (let i = index; i < renovacionesRen.length; i++) normalizarFila(i);
            renderHistorial();
            actualizarResumen();
            mostrarError('');
            validarFormulario();
        }

        function cambiarFechaInicio(index, fecha) {
            const r = renovacionesRen[index];
            if (!r || !fecha) return;

            const dt = dateObj(fecha);
            if (Number.isNaN(dt.getTime())) {
                renderHistorial();
                return;
            }

            r.inicio = fecha;
            r.fin = finPorMeses(r.inicio, r.meses);

            // Las fechas de las renovaciones siguientes siguen siendo automáticas:
            // comienzan el día siguiente al fin de la anterior.
            for (let i = index + 1; i < renovacionesRen.length; i++) {
                normalizarFila(i);
            }

            renderHistorial();
            actualizarResumen();
            mostrarError('');
            validarFormulario();
        }

        function eliminarRenovacion(index) {
            renovacionesRen.splice(index, 1);
            for (let i = index; i < renovacionesRen.length; i++) normalizarFila(i);
            renderHistorial();
            actualizarResumen();
            mostrarError('');
            validarFormulario();
        }

        function validarFormulario() {
            const btn = document.getElementById('btnGenerarRen');
            if (!empleadoRen || !renovacionesRen.length) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                return false;
            }

            let valido = true;
            let mensaje = '';
            let anterior = 0;
            renovacionesRen.forEach((r, index) => {
                const meses = Number(r.meses);
                if (index >= 3 && meses < 12) {
                    valido = false;
                    mensaje = 'La 4ta renovación debe ser igual o mayor a 1 año Art. 46 CST Ley 2466 de 2025';
                }
                if (index > 0 && meses < anterior) {
                    valido = false;

                    mensaje =
                        'Validación Legal de Renovación Corta: ' +
                        'No es posible registrar una duración menor a la del periodo anterior ' +
                        '(DURACION_CONTRATO_VIGENTE) mediante renovación automática. ' +
                        'Reducir el tiempo del contrato solo es legal si se suscribe un Otrosí de mutuo acuerdo.';
                }
                anterior = meses;
            });

            const estado = actualizarResumen();
            if (estado.total > 48) {
                valido = false;
                mensaje = `ALERTA: Esta persona debe pasar a Contrato Indefinido, ya que supera los 4 años o la próxima renovación lo haría superar. Total actual ${textoAcumulado(estado.total)}, le faltan 0 meses. Ley 2466 de 2025`;
            }

            if (estado.faltan > 0 && renovacionesRen.length < 4) {
                const siguienteMinimo = Math.max(Number(renovacionesRen[renovacionesRen.length - 1].meses), renovacionesRen.length >= 3 ? 12 : 1);
                if (estado.faltan < siguienteMinimo) {
                    const alerta = `ALERTA: Esta persona debe pasar a Contrato Indefinido, ya que supera los 4 años o la próxima renovación lo haría superar. Total actual ${textoAcumulado(estado.total)}, le faltan ${estado.faltan} ${estado.faltan === 1 ? 'mes' : 'meses'}. Ley 2466 de 2025`;
                    setAlerta(alerta);
                }
            }

            btn.disabled = !valido;
            btn.classList.toggle('opacity-50', !valido);
            btn.classList.toggle('cursor-not-allowed', !valido);
            if (!valido && mensaje) mostrarError(mensaje);
            else if (valido) mostrarError('');
            return valido;
        }

        async function buscarEmpleadoPorCedula(q) {
            try {
                const r = await fetch(`./api/formatos_renovacion_empleados.php?q=${encodeURIComponent(q)}`);
                const data = await r.json();
                if (!data.ok) throw new Error(data.error || 'No se pudo buscar el empleado.');
                const empleados = (data.empleados || []).filter(emp => String(emp.cedula || '').includes(q));
                document.getElementById('renResultados').innerHTML = empleados.map(emp => `
            <button type="button" onclick='seleccionarEmpleado(${JSON.stringify(emp)})'
                class="w-full text-left p-3 rounded-xl border border-gray-100 bg-white hover:bg-red-50 transition">
                <div class="font-medium text-gray-800">${escapeHtml(emp.nombre)}</div>
                <div class="text-xs text-gray-500">CC. ${escapeHtml(emp.cedula)} · ${escapeHtml(emp.cargo || 'Sin cargo')}</div>
            </button>
        `).join('') || '<div class="text-sm text-gray-400 p-3">No se encontró esa cédula.</div>';
            } catch (error) {
                mostrarError(error.message);
            }
        }

        document.getElementById('renBuscar').addEventListener('input', e => {
            const q = e.target.value.replace(/\D/g, '').trim();
            e.target.value = q;
            if (q.length < 3) {
                document.getElementById('renResultados').innerHTML = '';
                return;
            }
            buscarEmpleadoPorCedula(q);
        });

        function seleccionarEmpleado(emp) {
            empleadoRen = emp;
            renovacionesRen = [];
            document.getElementById('renCedula').value = emp.cedula;
            document.getElementById('renBuscar').value = emp.cedula;
            document.getElementById('renResultados').innerHTML = '';
            mostrarError('');
            setAlerta('');

            const sexo = sexoNormalizado(emp.sexo);
            const faltan = camposFaltantes(emp);
            document.getElementById('renEmpleado').classList.remove('hidden');
            document.getElementById('renEmpleado').innerHTML = `
        <div><span class="block text-xs text-gray-400">Nombre</span><strong>${escapeHtml(emp.nombre)}</strong></div>
        <div><span class="block text-xs text-gray-400">Cédula</span><strong>${escapeHtml(emp.cedula)}</strong></div>
        <div><span class="block text-xs text-gray-400">Sexo</span>${sexo === 'F' ? 'Femenino' : sexo === 'M' ? 'Masculino' : 'Sin dato válido'}</div>
        <div><span class="block text-xs text-gray-400">Tipo de personal</span>${escapeHtml(emp.tipo_de_personal || '-')}</div>
        <div><span class="block text-xs text-gray-400">Cargo</span>${escapeHtml(emp.cargo || '-')}</div>
        <div><span class="block text-xs text-gray-400">Contrato</span>${escapeHtml(emp.tipo_de_contrato || '-')}</div>
        <div><span class="block text-xs text-gray-400">Fecha fin contrato inicial</span><strong>${fmtDate(emp.fecha_fin_contrato)}</strong></div>
    `;

            const campos = document.getElementById('renCampos');
            if (faltan.length) {
                campos.classList.add('hidden');
                document.getElementById('renDatosFaltantes').innerHTML = `<strong>No se puede generar la renovación todavía.</strong><div class="mt-2">Faltan en la hoja de vida:</div><ul class="list-disc ml-5 mt-1">${faltan.map(x => `<li>${escapeHtml(x)}</li>`).join('')}</ul>`;
                document.getElementById('renDatosFaltantes').classList.remove('hidden');
                return;
            }

            document.getElementById('renDatosFaltantes').classList.add('hidden');
            document.getElementById('renFinContrato').textContent = fmtDate(emp.fecha_fin_contrato);
            campos.classList.remove('hidden');
            renderHistorial();
            actualizarResumen();
            validarFormulario();
        }

        document.getElementById('formRenovacion').addEventListener('submit', async e => {
            e.preventDefault();
            if (!validarFormulario()) return;

            const err = document.getElementById('renError');
            const btn = document.getElementById('btnGenerarRen');
            const fd = new FormData();
            fd.append('csrf_token', csrfToken);
            fd.append('cedula', empleadoRen.cedula);
            renovacionesRen.forEach((r, index) => fd.append(`duraciones[${index + 1}]`, String(r.meses)));

            btn.disabled = true;
            btn.textContent = 'Generando...';

            try {
                const r = await fetch('./api/formatos_renovacion_generar.php', { method: 'POST', body: fd });
                const data = await r.json();
                if (!data.ok) throw new Error(data.error || 'No se pudo generar el Word.');
                window.location.href = data.url;
                setTimeout(() => location.reload(), 1000);
            } catch (ex) {
                err.textContent = ex.message;
                err.classList.remove('hidden');
                btn.disabled = false;
                btn.textContent = 'Generar Word';
                validarFormulario();
            }
        });

        async function eliminarFormato(archivo) {
            if (!confirm('¿Eliminar este formato generado?')) return;
            const fd = new FormData();
            fd.append('csrf_token', csrfToken);
            const url = './api/formato_archivo.php?f=' + encodeURIComponent(archivo) + '&accion=eliminar';
            try {
                const r = await fetch(url, { method: 'POST', body: fd });
                const d = await r.json();
                if (d.ok) location.reload();
                else alert(d.error || 'No se pudo eliminar.');
            } catch (e) {
                alert('No se pudo eliminar el archivo.');
            }
        }
    </script>
</body>

</html>