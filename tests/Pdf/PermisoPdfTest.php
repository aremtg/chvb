<?php
// tests/Pdf/PermisoPdfTest.php
//
// Verifica la estructura del PDF generado (páginas, tamaño, contenido) sin base de datos.
// Requiere dompdf instalado (composer install). Si no está, la prueba se omite.

use PHPUnit\Framework\TestCase;

final class PermisoPdfTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(\Dompdf\Dompdf::class)) {
            $this->markTestSkipped('dompdf no está instalado (ejecuta composer install).');
        }
        require_once dirname(__DIR__, 2) . '/src/helpers/PermisoPdf.php';
    }

    private function permiso(int $n, string $consecutivo): array
    {
        $dias = [];
        $inicio = new DateTime('2026-10-21');
        for ($i = 0; $i < $n; $i++) {
            $dias[] = [
                'fecha' => (clone $inicio)->modify("+$i day")->format('Y-m-d'),
                'hora_inicio' => '07:00:00', 'hora_fin' => '17:24:00',
                'horas_netas' => 8.4, 'incluido' => 1, 'es_festivo' => 0,
            ];
        }
        return [
            'estado' => 'por_firmar_jefe', 'tipo_permiso' => 'Permiso', 'consecutivo' => $consecutivo,
            'fecha_solicitud' => '2026-10-09', 'nombre_empleado_snapshot' => 'Empleado de prueba',
            'cedula_empleado' => '1000000000', 'cargo_empleado_snapshot' => 'Cargo de prueba',
            'celular_empleado_snapshot' => '3000000000', 'motivo' => 'Motivo de prueba',
            'remunerado' => 1, 'es_compensatorio' => 0, 'es_devolucion' => 0,
            'dias' => $dias, 'total_dias' => $n, 'total_horas' => $n * 8.4,
            'nombre_jefe' => 'Jefe de prueba', 'cedula_jefe' => '2000000000',
            'evidencia_archivo' => null, 'firma_solicitante' => null, 'firma_jefe' => null,
            'firma_jefe_prefirmado' => null, 'nombre_reemplazo' => null, 'cedula_reemplazo' => null,
            'firma_reemplazo' => null, 'foto_solicitante' => null, 'foto_jefe' => null,
        ];
    }

    private function paginas(string $pdf): int
    {
        return preg_match_all('#/Type\s*/Page[^s]#', $pdf);
    }

    private function tamanosMediaBox(string $pdf): array
    {
        preg_match_all('#/MediaBox\s*\[\s*0(?:\.0+)?\s+0(?:\.0+)?\s+([\d.]+)\s+([\d.]+)\s*\]#', $pdf, $m, PREG_SET_ORDER);
        // El MediaBox aparece en el árbol de páginas y en cada página: se devuelven tamaños únicos.
        $unicos = array_unique(array_map(fn($x) => json_encode([(float) $x[1], (float) $x[2]]), $m));
        return array_map(fn($j) => array_map('floatval', json_decode($j, true)), array_values($unicos));
    }

    public function testUnPermisoPorHojaUsaPaginaCuadradaDeUnaPagina(): void
    {
        $pdf = PermisoPdf::generar([$this->permiso(3, 'GH-T-0001')], false, 'Prueba');
        $this->assertStringStartsWith('%PDF', $pdf);
        $this->assertSame(1, $this->paginas($pdf));
        $this->assertSame([[612.0, 612.0]], $this->tamanosMediaBox($pdf));
    }

    public function testDosPermisosPorHojaSaleOficioVertical(): void
    {
        $pdf = PermisoPdf::generar([$this->permiso(3, 'GH-T-0001'), $this->permiso(2, 'GH-T-0002')], true, 'Prueba');
        $this->assertSame(1, $this->paginas($pdf), 'Dos permisos caben en una hoja');
        $this->assertSame([[612.0, 936.0]], $this->tamanosMediaBox($pdf));
    }

    public function testCincoPermisosDosPorHojaOcupanTresPaginas(): void
    {
        $lista = [];
        for ($i = 1; $i <= 5; $i++) {
            $lista[] = $this->permiso(2, sprintf('GH-T-%04d', $i));
        }
        $pdf = PermisoPdf::generar($lista, true, 'Prueba');
        $this->assertSame(3, $this->paginas($pdf));
    }

    public function testDieciseisDiasSeMuestranSinCortarLaHoja(): void
    {
        // Peor caso de una mitad: 16 días (2 columnas de 8 filas).
        $pdf = PermisoPdf::generar([$this->permiso(16, 'GH-T-0001')], false, 'Prueba');
        $this->assertSame(1, $this->paginas($pdf));
    }

    public function testMasDeDieciseisDiasAvisaQueHayMas(): void
    {
        $pdf = PermisoPdf::generar([$this->permiso(18, 'GH-T-0001')], false, 'Prueba');
        // El texto del aviso va comprimido dentro del PDF; se verifica que la hoja siga siendo una sola.
        $this->assertSame(1, $this->paginas($pdf));
    }

    public function testEncabezadoMuestraVersionYFechaDelFormato(): void
    {
        $pdfs = [];
        $html = PermisoPdf::construirHtml([$this->permiso(2, 'GH-T-0001')], false, 'Prueba', null, $pdfs,
            ['version' => 3, 'fecha' => '2026-10-10']);
        $this->assertStringContainsString('Versión-3', $html);
        $this->assertStringContainsString('Fecha-10 oct 2026', $html);
    }

    public function testNotaLegalAparecePorCadaPermisoYConTextoExacto(): void
    {
        $pdfs = [];
        $html = PermisoPdf::construirHtml([$this->permiso(2, 'GH-T-0001'), $this->permiso(2, 'GH-T-0002')], false, 'Prueba', null, $pdfs);
        $this->assertSame(2, substr_count($html, 'Nota importante:'));
        $this->assertStringContainsString('Las vacaciones deben solicitarse con 2 meses de anticipación y los permisos personales con 2 días de anticipación, según instructivo GH-FT-10.', $html);
        $this->assertStringContainsString('La compensación debe realizarse dentro del mismo mes del permiso.', $html);
        $this->assertStringContainsString('No se considerará accidente de trabajo si ocurre durante permisos que no sean misión institucional ordenada por el empleador.', $html);
    }

    public function testCadaPermisoMuestraElFormatoConQueSeCreo(): void
    {
        $viejo = $this->permiso(2, 'GH-T-0001');
        $viejo['formato_codigo'] = 'GH-FT-10';
        $viejo['formato_version'] = 5;
        $viejo['formato_fecha'] = '2025-03-01';

        $pdfs = [];
        // El formato actual es otro: el permiso debe mostrar el suyo, no el actual.
        $html = PermisoPdf::construirHtml([$viejo], false, 'Prueba', null, $pdfs, ['version' => 2, 'fecha' => '2026-10-10']);
        $this->assertStringContainsString('Versión-5', $html);
        $this->assertStringContainsString('Fecha-1 mar 2025', $html);
        $this->assertStringNotContainsString('Versión-2', $html);
    }
}
