<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/BolsilloModel.php';
require_once __DIR__ . '/../../src/models/DocumentoModel.php';

header('Content-Type: application/json');
requireSuperAdmin();

$id = (int)($_GET['id'] ?? 0);
$bolsillo = BolsilloModel::obtenerPorId($id);

if (!$bolsillo) {
    echo json_encode(['ok' => false, 'error' => 'Bolsillo no encontrado.']);
    exit;
}

$bolsillo['documentos'] = DocumentoModel::listarPorBolsillo($id);
echo json_encode(['ok' => true, 'bolsillo' => $bolsillo]);