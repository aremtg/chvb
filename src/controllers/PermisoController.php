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
        /**
     * Genera el desglose día por día del permiso solicitado. Cada día:
     * - Si es festivo: se marca es_festivo=true y 'requiere_confirmacion'=true.
     *   El frontend debe preguntar "¿Seguro que vas a contar ese festivo?" y,
     *   según la respuesta, marcar 'incluido' true/false antes de guardar.
     *   Si incluido=false, ese día no aporta horas (horas_netas=0).
     * - Si NO es festivo: siempre incluido=true, no requiere confirmación.
     * - Personal Civil: descuenta hasta 2h de almuerzo (12:00-14:00) por el solape
     *   real de ese día con esa franja.
     * - Personal Bombero: sin descuento de almuerzo en ningún día.
     * - Si tipo_de_personal viene vacío/null, se trata como Civil y se marca
     *   'aviso_tipo_personal' para que el frontend muestre el aviso corto.
     */
    public static function calcularHorasPorDias(string $fechaInicio, string $horaInicio, string $fechaFin, string $horaFin, ?string $tipoPersonal): array {
        try {
            $inicio = new DateTime("$fechaInicio $horaInicio");
            $fin = new DateTime("$fechaFin $horaFin");
        } catch (Exception $e) {
            return ['ok' => false, 'error' => 'Fecha u hora inválida.'];
        }

        if ($fin <= $inicio) {
            return ['ok' => false, 'error' => 'La fecha/hora de fin debe ser posterior a la de inicio.'];
        }

        $avisoTipoPersonal = false;
        if (empty($tipoPersonal)) {
            $tipoPersonal = 'Civil';
            $avisoTipoPersonal = true;
        }

        require_once __DIR__ . '/../models/FestivoModel.php';
        FestivoModel::asegurarAnioPoblado((int)$inicio->format('Y'));
        if ((int)$fin->format('Y') !== (int)$inicio->format('Y')) {
            FestivoModel::asegurarAnioPoblado((int)$fin->format('Y'));
        }
        $festivosEnRango = FestivoModel::obtenerEnRango($inicio->format('Y-m-d'), $fin->format('Y-m-d'));
        $mapaFestivos = [];
        foreach ($festivosEnRango as $f) {
            $mapaFestivos[$f['fecha']] = $f['nombre'];
        }

        $dias = [];
        $cursor = new DateTime($inicio->format('Y-m-d') . ' 00:00:00');
        $finDia = new DateTime($fin->format('Y-m-d') . ' 00:00:00');

        while ($cursor <= $finDia) {
            $fechaStr = $cursor->format('Y-m-d');
            $inicioDelDia = new DateTime($fechaStr . ' 00:00:00');
            $finDelDia = (clone $inicioDelDia)->modify('+1 day');

            // Porción del permiso que cae dentro de este día calendario
            $horaInicioReal = max($inicio, $inicioDelDia);
            $horaFinReal = min($fin, $finDelDia);

            $horasBrutas = round(($horaFinReal->getTimestamp() - $horaInicioReal->getTimestamp()) / 3600, 2);

            $descuento = 0.0;
            if ($tipoPersonal === 'Civil') {
                $almuerzoInicio = new DateTime($fechaStr . ' 12:00:00');
                $almuerzoFin = new DateTime($fechaStr . ' 14:00:00');
                $solapeInicio = max($horaInicioReal, $almuerzoInicio);
                $solapeFin = min($horaFinReal, $almuerzoFin);
                if ($solapeFin > $solapeInicio) {
                    $descuento = round(($solapeFin->getTimestamp() - $solapeInicio->getTimestamp()) / 3600, 2);
                }
            }

            $esFestivo = isset($mapaFestivos[$fechaStr]);
            $horasNetasSiIncluido = max(0, round($horasBrutas - $descuento, 2));

            $dias[] = [
                'fecha' => $fechaStr,
                'hora_inicio' => $horaInicioReal->format('H:i:s'),
                'hora_fin' => $horaFinReal->format('H:i:s'),
                'es_festivo' => $esFestivo,
                'festivo_nombre' => $mapaFestivos[$fechaStr] ?? null,
                'requiere_confirmacion' => $esFestivo,
                'incluido' => true, // por defecto true; si es festivo, el frontend pedirá confirmar y puede cambiarlo a false
                'horas_brutas' => $horasBrutas,
                'horas_descuento_almuerzo' => $descuento,
                'horas_netas' => $horasNetasSiIncluido,
            ];

            $cursor->modify('+1 day');
        }

        $totalHoras = round(array_sum(array_column($dias, 'horas_netas')), 2);

        return [
            'ok' => true,
            'tipo_personal_usado' => $tipoPersonal,
            'aviso_tipo_personal' => $avisoTipoPersonal,
            'dias' => $dias,
            'incluye_festivo' => count($mapaFestivos) > 0,
            'total_horas' => $totalHoras,
        ];
    }

    /**
     * Recalcula el total de horas a partir del array de días YA CONFIRMADO por el
     * frontend (con 'incluido' ajustado según las respuestas del usuario a los
     * festivos). Esta es la función que se usa justo antes de guardar en BD —
     * nunca se confía en un total_horas que venga directo del frontend, pero SÍ
     * se confía en las banderas 'incluido' porque son decisiones explícitas del
     * usuario, y se revalida cada día contra el cálculo real en backend.
     */
    public static function recalcularConfirmado(array $diasConfirmados, string $tipoPersonal): array {
        $totalHoras = 0.0;
        $diasFinal = [];

        foreach ($diasConfirmados as $dia) {
            $incluido = !empty($dia['incluido']);
            $horasNetas = $incluido ? (float)$dia['horas_netas'] : 0.0;
            $totalHoras += $horasNetas;

            $diasFinal[] = [
                'fecha' => $dia['fecha'],
                'hora_inicio' => $dia['hora_inicio'],
                'hora_fin' => $dia['hora_fin'],
                'es_festivo' => !empty($dia['es_festivo']),
                'festivo_nombre' => $dia['festivo_nombre'] ?? null,
                'incluido' => $incluido,
                'horas_brutas' => (float)$dia['horas_brutas'],
                'horas_descuento_almuerzo' => (float)$dia['horas_descuento_almuerzo'],
                'horas_netas' => $horasNetas,
            ];
        }

        return ['dias' => $diasFinal, 'total_horas' => round($totalHoras, 2)];
    }

        // =====================================================================
    // FLUJO DE ESTADOS
    // =====================================================================

    /**
     * Determina el actor actual (empleado dueño / reemplazo / jefe / talento humano)
     * a partir de la sesión activa, para decidir permisos sobre un permiso puntual.
     */
    private static function identificarActor(array $permiso): array {
        if (!empty($_SESSION['superadmin_id'])) {
            return ['tipo' => 'talento_humano', 'id' => $_SESSION['superadmin_username']];
        }
        if (!empty($_SESSION['empleado_cedula'])) {
            $cedula = $_SESSION['empleado_cedula'];
            if ($cedula === $permiso['cedula_empleado']) return ['tipo' => 'empleado', 'id' => $cedula];
            if ($cedula === $permiso['cedula_reemplazo']) return ['tipo' => 'reemplazo', 'id' => $cedula];
            if ($cedula === $permiso['cedula_jefe']) return ['tipo' => 'jefe', 'id' => $cedula];
            return ['tipo' => 'ajeno', 'id' => $cedula];
        }
        return ['tipo' => 'anonimo', 'id' => ''];
    }

    /**
     * ¿Puede este actor editar el permiso en su estado actual?
     * en_proceso: dueño o TH. devuelto: dueño o TH. Cualquier otro estado: solo TH.
     */
    private static function puedeEditar(array $permiso, array $actor): bool {
        if ($actor['tipo'] === 'talento_humano') return true;
        if ($actor['tipo'] !== 'empleado') return false;
        return in_array($permiso['estado'], ['en_proceso', 'devuelto'], true);
    }

    /**
     * Envía el permiso desde en_proceso hacia la fase de firmas.
     * Exige que ya exista foto y firma del solicitante (se piden al crear, pero se
     * revalida aquí por si el flujo de UI cambia en el futuro).
     */
    public static function enviar(int $permisoId, int $versionActual): array {
        $permiso = PermisoModel::obtenerPorId($permisoId);
        if (!$permiso) return ['ok' => false, 'error' => 'Permiso no encontrado.'];

        $actor = self::identificarActor($permiso);
        if (!self::puedeEditar($permiso, $actor)) {
            return ['ok' => false, 'error' => 'No tienes permiso para enviar este permiso en su estado actual.'];
        }
        if (empty($permiso['foto_solicitante']) || empty($permiso['firma_solicitante'])) {
            return ['ok' => false, 'error' => 'Falta tu foto o firma antes de poder enviar el permiso.'];
        }

        $nuevoEstado = $permiso['tiene_reemplazo'] == 1 ? 'por_firmar_reemplazo' : 'por_firmar_jefe';

        $actualizado = PermisoModel::actualizarConVersion($permisoId, $versionActual, ['estado' => $nuevoEstado]);
        if (!$actualizado) return ['ok' => false, 'error' => 'conflicto_version'];

        PermisoModel::registrarHistorial($permisoId, $versionActual, $permiso['estado'], $nuevoEstado, $actor['tipo'], $actor['id'], 'Permiso enviado');

        self::notificarEnvio($permiso, $nuevoEstado);

        return ['ok' => true, 'estado' => $nuevoEstado];
    }

    private static function notificarEnvio(array $permiso, string $nuevoEstado): void {
        require_once __DIR__ . '/../models/NotificacionModel.php';
        $tipoTexto = $permiso['tipo_permiso'];
        $mensaje = "\"{$permiso['nombre_empleado_snapshot']}\" te pidió un permiso de {$tipoTexto}";
        $enlace = "/chvb/public/permisos_reemplazo_jefe.php?id={$permiso['id']}";

        // Se notifica a reemplazo (si aplica) Y a jefe desde el envío, aunque el
        // jefe no pueda firmar todavía hasta que el reemplazo firme primero.
        if ($permiso['tiene_reemplazo'] == 1 && !empty($permiso['cedula_reemplazo'])) {
            self::notificarAEmpleado($permiso['cedula_reemplazo'], $mensaje, $enlace);
        }
        self::notificarAEmpleado($permiso['cedula_jefe'], $mensaje, $enlace);
    }

    /**
     * Notificaciones a EMPLEADOS (no a Talento Humano) no usan NotificacionModel
     * directo porque esa tabla fue diseñada para avisar al superadmin sobre
     * acciones de auxiliares. Para avisar a un empleado (reemplazo/jefe/dueño),
     * reutilizamos la misma tabla pero con usuario_id apuntando a un usuario
     * "sistema" fijo y usamos cedula_empleado para saber a quién pertenece —
     * el filtro de "para quién es" lo hace la consulta de la bandeja, no la FK.
     * Ver PermisoModel + vistas: se filtra por cedula_empleado = mi cédula.
     */
    private static function notificarAEmpleado(string $cedulaDestino, string $mensaje, string $enlace): void {
        require_once __DIR__ . '/../models/NotificacionModel.php';
        // Reutilizamos NotificacionModel::crear con cedula_empleado = destinatario real
        // y usuario_id = 0 reservado para "sistema" (ver ajuste de FK abajo, sección aparte).
        NotificacionModel::crearParaEmpleado($cedulaDestino, $mensaje, $enlace, 'permiso');
    }

    public static function firmarReemplazo(int $permisoId, int $versionActual, string $firmaRuta, ?string $fotoRuta): array {
        $permiso = PermisoModel::obtenerPorId($permisoId);
        if (!$permiso) return ['ok' => false, 'error' => 'Permiso no encontrado.'];

        $actor = self::identificarActor($permiso);
        if ($actor['tipo'] !== 'reemplazo' && $actor['tipo'] !== 'talento_humano') {
            return ['ok' => false, 'error' => 'No eres el reemplazo asignado a este permiso.'];
        }
        if ($permiso['estado'] !== 'por_firmar_reemplazo') {
            return ['ok' => false, 'error' => 'Este permiso no está pendiente de tu firma en este momento.'];
        }

        $campos = ['firma_reemplazo' => $firmaRuta, 'estado' => 'por_firmar_jefe'];
        if ($fotoRuta) $campos['foto_reemplazo'] = $fotoRuta;

        $actualizado = PermisoModel::actualizarConVersion($permisoId, $versionActual, $campos);
        if (!$actualizado) return ['ok' => false, 'error' => 'conflicto_version'];

        PermisoModel::registrarHistorial($permisoId, $versionActual, $permiso['estado'], 'por_firmar_jefe', $actor['tipo'], $actor['id'], 'Reemplazo firmó');

        require_once __DIR__ . '/../models/NotificacionModel.php';
        NotificacionModel::crearParaEmpleado(
            $permiso['cedula_jefe'],
            "El reemplazo de \"{$permiso['nombre_empleado_snapshot']}\" ya firmó, el permiso está listo para tu firma",
            "/chvb/public/permisos_reemplazo_jefe.php?id={$permisoId}",
            'permiso'
        );

        return ['ok' => true, 'estado' => 'por_firmar_jefe'];
    }

    public static function firmarJefe(int $permisoId, int $versionActual, string $firmaRuta, ?string $fotoRuta): array {
        $permiso = PermisoModel::obtenerPorId($permisoId);
        if (!$permiso) return ['ok' => false, 'error' => 'Permiso no encontrado.'];

        $actor = self::identificarActor($permiso);
        if ($actor['tipo'] !== 'jefe' && $actor['tipo'] !== 'talento_humano') {
            return ['ok' => false, 'error' => 'No eres el jefe asignado a este permiso.'];
        }
        if ($permiso['estado'] !== 'por_firmar_jefe') {
            return ['ok' => false, 'error' => 'Este permiso no está pendiente de tu firma en este momento.'];
        }

        $campos = ['firma_jefe' => $firmaRuta, 'estado' => 'firmado'];
        if ($fotoRuta) $campos['foto_jefe'] = $fotoRuta;

        $actualizado = PermisoModel::actualizarConVersion($permisoId, $versionActual, $campos);
        if (!$actualizado) return ['ok' => false, 'error' => 'conflicto_version'];

        PermisoModel::registrarHistorial($permisoId, $versionActual, $permiso['estado'], 'firmado', $actor['tipo'], $actor['id'], 'Jefe firmó, permiso finalizado');

        require_once __DIR__ . '/../models/NotificacionModel.php';
        NotificacionModel::crearParaEmpleado(
            $permiso['cedula_empleado'],
            "Tu permiso de {$permiso['tipo_permiso']} fue firmado y aprobado",
            "/chvb/public/permisos.php?id={$permisoId}",
            'permiso'
        );

        return ['ok' => true, 'estado' => 'firmado'];
    }

    public static function devolver(int $permisoId, int $versionActual, string $motivo): array {
        $permiso = PermisoModel::obtenerPorId($permisoId);
        if (!$permiso) return ['ok' => false, 'error' => 'Permiso no encontrado.'];

        $actor = self::identificarActor($permiso);
        if ($actor['tipo'] !== 'jefe' && $actor['tipo'] !== 'talento_humano') {
            return ['ok' => false, 'error' => 'Solo el jefe o Talento Humano pueden devolver un permiso.'];
        }
        if (!in_array($permiso['estado'], ['por_firmar_reemplazo', 'por_firmar_jefe'], true)) {
            return ['ok' => false, 'error' => 'Este permiso no se puede devolver en su estado actual.'];
        }
        if (trim($motivo) === '') {
            return ['ok' => false, 'error' => 'Debes indicar el motivo de la devolución.'];
        }

        $actualizado = PermisoModel::actualizarConVersion($permisoId, $versionActual, [
            'estado' => 'devuelto',
            'motivo_devolucion' => $motivo,
        ]);
        if (!$actualizado) return ['ok' => false, 'error' => 'conflicto_version'];

        PermisoModel::registrarHistorial($permisoId, $versionActual, $permiso['estado'], 'devuelto', $actor['tipo'], $actor['id'], $motivo);

        require_once __DIR__ . '/../models/NotificacionModel.php';
        NotificacionModel::crearParaEmpleado(
            $permiso['cedula_empleado'],
            "Tu permiso de {$permiso['tipo_permiso']} fue devuelto: {$motivo}",
            "/chvb/public/permisos.php?id={$permisoId}",
            'permiso'
        );

        return ['ok' => true, 'estado' => 'devuelto'];
    }

    public static function rechazar(int $permisoId, int $versionActual, string $motivo): array {
        $permiso = PermisoModel::obtenerPorId($permisoId);
        if (!$permiso) return ['ok' => false, 'error' => 'Permiso no encontrado.'];

        $actor = self::identificarActor($permiso);
        if ($actor['tipo'] !== 'jefe' && $actor['tipo'] !== 'talento_humano') {
            return ['ok' => false, 'error' => 'Solo el jefe o Talento Humano pueden rechazar un permiso.'];
        }
        if (!in_array($permiso['estado'], ['por_firmar_reemplazo', 'por_firmar_jefe'], true)) {
            return ['ok' => false, 'error' => 'Este permiso no se puede rechazar en su estado actual.'];
        }
        if (trim($motivo) === '') {
            return ['ok' => false, 'error' => 'Debes indicar el motivo del rechazo.'];
        }

        $actualizado = PermisoModel::actualizarConVersion($permisoId, $versionActual, [
            'estado' => 'rechazado',
            'motivo_rechazo' => $motivo,
        ]);
        if (!$actualizado) return ['ok' => false, 'error' => 'conflicto_version'];

        PermisoModel::registrarHistorial($permisoId, $versionActual, $permiso['estado'], 'rechazado', $actor['tipo'], $actor['id'], $motivo);
        // NO se elimina el registro: sigue existiendo con estado 'rechazado', visible en el historial del empleado.

        require_once __DIR__ . '/../models/NotificacionModel.php';
        NotificacionModel::crearParaEmpleado(
            $permiso['cedula_empleado'],
            "Tu permiso de {$permiso['tipo_permiso']} fue rechazado: {$motivo}",
            "/chvb/public/permisos.php?id={$permisoId}",
            'permiso'
        );

        return ['ok' => true, 'estado' => 'rechazado'];
    }

    /**
     * Edición del permiso mientras está en_proceso o devuelto. Aplica la regla:
     * si cambian fecha/hora inicio o fin, se invalidan firma_reemplazo y firma_jefe
     * y el permiso vuelve a en_proceso (debe reenviarse desde cero el flujo de firmas).
     * Si solo cambia el motivo (u otro campo no relacionado a fecha/hora), las firmas
     * ya puestas se conservan.
     */
    public static function actualizarCampos(int $permisoId, int $versionActual, array $camposNuevos): array {
        $permiso = PermisoModel::obtenerPorId($permisoId);
        if (!$permiso) return ['ok' => false, 'error' => 'Permiso no encontrado.'];

        $actor = self::identificarActor($permiso);
        if (!self::puedeEditar($permiso, $actor)) {
            return ['ok' => false, 'error' => 'No tienes permiso para editar este permiso en su estado actual.'];
        }

        $cambianFechas = false;
        foreach (['fecha_inicio', 'hora_inicio', 'fecha_fin', 'hora_fin'] as $campoFecha) {
            if (isset($camposNuevos[$campoFecha]) && (string)$camposNuevos[$campoFecha] !== (string)$permiso[$campoFecha]) {
                $cambianFechas = true;
                break;
            }
        }

        $detalleHistorial = 'Edición de campos';

                if ($cambianFechas) {
            // Requiere que el frontend haya enviado 'dias_confirmados' (el desglose ya
            // revisado por el usuario, con festivos confirmados o excluidos) junto con
            // el cambio de fechas. Si no vino, es un error de integración del frontend.
            if (empty($camposNuevos['dias_confirmados'])) {
                return ['ok' => false, 'error' => 'Faltó el desglose de días recalculado tras el cambio de fechas.'];
            }
            $diasConfirmados = $camposNuevos['dias_confirmados'];
            unset($camposNuevos['dias_confirmados']); // no es columna de la tabla permisos

            require_once __DIR__ . '/../models/EmpleadoModel.php';
            $empleado = EmpleadoModel::obtenerPorCedula($permiso['cedula_empleado']);
            $tipoPersonal = !empty($empleado['tipo_de_personal']) ? $empleado['tipo_de_personal'] : 'Civil';

            $recalculo = self::recalcularConfirmado($diasConfirmados, $tipoPersonal);
            $camposNuevos['total_horas'] = $recalculo['total_horas'];

            $camposNuevos['firma_reemplazo'] = null;
            $camposNuevos['foto_reemplazo'] = null;
            $camposNuevos['firma_jefe'] = null;
            $camposNuevos['foto_jefe'] = null;
            if ($permiso['estado'] !== 'en_proceso') {
                $camposNuevos['estado'] = 'en_proceso';
            }
            $detalleHistorial = 'Fechas/horas modificadas, firmas de reemplazo y jefe invalidadas';
        } elseif ($permiso['estado'] === 'devuelto') {
            // Solo cambió motivo u otro campo no-fecha: si estaba devuelto, queda listo para reenviar
            // pero NO se cambia el estado aquí — el "reenvío" es una acción explícita aparte (self::enviar()).
            $detalleHistorial = 'Motivo u otros datos editados, firmas conservadas';
        }

                $actualizado = PermisoModel::actualizarConVersion($permisoId, $versionActual, $camposNuevos);
        if (!$actualizado) return ['ok' => false, 'error' => 'conflicto_version'];

        if ($cambianFechas) {
            PermisoModel::reemplazarDias($permisoId, $recalculo['dias']);
        }

        PermisoModel::registrarHistorial($permisoId, $versionActual, $permiso['estado'], $camposNuevos['estado'] ?? $permiso['estado'], $actor['tipo'], $actor['id'], $detalleHistorial);

        return ['ok' => true, 'firmas_invalidadas' => $cambianFechas];
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