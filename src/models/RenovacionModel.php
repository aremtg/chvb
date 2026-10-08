<?php
// src/models/RenovacionModel.php
// Datos del módulo "Control de Renovaciones". Las reglas viven en RenovacionReglas.
// No modifica ninguna tabla existente: solo lee `empleados` y escribe en las tablas nuevas
// `renovaciones_contrato` y `renovaciones_historial` (ver database/migrations/).
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/RenovacionReglas.php';

class RenovacionModel
{
    // ------------------------------------------------------------------
    // Lectura
    // ------------------------------------------------------------------

    public static function listarPorEmpleado(string $cedula): array
    {
        $stmt = getPDO()->prepare("SELECT * FROM renovaciones_contrato WHERE cedula = ? ORDER BY numero ASC");
        $stmt->execute([$cedula]);
        return $stmt->fetchAll();
    }

    public static function obtener(int $id): ?array
    {
        $stmt = getPDO()->prepare("SELECT * FROM renovaciones_contrato WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function historial(string $cedula, int $limite = 50): array
    {
        $limite = max(1, min(200, $limite));
        $stmt = getPDO()->prepare(
            "SELECT * FROM renovaciones_historial WHERE cedula = ? ORDER BY created_at DESC, id DESC LIMIT " . $limite
        );
        $stmt->execute([$cedula]);
        return $stmt->fetchAll();
    }

    /** Ficha de un empleado: contrato inicial + renovaciones + evaluación (vigencia, acumulado, advertencias). */
    public static function resumenEmpleado(string $cedula): ?array
    {
        $stmt = getPDO()->prepare("SELECT * FROM empleados WHERE cedula = ?");
        $stmt->execute([$cedula]);
        $empleado = $stmt->fetch();
        if (!$empleado) {
            return null;
        }

        $renovaciones = self::listarPorEmpleado($cedula);
        $problemas = RenovacionReglas::problemasContratoInicial($empleado);
        $evaluacion = $problemas ? null : RenovacionReglas::evaluarCadena($empleado, $renovaciones);
        $vigencia = ($evaluacion && $evaluacion['vigencia_fin'])
            ? RenovacionReglas::estadoVigencia($evaluacion['vigencia_fin'])
            : null;

        return [
            'empleado' => $empleado,
            'renovaciones' => $renovaciones,
            'problemas' => $problemas,
            'evaluacion' => $evaluacion,
            'vigencia' => $vigencia,
        ];
    }

    /**
     * Panel principal: empleados ACTIVOS con contrato que tiene fecha de fin, con su vigencia real
     * (fin de la última renovación, o fin del contrato inicial si no tiene renovaciones).
     * Ordenados del que vence primero al que vence último (los ya vencidos arriba).
     */
    public static function listarVigencias(): array
    {
        $tipos = RenovacionReglas::tiposControlados();
        $ph = implode(',', array_fill(0, count($tipos), '?'));

        $sql = "SELECT e.cedula, e.nombre, e.cargo, e.tipo_de_contrato,
                       e.fecha_inicio_contrato, e.fecha_fin_contrato,
                       COALESCE(r.num_renovaciones, 0) AS num_renovaciones,
                       COALESCE(r.meses_renovaciones, 0) AS meses_renovaciones,
                       r.ultima_fin
                FROM empleados e
                LEFT JOIN (
                    SELECT cedula,
                           COUNT(*) AS num_renovaciones,
                           SUM(duracion_meses) AS meses_renovaciones,
                           MAX(fecha_fin) AS ultima_fin
                    FROM renovaciones_contrato
                    GROUP BY cedula
                ) r ON r.cedula = e.cedula
                WHERE e.estado = 'activo'
                  AND e.tipo_de_contrato IN ($ph)
                  AND (e.fecha_fin_contrato IS NOT NULL OR r.ultima_fin IS NOT NULL)";
        $stmt = getPDO()->prepare($sql);
        $stmt->execute($tipos);

        $hoy = RenovacionReglas::hoy();
        $filas = [];
        foreach ($stmt->fetchAll() as $f) {
            $vigenciaFin = $f['ultima_fin'] ?: $f['fecha_fin_contrato'];

            $mesesIniciales = (!empty($f['fecha_inicio_contrato']) && !empty($f['fecha_fin_contrato']))
                ? RenovacionReglas::mesesEntre($f['fecha_inicio_contrato'], $f['fecha_fin_contrato'])
                : 0;
            $acumulado = $mesesIniciales + (int) $f['meses_renovaciones'];

            $f['vigencia_fin'] = $vigenciaFin;
            $f['meses_acumulados'] = $acumulado;
            $f['requiere_indefinido'] = $f['tipo_de_contrato'] === RenovacionReglas::TIPO_CON_REGLAS_LEGALES
                && $acumulado > RenovacionReglas::TOPE_MESES_FIJO;
            $f = array_merge($f, RenovacionReglas::estadoVigencia($vigenciaFin, $hoy));
            $filas[] = $f;
        }

        usort($filas, static fn(array $a, array $b): int => $a['dias_restantes'] <=> $b['dias_restantes']);
        return $filas;
    }

    /**
     * Empleados ACTIVOS cuyo tipo de contrato exige fecha de fin pero les falta alguna fecha del
     * contrato inicial: no se les puede calcular vencimiento hasta completarla en la hoja de vida.
     */
    public static function listarIncompletos(): array
    {
        $tipos = RenovacionReglas::tiposControlados();
        $ph = implode(',', array_fill(0, count($tipos), '?'));

        $stmt = getPDO()->prepare(
            "SELECT cedula, nombre, cargo, tipo_de_contrato, fecha_inicio_contrato, fecha_fin_contrato
             FROM empleados
             WHERE estado = 'activo'
               AND tipo_de_contrato IN ($ph)
               AND (fecha_inicio_contrato IS NULL OR fecha_fin_contrato IS NULL)
             ORDER BY nombre ASC"
        );
        $stmt->execute($tipos);

        $filas = [];
        foreach ($stmt->fetchAll() as $f) {
            $faltan = [];
            if (empty($f['fecha_inicio_contrato'])) {
                $faltan[] = 'Fecha de inicio';
            }
            if (empty($f['fecha_fin_contrato'])) {
                $faltan[] = 'Fecha de fin';
            }
            $f['faltan'] = $faltan;
            $filas[] = $f;
        }
        return $filas;
    }

    // ------------------------------------------------------------------
    // Escritura (cada operación es una transacción: renovación + bitácora)
    // ------------------------------------------------------------------

    /**
     * Registra la siguiente renovación (RNV{n+1}) del empleado.
     * @return array ['id' => int, 'numero' => int, 'duracion_meses' => int, 'advertencias' => string[]]
     */
    public static function crear(string $cedula, string $inicio, string $fin, ?string $observaciones, int $actorId, string $actorNombre): array
    {
        $pdo = getPDO();
        $pdo->beginTransaction();
        try {
            // Bloquea la fila del empleado para que dos personas no registren la misma RNV a la vez.
            $stmt = $pdo->prepare("SELECT * FROM empleados WHERE cedula = ? FOR UPDATE");
            $stmt->execute([$cedula]);
            $empleado = $stmt->fetch();
            if (!$empleado) {
                throw new InvalidArgumentException('El empleado no existe.');
            }

            $existentes = self::listarPorEmpleado($cedula);
            $numero = $existentes ? ((int) end($existentes)['numero'] + 1) : 1;
            if ($numero > 255) {
                throw new InvalidArgumentException('Se alcanzó el máximo de renovaciones registrables.');
            }

            $val = RenovacionReglas::validarRenovacion($empleado, $existentes, $numero, $inicio, $fin);

            $ins = $pdo->prepare(
                "INSERT INTO renovaciones_contrato
                    (cedula, numero, fecha_inicio, fecha_fin, duracion_meses, observaciones, creado_por, creado_por_nombre)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $ins->execute([
                $cedula, $numero, $inicio, $fin, $val['duracion_meses'],
                self::limpiarObservaciones($observaciones), $actorId ?: null, self::recortar($actorNombre, 50),
            ]);
            $id = (int) $pdo->lastInsertId();

            self::registrarHistorial(
                $cedula, $id, 'crear',
                "RNV{$numero} registrada: " . RenovacionReglas::formatear($inicio) . ' a ' . RenovacionReglas::formatear($fin)
                    . ' (' . RenovacionReglas::textoMeses($val['duracion_meses']) . ')',
                $actorId, $actorNombre
            );

            $pdo->commit();
            return ['id' => $id, 'numero' => $numero, 'duracion_meses' => $val['duracion_meses'], 'advertencias' => $val['advertencias']];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Corrige las fechas u observaciones de una renovación. Solo se puede editar la ÚLTIMA
     * renovación del empleado, para no descuadrar las que vienen después.
     * @return array ['duracion_meses' => int, 'advertencias' => string[]]
     */
    public static function actualizar(int $id, string $inicio, string $fin, ?string $observaciones, int $actorId, string $actorNombre): array
    {
        $pdo = getPDO();
        $pdo->beginTransaction();
        try {
            [$actual, $empleado] = self::bloquearParaEditar($pdo, $id);
            $cedula = $actual['cedula'];

            $todas = self::listarPorEmpleado($cedula);
            $ultima = end($todas);
            if ((int) $ultima['id'] !== $id) {
                throw new InvalidArgumentException('Solo se puede editar la última renovación registrada (RNV' . (int) $ultima['numero'] . ').');
            }
            $previas = array_slice($todas, 0, -1);
            $numero = (int) $actual['numero'];

            $val = RenovacionReglas::validarRenovacion($empleado, $previas, $numero, $inicio, $fin);

            $upd = $pdo->prepare(
                "UPDATE renovaciones_contrato
                 SET fecha_inicio = ?, fecha_fin = ?, duracion_meses = ?, observaciones = ?
                 WHERE id = ?"
            );
            $upd->execute([$inicio, $fin, $val['duracion_meses'], self::limpiarObservaciones($observaciones), $id]);

            self::registrarHistorial(
                $cedula, $id, 'editar',
                "RNV{$numero} editada: antes " . RenovacionReglas::formatear($actual['fecha_inicio']) . ' a ' . RenovacionReglas::formatear($actual['fecha_fin'])
                    . ', ahora ' . RenovacionReglas::formatear($inicio) . ' a ' . RenovacionReglas::formatear($fin),
                $actorId, $actorNombre
            );

            $pdo->commit();
            return ['duracion_meses' => $val['duracion_meses'], 'advertencias' => $val['advertencias']];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /** Elimina una renovación. Solo se puede eliminar la ÚLTIMA del empleado. */
    public static function eliminar(int $id, int $actorId, string $actorNombre): void
    {
        $pdo = getPDO();
        $pdo->beginTransaction();
        try {
            [$actual] = self::bloquearParaEditar($pdo, $id);
            $cedula = $actual['cedula'];

            $todas = self::listarPorEmpleado($cedula);
            $ultima = end($todas);
            if ((int) $ultima['id'] !== $id) {
                throw new InvalidArgumentException('Solo se puede eliminar la última renovación registrada (RNV' . (int) $ultima['numero'] . ').');
            }

            $pdo->prepare("DELETE FROM renovaciones_contrato WHERE id = ?")->execute([$id]);

            self::registrarHistorial(
                $cedula, $id, 'eliminar',
                'RNV' . (int) $actual['numero'] . ' eliminada: ' . RenovacionReglas::formatear($actual['fecha_inicio']) . ' a ' . RenovacionReglas::formatear($actual['fecha_fin']),
                $actorId, $actorNombre
            );

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    // ------------------------------------------------------------------
    // Internos
    // ------------------------------------------------------------------

    /**
     * Bloquea primero al empleado y luego la renovación (mismo orden que crear(), para evitar
     * bloqueos cruzados entre dos usuarios). Devuelve [renovación, empleado].
     */
    private static function bloquearParaEditar(PDO $pdo, int $id): array
    {
        $previa = self::obtener($id);
        if (!$previa) {
            throw new InvalidArgumentException('La renovación no existe.');
        }

        $stmt = $pdo->prepare("SELECT * FROM empleados WHERE cedula = ? FOR UPDATE");
        $stmt->execute([$previa['cedula']]);
        $empleado = $stmt->fetch();
        if (!$empleado) {
            throw new InvalidArgumentException('El empleado no existe.');
        }

        $stmt = $pdo->prepare("SELECT * FROM renovaciones_contrato WHERE id = ? FOR UPDATE");
        $stmt->execute([$id]);
        $actual = $stmt->fetch();
        if (!$actual) {
            throw new InvalidArgumentException('La renovación no existe.');
        }
        return [$actual, $empleado];
    }

    private static function registrarHistorial(string $cedula, ?int $renovacionId, string $accion, string $detalle, int $actorId, string $actorNombre): void
    {
        getPDO()->prepare(
            "INSERT INTO renovaciones_historial (cedula, renovacion_id, accion, detalle, actor_id, actor_nombre)
             VALUES (?, ?, ?, ?, ?, ?)"
        )->execute([$cedula, $renovacionId, $accion, self::recortar($detalle, 500), $actorId ?: null, self::recortar($actorNombre, 50)]);
    }

    private static function limpiarObservaciones(?string $texto): ?string
    {
        $texto = trim((string) $texto);
        return $texto === '' ? null : self::recortar($texto, 500);
    }

    private static function recortar(string $texto, int $max): string
    {
        return mb_substr($texto, 0, $max, 'UTF-8');
    }
}
