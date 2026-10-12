<?php
// tests/Integracion/AvisosAlarmasTest.php
// Los avisos de renovación (Panel de Alarmas) usan los mismos plazos que tenían las notificaciones:
// por vencer hasta RenovacionReglas::DIAS_AVISO (60) días antes, y vencidos hasta DIAS_VENCIDO_MAX (30) días después.
// SEGURIDAD: solo corre contra una base que termine en _test.

use PHPUnit\Framework\TestCase;

final class AvisosAlarmasTest extends TestCase
{
    private const CEDULAS = ['9900000011', '9900000012', '9900000013', '9900000014'];

    protected function setUp(): void
    {
        $bd = $_SERVER['DB_NAME'] ?? $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: '';
        if (!str_ends_with((string) $bd, '_test')) {
            throw new RuntimeException("Las pruebas de avisos se niegan a correr en la base '$bd'.");
        }
        $raiz = dirname(__DIR__, 2);
        require_once $raiz . '/config/database.php';
        require_once $raiz . '/src/helpers/RenovacionReglas.php';
        require_once $raiz . '/src/models/RenovacionModel.php';
        $this->limpiar();
    }

    protected function tearDown(): void
    {
        $this->limpiar();
    }

    private function limpiar(): void
    {
        $pdo = getPDO();
        $in = implode(',', array_fill(0, count(self::CEDULAS), '?'));
        $pdo->prepare("DELETE FROM renovaciones_contrato WHERE cedula IN ($in)")->execute(self::CEDULAS);
        $pdo->prepare("DELETE FROM empleados WHERE cedula IN ($in)")->execute(self::CEDULAS);
    }

    /** Crea un empleado con contrato a término fijo que termina en $diasDesdeHoy (negativo = ya venció). */
    private function empleadoConFin(string $cedula, int $diasDesdeHoy): void
    {
        $fin = date('Y-m-d', strtotime("{$diasDesdeHoy} days"));
        $inicio = date('Y-m-d', strtotime(($diasDesdeHoy - 365) . ' days'));
        getPDO()->prepare("INSERT INTO empleados (cedula, nombre, cargo, tipo_de_personal, tipo_jornada, celular, estado,
                           tipo_de_contrato, fecha_inicio_contrato, fecha_fin_contrato)
                           VALUES (?, ?, 'Cargo de prueba (test)', 'Civil', 'Administrativa', '3000000000', 'activo', 'Fijo', ?, ?)")
            ->execute([$cedula, "Empleado $cedula", $inicio, $fin]);
    }

    private function cedulasAvisadas(): array
    {
        return array_column(RenovacionModel::avisosParaAlarmas(), 'cedula');
    }

    public function testContratoQueVenceEnPocosDiasSeAvisa(): void
    {
        $this->empleadoConFin('9900000011', 20);
        $this->assertContains('9900000011', $this->cedulasAvisadas(), 'Faltan 20 días: por vencer, dentro de 60');
    }

    public function testContratoQueVenceMuyLejosNoSeAvisa(): void
    {
        $this->empleadoConFin('9900000012', 90);
        $this->assertNotContains('9900000012', $this->cedulasAvisadas(), 'Faltan 90 días: aún no es momento del aviso');
    }

    public function testVencidoHacePocoSigueAvisandose(): void
    {
        $this->empleadoConFin('9900000013', -10);
        $this->assertContains('9900000013', $this->cedulasAvisadas(), 'Venció hace 10 días: dentro de los 30 de aviso');
    }

    public function testVencidoHaceMuchoYaNoSeAvisa(): void
    {
        $this->empleadoConFin('9900000014', -60);
        $this->assertNotContains('9900000014', $this->cedulasAvisadas(), 'Venció hace 60 días: ya pasó la ventana de aviso');
    }

    public function testLosPlazosSonLosDeSiempre(): void
    {
        $this->assertSame(60, RenovacionReglas::DIAS_AVISO);
        $this->assertSame(30, RenovacionReglas::DIAS_VENCIDO_MAX);
    }
}
