<?php
// public/api/empleado_subir_certificado.php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/controllers/LibroController.php';
require_once __DIR__ . '/../../src/models/BolsilloModel.php';

header('Content-Type: application/json');
requireEmpleado();

$cedula = $_SESSION['empleado_cedula']; // nunca confiar en un valor enviado por el cliente

// Buscamos el bolsillo "certificados" que pertenezca a ESTE empleado y a la sección hoja_de_vida
$bolsillos = BolsilloModel::listarPorEmpleado($cedula);
$bolsilloCertificados = null;
foreach ($bolsillos as $b) {
    if ($b['nombre'] === 'certificados' && $b['seccion'] === 'hoja_de_vida') {
        $bolsilloCertificados = $b;
        break;
    }
}

if (!$bolsilloCertificados) {
    echo json_encode(['ok' => false, 'error' => 'No se encontró el bolsillo de certificados.']);
    exit;
}

if (!isset($_FILES['archivo'])) {
    echo json_encode(['ok' => false, 'error' => 'No se recibió ningún archivo.']);
    exit;
}

// pendienteRevision = true, forzado, según tu regla de negocio
$resultado = LibroController::subirDocumento((int)$bolsilloCertificados['id'], $cedula, $_FILES['archivo'], true);
echo json_encode($resultado);