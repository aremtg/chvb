<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';

// Solo se cierra sesión con POST + token CSRF. Un GET (enlace, imagen, prefetch)
// no cierra nada: simplemente lleva al login.
if (logoutSolicitadoPorPost()) {
    AuthController::logout();
}
header('Location: ./login.php');
exit;
