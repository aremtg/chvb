<?php
// src/helpers/RenovacionReglas.php
// Reglas y cálculos del módulo "Control de Renovaciones". Solo lógica: NO toca la BD
// (la BD la maneja RenovacionModel). Es el único lugar donde viven estas reglas en el módulo.
require_once __DIR__ . '/../../includes/reloj.php';
require_once __DIR__ . '/../controllers/EmpleadoController.php';

final class RenovacionReglas
{
    /** Días de anticipación con los que empieza a avisar el vencimiento. */
    public const DIAS_AVISO = 60;

    /** Tope de duración total de un contrato a término fijo (con renovaciones), en meses. */
    public const TOPE_MESES_FIJO = 48;

    /**
     * Desde cuándo cuenta el tope de 4 años (Ley 2466 de 2025, Art. 46 CST). Para los contratos que ya
     * estaban en curso cuando entró en vigencia la ley, el conteo empieza en esta fecha y no en la firma
     * original (concepto del Ministerio del Trabajo). Para contratos posteriores, cuenta todo desde el inicio.
     * El tiempo anterior NO se borra: sigue sumando en el "acumulado" (antigüedad), solo no cuenta para el tope.
     */
    public const FECHA_INICIO_TOPE = '2025-06-25';

    /** Base comercial laboral: mes de 30 días y año de 360 días. */
    public const DIAS_MES = 30;
    public const DIAS_ANIO = 360;

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
        return Reloj::zona();
    }

    public static function hoy(): DateTimeImmutable
    {
        return Reloj::hoy();
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
     * Días de un periodo en BASE COMERCIAL LABORAL (mes de 30 días, año de 360), contando ambos extremos.
     * El último día de cada mes cuenta como día 30, así que un periodo que empieza el 1 y termina el 30 o el 31
     * (o el 28/29 de febrero) completa el mes:
     *   01/01/2026 a 30/12/2026 = 360 días = 1 año     01/01/2026 a 31/12/2026 = 360 días = 1 año
     *   01/01/2026 a 01/01/2027 = 361 días = 1 año y 1 día
     */
    public static function dias360(string $inicio, string $fin): int
    {
        $a = self::fecha($inicio, 'La fecha de inicio');
        $b = self::fecha($fin, 'La fecha de fin');
        if ($b < $a) {
            return 0;
        }

        $d1 = (int) $a->format('j');
        $d2 = (int) $b->format('j');
        if ($d1 === 31) {
            $d1 = 30;
        }
        if ($b->format('j') === $b->format('t')) {   // el último día del mes cuenta como 30
            $d2 = 30;
        }

        $dias = ((int) $b->format('Y') - (int) $a->format('Y')) * self::DIAS_ANIO
            + ((int) $b->format('n') - (int) $a->format('n')) * self::DIAS_MES
            + ($d2 - $d1) + 1;

        return max(0, $dias);
    }

    /** Convierte días comerciales en años (360), meses (30) y días. */
    public static function periodoDesdeDias(int $dias): array
    {
        $dias = max(0, $dias);
        $resto = $dias % self::DIAS_ANIO;
        return [
            'anios' => intdiv($dias, self::DIAS_ANIO),
            'meses' => intdiv($resto, self::DIAS_MES),
            'dias' => $resto % self::DIAS_MES,
            'total_dias' => $dias,
        ];
    }

    /** Duración de un periodo (ambos extremos incluidos) como ['anios', 'meses', 'dias', 'total_dias']. */
    public static function periodo(string $inicio, string $fin): array
    {
        return self::periodoDesdeDias(self::dias360($inicio, $fin));
    }

    /**
     * Meses del periodo redondeando hacia arriba. Solo se guarda en la columna duracion_meses
     * como referencia; para mostrar o evaluar duraciones se usa periodo().
     */
    public static function mesesEntre(string $inicio, string $fin): int
    {
        return (int) ceil(self::dias360($inicio, $fin) / self::DIAS_MES);
    }

    /** "1 año y 1 día", "2 años, 3 meses y 5 días", "11 meses y 30 días". */
    public static function textoPeriodo(array $p): string
    {
        $partes = [];
        if ($p['anios'] > 0) {
            $partes[] = $p['anios'] . ($p['anios'] === 1 ? ' año' : ' años');
        }
        if ($p['meses'] > 0) {
            $partes[] = $p['meses'] . ($p['meses'] === 1 ? ' mes' : ' meses');
        }
        if ($p['dias'] > 0) {
            $partes[] = $p['dias'] . ($p['dias'] === 1 ? ' día' : ' días');
        }
        if (!$partes) {
            return '0 días';
        }
        if (count($partes) === 1) {
            return $partes[0];
        }
        $ultimo = array_pop($partes);
        return implode(', ', $partes) . ' y ' . $ultimo;
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

    /** Días (base 30/360) de un tramo que cuentan para el tope de 4 años: solo desde FECHA_INICIO_TOPE. */
    private static function diasParaTope(DateTimeImmutable $desde, DateTimeImmutable $hasta): int
    {
        $corte = self::fecha(self::FECHA_INICIO_TOPE);
        if ($hasta < $corte) {
            return 0;
        }
        $inicio = $desde > $corte ? $desde : $corte;
        return self::dias360($inicio->format('Y-m-d'), $hasta->format('Y-m-d'));
    }

    /**
     * Evalúa la cadena completa (contrato inicial + renovaciones ordenadas por numero).
     * Devuelve vigencia, tiempo acumulado (años, meses y días en base comercial 30/360) y ADVERTENCIAS.
     * Las advertencias NO bloquean el registro: el historial real puede tener casos que no cumplen
     * la regla y aun así hay que poder anotarlos.
     *
     * Cómo se acumula:
     *  - Los días que dos periodos comparten se cuentan UNA sola vez.
     *  - El tiempo entre dos periodos NO cuenta, salvo que la renovación que le sigue tenga marcado
     *    incluye_tiempo_previo.
     *  Las fechas se muestran tal cual constan: aquí no se generan textos sobre las fechas.
     */
    public static function evaluarCadena(array $empleado, array $renovaciones): array
    {
        $inicio = trim((string) ($empleado['fecha_inicio_contrato'] ?? ''));
        $fin = trim((string) ($empleado['fecha_fin_contrato'] ?? ''));
        $tieneInicial = $inicio !== '' && $fin !== '';

        $periodos = [];
        if ($tieneInicial) {
            $periodos[] = ['numero' => 0, 'inicio' => $inicio, 'fin' => $fin, 'incluye_previo' => false];
        }
        foreach (array_values($renovaciones) as $i => $r) {
            $periodos[] = [
                'numero' => (int) ($r['numero'] ?? ($i + 1)),
                'inicio' => (string) $r['fecha_inicio'],
                'fin' => (string) $r['fecha_fin'],
                'incluye_previo' => !empty($r['incluye_tiempo_previo']),
            ];
        }

        $esFijo = ($empleado['tipo_de_contrato'] ?? '') === self::TIPO_CON_REGLAS_LEGALES;
        $totalDias = 0;
        $diasTope = 0;             // los que cuentan para el tope de 4 años (desde FECHA_INICIO_TOPE)
        $finCubierto = null;       // hasta qué fecha llega lo ya contado
        $mesesAnterior = null;
        $advertencias = [];

        foreach ($periodos as $p) {
            $ini = self::fecha($p['inicio']);
            $f = self::fecha($p['fin']);
            $n = $p['numero'];

            if ($finCubierto === null) {
                $totalDias += self::dias360($p['inicio'], $p['fin']);
                $diasTope += self::diasParaTope($ini, $f);
                $finCubierto = $f;
            } else {
                $diaSiguiente = $finCubierto->modify('+1 day');

                if ($ini > $diaSiguiente && $p['incluye_previo']) {
                    // Tiempo entre el periodo anterior y este: solo cuenta si quien registró lo marcó.
                    $totalDias += self::dias360($diaSiguiente->format('Y-m-d'), $ini->modify('-1 day')->format('Y-m-d'));
                    $diasTope += self::diasParaTope($diaSiguiente, $ini->modify('-1 day'));
                }

                if ($f > $finCubierto) {
                    $efectivoIni = $ini > $finCubierto ? $ini : $diaSiguiente;
                    $totalDias += self::dias360($efectivoIni->format('Y-m-d'), $f->format('Y-m-d'));
                    $diasTope += self::diasParaTope($efectivoIni, $f);
                    $finCubierto = $f;
                }
            }

            // Reglas legales: se miden con la duración propia de cada periodo, tal como consta.
            if ($esFijo && $n > 0) {
                $dias = self::dias360($p['inicio'], $p['fin']);
                $meses = intdiv($dias, self::DIAS_MES);   // se compara en meses completos: 360 vs 361 días no es "menos"
                if ($n >= 4 && $dias < self::DIAS_ANIO) {
                    $advertencias[] = "RNV{$n}: desde la 4ta renovación la duración debe ser de 1 año o más (Art. 46 CST, Ley 2466 de 2025).";
                }
                if ($mesesAnterior !== null && $meses < $mesesAnterior) {
                    $advertencias[] = "RNV{$n}: dura menos que el periodo anterior; reducir el tiempo solo es legal con un Otrosí de mutuo acuerdo.";
                }
                $mesesAnterior = $meses;
            }
        }

        $acumulado = self::periodoDesdeDias($totalDias);
        $acumuladoTexto = self::textoPeriodo($acumulado);

        $superaTope = false;
        $restanteTexto = null;
        $topeTexto = null;
        if ($esFijo && $periodos) {
            $topeDias = self::TOPE_MESES_FIJO * self::DIAS_MES;   // 4 años = 1.440 días
            $topeTexto = self::textoPeriodo(self::periodoDesdeDias($diasTope));
            $superaTope = $diasTope > $topeDias;
            if ($superaTope) {
                $advertencias[] = 'Supera los 4 años del tope (cuenta ' . $topeTexto . ', desde el '
                    . self::fecha(self::FECHA_INICIO_TOPE)->format('d/m/Y') . ' en contratos que ya estaban en curso): debe pasar a Contrato Indefinido (Ley 2466 de 2025).';
            } elseif ($diasTope < $topeDias) {
                $restanteTexto = self::textoPeriodo(self::periodoDesdeDias($topeDias - $diasTope));
            }
        }

        return [
            'inicial_texto' => $tieneInicial ? self::textoPeriodo(self::periodo($inicio, $fin)) : '—',
            'acumulado' => $acumulado,
            'acumulado_texto' => $acumuladoTexto,          // antigüedad total en el contrato
            'tope_texto' => $topeTexto,                    // lo que cuenta para el tope de 4 años (null si no aplica)
            'restante_tope_texto' => $restanteTexto,        // null si no aplica o ya completó justo los 4 años
            'requiere_indefinido' => $superaTope,
            'num_renovaciones' => count($renovaciones),
            // La vigencia llega hasta la fecha de fin más lejana de toda la cadena.
            'vigencia_fin' => $finCubierto ? $finCubierto->format('Y-m-d') : ($fin !== '' ? $fin : null),
            'advertencias' => $advertencias,
        ];
    }

    /**
     * Valida una renovación nueva o editada. $previas = renovaciones con numero menor al de esta.
     *
     * $historico    = "registrar tal cual el soporte histórico": acepta las fechas como constan, incluso si
     *                 empiezan el mismo día (o antes) de que termine el periodo anterior, sin avisos.
     * $incluyeTiempoPrevio  = si entre el periodo anterior y este queda tiempo, incluirlo en el acumulado.
     *
     * Lanza InvalidArgumentException con mensaje para el usuario si hay un error que bloquea.
     * Devuelve ['duracion_meses' => int, 'duracion_texto' => string, 'incluye_tiempo_previo' => bool, 'advertencias' => string[]].
     */
    public static function validarRenovacion(
        array $empleado,
        array $previas,
        int $numero,
        string $inicio,
        string $fin,
        bool $historico = false,
        bool $incluyeTiempoPrevio = false
    ): array {
        $problemas = self::problemasContratoInicial($empleado);
        if ($problemas) {
            throw new InvalidArgumentException($problemas[0]);
        }

        $ini = self::fecha($inicio, 'La fecha de inicio');
        $f = self::fecha($fin, 'La fecha de fin');
        if ($f < $ini) {
            throw new InvalidArgumentException('La fecha de fin no puede ser anterior a la de inicio.');
        }

        $previo = $previas ? end($previas) : null;
        $iniPrevio = self::fecha($previo ? (string) $previo['fecha_inicio'] : (string) $empleado['fecha_inicio_contrato'], 'La fecha de inicio del periodo anterior');
        $finPrevio = self::fecha($previo ? (string) $previo['fecha_fin'] : (string) $empleado['fecha_fin_contrato'], 'La fecha de fin del periodo anterior');

        // Aunque se marque "histórico", un periodo no puede empezar antes (ni el mismo día) que el anterior.
        if ($ini <= $iniPrevio) {
            throw new InvalidArgumentException(
                'La renovación debe empezar después de que empezó el periodo anterior (' . $iniPrevio->format('d/m/Y') . ').'
            );
        }
        if (!$historico && $ini <= $finPrevio) {
            throw new InvalidArgumentException(
                'La renovación debe empezar después de que termina el periodo anterior (' . $finPrevio->format('d/m/Y')
                . '). Si así consta en el soporte, marca «Registrar tal cual el soporte histórico».'
            );
        }

        $hayTiempoPrevio = $ini > $finPrevio->modify('+1 day');
        $incluyeTiempoPrevio = $incluyeTiempoPrevio && $hayTiempoPrevio;   // la casilla solo tiene efecto si hay tiempo entre los dos periodos

        $duracion = self::mesesEntre($inicio, $fin);   // se guarda en la columna duracion_meses (aproximada, solo referencia)
        $cadena = array_merge(array_values($previas), [[
            'numero' => $numero,
            'fecha_inicio' => $inicio,
            'fecha_fin' => $fin,
            'duracion_meses' => $duracion,
            'incluye_tiempo_previo' => $incluyeTiempoPrevio ? 1 : 0,
        ]]);
        $evaluacion = self::evaluarCadena($empleado, $cadena);

        // Recordatorio puntual contra errores de digitación: solo se muestra al guardar, no queda registrado.
        // Con "tal cual el soporte histórico" marcado no se dice nada: las fechas se aceptan como constan.
        $avisos = $evaluacion['advertencias'];
        if ($hayTiempoPrevio && !$historico) {
            array_unshift($avisos, 'Verifica la fecha de inicio de RNV' . $numero . ': lo habitual es el día siguiente al fin del periodo anterior ('
                . $finPrevio->modify('+1 day')->format('d/m/Y') . ').');
        }

        return [
            'duracion_meses' => $duracion,
            'duracion_texto' => self::textoPeriodo(self::periodo($inicio, $fin)),
            'incluye_tiempo_previo' => $incluyeTiempoPrevio,
            'advertencias' => $avisos,
        ];
    }
}
