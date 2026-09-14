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

        /**
     * Calcula y guarda (si no existen ya) los festivos de un año usando FestivoCalculador.
     * Se puede llamar bajo demanda (ej. al abrir el calendario si el año no está poblado aún)
     * sin depender de internet ni de datos precargados manualmente.
     */
    public static function asegurarAnioPoblado(int $anio): void {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM festivos_colombia WHERE anio = :b1");
        $stmt->execute(['b1' => $anio]);
        if ((int)$stmt->fetch()['total'] > 0) {
            return; // ya está poblado, no se repite
        }

        require_once __DIR__ . '/../helpers/FestivoCalculador.php';
        $festivos = FestivoCalculador::calcularParaAnio($anio);

        $insert = $pdo->prepare("INSERT IGNORE INTO festivos_colombia (fecha, nombre, anio) VALUES (:b1, :b2, :b3)");
        foreach ($festivos as $f) {
            $insert->execute(['b1' => $f['fecha'], 'b2' => $f['nombre'], 'b3' => $anio]);
        }
    }
}