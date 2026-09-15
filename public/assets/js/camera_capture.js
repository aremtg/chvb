// public/assets/js/camera_capture.js
// Captura de foto SOLO por cámara en vivo (getUserMedia), nunca desde archivo/galería.

function inicializarCapturaFoto(contenedorId) {
    const contenedor = document.getElementById(contenedorId);
    let dataUrlCapturada = null;
    let stream = null;

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
                render();
            });
        } else {
            contenedor.innerHTML = `
                <button type="button" id="btnAbrirCamara" class="w-full border-2 border-dashed border-gray-300 rounded-xl py-6 text-sm text-gray-500 hover:border-red-400 hover:text-red-600 transition">
                    📷 Toca para tomar la foto con la cámara
                </button>
            `;
            document.getElementById('btnAbrirCamara').addEventListener('click', abrirModalCamara);
        }
    }

    async function abrirModalCamara() {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-[90]';
        modal.innerHTML = `
            <div class="bg-white rounded-xl overflow-hidden w-full max-w-sm">
                <video id="videoCamara" autoplay playsinline class="w-full bg-black" style="max-height:60vh"></video>
                <canvas id="canvasCaptura" class="hidden"></canvas>
                <div class="p-3 flex gap-2">
                    <button type="button" id="btnCancelarCamara" class="flex-1 border border-gray-300 rounded-xl py-2 text-gray-700 text-sm">Cancelar</button>
                    <button type="button" id="btnCapturar" class="flex-1 bg-red-600 hover:bg-red-700 text-white rounded-xl py-2 text-sm">Capturar</button>
                </div>
                <p id="errorCamara" class="hidden text-xs text-red-600 px-3 pb-3"></p>
            </div>
        `;
        document.body.appendChild(modal);

        const video = modal.querySelector('#videoCamara');
        const errorCamara = modal.querySelector('#errorCamara');

        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            video.srcObject = stream;
        } catch (err) {
            errorCamara.textContent = 'No se pudo acceder a la cámara. Debes permitir el acceso para poder continuar.';
            errorCamara.classList.remove('hidden');
        }

        function cerrar() {
            if (stream) stream.getTracks().forEach(t => t.stop());
            modal.remove();
        }

        modal.querySelector('#btnCancelarCamara').addEventListener('click', cerrar);

        modal.querySelector('#btnCapturar').addEventListener('click', () => {
            if (!stream) return;
            const canvas = modal.querySelector('#canvasCaptura');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            dataUrlCapturada = canvas.toDataURL('image/jpeg', 0.85);
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