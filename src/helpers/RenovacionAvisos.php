<?php
// src/helpers/RenovacionAvisos.php
// Avisos de vencimiento del módulo "Control de Renovaciones".
//
// Como el sistema no tiene tareas programadas, los avisos se generan cuando:
//   - alguien de Talento Humano abre el panel de renovaciones,
//   - la primera vez en el día que el menú lateral consulta las notificaciones (notificaciones_contar.php),
//   - o por la terminal con bin/renovaciones_avisos.php (para programarlo en el Programador de tareas).
// Es seguro llamarlo muchas veces: cada aviso se envía UNA sola vez (tabla renovaciones_avisos).
require_once __DIR__ . '/../models/RenovacionModel.php';
require_once __DIR__ . '/../models/NotificacionModel.php';

final class RenovacionAvisos
{
    /** Un contrato vencido solo genera aviso durante estos días; después sigue visible (en rojo) en el panel. */
    public const DIAS_VENCIDO_MAX = 30;

    /**
     * Crea las notificaciones que falten para todo Talento Humano (superadmin y auxiliar).
     *   - "por vencer": cuando faltan RenovacionReglas::DIAS_AVISO días o menos.
     *   - "vencido": cuando ya venció y no hay una renovación que lo cubra.
     * Devuelve cuántas notificaciones nuevas se crearon.
     */
    public static function generarPendientes(): int
    {
        $pdo = getPDO();
        $insertar = $pdo->prepare("INSERT IGNORE INTO renovaciones_avisos (cedula, vigencia_fin, tipo) VALUES (?, ?, ?)");

        $creadas = 0;
        foreach (RenovacionModel::listarVigencias() as $f) {
            $tipo = $f['estado'];
            if ($tipo === 'vigente') {
                continue;
            }
            if ($tipo === 'vencido' && $f['dias_restantes'] < -self::DIAS_VENCIDO_MAX) {
                continue;
            }

            // Primero se reserva el aviso: solo quien lo logra insertar (rowCount = 1) envía la notificación.
            $insertar->execute([$f['cedula'], $f['vigencia_fin'], $tipo]);
            if ($insertar->rowCount() !== 1) {
                continue;
            }

            $nombre = $f['nombre'];
            $fecha = RenovacionReglas::formatear($f['vigencia_fin']);
            $dias = abs((int) $f['dias_restantes']);

            if ($tipo === 'por_vencer') {
                $mensaje = $dias === 0
                    ? "El contrato de \"{$nombre}\" vence hoy ({$fecha})."
                    : "El contrato de \"{$nombre}\" vence el {$fecha}: " . ($dias === 1 ? 'falta 1 día' : "faltan {$dias} días")
                        . ' (aviso de ' . RenovacionReglas::DIAS_AVISO . ' días).';
            } else {
                $mensaje = "El contrato de \"{$nombre}\" venció el {$fecha} y no tiene una renovación registrada.";
            }

            NotificacionModel::crearParaRolesTalentoHumano(
                $f['cedula'],
                'renovacion_' . $tipo,
                $mensaje,
                '/chvb/public/renovaciones_empleado.php?cedula=' . urlencode($f['cedula'])
            );
            $creadas++;
        }

        return $creadas;
    }
}
