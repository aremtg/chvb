// public/assets/js/camera_capture.js
// Captura de foto obligatoria desde la cámara.
//
// 1) Celulares/tablets: cámara nativa con <input type="file" accept="image/*" capture="user">
//    (no requiere HTTPS ni permisos del sitio; es lo que ya funciona en "Evidencia").
// 2) Escritorio con HTTPS/localhost: cámara en vivo (getUserMedia), con botón de respaldo.
//
// API pública (sin cambios): inicializarCapturaFoto(id) -> { tieneFoto(), obtenerDataURL() }

function inicializarCapturaFoto(contenedorId) {
    const contenedor = document.getElementById(contenedorId);
    let dataUrlCapturada = null;
    let stream = null;
    let mensajeError = '';

    const MAX_LADO = 1280;      // px, lado mayor de la foto final
    const CALIDAD_JPEG = 0.85;

    // Celulares y tablets (incluye iPadOS, que se identifica como Mac con pantalla táctil):
    // siempre se usa la cámara nativa, igual que el botón de "Evidencia". Es más confiable
    // (no depende de HTTPS, ni de permisos del sitio, ni del navegador embebido de apps).
    const esMovil =
        /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent) ||
        (/Mac/i.test(navigator.platform || '') && navigator.maxTouchPoints > 1);

    // Cámara en vivo: solo escritorio con contexto seguro (HTTPS o localhost).
    const enVivoDisponible =
        !esMovil &&
        window.isSecureContext === true &&
        !!(navigator.mediaDevices && typeof navigator.mediaDevices.getUserMedia === 'function');

    function render() {
        if (dataUrlCapturada) {
            contenedor.innerHTML = `
                <div class="border border-gray-300 rounded-xl overflow-hidden">
                    <img src="${dataUrlCapturada}" class="w-full max-h-64 object-contain bg-black">
                </div>
                <button type="button" id="btnRepetirFoto" class="text-xs text-red-600 hover:underline mt-1">Tomar otra foto</button>
            `;
            document.getElementById('btnRepetirFoto').addEventListener('click', () => {
                dataUrlCapturada = null;
                mensajeError = '';
                render();
            });
        } else {
            contenedor.innerHTML = `
                <button type="button" id="btnAbrirCamara" class="w-full border-2 border-dashed border-gray-300 rounded-xl py-6 text-sm text-gray-500 hover:border-red-400 hover:text-red-600 transition">
                    📷 Toca para tomar la foto con la cámara
                </button>
                <p id="errorFoto" class="${mensajeError ? '' : 'hidden'} text-xs text-red-600 mt-1"></p>
            `;
            if (mensajeError) document.getElementById('errorFoto').textContent = mensajeError;
            document.getElementById('btnAbrirCamara').addEventListener('click', () => {
                mensajeError = '';
                if (enVivoDisponible) abrirModalCamara();
                else abrirCamaraNativa();
            });
        }
    }

    // ---------- Cámara nativa del teléfono (no requiere HTTPS) ----------

    function abrirCamaraNativa() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.setAttribute('capture', 'user');
        input.style.display = 'none';
        document.body.appendChild(input);

        const limpiar = () => input.remove();

        input.addEventListener('change', async () => {
            const archivo = input.files && input.files[0];
            limpiar();
            if (!archivo) return;

            if (!archivo.type || !archivo.type.startsWith('image/')) {
                mensajeError = 'El archivo seleccionado no es una imagen.';
                render();
                return;
            }

            try {
                dataUrlCapturada = await archivoADataUrl(archivo);
                mensajeError = '';
            } catch (e) {
                mensajeError = 'No se pudo procesar la foto. Intenta de nuevo.';
            }
            render();
        });
        input.addEventListener('cancel', limpiar);

        input.click();
    }

    // Redimensiona y comprime para no enviar fotos de 8-12 MB desde el celular.
    function archivoADataUrl(archivo) {
        return new Promise((resolve, reject) => {
            const url = URL.createObjectURL(archivo);
            const img = new Image();
            img.onload = () => {
                try {
                    const escala = Math.min(1, MAX_LADO / Math.max(img.naturalWidth, img.naturalHeight));
                    const canvas = document.createElement('canvas');
                    canvas.width = Math.round(img.naturalWidth * escala);
                    canvas.height = Math.round(img.naturalHeight * escala);
                    canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
                    resolve(canvas.toDataURL('image/jpeg', CALIDAD_JPEG));
                } catch (e) {
                    reject(e);
                } finally {
                    URL.revokeObjectURL(url);
                }
            };
            img.onerror = () => {
                URL.revokeObjectURL(url);
                reject(new Error('imagen inválida'));
            };
            img.src = url;
        });
    }

    // ---------- Cámara en vivo (solo HTTPS / localhost) ----------

    function mensajePorError(err) {
        switch (err && err.name) {
            case 'NotAllowedError':
            case 'SecurityError':
                return 'El permiso de cámara está bloqueado. Actívalo en los ajustes del navegador para este sitio o usa la cámara del teléfono.';
            case 'NotFoundError':
            case 'OverconstrainedError':
                return 'No se encontró una cámara disponible en este dispositivo.';
            case 'NotReadableError':
            case 'AbortError':
                return 'La cámara está siendo usada por otra aplicación. Ciérrala e inténtalo de nuevo.';
            default:
                return 'No se pudo acceder a la cámara en vivo.';
        }
    }

    async function abrirModalCamara() {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-[90]';
        modal.innerHTML = `
            <div class="bg-white rounded-xl overflow-hidden w-full max-w-sm">
                <video id="videoCamara" autoplay playsinline muted class="w-full bg-black" style="max-height:60vh"></video>
                <canvas id="canvasCaptura" class="hidden"></canvas>
                <div class="p-3 flex gap-2">
                    <button type="button" id="btnCancelarCamara" class="flex-1 border border-gray-300 rounded-xl py-2 text-gray-700 text-sm">Cancelar</button>
                    <button type="button" id="btnCapturar" class="flex-1 bg-red-600 hover:bg-red-700 text-white rounded-xl py-2 text-sm">Capturar</button>
                </div>
                <div id="bloqueError" class="hidden px-3 pb-3">
                    <p id="errorCamara" class="text-xs text-red-600 mb-2"></p>
                    <button type="button" id="btnCamaraNativa" class="w-full border border-red-300 text-red-600 rounded-xl py-2 text-sm">Usar la cámara del teléfono</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        const video = modal.querySelector('#videoCamara');
        const bloqueError = modal.querySelector('#bloqueError');
        const errorCamara = modal.querySelector('#errorCamara');
        const btnCapturar = modal.querySelector('#btnCapturar');

        function cerrar() {
            if (stream) stream.getTracks().forEach(t => t.stop());
            stream = null;
            modal.remove();
        }

        modal.querySelector('#btnCancelarCamara').addEventListener('click', cerrar);
        modal.querySelector('#btnCamaraNativa').addEventListener('click', () => {
            cerrar();
            abrirCamaraNativa();
        });

        try {
            // 'ideal' evita OverconstrainedError en equipos sin cámara frontal.
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'user' } },
                audio: false,
            });
            video.srcObject = stream;
        } catch (err) {
            console.error('getUserMedia:', err);
            errorCamara.textContent = mensajePorError(err);
            bloqueError.classList.remove('hidden');
            btnCapturar.disabled = true;
            btnCapturar.classList.add('opacity-50');
        }

        btnCapturar.addEventListener('click', () => {
            if (!stream || !video.videoWidth) return;
            const canvas = modal.querySelector('#canvasCaptura');
            const escala = Math.min(1, MAX_LADO / Math.max(video.videoWidth, video.videoHeight));
            canvas.width = Math.round(video.videoWidth * escala);
            canvas.height = Math.round(video.videoHeight * escala);
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            dataUrlCapturada = canvas.toDataURL('image/jpeg', CALIDAD_JPEG);
            cerrar();
            render();
        });
    }

    render();

    return {
        tieneFoto: () => dataUrlCapturada !== null,
        obtenerDataURL: () => dataUrlCapturada,
    };
}