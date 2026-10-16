<?php
// tests/Unit/PasswordPolicyTest.php

use PHPUnit\Framework\TestCase;

final class PasswordPolicyTest extends TestCase
{
    public function testAceptaUnaFraseLargaYRazonable(): void
    {
        $this->assertNull(PasswordPolicy::validar('Mi-frase-segura-2026', 'ana'));
    }

    public function testRechazaMenosDeDiezCaracteres(): void
    {
        $this->assertNotNull(PasswordPolicy::validar('Corta1!x'));
    }

    public function testRechazaMasDeSetentaYDosBytesPorqueBcryptLosIgnoraria(): void
    {
        $base = str_repeat('Abc123Xyz789', 7);   // 84 caracteres con variedad suficiente
        $this->assertNotNull(PasswordPolicy::validar(substr($base, 0, 73)));
        $this->assertNull(PasswordPolicy::validar(substr($base, 0, 72)));   // 72 bytes exactos
    }

    public function testRechazaContenerElNombreDeUsuarioSinImportarMayusculas(): void
    {
        $this->assertNotNull(PasswordPolicy::validar('xx-AUX1-xx-2026', 'aux1'));
    }

    public function testRechazaClavesComunesYMuyRepetitivas(): void
    {
        $this->assertNotNull(PasswordPolicy::validar('Password123'));
        $this->assertNotNull(PasswordPolicy::validar('aaaaaaaaaaaa'));
        $this->assertNotNull(PasswordPolicy::validar('abababababab'));
    }

    public function testLaTemporalGeneradaSiempreCumpleLaPoliticaYTieneFormato(): void
    {
        for ($i = 0; $i < 200; $i++) {
            $t = PasswordPolicy::generarTemporal();
            $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}$/', $t);
            $this->assertMatchesRegularExpression('/[A-Z]/', $t);
            $this->assertMatchesRegularExpression('/[a-z]/', $t);
            $this->assertMatchesRegularExpression('/[0-9]/', $t);
            $this->assertDoesNotMatchRegularExpression('/[0OIl1]/', $t);   // sin caracteres confundibles
            $this->assertNull(PasswordPolicy::validar($t, 'usuario'));
        }
    }

    public function testLasTemporalesNoSeRepiten(): void
    {
        $vistas = [];
        for ($i = 0; $i < 500; $i++) {
            $vistas[PasswordPolicy::generarTemporal()] = true;
        }
        $this->assertCount(500, $vistas);
    }
}
