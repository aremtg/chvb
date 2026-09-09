<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/controllers/AuthEmpleadoController.php';

AuthEmpleadoController::logout();
header('Location: /chvb/public/login_empleado.php');
exit;