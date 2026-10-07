document.getElementById('formLlegada').addEventListener('submit', async (e) => {
    e.preventDefault();
    const error = document.getElementById('errorLlegada');
    error.classList.add('hidden');

    const formData = new FormData();
    formData.append('csrf_token', document.getElementById('csrfTokenLlegada').value);
    formData.append('id', document.getElementById('permisoIdLlegada').value);
    formData.append('version', document.getElementById('versionLlegada').value);
    formData.append('fecha_fin', document.getElementById('fechaLlegada').value);
    formData.append('hora_fin', document.getElementById('horaLlegada').value);
    const evidencia = document.getElementById('evidenciaLlegada').files[0];
    if (evidencia) formData.append('evidencia', evidencia);

    const btn = Loading.submitter(e);
    Loading.start(btn, 'Registrando llegada...');
    try {
        const res = await fetch('./api/permisos_registrar_llegada.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.ok) {
            window.location.href = './permisos.php';
            return; // el spinner queda hasta que cargue la otra página
        }
        error.textContent = data.error || 'Error al registrar la llegada.';
        error.classList.remove('hidden');
    } catch (err) {
        error.textContent = 'Error de conexión con el servidor.';
        error.classList.remove('hidden');
    }
    Loading.stop(btn);
});