<?php
// tests/Integracion/FlujoPermisoTest.php
//
// Recorre el flujo de firmas de un permiso: enviar, firma del reemplazo, firma del jefe,
// devolver, rechazar, anular y registrar la llegada. Cada paso se ejecuta con la sesión
// de la persona que lo haría (solicitante, reemplazo, jefe o Talento Humano).
//
// SEGURIDAD: solo corre contra una base que termine en _test.

use PHPUnit\Framework\TestCase;

final class FlujoPermisoTest extends TestCase
{
    private const SOLICITANTE = '9900000001';
    private const JEFE = '9900000002';
    private const REEMPLAZO = '9900000003';
    private const AJENO = '9900000004';
    private const CEDULAS = [self::SOLICITANTE, self::JEFE, self::REEMPLAZO, self::AJENO];

    protected function setUp(): void
    {
        $bd = $_SERVER['DB_NAME'] ?? $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: '';
        if (!str_ends_with((string) $bd, '_test')) {
            throw new RuntimeException("Las pruebas de flujo se niegan a correr en la base '$bd'. Usa una base que termine en _test.");
        }
        $raiz = dirname(__DIR__, 2);
        require_once $raiz . '/config/database.php';
        require_once $raiz . '/src/helpers/JornadaHelper.php';
        require_once $raiz . '/src/controllers/PermisoController.php';
        require_once $raiz . '/src/models/PermisoModel.php';
        require_once $raiz . '/src/models/EmpleadoModel.php';
        require_once $raiz . '/src/models/NotificacionModel.php';

        $this->limpiar();
        $pdo = getPDO();
        $pdo->prepare('INSERT IGNORE INTO cargos (nombre, activo) VALUES (?, 1)')->execute(['Cargo de prueba (test)']);
        $ins = $pdo->prepare('INSERT INTO empleados (cedula, nombre, cargo, tipo_de_personal, tipo_jornada, celular, estado)
                              VALUES (?, ?, ?, ?, ?, ?, ?)');
        $ins->execute([self::SOLICITANTE, 'Solicitante de Prueba', 'Cargo de prueba (test)', 'Civil', 'Administrativa', '3000000001', 'activo']);
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $this->limpiar();
    }

    private function limpiar(): void
    {
        $pdo = getPDO();
        $in = implode(',', array_fill(0, count(self::CEDULAS), '?'));
        $ids = $pdo->prepare("SELECT id FROM permisos WHERE cedula_empleado IN ($in)");
        $ids->execute(self::CEDULAS);
        foreach ($ids->fetchAll(PDO::FETCH_COLUMN) as $id) {
            foreach (['permisos_historial', 'permisos_devoluciones', 'permisos_dias'] as $t) {
                $pdo->prepare("DELETE FROM $t WHERE permiso_id = ?")->execute([$id]);
            }
        }
        $pdo->prepare("DELETE FROM permisos WHERE cedula_empleado IN ($in)")->execute(self::CEDULAS);
        $pdo->prepare("DELETE FROM notificaciones WHERE cedula_empleado IN ($in)")->execute(self::CEDULAS);
        $pdo->prepare("DELETE FROM empleados WHERE cedula IN ($in)")->execute(self::CEDULAS);
    }

    /** Sesiones de cada actor. */
    private function comoSolicitante(): void { $_SESSION = ['empleado_cedula' => self::SOLICITANTE]; }
    private function comoJefe(): void { $_SESSION = ['empleado_cedula' => self::JEFE]; }
    private function comoReemplazo(): void { $_SESSION = ['empleado_cedula' => self::REEMPLAZO]; }
    private function comoAjeno(): void { $_SESSION = ['empleado_cedula' => self::AJENO]; }
    private function comoTalentoHumano(): void
    {
        $_SESSION = ['superadmin_id' => 1, 'superadmin_username' => 'th_prueba', 'superadmin_rol' => 'superadmin_talento_humano'];
    }

    /**
     * Crea un permiso en_proceso (como lo deja permisos_crear.php).
     * $opc: reemplazo (bool), salida (bool), fechas/horas.
     */
    private function crearBorrador(array $opc = []): int
    {
        $salida = !empty($opc['salida']);
        $tipoPersonal = 'Civil';
        if ($salida) {
            $dias = [];
            $total = null;
        } else {
            $dias = [];
            $ini = new DateTime($opc['fecha_inicio'] ?? '2026-10-19');
            $fin = new DateTime($opc['fecha_fin'] ?? '2026-10-19');
            for ($c = clone $ini; $c <= $fin; $c->modify('+1 day')) {
                $f = $c->format('Y-m-d');
                $r = PermisoController::calcularHorasPorDias($f, '07:00', $f, '17:24', $tipoPersonal, JornadaHelper::reglas([]));
                $dias = array_merge($dias, $r['dias']);
            }
            $rec = PermisoController::recalcularConfirmado($dias, $tipoPersonal);
            $dias = $rec['dias'];
            $total = $rec['total_horas'];
        }
        $ultimo = $dias ? $dias[count($dias) - 1] : null;
        $reemp = !empty($opc['reemplazo']);

        return PermisoModel::crear([
            'consecutivo' => PermisoModel::generarConsecutivo(),
            'cedula_empleado' => self::SOLICITANTE,
            'nombre_empleado_snapshot' => 'Solicitante de Prueba',
            'cargo_empleado_snapshot' => 'Cargo de prueba (test)',
            'celular_empleado_snapshot' => '3000000001',
            'tipo_permiso' => 'Permiso',
            'motivo' => 'Motivo de prueba del flujo',
            'fecha_inicio' => $opc['fecha_inicio'] ?? '2026-10-19',
            'hora_inicio' => $salida ? '14:00' : $dias[0]['hora_inicio'],
            'fecha_fin' => $salida ? null : $ultimo['fecha'],
            'hora_fin' => $salida ? null : $ultimo['hora_fin'],
            'total_horas' => $total,
            'incluye_festivo' => 0, 'festivo_confirmado' => 0,
            'remunerado' => 0, 'es_compensatorio' => 0, 'fecha_horas_extra' => null,
            'es_devolucion' => 0, 'devolucion_fecha' => null, 'devolucion_hora_inicio' => null,
            'devolucion_hora_fin' => null, 'devolucion_total_horas' => null,
            'es_salida_pendiente_regreso' => $salida ? 1 : 0,
            'tiene_reemplazo' => $reemp ? 1 : 0,
            'cedula_reemplazo' => $reemp ? self::REEMPLAZO : null,
            'cedula_jefe' => self::JEFE,
            'foto_solicitante' => 'permisos/fotos/prueba.jpg',
            'firma_solicitante' => 'firmas/prueba.png',
            'evidencia_archivo' => null,
        ], $dias);
    }

    private function estado(int $id): string
    {
        $st = getPDO()->prepare('SELECT estado FROM permisos WHERE id = ?');
        $st->execute([$id]);
        return (string) $st->fetchColumn();
    }

    private function version(int $id): int
    {
        $st = getPDO()->prepare('SELECT version FROM permisos WHERE id = ?');
        $st->execute([$id]);
        return (int) $st->fetchColumn();
    }

    private function historial(int $id): int
    {
        $st = getPDO()->prepare('SELECT COUNT(*) FROM permisos_historial WHERE permiso_id = ?');
        $st->execute([$id]);
        return (int) $st->fetchColumn();
    }

    // ------------------------------------------------------------------ escenarios

    public function testFlujoSinReemplazoDesdeEnviarHastaFirmado(): void
    {
        $id = $this->crearBorrador();
        $this->comoSolicitante();
        $r = PermisoController::enviar($id, $this->version($id));
        $this->assertTrue($r['ok'], $r['error'] ?? '');
        $this->assertSame('por_firmar_jefe', $this->estado($id));

        $this->comoJefe();
        $r = PermisoController::firmarJefe($id, $this->version($id), 'firmas/jefe.png', null);
        $this->assertTrue($r['ok'], $r['error'] ?? '');
        $this->assertSame('firmado', $this->estado($id));
        $this->assertGreaterThanOrEqual(2, $this->historial($id), 'Debe quedar historial de enviar y de firmar');
    }

    public function testFlujoConReemplazoPasaPorLaFirmaDelReemplazo(): void
    {
        $id = $this->crearBorrador(['reemplazo' => true]);
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));
        $this->assertSame('por_firmar_reemplazo', $this->estado($id));

