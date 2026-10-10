<?php
// src/models/FormatoPermisoModel.php
// Código, versión y fecha del formato que aparecen en cada permiso.
// Una sola fila (id = 1). Solo Talento Humano (admin y auxiliar) puede cambiarla.
// El código se usa como prefijo de los consecutivos NUEVOS; los ya creados no cambian.

require_once __DIR__ . '/../../config/database.php';

class FormatoPermisoModel
{
    public const CODIGO_DEFECTO = 'GH-FT-10';

    /** Valores actuales (o por defecto si la tabla o la fila no existen todavía). */
    public static function obtener(): array
    {
        $defecto = ['codigo' => self::CODIGO_DEFECTO, 'version' => 1, 'fecha' => date('Y-m-d')];
        try {
            $fila = getPDO()->query('SELECT codigo, version, fecha FROM permisos_formato WHERE id = 1')->fetch();
        } catch (\Throwable $e) {
            error_log('FormatoPermisoModel: ' . $e->getMessage());
            return $defecto;
        }
        if (!$fila) {
            return $defecto;
        }
        return [
            'codigo' => (string) $fila['codigo'],
            'version' => (int) $fila['version'],
            'fecha' => (string) $fila['fecha'],
        ];
    }

    /** Guarda código, versión y fecha. Valida antes de tocar la base. */
    public static function guardar(string $codigo, int $version, string $fecha, string $actor): void
    {
        $codigo = strtoupper(trim($codigo));
        if (!preg_match('/^[A-Z0-9][A-Z0-9-]{1,18}[A-Z0-9]$/', $codigo)) {
            throw new InvalidArgumentException('El código solo puede tener letras, números y guiones (ej. GH-FT-10).');
        }
        if ($version < 1 || $version > 999) {
            throw new InvalidArgumentException('La versión debe estar entre 1 y 999.');
        }
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$d || $d->format('Y-m-d') !== $fecha) {
            throw new InvalidArgumentException('La fecha no es válida.');
        }

        $stmt = getPDO()->prepare(
            'INSERT INTO permisos_formato (id, codigo, version, fecha, actualizado_por, actualizado_en)
             VALUES (1, :c, :v, :f, :a, NOW())
             ON DUPLICATE KEY UPDATE codigo = :c2, version = :v2, fecha = :f2, actualizado_por = :a2, actualizado_en = NOW()'
        );
        $stmt->execute(['c' => $codigo, 'v' => $version, 'f' => $fecha, 'a' => $actor,
            'c2' => $codigo, 'v2' => $version, 'f2' => $fecha, 'a2' => $actor]);
    }
}
