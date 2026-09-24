<?php
require_once __DIR__ . '/../../config/database.php';

class FormatoModel
{
    public static function listarRenovacionesGeneradas(string $directorio): array
    {
        if (!is_dir($directorio)) return [];

        $archivos = glob(rtrim($directorio, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'RENOVACION_*.docx') ?: [];
        $resultado = [];

        foreach ($archivos as $archivo) {
            if (!is_file($archivo)) continue;
            $resultado[] = [
                'archivo' => basename($archivo),
                'ruta' => $archivo,
                'tamano' => filesize($archivo),
                'fecha' => filemtime($archivo) ?: time(),
            ];
        }

        usort($resultado, static fn(array $a, array $b): int => $b['fecha'] <=> $a['fecha']);
        return $resultado;
    }

    public static function listarOtrosiSalarioGenerados(string $directorio): array
    {
        if (!is_dir($directorio)) return [];

        $archivos = glob(rtrim($directorio, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'GH-FT-25 OTROSI CAMBIO DE SALARIO *.docx') ?: [];
        $resultado = [];

        foreach ($archivos as $archivo) {
            if (!is_file($archivo)) continue;
            $resultado[] = [
                'archivo' => basename($archivo),
                'ruta' => $archivo,
                'tamano' => filesize($archivo),
                'fecha' => filemtime($archivo) ?: time(),
            ];
        }

        usort($resultado, static fn(array $a, array $b): int => $b['fecha'] <=> $a['fecha']);
        return $resultado;
    }

    public static function nombreSeguro(string $nombre): string
    {
        $nombre = strtoupper(trim($nombre));
        $nombre = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nombre) ?: $nombre;
        $nombre = preg_replace('/[^A-Z0-9]+/', '_', $nombre);
        return trim($nombre, '_') ?: 'EMPLEADO';
    }

    public static function formatearCedula(string $cedula): string
    {
        $cedula = trim($cedula);
        if (preg_match('/^\d+$/', $cedula)) {
            return number_format((int)$cedula, 0, ',', '.');
        }
        return $cedula;
    }

    public static function fechaLarga(string $fecha): string
    {
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$d || $d->format('Y-m-d') !== $fecha) throw new InvalidArgumentException('Fecha inválida.');
        $meses = [1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',7=>'julio',8=>'agosto',9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre'];
        return (int)$d->format('d') . ' de ' . $meses[(int)$d->format('m')] . ' de ' . $d->format('Y');
    }

    public static function fechaCorta(string $fecha): string
    {
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$d || $d->format('Y-m-d') !== $fecha) throw new InvalidArgumentException('Fecha inválida.');
        return $d->format('d/m/Y');
    }

    public static function calcularFin(string $inicio, int $meses): string
    {
        if ($meses < 1 || $meses > 48) throw new InvalidArgumentException('La duración debe estar entre 1 y 48 meses.');
        $d = DateTime::createFromFormat('Y-m-d', $inicio);
        if (!$d || $d->format('Y-m-d') !== $inicio) throw new InvalidArgumentException('Fecha de inicio inválida.');
        return $d->modify('+' . $meses . ' months -1 day')->format('Y-m-d');
    }

    public static function textoMeses(int $meses): string
    {
        $unidades = [
            1=>'un',2=>'dos',3=>'tres',4=>'cuatro',5=>'cinco',6=>'seis',7=>'siete',8=>'ocho',9=>'nueve',10=>'diez',
            11=>'once',12=>'doce',13=>'trece',14=>'catorce',15=>'quince',16=>'dieciséis',17=>'diecisiete',18=>'dieciocho',19=>'diecinueve',20=>'veinte',
            21=>'veintiún',22=>'veintidós',23=>'veintitrés',24=>'veinticuatro',25=>'veinticinco',26=>'veintiséis',27=>'veintisiete',28=>'veintiocho',29=>'veintinueve',30=>'treinta',
            31=>'treinta y un',32=>'treinta y dos',33=>'treinta y tres',34=>'treinta y cuatro',35=>'treinta y cinco',36=>'treinta y seis',37=>'treinta y siete',38=>'treinta y ocho',39=>'treinta y nueve',40=>'cuarenta',
            41=>'cuarenta y un',42=>'cuarenta y dos',43=>'cuarenta y tres',44=>'cuarenta y cuatro',45=>'cuarenta y cinco',46=>'cuarenta y seis',47=>'cuarenta y siete',48=>'cuarenta y ocho'
        ];
        return $unidades[$meses] ?? (string)$meses;
    }

    public static function primerNombre(string $nombre): string
    {
        $nombre = trim(preg_replace('/\s+/', ' ', $nombre));
        return $nombre !== '' ? explode(' ', $nombre)[0] : '';
    }
}
