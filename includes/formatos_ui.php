<?php
// includes/formatos_ui.php
// Piezas de interfaz compartidas por TODAS las vistas de formatos.
// Si algo cambia aquí (orden de botones, textos, estilos), cambia en todos a la vez.
require_once __DIR__ . '/icon.php';

/** Encabezado estándar: flecha para volver + título + código del formato. */
function formatosHeader(string $titulo, string $subtitulo): string
{
    return '<header class="bg-white border-b border-gray-100 px-4 sm:px-6 py-4 sticky top-0 z-30">'
        . '<div class="flex items-center gap-3">'
        . '<a href="./formatos.php" title="Volver a Formatos" class="w-8 h-8 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition">'
        . icon('chevron-left', 'w-5 h-5') . '</a>'
        . '<div><h1 class="text-base font-bold text-gray-800">' . htmlspecialchars($titulo) . '</h1>'
        . '<p class="text-xs text-gray-600">' . htmlspecialchars($subtitulo) . '</p></div>'
        . '</div></header>';
}

/**
 * Barra de acciones estándar, siempre en el mismo orden:
 *   [Volver a Formatos]  [Limpiar]  [Generar Word]
 * $tipoGenerar = 'submit' (dentro de un <form>) o 'button' (con $onGenerar).
 * "Generar Word" nace deshabilitado; el JS lo habilita con formatosSetBoton().
 */
function formatosBarraAcciones(string $idGenerar, string $onLimpiar, string $tipoGenerar = 'button', string $onGenerar = ''): string
{
    $base = 'w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm transition';
    $onclickGenerar = $tipoGenerar === 'button' && $onGenerar !== '' ? ' onclick="' . htmlspecialchars($onGenerar) . '"' : '';

    return '<div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-1">'
        . '<a href="./formatos.php" class="' . $base . ' bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 font-medium">'
        . icon('chevron-left', 'w-4 h-4') . ' Volver a Formatos</a>'
        . '<button type="button" onclick="' . htmlspecialchars($onLimpiar) . '" class="' . $base . ' bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 font-medium">'
        . icon('x', 'w-4 h-4') . ' Limpiar</button>'
        . '<button id="' . htmlspecialchars($idGenerar) . '" type="' . $tipoGenerar . '"' . $onclickGenerar . ' disabled '
        . 'class="' . $base . ' bg-red-600 hover:bg-red-700 text-white font-semibold shadow-sm opacity-50 cursor-not-allowed">'
        . icon('file-text', 'w-4 h-4') . ' Generar Word</button>'
        . '</div>';
}
