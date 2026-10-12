<?php
// tests/Integracion/CrearPermisoTest.php
//
// Simula la creación de permisos por distintas vías y revisa lo que queda en la base de datos.
// Arma los datos igual que public/api/permisos_crear.php (sin sesión, sin CSRF, sin subir archivos).
//
// SEGURIDAD: solo corre contra una base cuyo nombre termina en _test. Si no, la prueba se detiene.

use PHPUnit\Framework\TestCase;

final class CrearPermisoTest extends TestCase
{
    private const CEDULA = '9900000001';           // empleado de prueba (no es una persona real)
    private const CARGO = 'Cargo de prueba (test)';

    protected function setUp(): void
    {
        $bd = $_SERVER['DB_NAME'] ?? $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: '';
        if (!str_ends_with((string) $bd, '_test')) {
            throw new RuntimeException("Las pruebas de integración se niegan a correr en la base '$bd'. Usa una base que termine en _test (ver phpunit.xml).");
        }

        $raiz = dirname(__DIR__, 2);
        require_once $raiz . '/config/database.php';
        require_once $raiz . '/src/helpers/JornadaHelper.php';
        require_once $raiz . '/src/controllers/PermisoController.php';
        require_once $raiz . '/src/models/PermisoModel.php';
        require_once $raiz . '/src/models/EmpleadoModel.php';
        require_once $raiz . '/src/models/FormatoPermisoModel.php';

        $this->limpiar();
        $pdo = getPDO();
        $pdo->prepare('INSERT IGNORE INTO cargos (nombre, activo) VALUES (?, 1)')->execute([self::CARGO]);
        $pdo->prepare('INSERT INTO empleados (cedula, nombre, cargo, tipo_de_personal, tipo_jornada, celular, estado)
                       VALUES (?, ?, ?, ?, ?, ?, ?)')
            ->execute([self::CEDULA, 'Empleado de Prueba', self::CARGO, 'Civil', 'Administrativa', '3000000000', 'activo']);
    }

    protected function tearDown(): void
    {
        $this->limpiar();
    }

    /** Borra solo lo creado por estas pruebas (identificado por la cédula de prueba). */
    private function limpiar(): void
    {
        $pdo = getPDO();
        $ids = $pdo->prepare('SELECT id FROM permisos WHERE cedula_empleado = ?');
        $ids->execute([self::CEDULA]);
        foreach ($ids->fetchAll(PDO::FETCH_COLUMN) as $id) {
            foreach (['permisos_historial', 'permisos_devoluciones', 'permisos_dias'] as $tabla) {
                $pdo->prepare("DELETE FROM $tabla WHERE permiso_id = ?")->execute([$id]);
            }
        }
        $pdo->prepare('DELETE FROM permisos WHERE cedula_empleado = ?')->execute([self::CEDULA]);
        $pdo->prepare('DELETE FROM empleados WHERE cedula = ?')->execute([self::CEDULA]);
    }

    /**
     * Crea un permiso como lo hace el endpoint.
     * $p: tipo_permiso, fecha_inicio, hora_inicio, fecha_fin, hora_fin, remunerado, es_compensatorio,
     *     fecha_horas_extra, es_devolucion, devoluciones (lista), salida_pendiente, tiene_reemplazo, cedula_reemplazo
     */
    private function crearPermiso(array $p): int
    {
        $emp = EmpleadoModel::obtenerPorCedula(self::CEDULA);
        $tipoPersonal = JornadaHelper::tipoParaCalculo($emp) ?? 'Civil';
        $salidaPendiente = !empty($p['salida_pendiente']);

        if ($salidaPendiente) {
            $rec = ['dias' => [], 'total_horas' => null];
        } else {
            $dias = $this->diasComoLoHaceElFormulario($p['fecha_inicio'], $p['hora_inicio'], $p['fecha_fin'], $p['hora_fin'], $tipoPersonal);
            $rec = PermisoController::recalcularConfirmado($dias, $tipoPersonal);
        }

        $esDevolucion = !empty($p['es_devolucion']);
        $devolucionesRec = [];
        $totalDevolucion = 0.0;
        foreach ($p['devoluciones'] ?? [] as $d) {
            $res = PermisoController::calcularHorasDevolucion($d['fecha'], $d['hora_inicio'], $d['hora_fin']);
            $this->assertTrue($res['ok'], 'La devolución debe calcularse');
            $devolucionesRec[] = ['fecha' => $d['fecha'], 'hora_inicio' => $d['hora_inicio'], 'hora_fin' => $d['hora_fin'], 'total_horas' => $res['total_horas']];
            $totalDevolucion += $res['total_horas'];
        }

        $dias = $rec['dias'];
        $ultimo = $dias ? $dias[count($dias) - 1] : null;
        $esComp = !empty($p['es_compensatorio']);
        $tieneReemplazo = !empty($p['tiene_reemplazo']);

        $id = PermisoModel::crear([
            'consecutivo' => PermisoModel::generarConsecutivo(),
            'cedula_empleado' => self::CEDULA,
            'nombre_empleado_snapshot' => $emp['nombre'],
            'cargo_empleado_snapshot' => $emp['cargo'],
            'celular_empleado_snapshot' => $emp['celular'],
            'tipo_permiso' => $p['tipo_permiso'] ?? 'Permiso',
            'motivo' => $p['motivo'] ?? 'Motivo de prueba',
            'fecha_inicio' => $p['fecha_inicio'] ?? null,
            'hora_inicio' => $salidaPendiente ? $p['hora_inicio'] : $dias[0]['hora_inicio'],
            'fecha_fin' => $salidaPendiente ? null : $ultimo['fecha'],
            'hora_fin' => $salidaPendiente ? null : $ultimo['hora_fin'],
            'total_horas' => $rec['total_horas'],
            'incluye_festivo' => 0,
            'festivo_confirmado' => 0,
            'remunerado' => !empty($p['remunerado']) ? 1 : 0,
            'es_compensatorio' => $esComp ? 1 : 0,
            'fecha_horas_extra' => $esComp ? ($p['fecha_horas_extra'] ?? null) : null,
            'es_devolucion' => $esDevolucion ? 1 : 0,
            'devolucion_fecha' => null,
            'devolucion_hora_inicio' => null,
            'devolucion_hora_fin' => null,
            'devolucion_total_horas' => $esDevolucion ? $totalDevolucion : null,
            'es_salida_pendiente_regreso' => $salidaPendiente ? 1 : 0,
            'tiene_reemplazo' => $tieneReemplazo ? 1 : 0,
            'cedula_reemplazo' => $tieneReemplazo ? ($p['cedula_reemplazo'] ?? null) : null,
            'cedula_jefe' => '9900000002',
            'foto_solicitante' => 'permisos/fotos/prueba.jpg',
            'firma_solicitante' => 'firmas/prueba.png',
            'evidencia_archivo' => null,
        ], $dias);

        if ($esDevolucion) {
            PermisoModel::crearDevoluciones($id, $devolucionesRec);
        }
        return $id;
    }

    /**
     * Replica el cálculo del formulario: consulta el horario DÍA POR DÍA
     * (public/assets/js/permiso_nuevo.js llama a permisos_calcular_horas.php por cada fecha).
     */
    private function diasComoLoHaceElFormulario(string $fi, string $hi, string $ff, string $hf, string $tipo, ?array $jornada = null): array
    {
        $dias = [];
        for ($c = new DateTime($fi); $c <= new DateTime($ff); $c->modify('+1 day')) {
            $f = $c->format('Y-m-d');
            $r = PermisoController::calcularHorasPorDias($f, $hi, $f, $hf, $tipo, $jornada);
            $this->assertTrue($r['ok'], "El día $f debe calcularse");
            $dias = array_merge($dias, $r['dias']);
        }
        return $dias;
    }

    private function fila(int $id): array
    {
        $st = getPDO()->prepare('SELECT * FROM permisos WHERE id = ?');
        $st->execute([$id]);
        $f = $st->fetch();
        $this->assertNotFalse($f, "El permiso $id debe existir");
        return $f;
    }

    private function contar(string $tabla, int $permisoId): int
    {
        $st = getPDO()->prepare("SELECT COUNT(*) FROM $tabla WHERE permiso_id = ?");
        $st->execute([$permisoId]);
        return (int) $st->fetchColumn();
    }

    public function testPermisoDeUnDiaGuardaFechaHorasYTotal(): void
    {
        $id = $this->crearPermiso(['fecha_inicio' => '2026-10-19', 'hora_inicio' => '07:00', 'fecha_fin' => '2026-10-19', 'hora_fin' => '17:24']);
        $f = $this->fila($id);
        $this->assertSame('2026-10-19', $f['fecha_inicio']);
        $this->assertSame('2026-10-19', $f['fecha_fin']);
        $this->assertEquals(8.4, (float) $f['total_horas']);
        $this->assertSame(1, $this->contar('permisos_dias', $id));
    }

    public function testPermisoDeVariosDiasCuentaCadaDia(): void
    {
        // Lunes 19 a miércoles 21 de octubre de 2026: 3 días de 8,40 h
        $id = $this->crearPermiso(['fecha_inicio' => '2026-10-19', 'hora_inicio' => '07:00', 'fecha_fin' => '2026-10-21', 'hora_fin' => '17:24']);
        $f = $this->fila($id);
        $this->assertSame('2026-10-21', $f['fecha_fin']);
        $this->assertEquals(25.2, (float) $f['total_horas']);
        $this->assertSame(3, $this->contar('permisos_dias', $id));
    }

    /**
     * FALLA A PROPÓSITO: el cálculo por RANGO (PermisoController::calcularHorasPorDias con inicio y fin
     * de varios días) cuenta cada noche como 24 h. Lo usa registrarLlegada() de las salidas pendientes.
     * Debe dar 25,2 h (3 días de 8,40 h). Se corrige la regla y esta prueba pasa.
     */
    public function testRangoDeVariosDiasNoCuentaLasNochesComoHoras(): void
    {
        $calc = PermisoController::calcularHorasPorDias('2026-10-19', '07:00', '2026-10-21', '17:24', 'Civil');
        $this->assertEquals(25.2, (float) $calc['total_horas'], 'Rango de 3 días');
    }

    /**
     * Salida el 19 a las 14:00 y regreso el 20 a las 09:00 (Civil):
     * día 19 de 14:00 a 17:24 = 3,40 h; día 20 de 07:00 a 09:00 = 2,00 h. Total 5,40 h.
     */
    public function testSalidaQueRegresaAlDiaSiguienteCuentaCadaTramo(): void
    {
        $calc = PermisoController::calcularHorasPorDias('2026-10-19', '14:00', '2026-10-20', '09:00', 'Civil');
        $this->assertTrue($calc['ok']);
        $this->assertEquals(5.4, (float) $calc['total_horas'], 'Salida 14:00 día 1, regreso 09:00 día 2');
    }

    /**
     * Jornada restringida 07:00-12:00 (5 h por día, sin almuerzo): 3 días = 15 h.
     * Antes el rango usaba 07:00-17:24 y daba 25,2 h.
     */
    public function testJornadaRestringidaCuentaSuHorarioEnElRango(): void
    {
        $jornada = JornadaHelper::reglas(['tipo_jornada' => JornadaHelper::RESTRINGIDA,
            'jornada_hora_entrada' => '07:00:00', 'jornada_hora_salida' => '12:00:00']);
        $calc = PermisoController::calcularHorasPorDias('2026-10-19', '07:00', '2026-10-21', '12:00', 'Civil', $jornada);
        $this->assertTrue($calc['ok']);
        $this->assertEquals(15.0, (float) $calc['total_horas'], 'Restringida 07:00-12:00, 3 días');
    }

    /**
     * El navegador manda horas_netas inventadas (99 h). El servidor debe recalcular
     * el día desde su fecha y horario y guardar 8,40 h.
     */
    public function testServidorIgnoraLasHorasInventadasPorElNavegador(): void
    {
        $enviado = [['fecha' => '2026-10-19', 'hora_inicio' => '07:00', 'hora_fin' => '17:24',
            'horas_brutas' => 99, 'horas_descuento_almuerzo' => 0, 'horas_netas' => 99]];
        $rec = PermisoController::recalcularConfirmado($enviado, 'Civil', JornadaHelper::reglas([]));
        $this->assertEquals(8.4, (float) $rec['total_horas'], 'Debe recalcularse, no aceptar 99 h');
        $this->assertEquals(8.4, (float) $rec['dias'][0]['horas_netas']);
    }

    public function testVacacionesRemuneradasGuardanSi(): void
    {
        $id = $this->crearPermiso(['tipo_permiso' => 'Vacaciones', 'remunerado' => true,
            'fecha_inicio' => '2026-12-14', 'hora_inicio' => '07:00', 'fecha_fin' => '2026-12-16', 'hora_fin' => '17:24']);
        $f = $this->fila($id);
        $this->assertSame('Vacaciones', $f['tipo_permiso']);
        $this->assertSame(1, (int) $f['remunerado']);
        $this->assertSame(0, (int) $f['es_compensatorio']);
        $this->assertSame(0, (int) $f['es_devolucion']);
    }

    public function testSinMarcarNadaQuedaTodoEnNo(): void
    {
        $id = $this->crearPermiso(['fecha_inicio' => '2026-10-19', 'hora_inicio' => '07:00', 'fecha_fin' => '2026-10-19', 'hora_fin' => '17:24']);
        $f = $this->fila($id);
        $this->assertSame(0, (int) $f['remunerado']);
        $this->assertSame(0, (int) $f['es_compensatorio']);
        $this->assertSame(0, (int) $f['es_devolucion']);
    }

    public function testCompensatorioGuardaLaFechaDeLasHorasExtra(): void
    {
        $id = $this->crearPermiso(['es_compensatorio' => true, 'fecha_horas_extra' => '2026-10-05',
            'fecha_inicio' => '2026-10-19', 'hora_inicio' => '07:00', 'fecha_fin' => '2026-10-19', 'hora_fin' => '17:24']);
        $f = $this->fila($id);
        $this->assertSame(1, (int) $f['es_compensatorio']);
        $this->assertSame('2026-10-05', $f['fecha_horas_extra']);
    }

    public function testDevolucionGuardaDetalleYCubreLasHoras(): void
    {
        $devs = [
            ['fecha' => '2026-10-22', 'hora_inicio' => '07:00', 'hora_fin' => '12:00'],
            ['fecha' => '2026-10-23', 'hora_inicio' => '07:00', 'hora_fin' => '11:00'],
        ];
        $id = $this->crearPermiso(['es_devolucion' => true, 'devoluciones' => $devs,
            'fecha_inicio' => '2026-10-19', 'hora_inicio' => '07:00', 'fecha_fin' => '2026-10-19', 'hora_fin' => '17:24']);
        $f = $this->fila($id);
        $this->assertSame(1, (int) $f['es_devolucion']);
        $this->assertSame(2, $this->contar('permisos_devoluciones', $id));
        $this->assertGreaterThanOrEqual((float) $f['total_horas'], (float) $f['devolucion_total_horas'],
            'Las devoluciones deben cubrir las horas del permiso');
    }

    public function testSalidaPendienteRegresoNoTieneDias(): void
    {
        $id = $this->crearPermiso(['salida_pendiente' => true, 'tipo_permiso' => 'Permiso',
            'fecha_inicio' => '2026-10-19', 'hora_inicio' => '08:00']);
        $f = $this->fila($id);
        $this->assertSame(1, (int) $f['es_salida_pendiente_regreso']);
        $this->assertNull($f['total_horas']);
        $this->assertSame(0, $this->contar('permisos_dias', $id));
    }

    public function testReemplazoGuardaLaCedula(): void
    {
        $id = $this->crearPermiso(['tiene_reemplazo' => true, 'cedula_reemplazo' => '9900000003',
            'fecha_inicio' => '2026-10-19', 'hora_inicio' => '07:00', 'fecha_fin' => '2026-10-19', 'hora_fin' => '17:24']);
        $f = $this->fila($id);
        $this->assertSame(1, (int) $f['tiene_reemplazo']);
        $this->assertSame('9900000003', $f['cedula_reemplazo']);
    }

    public function testSinReemplazoNoGuardaCedula(): void
    {
        $id = $this->crearPermiso(['fecha_inicio' => '2026-10-19', 'hora_inicio' => '07:00', 'fecha_fin' => '2026-10-19', 'hora_fin' => '17:24']);
        $f = $this->fila($id);
        $this->assertSame(0, (int) $f['tiene_reemplazo']);
        $this->assertNull($f['cedula_reemplazo']);
    }

    public function testFormatoVigenteQuedaGrabadoEnElPermiso(): void
    {
        $formato = FormatoPermisoModel::obtener();
        $id = $this->crearPermiso(['fecha_inicio' => '2026-10-19', 'hora_inicio' => '07:00', 'fecha_fin' => '2026-10-19', 'hora_fin' => '17:24']);
        $f = $this->fila($id);
        $this->assertSame($formato['codigo'], $f['formato_codigo']);
        $this->assertSame($formato['version'], (int) $f['formato_version']);
        $this->assertSame($formato['fecha'], $f['formato_fecha']);
    }

    public function testConsecutivosSonUnicosYUsanElCodigoDelFormato(): void
    {
        $codigo = FormatoPermisoModel::obtener()['codigo'];
        $a = $this->fila($this->crearPermiso(['fecha_inicio' => '2026-10-19', 'hora_inicio' => '07:00', 'fecha_fin' => '2026-10-19', 'hora_fin' => '17:24']));
        $b = $this->fila($this->crearPermiso(['fecha_inicio' => '2026-10-20', 'hora_inicio' => '07:00', 'fecha_fin' => '2026-10-20', 'hora_fin' => '17:24']));
        $this->assertNotSame($a['consecutivo'], $b['consecutivo']);
        $this->assertStringStartsWith($codigo . '-2026-', $a['consecutivo']);
        $this->assertStringStartsWith($codigo . '-2026-', $b['consecutivo']);
    }
}
