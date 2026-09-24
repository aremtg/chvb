<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../src/helpers/FileManager.php';
require_once __DIR__ . '/../src/models/BolsilloModel.php';
require_once __DIR__ . '/../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../src/helpers/ReconciliadorArchivos.php';

$pdo = getPDO();
$empleados = $pdo->query("SELECT cedula FROM empleados")->fetchAll(PDO::FETCH_COLUMN);

foreach ($empleados as $cedula) {
    echo "Revisando $cedula...<br>";
    try {
        FileManager::crearEstructuraEmpleado($cedula);
        // Si ya tiene bolsillos (porque los importaste) no los duplica
        $existen = $pdo->prepare("SELECT COUNT(*) FROM bolsillos WHERE cedula_empleado = ?");
        $existen->execute([$cedula]);
        if ($existen->fetchColumn() == 0) {
            BolsilloModel::crearBolsillosParaEmpleado($cedula);
            echo " -> Bolsillos creados<br>";
        }
        ReconciliadorArchivos::importarDocumentosExistentes($cedula);
        echo " -> Carpeta OK<br>";
    } catch (Exception $e) {
        echo " -> ERROR: " . $e->getMessage() . "<br>";
    }
}
echo "Listo.";