<?php
// includes/icon.php

/**
 * Inserta un ícono SVG de Lucide inline, listo para colorear con clases Tailwind (text-*)
 * y dimensionar con clases de tamaño (w-* h-*).
 *
 * Uso: <?= icon('pencil', 'w-4 h-4') ?>
 */
function icon(string $nombre, string $clases = 'w-5 h-5'): string {
    static $cache = [];

    if (!isset($cache[$nombre])) {
        $ruta = __DIR__ . '/../public/assets/icons/' . $nombre . '.svg';
        if (!is_file($ruta)) {
            return '<!-- icono no encontrado: ' . htmlspecialchars($nombre) . ' -->';
        }
        $cache[$nombre] = file_get_contents($ruta);
    }

    $svg = $cache[$nombre];
    // Reemplaza el atributo class del <svg> raíz por las clases que pedimos
    $svg = preg_replace('/class="[^"]*"/', 'class="' . htmlspecialchars($clases) . ' inline-block align-middle"', $svg, 1);

    return $svg;
}