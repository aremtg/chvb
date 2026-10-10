<?php
// Guarda código, versión y fecha del formato. Solo admin y auxiliar de Talento Humano.
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/formatos_guard.php';
require_once __DIR__ . '/../../src/models/FormatoPermisoModel.php';

header('Content-Type: application/json; charset=utf-8');
requireFormatosAccess(true);
validarCSRF();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
    exit;
}

try {
    FormatoPermisoModel::guardar(
        (string) ($_POST['codigo'] ?? ''),
        (int) ($_POST['version'] ?? 0),
        (string) ($_POST['fecha'] ?? ''),
        (string) ($_SESSION['superadmin_username'] ?? '')
    );
    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
