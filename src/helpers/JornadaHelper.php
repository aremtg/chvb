<?php
// src/helpers/JornadaHelper.php
//
// FUENTE ÚNICA de dos reglas que antes estaban repetidas por todo el código:
//
//  1) Cómo se cuenta la jornada de un empleado (permisos, días completos, horas):
//       - Turnos         -> operativo de bomberos: opera por turnos; un día solo cuenta
//                           completo si se marca de 00:00 a 23:59; sin descuento de almuerzo.
//       - Administrativa -> horario de oficina 07:00-12:00 y 14:00-17:24 (8,40 h/día).
//       - Restringida    -> horario reducido por incapacidad/recomendación médica, con
//                           hora de entrada y salida propias (ej: 07:00-12:00 = 5 h/día).
//
//  2) Cómo se muestra el cargo: un bombero integral aparece como
//     "Bombero integral con funciones de <cargo>".
//
// IMPORTANTE: "tipo_de_personal" (Bombero/Civil) y "es_bombero_integral" describen QUIÉN es
// la persona. "tipo_jornada" describe CÓMO se cuentan sus horas. Son cosas distintas a
// propósito: un maquinista bombero integral es Bombero pero con jornada Administrativa.

class JornadaHelper
{
    public const TURNOS = 'Turnos';
    public const ADMINISTRATIVA = 'Administrativa';
    public const RESTRINGIDA = 'Restringida';

    /** Valores válidos => texto que ve el usuario. */
    public const TIPOS = [
        self::TURNOS => 'Operativo (turnos de bombero)',
        self::ADMINISTRATIVA => 'Administrativa (horario de oficina)',
        self::RESTRINGIDA => 'Horario reducido (restricción médica)',
    ];

    // Jornada administrativa: 07:00-12:00 + 14:00-17:24 = 8 h 24 min = 8,40 h
    public const ADMIN_ENTRADA = '07:00';
    public const ADMIN_SALIDA = '17:24';
    public const ALMUERZO_INICIO = '12:00';
    public const ALMUERZO_FIN = '14:00';
    public const ADMIN_HORAS = 8.4;

    /** Tipo de jornada efectivo del empleado (nunca devuelve un valor inválido). */
    public static function tipoJornada(array $emp): string
    {
        $t = $emp['tipo_jornada'] ?? null;
        if (is_string($t) && isset(self::TIPOS[$t])) {
            return $t;
        }
        // Registro sin dato: se comporta como antes de existir este campo.
        return ($emp['tipo_de_personal'] ?? null) === 'Bombero' ? self::TURNOS : self::ADMINISTRATIVA;
    }

