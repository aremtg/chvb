<?php
// src/models/FuncionModel.php
// CRUD de funciones por cargo. Un único modelo sirve para los dos catálogos:
//   'certificado' -> funciones_certificados (texto corto)
//   'contrato'    -> funciones_contratos    (texto detallado)
require_once __DIR__ . '/../../config/database.php';

class FuncionModel
{
    /** Lista blanca: el nombre de tabla NUNCA viene del usuario. */
    private const TIPOS = [
        'certificado' => ['tabla' => 'funciones_certificados', 'max' => 160],
        'contrato'    => ['tabla' => 'funciones_contratos',    'max' => 5000],
    ];

    public static function cfg(string $tipo): array
    {
        if (!isset(self::TIPOS[$tipo])) {
            throw new InvalidArgumentException('Tipo de funciones no válido.');
        }
        return self::TIPOS[$tipo];
    }

    public static function cargos(): array
    {
        return getPDO()->query('SELECT id, nombre FROM cargos ORDER BY nombre')->fetchAll();
    }

    /** Id del cargo por nombre exacto (como viene en empleados.cargo). */
    public static function cargoIdPorNombre(string $nombre): ?int
    {
        $pdo = getPDO();
        $st = $pdo->prepare('SELECT id FROM cargos WHERE nombre = ? LIMIT 1');
        $st->execute([$nombre]);
        $id = $st->fetchColumn();
        return $id === false ? null : (int)$id;
    }

    public static function listar(string $tipo, ?int $cargoId = null, bool $soloActivas = false): array
    {
        $t = self::cfg($tipo)['tabla'];
        $where = [];
        $params = [];
        if ($cargoId !== null) {
            $where[] = 'f.cargo_id = ?';
            $params[] = $cargoId;
        }
        if ($soloActivas) {
            $where[] = 'f.activo = 1';
        }
        $sql = "SELECT f.id, f.cargo_id, c.nombre AS cargo, f.texto, f.orden, f.activo
                FROM {$t} f
                JOIN cargos c ON c.id = f.cargo_id"
            . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
            . ' ORDER BY c.nombre, f.orden, f.id';
        $st = getPDO()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    /** Crea (id = null) o edita. Devuelve el id. */
    public static function guardar(string $tipo, ?int $id, int $cargoId, string $texto, int $orden, bool $activo): int
    {
        $cfg = self::cfg($tipo);
        $t = $cfg['tabla'];

        $texto = trim(preg_replace('/\s+/u', ' ', $texto));
        if ($texto === '') {
            throw new InvalidArgumentException('El texto de la función es obligatorio.');
        }
        if (mb_strlen($texto, 'UTF-8') > $cfg['max']) {
            throw new InvalidArgumentException("El texto no puede superar {$cfg['max']} caracteres.");
        }
        if ($orden < 0 || $orden > 255) {
            throw new InvalidArgumentException('El orden debe estar entre 0 y 255.');
        }

        $pdo = getPDO();
        $st = $pdo->prepare('SELECT 1 FROM cargos WHERE id = ?');
        $st->execute([$cargoId]);
        if (!$st->fetchColumn()) {
            throw new InvalidArgumentException('Selecciona un cargo válido.');
        }

        try {
            if ($id === null) {
                $st = $pdo->prepare("INSERT INTO {$t} (cargo_id, texto, orden, activo) VALUES (?,?,?,?)");
                $st->execute([$cargoId, $texto, $orden, (int)$activo]);
                return (int)$pdo->lastInsertId();
            }
            $st = $pdo->prepare("UPDATE {$t} SET cargo_id = ?, texto = ?, orden = ?, activo = ? WHERE id = ?");
            $st->execute([$cargoId, $texto, $orden, (int)$activo, $id]);
            return $id;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new InvalidArgumentException('Ya existe esa función para ese cargo.');
            }
            throw $e;
        }
    }

    public static function eliminar(string $tipo, int $id): void
    {
        $t = self::cfg($tipo)['tabla'];
        $st = getPDO()->prepare("DELETE FROM {$t} WHERE id = ?");
        $st->execute([$id]);
        if ($st->rowCount() === 0) {
            throw new InvalidArgumentException('La función ya no existe.');
        }
    }
}
