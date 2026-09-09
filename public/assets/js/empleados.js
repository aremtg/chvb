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