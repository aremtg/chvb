<?php
/**
 * Elementos comunes del <head> de CHVB.
 *
 * Centraliza el favicon y el sistema de loading global (spinner en botones,
 * barra superior, overlay). Como TODAS las páginas incluyen este archivo,
 * el loading queda disponible en todo el sistema sin tocar cada página.
 *
 * El ?v=filemtime fuerza al navegador a bajar la versión nueva cuando editas
 * el archivo (mismo patrón que ya usas con empleados.js, etc.).
 */
$chvLoadingCss = __DIR__ . '/../public/assets/css/loading.css';
$chvLoadingJs  = __DIR__ . '/../public/assets/js/loading.js';
?>
<link rel="alternate icon" type="image/svg+xml" href="assets/img/logo-bomberos.svg">
<link rel="icon" type="image/png" href="assets/img/logo-bomberos.png">
<link rel="stylesheet" href="./assets/css/loading.css?v=<?= (int) @filemtime($chvLoadingCss) ?>">
<?php // Sin "defer": así Loading existe también para los <script> inline del final de cada página. ?>
<script src="./assets/js/loading.js?v=<?= (int) @filemtime($chvLoadingJs) ?>"></script>