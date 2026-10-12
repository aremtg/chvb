<?php
// src/helpers/CompensatorioOrigen.php
// Causa por la que se genera un compensatorio. Única fuente de las opciones.

final class CompensatorioOrigen
{
    public const OPCIONES = [
        'horas_extra' => 'Horas extra',
        'jornada_electoral' => 'Jornada electoral',
        'festivo_trabajado' => 'Festivo trabajado',
        'otro' => 'Otro',
    ];

    public static function valido(string $clave): bool
    {
        return array_key_exists($clave, self::OPCIONES);
    }

    /** Texto para mostrar: "Horas extra", o "Otro: detalle" cuando la causa es otra. */
    public static function etiqueta(?string $clave, ?string $detalle = null): string
    {
        if ($clave === null || !self::valido($clave)) {
            return '';
        }
        if ($clave === 'otro' && trim((string) $detalle) !== '') {
            return 'Otro: ' . trim((string) $detalle);
        }
        return self::OPCIONES[$clave];
    }
}
