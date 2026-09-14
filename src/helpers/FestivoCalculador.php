<?php
// src/helpers/FestivoCalculador.php (nuevo archivo)

class FestivoCalculador {

    /**
     * Calcula el Domingo de Pascua para un año dado (algoritmo de Meeus/Jones/Butcher).
     * Devuelve un objeto DateTime.
     */
    private static function domingoPascua(int $anio): DateTime {
        $a = $anio % 19;
        $b = intdiv($anio, 100);
        $c = $anio % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $mes = intdiv($h + $l - 7 * $m + 114, 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

        return new DateTime(sprintf('%04d-%02d-%02d', $anio, $mes, $dia));
    }

    /** Traslada una fecha al lunes siguiente si no cae ya en lunes (Ley Emiliani). */
    private static function trasladarALunes(DateTime $fecha): DateTime {
        $copia = clone $fecha;
        $diaSemana = (int) $copia->format('N'); // 1 = lunes ... 7 = domingo
        if ($diaSemana !== 1) {
            $diasAAgregar = (8 - $diaSemana) % 7;
            $copia->modify("+{$diasAAgregar} days");
        }
        return $copia;
    }

    /**
     * Genera los 18 festivos oficiales de Colombia para el año dado.
     * Devuelve un array de ['fecha' => 'Y-m-d', 'nombre' => '...'].
     */
    public static function calcularParaAnio(int $anio): array {
        $pascua = self::domingoPascua($anio);
        $festivos = [];

        // Fechas inamovibles
        $festivos[] = ['fecha' => "$anio-01-01", 'nombre' => 'Año Nuevo'];
        $festivos[] = ['fecha' => "$anio-05-01", 'nombre' => 'Día del Trabajo'];
        $festivos[] = ['fecha' => "$anio-07-20", 'nombre' => 'Día de la Independencia'];
        $festivos[] = ['fecha' => "$anio-08-07", 'nombre' => 'Batalla de Boyacá'];
        $festivos[] = ['fecha' => "$anio-12-08", 'nombre' => 'Inmaculada Concepción'];
        $festivos[] = ['fecha' => "$anio-12-25", 'nombre' => 'Navidad'];

        // Dependen de Pascua (fechas fijas relativas, sin traslado)
        $juevesSanto = (clone $pascua)->modify('-3 days');
        $viernesSanto = (clone $pascua)->modify('-2 days');
        $festivos[] = ['fecha' => $juevesSanto->format('Y-m-d'), 'nombre' => 'Jueves Santo'];
        $festivos[] = ['fecha' => $viernesSanto->format('Y-m-d'), 'nombre' => 'Viernes Santo'];

        // Ley Emiliani: se trasladan al lunes siguiente
        $trasladables = [
            ['base' => new DateTime("$anio-01-06"), 'nombre' => 'Reyes Magos'],
            ['base' => new DateTime("$anio-03-19"), 'nombre' => 'Día de San José'],
            ['base' => (clone $pascua)->modify('+39 days'), 'nombre' => 'Ascensión de Jesús'],
            ['base' => (clone $pascua)->modify('+60 days'), 'nombre' => 'Corpus Christi'],
            ['base' => (clone $pascua)->modify('+68 days'), 'nombre' => 'Sagrado Corazón'],
            ['base' => new DateTime("$anio-06-29"), 'nombre' => 'San Pedro y San Pablo'],
            ['base' => new DateTime("$anio-07-09"), 'nombre' => 'Día de Nuestra Señora de Chiquinquirá'],
            ['base' => new DateTime("$anio-08-15"), 'nombre' => 'Asunción de la Virgen'],
            ['base' => new DateTime("$anio-10-12"), 'nombre' => 'Día de la Raza'],
            ['base' => new DateTime("$anio-11-01"), 'nombre' => 'Todos los Santos'],
            ['base' => new DateTime("$anio-11-11"), 'nombre' => 'Independencia de Cartagena'],
        ];

        foreach ($trasladables as $t) {
            $trasladado = self::trasladarALunes($t['base']);
            $festivos[] = ['fecha' => $trasladado->format('Y-m-d'), 'nombre' => $t['nombre']];
        }

        usort($festivos, fn($a, $b) => strcmp($a['fecha'], $b['fecha']));
        return $festivos;
    }
}