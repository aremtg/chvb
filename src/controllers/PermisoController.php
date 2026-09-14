<?php
// src/controllers/PermisoController.php (nuevo archivo)

class PermisoController {

        /**
     * Calcula horas netas de un permiso.
     *
     * Personal Civil (administrativo): jornada real de referencia es 07:00-12:00 y
     * 14:00-17:24 (fraccionada, 42h semanales según Ley 2101 de 2021). El almuerzo
     * 12:00-14:00 se descuenta por cada día calendario que el rango solicitado
     * atraviese y solape con esa franja (solo la porción real de solape, no siempre
     * 2h fijas si el permiso empieza/termina a mitad del almuerzo).
     *
     * Personal Bombero (operativo): labora por turnos rotativos programados por la
     * Dirección de Operaciones (disponibilidad y atención de emergencias), no bajo
     * la jornada fraccionada administrativa. Por eso NO aplica el descuento de
     * almuerzo fijo — se cuenta la totalidad del rango solicitado. (Punto pendiente
     * de confirmar con Talento Humano: si existe algún pacto interno de descanso
     * nocturno para turnos largos, se agregaría como regla aparte más adelante.)
     *
     * NUNCA confiar en un total_horas que venga del frontend: este método es la
     * única fuente de verdad, se llama tanto en el endpoint de "calcular en vivo"
     * como justo antes de guardar en PermisoModel::crear()/actualizar().
     */
    public static function calcularHoras(string $fechaInicio, string $horaInicio, string $fechaFin, string $horaFin, string $tipoPersonal): array {
        try {
            $inicio = new DateTime("$fechaInicio $horaInicio");
            $fin = new DateTime("$fechaFin $horaFin");
        } catch (Exception $e) {
            return ['ok' => false, 'error' => 'Fecha u hora inválida.'];
        }

        if ($fin <= $inicio) {
            return ['ok' => false, 'error' => 'La fecha/hora de fin debe ser posterior a la de inicio.'];
        }

        $segundosTotales = $fin->getTimestamp() - $inicio->getTimestamp();
        $horasBrutas = $segundosTotales / 3600;
        $horasDescuento = 0.0;

        if ($tipoPersonal === 'Civil') {
            $cursor = new DateTime($inicio->format('Y-m-d') . ' 00:00:00');
            $finDia = new DateTime($fin->format('Y-m-d') . ' 00:00:00');

            while ($cursor <= $finDia) {
                $almuerzoInicio = new DateTime($cursor->format('Y-m-d') . ' 12:00:00');
                $almuerzoFin = new DateTime($cursor->format('Y-m-d') . ' 14:00:00');

                $solapeInicio = max($inicio, $almuerzoInicio);
                $solapeFin = min($fin, $almuerzoFin);

                if ($solapeFin > $solapeInicio) {
                    $horasDescuento += ($solapeFin->getTimestamp() - $solapeInicio->getTimestamp()) / 3600;
                }

                $cursor->modify('+1 day');
            }
        }

        $horasNetas = round($horasBrutas - $horasDescuento, 2);

        return [
            'ok' => true,
            'horas_brutas' => round($horasBrutas, 2),
            'horas_descuento_almuerzo' => round($horasDescuento, 2),
            'total_horas' => max(0, $horasNetas),
        ];
    }

    /**
     * Calcula horas de devolución (mismo motor, sin descuento de almuerzo nunca,
     * porque una devolución es tiempo trabajado de más, no jornada laboral normal).
     */
    public static function calcularHorasDevolucion(string $fecha, string $horaInicio, string $horaFin): array {
        try {
            $inicio = new DateTime("$fecha $horaInicio");
            $fin = new DateTime("$fecha $horaFin");
        } catch (Exception $e) {
            return ['ok' => false, 'error' => 'Fecha u hora de devolución inválida.'];
        }

        if ($fin <= $inicio) {
            return ['ok' => false, 'error' => 'La hora de fin de devolución debe ser posterior a la de inicio.'];
        }

        $horas = round(($fin->getTimestamp() - $inicio->getTimestamp()) / 3600, 2);
        return ['ok' => true, 'total_horas' => $horas];
    }
}