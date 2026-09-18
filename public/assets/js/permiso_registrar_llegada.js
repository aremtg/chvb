document.getElementById('formLlegada').addEventListener('submit', async (e) => {
    e.preventDefault();
    const error = document.getElementById('errorLlegada');
    error.classList.add('hidden');

    const formData = new FormData();
    formData.append('id', document.getElementById('permisoIdLlegada').value);
    formData.append('version', document.getElementById('versionLlegada').value);
    formData.append('fecha_fin', document.getElementById('fechaLlegada').value);
    formData.append('hora_fin', document.getElementById('horaLlegada').value);
    formData.append('evidencia', document.getElementById('evidenciaLlegada').files[0]);

    const res = await fetch('./api/permisos_registrar_llegada.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.ok) {
        window.location.href = './permisos.php';
    } else {
        error.textContent = data.error || 'Error al registrar la llegada.';
        error.classList.remove('hidden');
    }
});