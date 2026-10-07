// public/assets/js/firma_canvas.js

function inicializarCanvasFirma(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;

    const ctx = canvas.getContext('2d');
    let dibujando = false;
    let huboTrazo = false;

    // Alto cómodo para firmar (se puede cambiar con data-alto="220" en el <canvas>).
    const altoCss = parseInt(canvas.dataset.alto, 10) || 180;
    canvas.style.width = '100%';
    canvas.style.height = altoCss + 'px';
    canvas.style.display = 'block';

    let ultimoAncho = 0;
    let ultimoAlto = 0;

    function ajustarTamano() {
        const ratio = window.devicePixelRatio || 1;
        const rect = canvas.getBoundingClientRect();
        // Si está oculto (modal cerrado, pestaña oculta) no hay tamaño real: se omite.
        if (rect.width === 0 || rect.height === 0) return;
        // Si el tamaño no cambió, no se toca (redimensionar borra el trazo).
        if (rect.width === ultimoAncho && rect.height === ultimoAlto) return;
        ultimoAncho = rect.width;
        ultimoAlto = rect.height;
        canvas.width = rect.width * ratio;
        canvas.height = rect.height * ratio;
        ctx.scale(ratio, ratio);
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#1f2937';
        huboTrazo = false;
    }
    ajustarTamano();
    window.addEventListener('resize', ajustarTamano);
    // Cuando el canvas pasa de oculto a visible, se ajusta solo.
    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(ajustarTamano).observe(canvas);
    }

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
            ctx.save();
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.restore();
            huboTrazo = false;
        },
        estaVacio() {
            return !huboTrazo;
        },
        obtenerDataURL() {
            return canvas.toDataURL('image/png');
        },
        redimensionar() {
            ultimoAncho = 0;
            ajustarTamano();
        },
    };
}