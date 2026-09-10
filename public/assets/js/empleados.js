// public/assets/js/empleados.js

function toggleMenu(cedula) {
    document.querySelectorAll('[id^="menu-"]').forEach(m => {
        if (m.id !== `menu-${cedula}`) m.classList.add('hidden');
    });
    document.getElementById(`menu-${cedula}`).classList.toggle('hidden');
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('[id^="menu-"]') && !e.target.closest('button[onclick^="toggleMenu"]')) {
        document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
    }
});

// --- Validación en vivo de cédula ---
const inputCedula = document.getElementById('inputCedula');
const errorCedula = document.getElementById('errorCedula');
const regexCedula = /^[A-Za-z0-9]{5,10}$/;

inputCedula.addEventListener('input', () => {
    const valor = inputCedula.value;
    if (valor.length > 0 && !regexCedula.test(valor)) {
        errorCedula.classList.remove('hidden');
    } else {
        errorCedula.classList.add('hidden');
    }
});

// --- Envío del formulario de creación ---
const formCrear = document.getElementById('formCrear');
const erroresCrear = document.getElementById('erroresCrear');

formCrear.addEventListener('submit', async (e) => {
    e.preventDefault();
    erroresCrear.classList.add('hidden');
    erroresCrear.innerHTML = '';

    const cedula = inputCedula.value;
    if (!regexCedula.test(cedula)) {
        erroresCrear.innerHTML = 'La cédula no puede tener más de 10 caracteres y puede ser extranjera (letras y números permitidos).';
        erroresCrear.classList.remove('hidden');
        return;
    }

    const formData = new FormData(formCrear);

    try {
        const res = await fetch('/chvb/public/api/empleados_crear.php', {
            method: 'POST',
            body: formData,
        });
        const data = await res.json();

        if (data.ok) {
            window.location.reload();
        } else {
            erroresCrear.innerHTML = data.errores.join('<br>');
            erroresCrear.classList.remove('hidden');
        }
    } catch (err) {
        erroresCrear.innerHTML = 'Error de conexión con el servidor.';
        erroresCrear.classList.remove('hidden');
    }
});

// --- Modal de eliminación ---
function abrirModalEliminar(cedula, nombre) {
    document.getElementById('cedulaEliminar').value = cedula;
    document.getElementById('nombreEliminar').textContent = nombre;
    document.getElementById('errorEliminar').classList.add('hidden');
    document.getElementById('modalEliminar').classList.remove('hidden');
    document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
}

const formEliminar = document.getElementById('formEliminar');
const errorEliminar = document.getElementById('errorEliminar');

formEliminar.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorEliminar.classList.add('hidden');

    const formData = new FormData(formEliminar);

    try {
        const res = await fetch('/chvb/public/api/empleados_eliminar.php', {
            method: 'POST',
            body: formData,
        });
        const data = await res.json();

        if (data.ok) {
            window.location.reload();
        } else {
            errorEliminar.textContent = data.error;
            errorEliminar.classList.remove('hidden');
        }
    } catch (err) {
        errorEliminar.textContent = 'Error de conexión con el servidor.';
        errorEliminar.classList.remove('hidden');
    }
});

// --- Ver Empleado ---
async function abrirModalVer(cedula) {
    document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
    const res = await fetch(`/chvb/public/api/empleados_obtener.php?cedula=${encodeURIComponent(cedula)}`);
    const data = await res.json();

    if (!data.ok) {
        alert(data.error || 'Error al cargar el empleado.');
        return;
    }

    const emp = data.empleado;
    document.getElementById('contenidoVer').innerHTML = `
        <div class="flex justify-center mb-3">
            ${emp.foto
                ? `<img src="/chvb/public/api/foto_ver.php?cedula=${encodeURIComponent(emp.cedula)}" class="w-24 h-24 rounded-full object-cover border border-gray-200">`
                : `<span class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-4xl">👤</span>`
            }
        </div>
        <p><strong>Nombre:</strong> ${emp.nombre}</p>
        <p><strong>Cédula:</strong> ${emp.cedula}</p>
        <p><strong>Cargo:</strong> ${emp.es_bombero_integral == 1 ? `Bombero integral con funciones de ${emp.cargo}` : emp.cargo}</p>
        <p><strong>Tipo de contrato:</strong> ${emp.tipo_de_contrato}</p>
        <p><strong>Estado:</strong> ${emp.estado}</p>
        <p><strong>Celular:</strong> ${emp.celular || '-'}</p>
        <p><strong>Correo:</strong> ${emp.correo || '-'}</p>
        <p><strong>Fecha de nacimiento:</strong> ${emp.fecha_nacimiento || '-'}</p>
    `;
    document.getElementById('modalVer').classList.remove('hidden');
}

// --- Editar Empleado ---
async function abrirModalEditar(cedula) {
    document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
    const res = await fetch(`/chvb/public/api/empleados_obtener.php?cedula=${encodeURIComponent(cedula)}`);
    const data = await res.json();

    if (!data.ok) {
        alert(data.error || 'Error al cargar el empleado.');
        return;
    }

    const emp = data.empleado;
    document.getElementById('editCedulaActual').value = emp.cedula;
    document.getElementById('editNombre').value = emp.nombre;
    document.getElementById('editCedula').value = emp.cedula;
    document.getElementById('editCargo').value = emp.cargo;
    document.getElementById('editBomberoIntegral').checked = emp.es_bombero_integral == 1;
    document.getElementById('editContrato').value = emp.tipo_de_contrato;
    document.getElementById('editEstado').value = emp.estado;
    document.getElementById('editCelular').value = emp.celular || '';
    document.getElementById('editCorreo').value = emp.correo || '';
    document.getElementById('editFechaNacimiento').value = emp.fecha_nacimiento || '';

    const editFoto = document.getElementById('editFotoActual');
    const editFotoPlaceholder = document.getElementById('editFotoPlaceholder');
    if (emp.foto) {
        editFoto.src = `/chvb/public/api/foto_ver.php?cedula=${encodeURIComponent(emp.cedula)}`;
        editFoto.classList.remove('hidden');
        editFotoPlaceholder.classList.add('hidden');
    } else {
        editFoto.classList.add('hidden');
        editFotoPlaceholder.classList.remove('hidden');
    }

    document.getElementById('erroresEditar').classList.add('hidden');
    document.getElementById('modalEditar').classList.remove('hidden');
}

const formEditar = document.getElementById('formEditar');
document.getElementById('formEditar').addEventListener('submit', async (e) => {
    e.preventDefault();
    const erroresEditar = document.getElementById('erroresEditar');
    erroresEditar.classList.add('hidden');

    const formData = new FormData(formEditar);

    try {
        const res = await fetch('/chvb/public/api/empleados_actualizar.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.ok) {
            window.location.reload();
        } else {
            erroresEditar.innerHTML = data.errores.join('<br>');
            erroresEditar.classList.remove('hidden');
        }
    } catch (err) {
        erroresEditar.innerHTML = 'Error de conexión con el servidor.';
        erroresEditar.classList.remove('hidden');
    }
});