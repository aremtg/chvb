<?php
// src/models/FirmaModel.php (nuevo archivo)
require_once __DIR__ . '/../../config/database.php';

class FirmaModel {

    public static function obtenerPorCedula(string $cedula): ?array {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM firmas_guardadas WHERE cedula = :b1");
        $stmt->execute(['b1' => $cedula]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    /** Guarda/reemplaza la firma reutilizable del empleado (UPSERT manual, tabla con UNIQUE en cedula). */
    public static function guardarComoActiva(string $cedula, string $rutaImagen): void {
        $pdo = getPDO();
        $existente = self::obtenerPorCedula($cedula);
        if ($existente) {
            $stmt = $pdo->prepare("UPDATE firmas_guardadas SET ruta_imagen = :b1 WHERE cedula = :b2");
            $stmt->execute(['b1' => $rutaImagen, 'b2' => $cedula]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO firmas_guardadas (cedula, ruta_imagen) VALUES (:b1, :b2)");
            $stmt->execute(['b1' => $cedula, 'b2' => $rutaImagen]);
        }
    }
}