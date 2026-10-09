<?php
// includes/roles.php
// ÚNICO lugar donde se nombran los roles de Talento Humano. Se carga desde includes/session.php
// (con require_once, así que se declara una sola vez).
// Los guards (formatos_guard.php, renovaciones_guard.php, ...) y las páginas usan estas constantes
// en vez de repetir la lista de roles a mano.
const ROL_SUPERADMIN_TH = 'superadmin_talento_humano';
const ROL_AUXILIAR_TH = 'auxiliar_talento_humano';
const ROLES_TALENTO_HUMANO = [ROL_SUPERADMIN_TH, ROL_AUXILIAR_TH];
