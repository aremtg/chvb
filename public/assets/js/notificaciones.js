async function eliminarNotificacion(id) {
    const formData = new FormData();
    formData.append('id', id);

    const res = await fetch('/chvb/public/api/notificaciones_eliminar.php', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.ok) {
        document.getElementById(`notif-${id}`).remove();
    }
}

async function eliminarTodasNotificaciones() {
    if (!confirm('¿Eliminar todas las notificaciones? Esta acción no se puede deshacer.')) return;

    const res = await fetch('/chvb/public/api/notificaciones_eliminar_todas.php', { method: 'POST' });
    const data = await res.json();

    if (data.ok) {
        window.location.reload();
    }
}