<?php
// tests/Unit/JornadaHelperTest.php

use PHPUnit\Framework\TestCase;

final class JornadaHelperTest extends TestCase
{
    public function testAdministrativaDescuentaElAlmuerzoYDaOchoCuartoHoras(): void
    {
        // 07:00-12:00 (5 h) + 14:00-17:24 (3 h 24 min) = 8,40 h
        $this->assertSame(8.4, JornadaHelper::horasEntre('07:00', '17:24'));
    }

    public function testUnaFranjaQueCaeDentroDelAlmuerzoNoCuentaNada(): void
    {
        $this->assertSame(0.0, JornadaHelper::horasEntre('12:00', '14:00'));
    }

    public function testSalidaAntesDeEntradaDaCero(): void
    {
        $this->assertSame(0.0, JornadaHelper::horasEntre('10:00', '09:00'));
    }

    public function testRestringidaUsaSuPropioHorario(): void
    {
        $emp = [
            'tipo_jornada' => JornadaHelper::RESTRINGIDA,
            'jornada_hora_entrada' => '07:00:00',
            'jornada_hora_salida' => '12:00:00',
        ];
        $r = JornadaHelper::reglas($emp);
        $this->assertSame(5.0, $r['horas_dia']);
        $this->assertFalse($r['por_turnos']);
    }

    public function testRestringidaSinHorarioValidoCaeEnAdministrativa(): void
    {
        $emp = ['tipo_jornada' => JornadaHelper::RESTRINGIDA, 'jornada_hora_entrada' => '', 'jornada_hora_salida' => ''];
        $r = JornadaHelper::reglas($emp);
        $this->assertSame(JornadaHelper::ADMINISTRATIVA, $r['tipo']);
        $this->assertSame(8.4, $r['horas_dia']);
    }

    public function testTurnosCuentaDiaCompletoSoloDeMedianocheAMedianoche(): void
    {
        $r = JornadaHelper::reglas(['tipo_jornada' => JornadaHelper::TURNOS]);
        $this->assertTrue($r['por_turnos']);
        $this->assertTrue(JornadaHelper::esDiaCompleto($r, '00:00:00', '23:59:00', 24.0));
        $this->assertFalse(JornadaHelper::esDiaCompleto($r, '07:00:00', '17:24:00', 10.4));
    }

    public function testAdministrativaNecesitaLasHorasDelDia(): void
    {
        $r = JornadaHelper::reglas(['tipo_jornada' => JornadaHelper::ADMINISTRATIVA]);
        $this->assertTrue(JornadaHelper::esDiaCompleto($r, '07:00:00', '17:24:00', 8.4));
        $this->assertFalse(JornadaHelper::esDiaCompleto($r, '07:00:00', '12:00:00', 5.0));
    }

    public function testTipoInvalidoOSinDatoCaeEnTurnosSiEsBombero(): void
    {
        $this->assertSame(JornadaHelper::TURNOS, JornadaHelper::tipoJornada(['tipo_de_personal' => 'Bombero']));
        $this->assertSame(JornadaHelper::ADMINISTRATIVA, JornadaHelper::tipoJornada(['tipo_de_personal' => 'Civil']));
        $this->assertSame(JornadaHelper::ADMINISTRATIVA, JornadaHelper::tipoJornada(['tipo_jornada' => 'Inventado']));
    }

    public function testNormalizarHoraAceptaSoloHorasReales(): void
    {
        $this->assertSame('07:05', JornadaHelper::normalizarHora('07:05:00'));
        $this->assertNull(JornadaHelper::normalizarHora('25:00'));
        $this->assertNull(JornadaHelper::normalizarHora('7:5'));
        $this->assertNull(JornadaHelper::normalizarHora(''));
    }

    public function testTipoParaCalculoSinTipoDePersonalDevuelveNull(): void
    {
        $this->assertNull(JornadaHelper::tipoParaCalculo(['tipo_de_personal' => '']));
        // Sin tipo_de_personal la función devuelve null (así está documentado): se prueba con el dato completo.
        $this->assertSame('Bombero', JornadaHelper::tipoParaCalculo(['tipo_de_personal' => 'Bombero', 'tipo_jornada' => JornadaHelper::TURNOS]));
        $this->assertSame('Civil', JornadaHelper::tipoParaCalculo(['tipo_de_personal' => 'Civil', 'tipo_jornada' => JornadaHelper::ADMINISTRATIVA]));
    }
}
