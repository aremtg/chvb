<?php
// src/helpers/RenovacionReglas.php
// Reglas y cálculos del módulo "Control de Renovaciones". Solo lógica: NO toca la BD
// (la BD la maneja RenovacionModel). Es el único lugar donde viven estas reglas en el módulo.
require_once __DIR__ . '/../controllers/EmpleadoController.php';

final class RenovacionReglas
{
    /** Días de anticipación con los que empieza a avisar el vencimiento. */
    public const DIAS_AVISO = 60;

    /** Tope de duración total de un contrato a término fijo (con renovaciones), en meses. */
    public const TOPE_MESES_FIJO = 48;

    /** Tipo de contrato al que se le aplican las reglas legales de renovación. */
    public const TIPO_CON_REGLAS_LEGALES = 'Fijo';

    /**
     * Tipos de contrato que se controlan: los que tienen fecha de fin.
     * Misma fuente que usa la hoja de vida (EmpleadoController::$contratosConFin).
     */
    public static function tiposControlados(): array
    {
        return EmpleadoController::$contratosConFin;
    }

    private static function zona(): DateTimeZone
    {
        return new DateTimeZone('America/Bogota');
    }

    public static function hoy(): DateTimeImmutable
    {
        return new DateTimeImmutable('today', self::zona());
    }

    /** Convierte 'Y-m-d' en fecha. Lanza InvalidArgumentException si no es una fecha real. */
    public static function fecha(string $valor, string $etiqueta = 'La fecha'): DateTimeImmutable
    {
        $d = DateTimeImmutable::createFromFormat('!Y-m-d', trim($valor), self::zona());
        $errores = DateTimeImmutable::getLastErrors();
        $conErrores = is_array($errores) && ($errores['warning_count'] > 0 || $errores['error_count'] > 0);
        if (!$d || $conErrores || $d->format('Y-m-d') !== trim($valor)) {
            throw new InvalidArgumentException("{$etiqueta} no es válida.");
        }
        return $d;
    }

    public static function formatear(string $fechaIso): string
    {
        return self::fecha($fechaIso)->format('d/m/Y');
    }

    /**
     * Meses de un periodo contando ambos extremos (01/01 al 30/06 = 6 meses).
     * Mismo cálculo que ya usa el generador de renovaciones: meses completos, redondeando hacia arriba.
     */
    public static function mesesEntre(string $inicio, string $fin): int
    {
        $a = self::fecha($inicio, 'La fecha de inicio');
        $finMasUnDia = self::fecha($fin, 'La fecha de fin')->modify('+1 day');

        $meses = ((int) $finMasUnDia->format('Y') - (int) $a->format('Y')) * 12
            + ((int) $finMasUnDia->format('m') - (int) $a->format('m'));
        if ($meses < 0) {
            return 0;
        }

        $candidato = $a->modify('+' . $meses . ' months');
        if ($candidato < $finMasUnDia) {
            $meses++;
        }
        return max(0, $meses);
    }

    public static function textoMeses(int $meses): string
    {
        $anos = intdiv($meses, 12);
        $resto = $meses % 12;
        $partes = [];
        if ($anos > 0) {
            $partes[] = $anos . ' ' . ($anos === 1 ? 'año' : 'años');
        }
        if ($resto > 0) {
            $partes[] = $resto . ' ' . ($resto === 1 ? 'mes' : 'meses');
        }
        return $partes ? implode(' y ', $partes) : '0 meses';
    }

    /** "Vence en 23 días", "Vence hoy" o "Venció hace 5 días", a partir de los días restantes (negativo = ya venció). */
    public static function textoDias(int $dias): string
    {
        if ($dias < 0) {
            $n = abs($dias);
            return 'Venció hace ' . $n . ($n === 1 ? ' día' : ' días');
        }
        if ($dias === 0) {
            return 'Vence hoy';
        }
        return 'Vence en ' . $dias . ($dias === 1 ? ' día' : ' días');
    }

    /**
     * Estado de vigencia de un contrato según su fecha de fin (el contrato vale HASTA esa fecha, inclusive).
     *   vencido    -> la fecha de fin ya pasó
     *   por_vencer -> faltan DIAS_AVISO días o menos
     *   vigente    -> faltan más de DIAS_AVISO días
     */
    public static function estadoVigencia(string $fechaFin, ?DateTimeImmutable $hoy = null): array
    {
        $hoy = $hoy ?? self::hoy();
        $fin = self::fecha($fechaFin, 'La fecha de fin');
        $dias = (int) $hoy->diff($fin)->format('%r%a');   // fin - hoy, con signo

        if ($dias < 0) {
            $estado = 'vencido';
        } elseif ($dias <= self::DIAS_AVISO) {
            $estado = 'por_vencer';
        } else {
            $estado = 'vigente';
        }

        return [
            'estado' => $estado,
            'dias_restantes' => $dias,                 // negativo si ya venció
            'fecha_aviso' => $fin->modify('-' . self::DIAS_AVISO . ' days')->format('Y-m-d'),
        ];
    }

