<?php
// src/models/CargoModel.php
// Catálogo de cargos (tabla `cargos`). Es la ÚNICA fuente de los cargos que se pueden
// elegir al crear/editar un empleado y en los filtros.
//
// empleados.cargo guarda el NOMBRE del cargo y tiene una llave foránea hacia cargos.nombre
// con ON UPDATE CASCADE / ON DELETE RESTRICT:
//   - renombrar un cargo actualiza solo a todos los empleados que lo tienen;
//   - un cargo con empleados no se puede eliminar.
// Las funciones de certificados/contratos cuelgan de cargos.id, así que siguen ligadas al renombrar.
require_once __DIR__ . '/../../config/database.php';

class CargoModel
{
    public const MIN_NOMBRE = 2;
    public const MAX_NOMBRE = 100;

    /** Nombre que el código trata de forma especial (JornadaHelper y empleados.js): no se renombra ni se elimina. */
    public const PROTEGIDO = 'Bombero integral';

    public static function esProtegido(string $nombre): bool
    {
        return mb_strtolower(trim($nombre), 'UTF-8') === mb_strtolower(self::PROTEGIDO, 'UTF-8');
    }

    /** Nombres de todos los cargos (orden alfabético): lo que aparece al crear/editar/filtrar empleados. */
    public static function nombres(): array
    {
        return getPDO()->query('SELECT nombre FROM cargos ORDER BY nombre')->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Lista para el modal, con cuántos empleados y funciones usa cada cargo. */
    public static function listar(): array
    {
        $filas = getPDO()->query(
            'SELECT c.id, c.nombre,
                    (SELECT COUNT(*) FROM empleados e WHERE e.cargo = c.nombre) AS empleados,
                    (SELECT COUNT(*) FROM funciones_certificados fc WHERE fc.cargo_id = c.id)
                  + (SELECT COUNT(*) FROM funciones_contratos fk WHERE fk.cargo_id = c.id) AS funciones
             FROM cargos c
             ORDER BY c.nombre'
        )->fetchAll();

        foreach ($filas as &$f) {
            $f['id'] = (int)$f['id'];
            $f['empleados'] = (int)$f['empleados'];
            $f['funciones'] = (int)$f['funciones'];
            $f['protegido'] = self::esProtegido((string)$f['nombre']);
        }
        return $filas;
    }

    /** Limpia espacios y valida. Lanza InvalidArgumentException con un mensaje listo para mostrar. */
    private static function normalizar(string $nombre): string
    {
        $nombre = trim(preg_replace('/\s+/u', ' ', $nombre) ?? '');
        $largo = mb_strlen($nombre, 'UTF-8');
        if ($largo < self::MIN_NOMBRE || $largo > self::MAX_NOMBRE) {
            throw new InvalidArgumentException(
                'El nombre del cargo debe tener entre ' . self::MIN_NOMBRE . ' y ' . self::MAX_NOMBRE . ' caracteres.'
            );
        }
        if (preg_match('/[\x00-\x1F\x7F]/u', $nombre)) {
            throw new InvalidArgumentException('El nombre del cargo tiene caracteres no válidos.');
        }
        return $nombre;
    }

    private static function buscar(int $id): array
    {
        $st = getPDO()->prepare('SELECT id, nombre FROM cargos WHERE id = ?');
        $st->execute([$id]);
        $c = $st->fetch();
        if (!$c) {
            throw new InvalidArgumentException('El cargo ya no existe. Recarga la lista.');
        }
        return $c;
    }

    private static function assertNombreLibre(string $nombre, int $exceptoId = 0): void
    {
        // La colación es case-insensitive: "maquinista" y "Maquinista" cuentan como el mismo cargo.
        $st = getPDO()->prepare('SELECT 1 FROM cargos WHERE nombre = ? AND id <> ? LIMIT 1');
        $st->execute([$nombre, $exceptoId]);
        if ($st->fetchColumn()) {
            throw new InvalidArgumentException('Ya existe un cargo con ese nombre.');
        }
    }

    public static function crear(string $nombre): int
    {
        $nombre = self::normalizar($nombre);
        self::assertNombreLibre($nombre);
        $pdo = getPDO();
        try {
            $pdo->prepare('INSERT INTO cargos (nombre) VALUES (?)')->execute([$nombre]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new InvalidArgumentException('Ya existe un cargo con ese nombre.');
            }
            throw $e;
        }
        return (int)$pdo->lastInsertId();
    }

    /** Renombra. La llave foránea propaga el cambio a empleados.cargo en la misma operación. */
    public static function actualizar(int $id, string $nombre): void
    {
        $actual = self::buscar($id);
        if (self::esProtegido((string)$actual['nombre'])) {
            throw new InvalidArgumentException(
                'El cargo «' . self::PROTEGIDO . '» no se puede modificar: el sistema lo usa para identificar a los bomberos integrales.'
            );
        }
        $nombre = self::normalizar($nombre);
        if ($nombre === (string)$actual['nombre']) {
            return;   // sin cambios
        }
        if (self::esProtegido($nombre)) {
            throw new InvalidArgumentException('Ese nombre está reservado por el sistema.');
        }
        self::assertNombreLibre($nombre, $id);
        try {
            getPDO()->prepare('UPDATE cargos SET nombre = ? WHERE id = ?')->execute([$nombre, $id]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new InvalidArgumentException('Ya existe un cargo con ese nombre.');
            }
            throw $e;
        }
    }

    /** Elimina el cargo y sus funciones (ON DELETE CASCADE). Si algún empleado lo tiene, NO se elimina. */
    public static function eliminar(int $id): void
    {
        $actual = self::buscar($id);
        if (self::esProtegido((string)$actual['nombre'])) {
            throw new InvalidArgumentException(
                'El cargo «' . self::PROTEGIDO . '» no se puede eliminar: el sistema lo usa para identificar a los bomberos integrales.'
            );
        }

        $pdo = getPDO();
        $st = $pdo->prepare('SELECT COUNT(*) FROM empleados WHERE cargo = ?');
        $st->execute([$actual['nombre']]);
        $n = (int)$st->fetchColumn();
        if ($n > 0) {
            throw new InvalidArgumentException(
                'No se puede eliminar: ' . $n . ($n === 1 ? ' empleado tiene' : ' empleados tienen')
                . ' este cargo. Cámbiales el cargo primero.'
            );
        }

        try {
            $pdo->prepare('DELETE FROM cargos WHERE id = ?')->execute([$id]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {   // alguien lo asignó justo ahora: la FK lo impide
                throw new InvalidArgumentException('No se puede eliminar: hay empleados que tienen este cargo.');
            }
            throw $e;
        }
    }
}
