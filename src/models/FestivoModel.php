<?php
// src/models/FestivoModel.php (nuevo archivo)
require_once __DIR__ . '/../../config/database.php';

class FestivoModel {

    /**
     * Devuelve los festivos colombianos que caen dentro del rango de fechas del permiso
     * (inclusive ambos extremos, comparando solo la parte de fecha, no la hora).
     */
    public static function obtenerEnRango(string $fechaInicio, string $fechaFin): array {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "SELECT fecha, nombre FROM festivos_colombia 
             WHERE fecha BETWEEN :b1 AND :b2 
             ORDER BY fecha ASC"
        );
        $stmt->execute(['b1' => $fechaInicio, 'b2' => $fechaFin]);
        return $stmt->fetchAll();
    }
}