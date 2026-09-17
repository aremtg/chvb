// public/assets/js/firma_canvas.js

function inicializarCanvasFirma(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;

    const ctx = canvas.getContext('2d');
    let dibujando = false;
    let huboTrazo = false;

    function ajustarTamano() {
        const ratio = window.devicePixelRatio || 1;
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * ratio;
        canvas.height = rect.height * ratio;
        ctx.scale(ratio, ratio);
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#1f2937';
    }
    ajustarTamano();
    window.addEventListener('resize', ajustarTamano);

    function obtenerPos(e) {
        const rect = canvas.getBoundingClientRect();
        const punto = e.touches ? e.touches[0] : e;
        return { x: punto.clientX - rect.left, y: punto.clientY - rect.top };
    }

    function iniciar(e) {
        e.preventDefault();
        dibujando = true;
        huboTrazo = true;
        const pos = obtenerPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
    }

    function mover(e) {
        if (!dibujando) return;
        e.preventDefault();
        const pos = obtenerPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    }

    function terminar() {
        dibujando = false;
    }

    canvas.addEventListener('mousedown', iniciar);
    canvas.addEventListener('mousemove', mover);
    canvas.addEventListener('mouseup', terminar);
    canvas.addEventListener('mouseleave', terminar);
    canvas.addEventListener('touchstart', iniciar, { passive: false });
    canvas.addEventListener('touchmove', mover, { passive: false });
    canvas.addEventListener('touchend', terminar);

    return {
        limpiar() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            huboTrazo = false;
        },
        estaVacio() {
            return !huboTrazo;
        },
        obtenerDataURL() {
            return canvas.toDataURL('image/png');
        },
        redimensionar() {
            ajustarTamano();
        },
    };
}