    /**
     * Debe poder controlarse el contrato de este empleado? Devuelve la lista de problemas
     * (vacía si todo está bien). Se usa para el panel de "datos incompletos" y para validar.
     */
    public static function problemasContratoInicial(array $empleado): array
    {
        $tipo = (string) ($empleado['tipo_de_contrato'] ?? '');
        if (!in_array($tipo, self::tiposControlados(), true)) {
            return ['El tipo de contrato "' . ($tipo !== '' ? $tipo : 'sin definir') . '" no tiene fecha de fin, por eso no maneja renovaciones.'];
        }

        $faltan = [];
        if (trim((string) ($empleado['fecha_inicio_contrato'] ?? '')) === '') {
            $faltan[] = 'Fecha de inicio del contrato';
        }
        if (trim((string) ($empleado['fecha_fin_contrato'] ?? '')) === '') {
            $faltan[] = 'Fecha de fin del contrato';
        }
        return $faltan ? ['Faltan en la hoja de vida: ' . implode(', ', $faltan) . '.'] : [];
    }

    /**
     * Evalúa la cadena completa (contrato inicial + renovaciones ordenadas por numero).
     * Devuelve vigencia, acumulado y ADVERTENCIAS legales. Las advertencias NO bloquean el registro:
     * el historial real puede tener casos que no cumplen la regla y aun así hay que poder anotarlos.
     */
    public static function evaluarCadena(array $empleado, array $renovaciones): array
    {
        $inicio = trim((string) ($empleado['fecha_inicio_contrato'] ?? ''));
        $fin = trim((string) ($empleado['fecha_fin_contrato'] ?? ''));

        $mesesIniciales = ($inicio !== '' && $fin !== '') ? self::mesesEntre($inicio, $fin) : 0;
        $mesesRenovaciones = 0;
        foreach ($renovaciones as $r) {
            $mesesRenovaciones += (int) $r['duracion_meses'];
        }
        $acumulado = $mesesIniciales + $mesesRenovaciones;

        $ultima = $renovaciones ? end($renovaciones) : null;
        $vigenciaFin = $ultima ? (string) $ultima['fecha_fin'] : ($fin !== '' ? $fin : null);

        $advertencias = [];
        $esFijo = ($empleado['tipo_de_contrato'] ?? '') === self::TIPO_CON_REGLAS_LEGALES;

        if ($esFijo) {
            $anterior = null;
            foreach (array_values($renovaciones) as $i => $r) {
                $n = (int) ($r['numero'] ?? ($i + 1));
                $dur = (int) $r['duracion_meses'];
                if ($n >= 4 && $dur < 12) {
                    $advertencias[] = "RNV{$n}: desde la 4ta renovación la duración debe ser de 1 año o más (Art. 46 CST, Ley 2466 de 2025).";
                }
                if ($anterior !== null && $dur < $anterior) {
                    $advertencias[] = "RNV{$n}: dura menos que el periodo anterior; reducir el tiempo solo es legal con un Otrosí de mutuo acuerdo.";
                }
                $anterior = $dur;
            }
            if ($acumulado > self::TOPE_MESES_FIJO) {
                $advertencias[] = 'Lleva ' . self::textoMeses($acumulado) . ', supera los 4 años: debe pasar a Contrato Indefinido (Ley 2466 de 2025).';
            }
        }

        return [
            'meses_iniciales' => $mesesIniciales,
            'meses_renovaciones' => $mesesRenovaciones,
            'meses_acumulados' => $acumulado,
            'meses_restantes_tope' => $esFijo ? max(0, self::TOPE_MESES_FIJO - $acumulado) : null,
            'requiere_indefinido' => $esFijo && $acumulado > self::TOPE_MESES_FIJO,
            'num_renovaciones' => count($renovaciones),
            'vigencia_fin' => $vigenciaFin,
            'advertencias' => $advertencias,
        ];
    }

    /**
     * Valida una renovación nueva o editada. $previas = renovaciones con numero menor al de esta.
     * Lanza InvalidArgumentException con mensaje para el usuario si hay un error que bloquea.
     * Devuelve ['duracion_meses' => int, 'advertencias' => string[]].
     */
    public static function validarRenovacion(array $empleado, array $previas, int $numero, string $inicio, string $fin): array
    {
        $problemas = self::problemasContratoInicial($empleado);
        if ($problemas) {
            throw new InvalidArgumentException($problemas[0]);
        }

        $ini = self::fecha($inicio, 'La fecha de inicio');
        $f = self::fecha($fin, 'La fecha de fin');
        if ($f < $ini) {
            throw new InvalidArgumentException('La fecha de fin no puede ser anterior a la de inicio.');
        }

        $finPrevio = $previas ? (string) end($previas)['fecha_fin'] : (string) $empleado['fecha_fin_contrato'];
        $finPrevioDt = self::fecha($finPrevio, 'La fecha de fin del periodo anterior');
        if ($ini <= $finPrevioDt) {
            throw new InvalidArgumentException(
                'La renovación debe empezar después de que termina el periodo anterior (' . $finPrevioDt->format('d/m/Y') . ').'
            );
        }

        $advertencias = [];
        $esperado = $finPrevioDt->modify('+1 day');
        if ($ini != $esperado) {
            $advertencias[] = 'RNV' . $numero . ' no empieza el día siguiente al fin del periodo anterior (' . $esperado->format('d/m/Y') . '): hay un hueco entre los dos periodos.';
        }

        $duracion = self::mesesEntre($inicio, $fin);
        $cadena = array_merge(array_values($previas), [[
            'numero' => $numero,
            'fecha_inicio' => $inicio,
            'fecha_fin' => $fin,
            'duracion_meses' => $duracion,
        ]]);
        $evaluacion = self::evaluarCadena($empleado, $cadena);

        return [
            'duracion_meses' => $duracion,
            'advertencias' => array_merge($advertencias, $evaluacion['advertencias']),
        ];
    }
}
