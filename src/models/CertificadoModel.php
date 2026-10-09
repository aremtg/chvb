<?php
// src/models/CertificadoModel.php
// Certificado laboral GH-FT-10. Hoy implementa el tipo 'actual' (labora actualmente);
// el tipo 'retirado' (laboró hasta) reutiliza todo esto con otra plantilla.
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/FormatoModel.php';
require_once __DIR__ . '/FuncionModel.php';

class CertificadoModel
{
    private const PLANTILLAS = [
        'actual' => 'GH-FT-10 CERTIFICADO LABORAL ACTUAL.docx',
        // 'retirado' => 'GH-FT-10 CERTIFICADO LABORAL RETIRADO.docx',  // segundo formato
    ];

    private const MESES = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
        7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
    ];

    /** Empleado con los datos que usa el certificado + id de su cargo (por nombre). */
    public static function empleado(string $cedula): ?array
    {
        $st = getPDO()->prepare(
            'SELECT cedula, nombre, sexo, cargo, lugar_expedicion, fecha_inicio_contrato, fecha_fin_contrato, estado
             FROM empleados WHERE cedula = ? LIMIT 1'
        );
        $st->execute([$cedula]);
        $e = $st->fetch();
        if (!$e) {
            return null;
        }
        $e['cargo_id'] = FuncionModel::cargoIdPorNombre((string)$e['cargo']);
        return $e;
    }

    /** Motivos que impiden generar el certificado "actual". Vacío = todo bien. */
    public static function problemas(array $e): array
    {
        $p = [];
        if (($e['estado'] ?? '') !== 'activo') {
            $p[] = 'El empleado está NO ACTIVO. Para generarle formatos primero actívalo en Empleados → Editar.';
        }
        if (empty($e['fecha_inicio_contrato'])) {
            $p[] = 'Falta la fecha de inicio de contrato del empleado.';
        }
        if (trim((string)($e['lugar_expedicion'] ?? '')) === '') {
            $p[] = 'Falta el lugar de expedición de la cédula del empleado.';
        }
        if (!in_array($e['sexo'] ?? '', ['M', 'F'], true)) {
            $p[] = 'Falta el sexo del empleado (se usa para «el señor / la señora»).';
        }
        if (empty($e['cargo_id'])) {
            $p[] = 'El cargo del empleado no está registrado en el catálogo de cargos.';
        }
        return $p;
    }

    public static function funcionesDisponibles(int $cargoId): array
    {
        return FuncionModel::listar('certificado', $cargoId, true);
    }

    /** Certificados generados, leídos de la carpeta (más recientes primero). No hay registro en BD. */
    public static function recientes(string $directorio, int $limite = 30): array
    {
        if (!is_dir($directorio)) {
            return [];
        }
        $archivos = glob(rtrim($directorio, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'GH-FT-10-*-CERTIFICADO LABORAL *.docx') ?: [];
        $res = [];
        foreach ($archivos as $ruta) {
            if (is_file($ruta)) {
                $res[] = ['archivo' => basename($ruta), 'fecha' => filemtime($ruta) ?: time()];
            }
        }
        usort($res, static fn(array $a, array $b): int => $b['fecha'] <=> $a['fecha']);
        return array_slice($res, 0, $limite);
    }

    /** 1 función: tal cual. 2 funciones: "A y B" (o "A e B" si B empieza por "i"/"hi"). */
    public static function unirFunciones(array $textos): string
    {
        $t = array_values(array_map(
            static fn($s) => rtrim(trim((string)$s), " .;,"),
            $textos
        ));
        if (count($t) === 1) {
            return $t[0];
        }
        $conj = preg_match('/^h?i(?![aeou])/iu', $t[1]) ? ' e ' : ' y ';
        return $t[0] . $conj . $t[1];
    }

    /** "a los dieciocho (18) días del mes de septiembre de 2026" / "al primer (1) día del mes de ..." */
    public static function fechaExpedicionTexto(DateTimeInterface $f): string
    {
        $dia = (int)$f->format('j');
        $mes = self::MESES[(int)$f->format('n')];
        $resto = ' del mes de ' . $mes . ' de ' . $f->format('Y');
        if ($dia === 1) {
            return 'al primer (1) día' . $resto;
        }
        return 'a los ' . FormatoModel::textoMeses($dia) . ' (' . $dia . ') días' . $resto;
    }

    /**
     * Genera el Word. Consecutivo + archivo van en UNA transacción:
     * si algo falla, el número no se consume (sin huecos en el consecutivo).
     * El certificado NO se registra en la BD: solo queda el archivo en uploads/generados.
     *
     * @param int[] $funcionIds 1 o 2 ids de funciones_certificados
     * @return array{archivo:string,consecutivo:string}
     */
    public static function generar(string $tipo, string $cedula, array $funcionIds): array
    {
        if (!isset(self::PLANTILLAS[$tipo])) {
            throw new InvalidArgumentException('Tipo de certificado no disponible todavía.');
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $funcionIds), static fn($i) => $i > 0)));
        if (count($ids) < 1 || count($ids) > 2) {
            throw new InvalidArgumentException('Selecciona 1 o 2 funciones para el certificado.');
        }

        $emp = self::empleado($cedula);
        if (!$emp) {
            throw new InvalidArgumentException('No se encontró el empleado seleccionado.');
        }
        $problemas = self::problemas($emp);
        if ($problemas) {
            throw new InvalidArgumentException($problemas[0]);
        }

        // El servidor revalida que las funciones sean del cargo del empleado y estén activas.
        $marcas = implode(',', array_fill(0, count($ids), '?'));
        $st = getPDO()->prepare(
            "SELECT id, texto FROM funciones_certificados WHERE activo = 1 AND cargo_id = ? AND id IN ($marcas)"
        );
        $st->execute(array_merge([(int)$emp['cargo_id']], $ids));
        $porId = [];
        foreach ($st->fetchAll() as $f) {
            $porId[(int)$f['id']] = (string)$f['texto'];
        }
        if (count($porId) !== count($ids)) {
            throw new InvalidArgumentException('Alguna función ya no existe o no corresponde al cargo del empleado. Vuelve a seleccionarlas.');
        }
        $textos = [];
        foreach ($ids as $id) {          // conserva el orden elegido por el usuario
            $textos[] = $porId[$id];
        }

        $plantilla = __DIR__ . '/../../uploads/plantillas/' . self::PLANTILLAS[$tipo];
        if (!is_file($plantilla)) {
            throw new RuntimeException('No se encontró la plantilla ' . self::PLANTILLAS[$tipo] . ' en uploads/plantillas.');
        }
        $directorio = __DIR__ . '/../../uploads/generados';
        if (!is_dir($directorio) && !mkdir($directorio, 0775, true) && !is_dir($directorio)) {
            throw new RuntimeException('No fue posible crear la carpeta de archivos generados.');
        }

        $hoy = Reloj::ahora();
        $pdo = getPDO();
        $ruta = null;

        $pdo->beginTransaction();
        try {
            // --- consecutivo global con bloqueo de fila ---
            $pdo->exec('INSERT IGNORE INTO certificados_consecutivos (id, ultimo_numero) VALUES (1, 0)');
            $numero = (int)$pdo->query('SELECT ultimo_numero FROM certificados_consecutivos WHERE id = 1 FOR UPDATE')->fetchColumn() + 1;
            $pdo->prepare('UPDATE certificados_consecutivos SET ultimo_numero = ? WHERE id = 1')->execute([$numero]);
            $consecutivo = sprintf('%04d', $numero);

            // --- nombre de archivo: GH-FT-10-0001-CERTIFICADO LABORAL NOMBRE.docx ---
            $nombreArchivo = preg_replace('/[\\\\\/:*?"<>|]+/u', '', trim((string)$emp['nombre']));
            $nombreArchivo = trim(preg_replace('/\s+/u', ' ', $nombreArchivo)) ?: 'EMPLEADO';
            $archivo = "GH-FT-10-{$consecutivo}-CERTIFICADO LABORAL {$nombreArchivo}.docx";
            $ruta = $directorio . DIRECTORY_SEPARATOR . $archivo;

            // --- inyección de variables ---
            \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);   // escapa & < > en los valores
            $proc = new \PhpOffice\PhpWord\TemplateProcessor($plantilla);
            $proc->setValues([
                'consecutivo'      => $consecutivo,
                'nombre_empleado'  => mb_strtoupper((string)$emp['nombre'], 'UTF-8'),
                'cedula'           => FormatoModel::formatearCedula((string)$emp['cedula']),
                'lugar_expedicion' => str_replace(', ', ' – ', trim((string)$emp['lugar_expedicion'])),
                'cargo'            => (string)$emp['cargo'],
                'tratamiento'      => $emp['sexo'] === 'F' ? 'la señora' : 'el señor',
                'fecha_inicio'     => FormatoModel::fechaLarga((string)$emp['fecha_inicio_contrato']),
                'funciones'        => self::unirFunciones($textos),
                'fecha_expedicion' => self::fechaExpedicionTexto($hoy),
            ]);
            $proc->saveAs($ruta);

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if ($ruta && is_file($ruta)) {
                @unlink($ruta);
            }
            throw $e;
        }

        return ['archivo' => $archivo, 'consecutivo' => $consecutivo];
    }
}
