<?php
// src/models/DocumentoModel.php
require_once __DIR__ . '/../../config/database.php';

class DocumentoModel
{

    public static function listarPorBolsillo(int $bolsilloId): array
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM documentos WHERE bolsillo_id = :id ORDER BY orden ASC");
        $stmt->execute(['id' => $bolsilloId]);
        return $stmt->fetchAll();
    }

    public static function siguienteOrden(int $bolsilloId): int
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT MAX(orden) as maximo FROM documentos WHERE bolsillo_id = :id");
        $stmt->execute(['id' => $bolsilloId]);
        $r = $stmt->fetch();
        return ((int) ($r['maximo'] ?? 0)) + 1;
    }

    public static function crear(int $bolsilloId, string $nombreArchivo, string $ruta, bool $pendienteRevision = false): int
    {
        $pdo = getPDO();
        $orden = self::siguienteOrden($bolsilloId);
        $stmt = $pdo->prepare(
            "INSERT INTO documentos (bolsillo_id, nombre_archivo, ruta, orden, pendiente_revision)
             VALUES (:bolsillo_id, :nombre, :ruta, :orden, :pendiente)"
        );
        $stmt->execute([
            'bolsillo_id' => $bolsilloId,
            'nombre' => $nombreArchivo,
            'ruta' => $ruta,
            'orden' => $orden,
            'pendiente' => $pendienteRevision ? 1 : 0,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function obtenerPorId(int $id): ?array
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM documentos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public static function eliminar(int $id): void
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("DELETE FROM documentos WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    /**
     * Intercambia el orden entre dos documentos (para mover arriba/abajo).
     */
    public static function intercambiarOrden(int $idA, int $ordenA, int $idB, int $ordenB): void
    {
        $pdo = getPDO();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE documentos SET orden = :orden WHERE id = :id");
            $stmt->execute(['orden' => $ordenB, 'id' => $idA]);
            $stmt->execute(['orden' => $ordenA, 'id' => $idB]);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function marcarRevisado(int $id): void
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("UPDATE documentos SET pendiente_revision = 0 WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public static function actualizarRutasPorCambioCedula(string $cedulaAnterior, string $cedulaNueva): void
    {
        $pdo = getPDO();
        $sql = "UPDATE documentos d
            INNER JOIN bolsillos b ON d.bolsillo_id = b.id
            SET d.ruta = REPLACE(d.ruta, CONCAT('hv_', :anterior, '/'), CONCAT('hv_', :nueva, '/'))
            WHERE b.cedula_empleado = :nueva2";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['anterior' => $cedulaAnterior, 'nueva' => $cedulaNueva, 'nueva2' => $cedulaNueva]);
    }
}