    /** "HH:MM:SS" o "HH:MM" -> "HH:MM"; cualquier otra cosa -> null. */
    public static function normalizarHora($h): ?string
    {
        $h = trim((string) $h);
        if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/', $h, $m)) {
            return null;
        }
        return $m[1] . ':' . $m[2];
    }

    /** Horas entre entrada y salida descontando el solape con el almuerzo 12:00-14:00. */
    public static function horasEntre(string $entrada, string $salida): float
    {
        $aMin = static fn(string $h): int => ((int) substr($h, 0, 2)) * 60 + (int) substr($h, 3, 2);
        $e = $aMin($entrada);
        $s = $aMin($salida);
        if ($s <= $e) {
            return 0.0;
        }
        $solape = max(0, min($s, $aMin(self::ALMUERZO_FIN)) - max($e, $aMin(self::ALMUERZO_INICIO)));
        return round(($s - $e - $solape) / 60, 2);
    }

    /**
     * Reglas listas para usar por permisos, listados y JavaScript.
     * por_turnos: true => día completo = 00:00-23:59, sin umbral de horas.
     * horas_dia:  horas que equivalen a "1 día" (null cuando es por turnos).
     */
    public static function reglas(array $emp): array
    {
        $tipo = self::tipoJornada($emp);

        if ($tipo === self::RESTRINGIDA) {
            $entrada = self::normalizarHora($emp['jornada_hora_entrada'] ?? '');
            $salida = self::normalizarHora($emp['jornada_hora_salida'] ?? '');
            $horas = ($entrada && $salida) ? self::horasEntre($entrada, $salida) : 0.0;
            if ($horas > 0) {
                return [
                    'tipo' => $tipo,
                    'por_turnos' => false,
                    'entrada' => $entrada,
                    'salida' => $salida,
                    'horas_dia' => $horas,
                ];
            }
            // Restringida sin horario válido guardado: se trata como administrativa
            // para no inventar cifras (la validación al guardar evita llegar aquí).
            $tipo = self::ADMINISTRATIVA;
        }

        if ($tipo === self::TURNOS) {
            return ['tipo' => $tipo, 'por_turnos' => true, 'entrada' => '00:00', 'salida' => '23:59', 'horas_dia' => null];
        }

        return [
            'tipo' => self::ADMINISTRATIVA,
            'por_turnos' => false,
            'entrada' => self::ADMIN_ENTRADA,
            'salida' => self::ADMIN_SALIDA,
            'horas_dia' => self::ADMIN_HORAS,
        ];
    }

    /**
     * Valor que entiende PermisoController::calcularHorasPorDias():
     * 'Bombero' = sin descuento de almuerzo (turnos), 'Civil' = con descuento por solape.
     * Devuelve null si el empleado no tiene tipo_de_personal (el cálculo avisa y usa Civil).
     */
    public static function tipoParaCalculo(array $emp): ?string
    {
        if (trim((string) ($emp['tipo_de_personal'] ?? '')) === '') {
            return null;
        }
        return self::reglas($emp)['por_turnos'] ? 'Bombero' : 'Civil';
    }

    /** ¿Estas horas netas de un día alcanzan para contarlo como 1 día completo? */
    public static function esDiaCompleto(array $reglas, string $horaInicio, string $horaFin, float $horasNetas): bool
    {
        if ($reglas['por_turnos']) {
            return $horaInicio <= '00:00:59' && $horaFin >= '23:59:00';
        }
        return $horasNetas >= ((float) $reglas['horas_dia']) - 0.001;
    }

    /** Texto corto de la jornada para mostrar en detalle de empleado. */
    public static function etiquetaJornada(array $emp): string
    {
        $r = self::reglas($emp);
        if ($r['tipo'] === self::TURNOS) {
            return 'Operativo (turnos de bombero)';
        }
        $horas = rtrim(rtrim(number_format((float) $r['horas_dia'], 2, ',', ''), '0'), ',');
        if ($r['tipo'] === self::RESTRINGIDA) {
            return "Horario reducido {$r['entrada']} a {$r['salida']} ({$horas} h/día)";
        }
        return "Administrativa {$r['entrada']} a {$r['salida']} ({$horas} h/día)";
    }

    /**
     * Cargo tal como debe verse en pantalla.
     *  - Bombero integral con cargo "X"  -> "Bombero integral con funciones de X"
     *  - Bombero integral con cargo "Bombero integral" -> "Bombero integral"
     *  - Cualquier otra persona -> el cargo tal cual
     * Solo es para mostrar: la BD y los documentos guardan el cargo sin adornos.
     */
    public static function cargoDetalle(array $emp): string
    {
        $cargo = trim((string) ($emp['cargo'] ?? ''));
        $esIntegral = !empty($emp['es_bombero_integral']) && (int) $emp['es_bombero_integral'] === 1;
        if (!$esIntegral || $cargo === '') {
            return $cargo;
        }
        if (mb_strtolower($cargo, 'UTF-8') === 'bombero integral') {
            return 'Bombero integral';
        }
        return 'Bombero integral con funciones de ' . $cargo;
    }

    /** Paquete que se entrega al navegador (JSON) con todo lo derivado del empleado. */
    public static function resumen(array $emp): array
    {
        return self::reglas($emp) + [
            'etiqueta' => self::etiquetaJornada($emp),
            'cargo_detalle' => self::cargoDetalle($emp),
        ];
    }
}
