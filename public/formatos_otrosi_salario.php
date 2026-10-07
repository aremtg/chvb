<?php
declare(strict_types=1);
date_default_timezone_set('America/Bogota');
require_once __DIR__ . '/../includes/formatos_guard.php';
require_once __DIR__ . '/../includes/formatos_ui.php';
require_once __DIR__ . '/../src/models/FormatoModel.php';
requireFormatosAccess();


$generados = FormatoModel::listarOtrosiSalarioGenerados(__DIR__ . '/../uploads/generados');
$csrf = csrfToken();
$hoy = new DateTimeImmutable('today', new DateTimeZone('America/Bogota'));
$hoyIso = $hoy->format('Y-m-d');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Otrosí Cambio de Salario</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <style>
        .salario-card { overflow: visible !important; }
        .salario-resultado { border: 1px solid #f3f4f6; background: #f9fafb; }
        .salario-empleado-option { border: 1px solid #e5e7eb; background: #fff; }
        .salario-empleado-option:hover { border-color: #fecaca; background: #fffafa; }
        .salario-generado { display:flex; flex-wrap:wrap; gap:10px; align-items:center; }
        .salario-generado-contenido { min-width:0; flex:1 1 240px; }
        .salario-generado-acciones { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:8px; width:100%; }
        .salario-generado-acciones .btn-eliminar { grid-column:1/-1; }
        @media(max-width:380px){ .salario-generado-acciones{grid-template-columns:1fr}.salario-generado-acciones .btn-eliminar{grid-column:auto} }
    </style>
</head>
<body class="bg-gray-50 min-h-screen text-gray-800">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<div class="md:ml-64 pt-14 md:pt-0">
    <?= formatosHeader('Otrosí cambio de salario', 'Cambio de remuneración al contrato individual de trabajo · GH-FT-25') ?>

    <main class="p-3 sm:p-5 lg:p-6 max-w-5xl mx-auto space-y-4 sm:space-y-5">
        <section class="salario-card bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-100">
                <div class="flex items-start gap-3">
                    <span class="w-10 h-10 shrink-0 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                        <?= icon('file-signature','w-5 h-5') ?>
                    </span>
                    <div>
                        <h2 class="font-bold text-gray-800">Generar Otrosí cambio de salario</h2>
                        <p class="text-xs text-gray-600">Busca al empleado por nombre o cédula. Los datos contractuales se cargan automáticamente.</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-5 lg:p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar empleado</label>
                    <input id="salarioBuscar" type="text" autocomplete="off" placeholder="Nombre o cédula..."
                            class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition">
                    <div id="salarioResultados" class="mt-2 space-y-1"></div>
                    <p class="text-[11px] text-gray-600 mt-1.5">Puedes buscar por nombre completo, parte del nombre o número de cédula.</p>
                </div>

                <div id="salarioError" class="hidden rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm p-3"></div>
                

                <div id="salarioEmpleado" class="hidden salario-resultado rounded-xl p-4 sm:p-5 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                        <div><span class="block text-xs text-gray-600">Nombre</span><strong id="salarioNombre" class="block text-gray-800 uppercase"></strong></div>
                        <div><span class="block text-xs text-gray-600">Cédula</span><strong id="salarioCedula" class="block text-gray-800"></strong></div>
                        <div><span class="block text-xs text-gray-600">Inicio de contrato</span><span id="salarioInicioContrato" class="block text-gray-700"></span></div>
                        <div><span class="block text-xs text-gray-600">Fecha actual</span><span id="salarioFechaActual" class="block text-gray-700"></span></div>
                    </div>

                    <div class="border-t border-gray-200 pt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">REMUNERACIÓN. A partir del día</label>
                            <input id="salarioFechaRemuneracion" type="date" value="<?= htmlspecialchars($hoyIso) ?>"
                                class="w-full h-10 border border-gray-200 bg-white rounded-lg px-3.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition">
                            <p id="salarioFechaRemuneracionTexto" class="text-[11px] text-gray-600 mt-1.5"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">La suma de</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-600 text-sm">$</span>
                                <input id="salarioValor" type="text" inputmode="numeric" autocomplete="off" placeholder="Ej. 2.500.000"
                                    class="w-full h-10 border border-gray-200 bg-white rounded-lg pl-7 pr-3.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-300 transition">
                            </div>
                            <p id="salarioValorTexto" class="text-[11px] text-gray-600 mt-1.5"></p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <p class="text-xs text-gray-600 mb-2">Vista previa</p>
                        <p class="text-sm text-gray-700 leading-6"><strong>REMUNERACIÓN.</strong> A partir del día <span id="previewFecha" class="font-medium"></span>, la suma de <span id="previewSalario" class="font-medium"></span>.</p>
                    </div>
                </div>
                <?= formatosBarraAcciones('btnGenerarSalario', 'limpiarSalario()', 'button', 'generarOtrosiSalario()') ?>
            </div>
        </section>

        <section class="salario-card bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">Generados</h2>
                <p class="text-xs text-gray-600">Archivos generados recientemente</p>
            </div>
            <div class="p-4 sm:p-5 lg:p-6">
                <?php if ($generados): ?>
                    <div class="space-y-2">
                        <?php foreach ($generados as $g): ?>
                            <div class="salario-generado border border-gray-100 rounded-xl p-3 hover:bg-gray-50/70 transition">
                                <div class="salario-generado-contenido">
                                    <p class="text-sm font-medium text-gray-700 break-words" title="<?= htmlspecialchars($g['archivo']) ?>"><?= htmlspecialchars($g['archivo']) ?></p>
                                    <p class="text-xs text-gray-600"><?= date('d/m/Y H:i', $g['fecha']) ?></p>
                                </div>
                                <div class="salario-generado-acciones">
                                    <a href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=ver" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-lg transition"><?= icon('eye','w-4 h-4') ?> Ver</a>
                                    <a href="./api/formato_archivo.php?f=<?= rawurlencode($g['archivo']) ?>&accion=descargar" class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-lg transition"><?= icon('download','w-4 h-4') ?> Descargar</a>
                                    <button type="button" onclick='eliminarSalario(<?= json_encode($g['archivo'], JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT) ?>)' class="btn-eliminar inline-flex items-center gap-2 px-3 py-2 text-sm text-red-600 border border-red-200 hover:bg-red-50 rounded-lg transition"><?= icon('trash-2','w-4 h-4') ?> Eliminar</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-xs text-gray-600 text-center py-8">Todavía no hay Otrosí de cambio de salario generados.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>

<script src="./assets/js/empleado_buscador.js"></script>
<script>
const csrfToken = <?= json_encode($csrf) ?>;
let empleadoSalario = null;

function mostrarErrorSalario(mensaje) {
    const box = document.getElementById('salarioError');
    box.textContent = mensaje || '';
    box.classList.toggle('hidden', !mensaje);
}

function escaparHtml(s) {
    return String(s ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c]));
}

function formatoCedula(cedula) {
    const n = String(cedula ?? '').replace(/\D/g, '');
    return n ? Number(n).toLocaleString('es-CO') : '';
}

function fechaLarga(fecha) {
    if (!fecha) return 'No registrada';
    const [y,m,d] = fecha.split('-').map(Number);
    const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    return `${String(d).padStart(2,'0')} de ${meses[m-1]} de ${y}`;
}

function fechaLegal(fecha) {
    if (!fecha) return '';
    const [y,m,d] = fecha.split('-').map(Number);
    const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    const dia = d === 1 ? 'primer' : numeroGrupo(d);
   
    return `${dia} (${String(d).padStart(2,'0')}) del mes de ${meses[m-1]} de ${y}`;
}

function numeroGrupo(n) {
    const u=['cero','uno','dos','tres','cuatro','cinco','seis','siete','ocho','nueve','diez','once','doce','trece','catorce','quince','dieciséis','diecisiete','dieciocho','diecinueve','veinte','veintiuno','veintidós','veintitrés','veinticuatro','veinticinco','veintiséis','veintisiete','veintiocho','veintinueve'];
    const d={30:'treinta',40:'cuarenta',50:'cincuenta',60:'sesenta',70:'setenta',80:'ochenta',90:'noventa'};
    const c={100:'cien',200:'doscientos',300:'trescientos',400:'cuatrocientos',500:'quinientos',600:'seiscientos',700:'setecientos',800:'ochocientos',900:'novecientos'};
    if(n<30)return u[n]; if(n<100)return d[Math.floor(n/10)*10]+(n%10?' y '+u[n%10]:'');
    const centena=Math.floor(n/100)*100,r=n%100;
    if(n===100)return 'cien';
    // Del 101 al 199 se usa "ciento": ciento setenta y siete, etc.
    const base=centena===100?'ciento':c[centena];
    return base+(r?' '+numeroGrupo(r):'');
}

function numeroEnPalabras(n) {
    n = Number(n);
    if (!n) return 'cero';
    const grupo = x => numeroGrupo(x).replace(/\buno$/u,'un').replace(/\bveintiuno$/u,'veintiún');
    const partes=[];
    const millones=Math.floor(n/1000000); let resto=n%1000000;
    if(millones) partes.push(millones===1?'un millón':grupo(millones)+' millones');
    const miles=Math.floor(resto/1000); resto%=1000;
    if(miles) partes.push(miles===1?'mil':grupo(miles)+' mil');
    if(resto) partes.push(numeroGrupo(resto));
    return partes.join(' ');
}

function actualizarPreview() {
    const fecha = document.getElementById('salarioFechaRemuneracion').value;
    const valor = document.getElementById('salarioValor').value.replace(/\D/g,'');
    const n = valor ? Number(valor) : 0;
    const cop = n ? '$' + n.toLocaleString('es-CO') : '';
    const texto = n ? numeroEnPalabras(n).toUpperCase() + ' PESOS M/CTE' : '';
    document.getElementById('salarioFechaRemuneracionTexto').textContent = fecha ? fechaLegal(fecha) : '';
    document.getElementById('salarioValorTexto').textContent = texto ? texto + ' (' + cop + ')' : '';
    document.getElementById('previewFecha').textContent = fechaLegal(fecha);
    document.getElementById('previewSalario').textContent = texto ? texto + ' (' + cop + ')' : '';
}

function seleccionarEmpleadoSalario(emp) {
    empleadoSalario = emp;
    mostrarErrorSalario('');
    document.getElementById('salarioNombre').textContent = String(emp.nombre || '').toUpperCase();
    document.getElementById('salarioCedula').textContent = formatoCedula(emp.cedula);
    document.getElementById('salarioInicioContrato').textContent = fechaLarga(emp.fecha_inicio_contrato);
    document.getElementById('salarioFechaActual').textContent = fechaLegal(<?= json_encode($hoyIso) ?>);
    document.getElementById('salarioEmpleado').classList.remove('hidden');
    formatosSetBoton(document.getElementById('btnGenerarSalario'), true);
    actualizarPreview();
}

function limpiarSeleccionSalario() {
    empleadoSalario = null;
    document.getElementById('salarioEmpleado').classList.add('hidden');
    formatosSetBoton(document.getElementById('btnGenerarSalario'), false);
    mostrarErrorSalario('');
}

const buscadorSalario = crearBuscadorEmpleado({
    input: document.getElementById('salarioBuscar'),
    lista: document.getElementById('salarioResultados'),
    onSelect: seleccionarEmpleadoSalario,
    onClear: limpiarSeleccionSalario,
    onError: mostrarErrorSalario
});

document.getElementById('salarioFechaRemuneracion').addEventListener('change', actualizarPreview);
document.getElementById('salarioValor').addEventListener('input', e => { const raw=e.target.value.replace(/\D/g,''); e.target.value=raw ? Number(raw).toLocaleString('es-CO') : ''; actualizarPreview(); });

async function generarOtrosiSalario() {
    if (!empleadoSalario) { mostrarErrorSalario('Primero busca y selecciona un empleado.'); return; }
    const fecha = document.getElementById('salarioFechaRemuneracion').value;
    const salario = document.getElementById('salarioValor').value.replace(/\D/g,'');
    if (!fecha) { mostrarErrorSalario('Selecciona la fecha a partir de la cual aplica la nueva remuneración.'); return; }
    if (!salario || Number(salario) < 1) { mostrarErrorSalario('Escribe un valor de salario válido.'); return; }
    mostrarErrorSalario('');
    const btn=document.getElementById('btnGenerarSalario'); const fd=new FormData();
    fd.append('csrf_token',csrfToken); fd.append('cedula',empleadoSalario.cedula); fd.append('fecha_remuneracion',fecha); fd.append('salario',salario);
    Loading.start(btn, 'Generando Word...');
    try {
        const r=await fetch('./api/formatos_otrosi_salario_generar.php',{method:'POST',body:fd});
        const data=await r.json();
        if(!data.ok) throw new Error(data.error || 'No se pudo generar el Word.');
        window.location.href=data.url;
        setTimeout(()=>location.reload(),1000);
    } catch(e) { mostrarErrorSalario(e.message || 'No se pudo generar el Word.'); Loading.stop(btn); formatosSetBoton(btn,true); }
}

function limpiarSalario() {
    buscadorSalario.reset();
    document.getElementById('salarioValor').value = '';
    document.getElementById('salarioFechaRemuneracion').value = <?= json_encode($hoyIso) ?>;
    actualizarPreview();
}

async function eliminarSalario(archivo) {
    if (!confirm('¿Eliminar este Otrosí de cambio de salario generado?')) return;
    const fd=new FormData(); fd.append('csrf_token',csrfToken);
    Loading.show('Eliminando...');
    try { const r=await fetch('./api/formato_archivo.php?f='+encodeURIComponent(archivo)+'&accion=eliminar',{method:'POST',body:fd}); const d=await r.json(); if(d.ok) location.reload(); else { Loading.hide(); alert(d.error||'No se pudo eliminar.'); } } catch(e) { Loading.hide(); alert('No se pudo eliminar el archivo.'); }
}
actualizarPreview();
</script>
</body>
</html>
