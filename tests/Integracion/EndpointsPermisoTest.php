<?php
// tests/Integracion/EndpointsPermisoTest.php
//
// Llama a los endpoints reales de firma (public/api/permisos_*.php) como lo haría el navegador,
// con sesión, rol y token CSRF. Cada petición corre en un proceso PHP aparte (ep_runner.php).
//
// SEGURIDAD: solo corre contra una base que termine en _test.

use PHPUnit\Framework\TestCase;

final class EndpointsPermisoTest extends TestCase
{
    private const SOLICITANTE = '9900000001';
    private const AJENO = '9900000004';
    private const TOKEN = 'token-de-prueba-csrf';
    private const USUARIO_TH = 'th_endpoint_test';
    private const CEDULAS = [self::SOLICITANTE, self::AJENO];

    private static string $raiz;

    protected function setUp(): void
    {
        $bd = $_SERVER['DB_NAME'] ?? $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: '';
        if (!str_ends_with((string) $bd, '_test')) {
            throw new RuntimeException("Las pruebas de endpoints se niegan a correr en la base '$bd'. Usa una base que termine en _test.");
        }
        self::$raiz = dirname(__DIR__, 2);
        require_once self::$raiz . '/config/database.php';
        require_once self::$raiz . '/src/helpers/JornadaHelper.php';
        require_once self::$raiz . '/src/controllers/PermisoController.php';
        require_once self::$raiz . '/src/models/PermisoModel.php';

        $this->limpiar();
        $pdo = getPDO();
        $pdo->prepare('INSERT IGNORE INTO cargos (nombre, activo) VALUES (?, 1)')->execute(['Cargo de prueba (test)']);
        $hash = password_hash('clave-de-prueba', PASSWORD_DEFAULT);
        foreach ([self::SOLICITANTE, self::AJENO] as $i => $cedula) {
            $pdo->prepare('INSERT INTO empleados (cedula, nombre, cargo, tipo_de_personal, tipo_jornada, celular, estado)
                           VALUES (?, ?, ?, ?, ?, ?, ?)')
                ->execute([$cedula, "Empleado de prueba $i", 'Cargo de prueba (test)', 'Civil', 'Administrativa', '300000000' . $i, 'activo']);
            $pdo->prepare('INSERT INTO usuarios_empleados (cedula, password_hash, activo) VALUES (?, ?, 1)')->execute([$cedula, $hash]);
        }
        $pdo->prepare('INSERT INTO usuarios (username, password_hash, rol) VALUES (?, ?, ?)')
            ->execute([self::USUARIO_TH, $hash, 'superadmin_talento_humano']);
    }

    protected function tearDown(): void
    {
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
        $pdo->prepare("DELETE FROM usuarios_empleados WHERE cedula IN ($in)")->execute(self::CEDULAS);
        $pdo->prepare("DELETE FROM empleados WHERE cedula IN ($in)")->execute(self::CEDULAS);
        $pdo->prepare('DELETE FROM usuarios WHERE username = ?')->execute([self::USUARIO_TH]);
    }

    /** Crea un permiso en_proceso del solicitante (como permisos_crear.php). */
    private function crearPermiso(string $estado = 'en_proceso', string $cedula = self::SOLICITANTE): int
    {
        $dias = [];
        $r = PermisoController::calcularHorasPorDias('2026-10-19', '07:00', '2026-10-19', '17:24', 'Civil', JornadaHelper::reglas([]));
        $rec = PermisoController::recalcularConfirmado($r['dias'], 'Civil');
        $dia = $rec['dias'][0];
        $id = PermisoModel::crear([
            'consecutivo' => PermisoModel::generarConsecutivo(),
            'cedula_empleado' => $cedula, 'nombre_empleado_snapshot' => 'Empleado de prueba',
            'cargo_empleado_snapshot' => 'Cargo de prueba (test)', 'celular_empleado_snapshot' => '3000000000',
            'tipo_permiso' => 'Permiso', 'motivo' => 'Prueba de endpoint',
            'fecha_inicio' => '2026-10-19', 'hora_inicio' => $dia['hora_inicio'], 'fecha_fin' => '2026-10-19',
            'hora_fin' => $dia['hora_fin'], 'total_horas' => $rec['total_horas'],
            'incluye_festivo' => 0, 'festivo_confirmado' => 0, 'remunerado' => 0, 'es_compensatorio' => 0,
            'fecha_horas_extra' => null, 'es_devolucion' => 0, 'devolucion_fecha' => null,
            'devolucion_hora_inicio' => null, 'devolucion_hora_fin' => null, 'devolucion_total_horas' => null,
            'es_salida_pendiente_regreso' => 0, 'tiene_reemplazo' => 0, 'cedula_reemplazo' => null,
            'cedula_jefe' => '9900000002', 'foto_solicitante' => 'permisos/fotos/prueba.jpg',
            'firma_solicitante' => 'firmas/prueba.png', 'evidencia_archivo' => null,
        ], $rec['dias']);
        if ($estado !== 'en_proceso') {
            getPDO()->prepare('UPDATE permisos SET estado = ? WHERE id = ?')->execute([$estado, $id]);
        }
        return $id;
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

    /**
     * Ejecuta un endpoint en un proceso aparte.
     * @return array{cuerpo: string, http: int, cabeceras: string[]}
     */
    private function peticion(string $endpoint, array $sesion, array $post, bool $conToken = true, array $get = [], string $metodo = 'POST'): array
    {
        if ($conToken) {
            $sesion['csrf_token'] = self::TOKEN;
            $post['csrf_token'] = self::TOKEN;
        }
        $archivo = tempnam(sys_get_temp_dir(), 'ep_');
        file_put_contents($archivo, json_encode([
            'endpoint' => self::$raiz . '/public/api/' . $endpoint,
            'script_name' => '/chvb/public/api/' . $endpoint,
            'session' => $sesion,
            'post' => $post,
            'get' => $get,
            'method' => $metodo,
        ]));

        // Hereda TODO el entorno (en Windows PHP no arranca sin SYSTEMROOT) y fija la base de pruebas.
        $entorno = array_merge(getenv(), [
            'DB_HOST' => $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1',
            'DB_NAME' => $_SERVER['DB_NAME'] ?? 'chvb_test',
            'DB_USER' => $_SERVER['DB_USER'] ?? getenv('DB_USER') ?: 'root',
            'DB_PASS' => $_SERVER['DB_PASS'] ?? getenv('DB_PASS') ?: '',
        ]);
        $proc = proc_open([PHP_BINARY, __DIR__ . '/ep_runner.php', $archivo], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null, $entorno);
        $cuerpo = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($proc);
        @unlink($archivo);

        // Si no se fijó ningún código, PHP responde 200.
        $http = 200;
        $cabeceras = [];
        foreach (explode("\n", $err) as $linea) {
            if (str_starts_with($linea, 'HTTP=') && trim(substr($linea, 5)) !== '') $http = (int) substr($linea, 5);
        }
        return ['cuerpo' => trim($cuerpo), 'http' => $http, 'cabeceras' => $cabeceras];
    }

    private function sesionEmpleado(string $cedula): array
    {
        return ['auth_type' => 'empleado', 'empleado_cedula' => $cedula];
    }

    private function sesionTH(): array
    {
        $id = (int) getPDO()->query("SELECT id FROM usuarios WHERE username = '" . self::USUARIO_TH . "'")->fetchColumn();
        return ['auth_type' => 'admin', 'superadmin_id' => $id, 'superadmin_username' => self::USUARIO_TH,
            'superadmin_rol' => 'superadmin_talento_humano'];
    }

    // ------------------------------------------------------------------ escenarios nuevos

    private function editarPost(int $id, int $version, array $extra = []): array
    {
        return array_merge([
            'id' => $id, 'version' => $version, 'tipo_permiso' => 'Permiso',
            'motivo' => 'Motivo corregido', 'cedula_jefe' => '9900000002',
            'dias_confirmados' => json_encode([['fecha' => '2026-10-19', 'hora_inicio' => '07:00', 'hora_fin' => '17:24']]),
            'remunerado' => '0', 'es_compensatorio' => '0', 'es_devolucion' => '0',
        ], $extra);
    }

    public function testEditarUnDevueltoLoEnviaDeNuevoAFirma(): void
    {
        $id = $this->crearPermiso('devuelto');
        $r = $this->peticion('permisos_editar.php', $this->sesionEmpleado(self::SOLICITANTE),
            $this->editarPost($id, $this->version($id)));
        $json = json_decode($r['cuerpo'], true);
        $this->assertTrue($json['ok'] ?? false, $r['cuerpo']);
        $this->assertSame('por_firmar_jefe', $this->estado($id));
        $fila = getPDO()->query("SELECT motivo FROM permisos WHERE id = $id")->fetch();
        $this->assertSame('Motivo corregido', $fila['motivo']);
    }

    public function testEditarUnPermisoQueNoEstaDevueltoSeRechaza(): void
    {
        $id = $this->crearPermiso('por_firmar_jefe');
        $r = $this->peticion('permisos_editar.php', $this->sesionEmpleado(self::SOLICITANTE),
            $this->editarPost($id, $this->version($id)));
        $this->assertFalse(json_decode($r['cuerpo'], true)['ok'] ?? true);
        $this->assertSame('por_firmar_jefe', $this->estado($id));
    }

    public function testCrearCompensatorioSinOrigenSeRechazaAntesDeGuardar(): void
    {
        $r = $this->peticion('permisos_crear.php', $this->sesionEmpleado(self::SOLICITANTE), [
            'tipo_permiso' => 'Permiso', 'motivo' => 'Compensar horas', 'cedula_jefe' => '9900000002',
            'fecha_inicio' => '2026-10-19', 'dias_confirmados' => json_encode([['fecha' => '2026-10-19', 'hora_inicio' => '07:00', 'hora_fin' => '17:24']]),
            'es_compensatorio' => '1', 'fecha_horas_extra' => '2026-10-05',
            'remunerado' => '0', 'es_devolucion' => '0',
        ]);
        $json = json_decode($r['cuerpo'], true);
        $this->assertFalse($json['ok'] ?? true, $r['cuerpo']);
        $this->assertTrue(in_array('Indica el origen del compensatorio.', $json['errores'] ?? [], true), 'Debe pedir el origen');
    }

    public function testBuscadorDeFormatosEncuentraPorCedulaConPuntosYPorNombre(): void
    {
        $porPuntos = $this->peticion('formatos_empleados_buscar.php', $this->sesionTH(), [], false, ['q' => '9.900.000.001'], 'GET');
        $cedulas = array_column(json_decode($porPuntos['cuerpo'], true)['empleados'] ?? [], 'cedula');
        $this->assertContains(self::SOLICITANTE, $cedulas, 'La cédula con puntos debe encontrar al empleado');

        $porNombre = $this->peticion('formatos_empleados_buscar.php', $this->sesionTH(), [], false, ['q' => 'Empleado prueba'], 'GET');
        $cedulas = array_column(json_decode($porNombre['cuerpo'], true)['empleados'] ?? [], 'cedula');
        $this->assertContains(self::SOLICITANTE, $cedulas, 'Varias palabras deben encontrarse en el nombre');
    }

    public function testUnaPersonaAjenaNoPuedeEditar(): void
    {
        $id = $this->crearPermiso('devuelto');
        $r = $this->peticion('permisos_editar.php', $this->sesionEmpleado(self::AJENO),
            $this->editarPost($id, $this->version($id)));
        $this->assertFalse(json_decode($r['cuerpo'], true)['ok'] ?? true);
        $this->assertSame('devuelto', $this->estado($id));
    }

    public function testCadaEmpleadoSoloVeSusPropiosPermisos(): void
    {
        $propio = $this->crearPermiso('en_proceso', self::SOLICITANTE);
        $ajeno = $this->crearPermiso('en_proceso', self::AJENO);
        $r = $this->peticion('permisos_listar_propios.php', $this->sesionEmpleado(self::SOLICITANTE), [], false, [], 'GET');
        $ids = array_column(json_decode($r['cuerpo'], true)['permisos'] ?? [], 'id');
        $this->assertContains($propio, array_map('intval', $ids));
        $this->assertNotContains($ajeno, array_map('intval', $ids), 'No debe ver permisos de otra persona');
    }

    public function testTalentoHumanoVeTodosYElEmpleadoNoEntraALaBandeja(): void
    {
        $a = $this->crearPermiso('en_proceso', self::SOLICITANTE);
        $b = $this->crearPermiso('en_proceso', self::AJENO);
        $r = $this->peticion('permisos_th_listar.php', $this->sesionTH(), [], false, [], 'GET');
        $ids = array_map('intval', array_column(json_decode($r['cuerpo'], true)['permisos'] ?? [], 'id'));
        $this->assertContains($a, $ids);
        $this->assertContains($b, $ids);

        $r = $this->peticion('permisos_th_listar.php', $this->sesionEmpleado(self::SOLICITANTE), [], false, [], 'GET');
        $this->assertSame(302, $r['http'], 'El empleado no entra a la bandeja de Talento Humano');
    }

    public function testPdfSoloParaTalentoHumanoConTokenYDevuelvePdf(): void
    {
        $id = $this->crearPermiso('firmado');
        $r = $this->peticion('permisos_pdf.php', $this->sesionTH(), ['id' => $id]);
        $this->assertStringStartsWith('%PDF', $r['cuerpo'], 'Debe devolver un PDF');

        $r = $this->peticion('permisos_pdf.php', $this->sesionTH(), ['id' => $id], false);
        $this->assertSame(403, $r['http'], 'El PDF también exige token');

        $r = $this->peticion('permisos_pdf.php', $this->sesionEmpleado(self::SOLICITANTE), ['id' => $id]);
        $this->assertSame(302, $r['http'], 'El empleado no descarga el PDF desde Permisos');
    }

    // ------------------------------------------------------------------ escenarios

    public function testEnviarSinTokenCsrfSeRechaza(): void
    {
        $id = $this->crearPermiso();
        $r = $this->peticion('permisos_enviar.php', $this->sesionEmpleado(self::SOLICITANTE),
            ['id' => $id, 'version' => $this->version($id)], false);
        $this->assertSame(403, $r['http']);
        $this->assertFalse(json_decode($r['cuerpo'], true)['ok'] ?? true, 'La respuesta debe ser un error JSON');
        $this->assertSame('en_proceso', $this->estado($id));
    }

    public function testEnviarSinSesionRedirigeAlLoginDeEmpleado(): void
    {
        $id = $this->crearPermiso();
        $r = $this->peticion('permisos_enviar.php', [], ['id' => $id, 'version' => $this->version($id)]);
        // En CLI no se pueden leer las cabeceras Location; se verifica la redirección (302) y que no hizo nada.
        $this->assertSame(302, $r['http']);
        $this->assertSame('', $r['cuerpo']);
        $this->assertSame('en_proceso', $this->estado($id));
    }

    public function testElDuenoEnviaConTokenYCambiaElEstado(): void
    {
        $id = $this->crearPermiso();
        $r = $this->peticion('permisos_enviar.php', $this->sesionEmpleado(self::SOLICITANTE),
            ['id' => $id, 'version' => $this->version($id)]);
        $json = json_decode($r['cuerpo'], true);
        $this->assertTrue($json['ok'] ?? false, $r['cuerpo']);
        $this->assertSame('por_firmar_jefe', $this->estado($id));
    }

    public function testUnaPersonaAjenaNoPuedeEnviarPorLaAPI(): void
    {
        $id = $this->crearPermiso();
        $r = $this->peticion('permisos_enviar.php', $this->sesionEmpleado(self::AJENO),
            ['id' => $id, 'version' => $this->version($id)]);
        $json = json_decode($r['cuerpo'], true);
        $this->assertFalse($json['ok'] ?? true);
        $this->assertSame('en_proceso', $this->estado($id));
    }

    public function testElSolicitanteNoPuedeAnularPorLaAPI(): void
    {
        $id = $this->crearPermiso('firmado');
        $r = $this->peticion('permisos_anular.php', $this->sesionEmpleado(self::SOLICITANTE),
            ['id' => $id, 'version' => $this->version($id), 'motivo' => 'Quiero anularlo yo']);
        $json = json_decode($r['cuerpo'], true);
        $this->assertFalse($json['ok'] ?? true, 'El solicitante no puede anular aunque entre al endpoint');
        $this->assertSame('firmado', $this->estado($id));
    }

    public function testAnularSinTokenCsrfSeRechaza(): void
    {
        $id = $this->crearPermiso('firmado');
        $r = $this->peticion('permisos_anular.php', $this->sesionTH(),
            ['id' => $id, 'version' => $this->version($id), 'motivo' => 'Error'], false);
        $this->assertSame(403, $r['http']);
        $this->assertSame('firmado', $this->estado($id));
    }

    public function testTalentoHumanoAnulaUnFirmadoConTokenYMotivo(): void
    {
        $id = $this->crearPermiso('firmado');
        $r = $this->peticion('permisos_anular.php', $this->sesionTH(),
            ['id' => $id, 'version' => $this->version($id), 'motivo' => 'Error en las fechas']);
        $json = json_decode($r['cuerpo'], true);
        $this->assertTrue($json['ok'] ?? false, $r['cuerpo']);
        $this->assertSame('anulado', $this->estado($id));
    }
}
