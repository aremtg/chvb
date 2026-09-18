<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/FirmaModel.php';
requireEmpleado();
$cedula = $_GET['cedula'] ?? '';
if ($cedula !== $_SESSION['empleado_cedula']) { http_response_code(403); exit; }
$firma = FirmaModel::obtenerPorCedula($cedula);
if (!$firma || empty($firma['ruta_imagen'])) { http_response_code(404); exit; }
require_once __DIR__ . '/../../src/helpers/FileManager.php';
$ruta = FileManager::rutaAbsolutaDesdeRelativa($firma['ruta_imagen']);
if (!$ruta || !is_file($ruta)) { http_response_code(404); exit; }
$mime = mime_content_type($ruta) ?: 'image/png';
header('Content-Type: '.$mime); header('Cache-Control: private, max-age=300'); readfile($ruta);
