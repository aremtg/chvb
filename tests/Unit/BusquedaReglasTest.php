<?php
// tests/Unit/BusquedaTest.php

use PHPUnit\Framework\TestCase;

final class BusquedaReglasTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/src/helpers/Busqueda.php';
    }

    public function testSeparaPalabrasIgnorandoEspaciosRepetidos(): void
    {
        $this->assertSame(['Angel', 'Camargo'], Busqueda::palabras('  Angel    Camargo  '));
        $this->assertSame([], Busqueda::palabras('   '));
    }

    public function testMaximoCincoPalabras(): void
    {
        $this->assertCount(Busqueda::MAX_PALABRAS, Busqueda::palabras('a b c d e f g'));
    }

    public function testCedulaConYSinPuntosDaLOsMismosDigitos(): void
    {
        $this->assertSame('1118555586', Busqueda::digitos('1.118.555.586'));
        $this->assertSame('1118555586', Busqueda::digitos('1118555586'));
    }

    public function testEscapaComodinesDeLike(): void
    {
        $this->assertSame('100\\%\\_x', Busqueda::escapar('100%_x'));
    }

    public function testCondicionUnePalabrasConY(): void
    {
        [$sql, $params] = Busqueda::condicion('Angel Camargo', ['nombre'], 'cedula', 'q');
        // Una condición por palabra, unidas con AND
        $this->assertSame(2, substr_count($sql, ' LIKE :'));
        $this->assertStringContainsString(' AND ', $sql);
        $this->assertSame('%Angel%', $params['q0_0']);
        $this->assertSame('%Camargo%', $params['q1_0']);
    }

    public function testCondicionIncluyeCedulaSinPuntosSoloSiHayDigitos(): void
    {
        [$sql, $params] = Busqueda::condicion('1.118', ['nombre'], 'cedula', 'q');
        $this->assertStringContainsString("REPLACE(cedula, '.', '') LIKE :q0_c", $sql);
        $this->assertSame('%1118%', $params['q0_c']);

        [$sinDigitos] = Busqueda::condicion('Angel', ['nombre'], 'cedula', 'q');
        $this->assertStringNotContainsString('REPLACE', $sinDigitos);
    }

    public function testSinPalabrasNoHayCondicion(): void
    {
        [$sql, $params] = Busqueda::condicion('   ', ['nombre'], 'cedula', 'q');
        $this->assertSame('', $sql);
        $this->assertSame([], $params);
    }
}