        $this->comoReemplazo();
        $r = PermisoController::firmarReemplazo($id, $this->version($id), 'firmas/reemplazo.png', null);
        $this->assertTrue($r['ok'], $r['error'] ?? '');
        $this->assertSame('por_firmar_jefe', $this->estado($id));

        $this->comoJefe();
        PermisoController::firmarJefe($id, $this->version($id), 'firmas/jefe.png', null);
        $this->assertSame('firmado', $this->estado($id));
    }

    public function testElJefeNoPuedeFirmarAntesQueElReemplazo(): void
    {
        $id = $this->crearBorrador(['reemplazo' => true]);
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));

        $this->comoJefe();
        $r = PermisoController::firmarJefe($id, $this->version($id), 'firmas/jefe.png', null);
        $this->assertFalse($r['ok']);
        $this->assertSame('por_firmar_reemplazo', $this->estado($id));
    }

    public function testElReemplazoNoPuedeFirmarSinQueSeEnvie(): void
    {
        $id = $this->crearBorrador(['reemplazo' => true]);
        $this->comoReemplazo();
        $r = PermisoController::firmarReemplazo($id, $this->version($id), 'firmas/reemplazo.png', null);
        $this->assertFalse($r['ok']);
        $this->assertSame('en_proceso', $this->estado($id));
    }

    public function testUnaPersonaAjenaNoPuedeEnviar(): void
    {
        $id = $this->crearBorrador();
        $this->comoAjeno();
        $r = PermisoController::enviar($id, $this->version($id));
        $this->assertFalse($r['ok']);
        $this->assertSame('en_proceso', $this->estado($id));
    }

    public function testEnviarConVersionVencidaSeRechaza(): void
    {
        $id = $this->crearBorrador();
        $versionVieja = $this->version($id) - 1;
        $this->comoSolicitante();
        $r = PermisoController::enviar($id, $versionVieja);
        $this->assertFalse($r['ok']);
        $this->assertSame('conflicto_version', $r['error']);
        $this->assertSame('en_proceso', $this->estado($id));
    }

    public function testJefeRechazaConMotivoYYaNoPuedeFirmar(): void
    {
        $id = $this->crearBorrador();
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));

        $this->comoJefe();
        $r = PermisoController::rechazar($id, $this->version($id), 'Fechas en conflicto con otro permiso');
        $this->assertTrue($r['ok'], $r['error'] ?? '');
        $this->assertSame('rechazado', $this->estado($id));

        $r = PermisoController::firmarJefe($id, $this->version($id), 'firmas/jefe.png', null);
        $this->assertFalse($r['ok']);
        $this->assertSame('rechazado', $this->estado($id));
    }

    public function testRechazarSinMotivoSeNiega(): void
    {
        $id = $this->crearBorrador();
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));
        $this->comoJefe();
        $r = PermisoController::rechazar($id, $this->version($id), '   ');
        $this->assertFalse($r['ok']);
        $this->assertSame('por_firmar_jefe', $this->estado($id));
    }

    public function testReemplazoDevuelveYElSolicitanteVuelveAEnviar(): void
    {
        $id = $this->crearBorrador(['reemplazo' => true]);
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));

        $this->comoReemplazo();
        $r = PermisoController::devolver($id, $this->version($id), 'Falta anexar la fecha correcta');
        $this->assertTrue($r['ok'], $r['error'] ?? '');
        $this->assertSame('devuelto', $this->estado($id));

        $this->comoSolicitante();
        $r = PermisoController::enviar($id, $this->version($id));
        $this->assertTrue($r['ok'], $r['error'] ?? '');
        $this->assertSame('por_firmar_reemplazo', $this->estado($id));
    }

    public function testJefeDevuelveEnSuFirma(): void
    {
        $id = $this->crearBorrador();
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));
        $this->comoJefe();
        $r = PermisoController::devolver($id, $this->version($id), 'Revisar el motivo');
        $this->assertTrue($r['ok'], $r['error'] ?? '');
        $this->assertSame('devuelto', $this->estado($id));
    }

    public function testSoloTalentoHumanoAnulaUnPermisoFirmado(): void
    {
        $id = $this->crearBorrador();
        $this->comoTalentoHumano();
        $r = PermisoController::anular($id, $this->version($id), 'Motivo');
        $this->assertFalse($r['ok'], 'No se anula un permiso que no está firmado');

        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));
        $this->comoJefe();
        PermisoController::firmarJefe($id, $this->version($id), 'firmas/jefe.png', null);
        $this->assertSame('firmado', $this->estado($id));

        $this->comoSolicitante();
        $r = PermisoController::anular($id, $this->version($id), 'Intento del solicitante');
        $this->assertFalse($r['ok'], 'El solicitante no puede anular');

        $this->comoTalentoHumano();
        $r = PermisoController::anular($id, $this->version($id), '');
        $this->assertFalse($r['ok'], 'La anulación exige motivo');

        $r = PermisoController::anular($id, $this->version($id), 'Error en las fechas');
        $this->assertTrue($r['ok'], $r['error'] ?? '');
        $this->assertSame('anulado', $this->estado($id));
    }

    /**
     * Salida pendiente: 19 de octubre a las 14:00, regreso el 20 a las 09:00.
     * Jefe prefirma -> registro de llegada del solicitante -> firma final del jefe.
     */
    public function testSalidaPendienteCompletaElCicloConLlegada(): void
    {
        $id = $this->crearBorrador(['salida' => true]);
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));

        $this->comoJefe();
        $r = PermisoController::firmarJefe($id, $this->version($id), 'firmas/jefe.png', null);
        $this->assertTrue($r['ok'], $r['error'] ?? '');
        $this->assertSame('aprobado_pendiente_regreso', $this->estado($id));

        $this->comoSolicitante();
        $r = PermisoController::registrarLlegada($id, $this->version($id), '2026-10-20', '09:00', null);
        $this->assertTrue($r['ok'], $r['error'] ?? '');
        $this->assertSame('por_firmar_jefe_final', $this->estado($id));
        $this->assertEquals(5.4, (float) $r['total_horas'], 'Salida 14:00 día 1, regreso 09:00 día 2');

        $this->comoJefe();
        $r = PermisoController::firmarJefe($id, $this->version($id), 'firmas/jefe_final.png', null);
        $this->assertTrue($r['ok'], $r['error'] ?? '');
        $this->assertSame('firmado', $this->estado($id));
    }

    // ------------------------------------------------------------------ notificaciones

    /** Mensajes guardados para una cédula (destinatario en cedula_empleado). */
    private function mensajesPara(string $cedula): array
    {
        $st = getPDO()->prepare('SELECT mensaje FROM notificaciones WHERE cedula_empleado = ?');
        $st->execute([$cedula]);
        return $st->fetchAll(PDO::FETCH_COLUMN);
    }

    private function consecutivo(int $id): string
    {
        $st = getPDO()->prepare('SELECT consecutivo FROM permisos WHERE id = ?');
        $st->execute([$id]);
        return (string) $st->fetchColumn();
    }

    public function testEnviarConReemplazoAvisaAlReemplazoYAlJefe(): void
    {
        $id = $this->crearBorrador(['reemplazo' => true]);
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));

        $c = $this->consecutivo($id);
        $this->assertTrue((bool) array_filter($this->mensajesPara(self::REEMPLAZO), fn($m) => str_contains($m, 'permiso')), 'El reemplazo debe recibir aviso');
        $this->assertTrue((bool) array_filter($this->mensajesPara(self::JEFE), fn($m) => str_contains($m, 'permiso')), 'El jefe debe recibir aviso');
    }

    public function testLaFirmaDelReemplazoAvisaAlJefeConElConsecutivo(): void
    {
        $id = $this->crearBorrador(['reemplazo' => true]);
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));
        $this->comoReemplazo();
        PermisoController::firmarReemplazo($id, $this->version($id), 'firmas/reemplazo.png', null);

        $c = $this->consecutivo($id);
        $avisos = array_filter($this->mensajesPara(self::JEFE), fn($m) => str_contains($m, 'ya firmó') && str_contains($m, $c));
        $this->assertNotEmpty($avisos, 'El jefe debe saber que el reemplazo ya firmó, con el consecutivo');
    }

    public function testLaDevolucionLlegaAlSolicitanteConElMotivo(): void
    {
        $id = $this->crearBorrador();
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));
        $this->comoJefe();
        PermisoController::devolver($id, $this->version($id), 'Falta el motivo detallado');

        $avisos = array_filter($this->mensajesPara(self::SOLICITANTE), fn($m) => str_contains($m, 'devuelto') && str_contains($m, 'Falta el motivo detallado'));
        $this->assertNotEmpty($avisos, 'El solicitante debe recibir la devolución con el motivo');
    }

    public function testElRechazoAvisaAlSolicitanteYATalentoHumano(): void
    {
        $id = $this->crearBorrador();
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));
        $this->comoJefe();
        PermisoController::rechazar($id, $this->version($id), 'Conflicto de fechas');

        $c = $this->consecutivo($id);
        $th = getPDO()->prepare("SELECT COUNT(*) FROM notificaciones WHERE cedula_empleado = ? AND campo = 'permiso_rechazado' AND mensaje LIKE ?");
        $th->execute([self::SOLICITANTE, '%Conflicto de fechas%']);
        $this->assertGreaterThanOrEqual(1, (int) $th->fetchColumn(), 'Talento Humano debe ver el rechazo con su motivo');
    }

    public function testLaFirmaFinalAvisaAlSolicitante(): void
    {
        $id = $this->crearBorrador();
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));
        $this->comoJefe();
        PermisoController::firmarJefe($id, $this->version($id), 'firmas/jefe.png', null);

        $avisos = array_filter($this->mensajesPara(self::SOLICITANTE), fn($m) => str_contains($m, 'firmado y aprobado'));
        $this->assertNotEmpty($avisos, 'El solicitante debe saber que su permiso quedó aprobado');
    }

    // ------------------------------------------------------------------ reglas nuevas

    private function firmadoListo(): int
    {
        $id = $this->crearBorrador();
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));
        $this->comoJefe();
        PermisoController::firmarJefe($id, $this->version($id), 'firmas/jefe.png', null);
        $this->assertSame('firmado', $this->estado($id));
        return $id;
    }

    public function testElJefeAnulaUnFirmadoYElSolicitanteNo(): void
    {
        $id = $this->firmadoListo();

        $this->comoSolicitante();
        $r = PermisoController::anular($id, $this->version($id), 'Quiero anularlo yo');
        $this->assertFalse($r['ok'], 'El solicitante no puede anular');
        $this->assertSame('firmado', $this->estado($id));

        $this->comoJefe();
        $r = PermisoController::anular($id, $this->version($id), 'Error en la firma');
        $this->assertTrue($r['ok'], $r['error'] ?? '');
        $this->assertSame('anulado', $this->estado($id));
    }

    public function testElJefeNoPuedeAnularUnPermisoQueNoEstaFirmado(): void
    {
        $id = $this->crearBorrador();
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));
        $this->comoJefe();
        $r = PermisoController::anular($id, $this->version($id), 'Motivo');
        $this->assertFalse($r['ok']);
        $this->assertSame('por_firmar_jefe', $this->estado($id));
    }

    public function testTalentoHumanoNoPuedeRechazarSoloElJefe(): void
    {
        $id = $this->crearBorrador();
        $this->comoSolicitante();
        PermisoController::enviar($id, $this->version($id));
        $this->comoTalentoHumano();
        $r = PermisoController::rechazar($id, $this->version($id), 'Motivo');
        $this->assertFalse($r['ok'], 'Rechazar es solo del jefe');
        $this->assertSame('por_firmar_jefe', $this->estado($id));
    }

    public function testAlertaIncluyeLosProximosSinFirmaYNoLosLejanos(): void
    {
        $cerca = $this->crearBorrador(['fecha_inicio' => date('Y-m-d', strtotime('+1 day')), 'fecha_fin' => date('Y-m-d', strtotime('+1 day'))]);
        $lejos = $this->crearBorrador(['fecha_inicio' => date('Y-m-d', strtotime('+30 days')), 'fecha_fin' => date('Y-m-d', strtotime('+30 days'))]);
        $this->comoSolicitante();
        PermisoController::enviar($cerca, $this->version($cerca));
        PermisoController::enviar($lejos, $this->version($lejos));

        $ids = array_map('intval', array_column(PermisoModel::pendientesProximosAVencer(5), 'id'));
        $this->assertContains($cerca, $ids, 'Un permiso de mañana sin firma debe aparecer en la alerta');
        $this->assertNotContains($lejos, $ids, 'Un permiso dentro de 30 días no debe aparecer todavía');
        $this->assertSame('por_firmar_jefe', $this->estado($cerca), 'La alerta no cambia el estado');
    }
}
