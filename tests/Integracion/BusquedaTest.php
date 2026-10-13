<?php
// tests/Integracion/BusquedaTest.php
// Cédula con y sin puntos, y nombres con palabras separadas, contra la base real de pruebas.
// SEGURIDAD: solo corre contra una base que termine en _test.

use PHPUnit\Framework\TestCase;

final class BusquedaTest extends TestCase
{
    private const CEDULA = '9900000031';
    private const NOMBRE = 'Angel Gabriel Camargo Pezca';

    protected function setUp(): void
    {
        $bd = $_SERVER['DB_NAME'] ?? $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: '';
        if (!str_ends_with((string) $bd, '_test')) {
            throw new RuntimeException("Las pruebas de búsqueda se niegan a correr en la base '$bd'.");
        }
        $raiz = dirname(__DIR__, 2);
        require_once $raiz . '/config/database.php';
        require_once $raiz . '/src/models/EmpleadoModel.php';
        $this->limpiar();
        $pdo = getPDO();
        $pdo->prepare('INSERT IGNORE INTO cargos (nombre, activo) VALUES (?, 1)')->execute(['Cargo de prueba (test)']);
        $pdo->prepare('INSERT INTO empleados (cedula, nombre, cargo, tipo_de_personal, tipo_jornada, celular, estado)
                       VALUES (?, ?, ?, ?, ?, ?, ?)')
            ->execute([self::CEDULA, self::NOMBRE, 'Cargo de prueba (test)', 'Civil', 'Administrativa', '3000000031', 'activo']);
    }

    protected function tearDown(): void
    {
        $this->limpiar();
    }

    private function limpiar(): void
    {
        getPDO()->prepare('DELETE FROM empleados WHERE cedula = ?')->execute([self::CEDULA]);
    }

    private function encuentra(string $q): bool
    {
        return in_array(self::CEDULA, array_column(EmpleadoModel::listar($q, 100, 0), 'cedula'), true);
    }

    public function testCedulaSinPuntos(): void
    {
        $this->assertTrue($this->encuentra('9900000031'));
    }

    public function testCedulaConPuntos(): void
    {
        $this->assertTrue($this->encuentra('9.900.000.031'));
    }

    public function testNombreCompleto(): void
    {
        $this->assertTrue($this->encuentra(self::NOMBRE));
    }

    public function testNombreConPalabrasSeparadasSinSegundoNombre(): void
    {
        $this->assertTrue($this->encuentra('Angel Camargo'), 'Debe encontrar aunque falte el segundo nombre');
    }

    public function testNoEncuentraOtraPersona(): void
    {
        $this->assertFalse($this->encuentra('Juan Perez'));
    }

    public function testBusquedaSinPalabrasTraeTodos(): void
    {
        $this->assertTrue($this->encuentra('   '));
    }
}
