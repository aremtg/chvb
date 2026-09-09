<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';

AuthController::logout();
header('Location: /chvb/public/login.php');
exit;