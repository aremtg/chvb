<?php
// src/models/EmpleadoModel.php
require_once __DIR__ . '/../../config/database.php';

class EmpleadoModel
{

    public static function crear(array $datos): void
    {
        $pdo = getPDO();
        $sql = "INSERT INTO empleados 
            (cedula, nombre, sexo, cargo, tipo_de_personal, grupo, eps, pension, salario_basico,
             es_bombero_integral, tipo_de_contrato, estado, celular, correo, fecha_nacimiento)
            VALUES 
            (:cedula, :nombre, :sexo, :cargo, :tipo_personal, :grupo, :eps, :pension, :salario,
             :bombero, :contrato, :estado, :celular, :correo, :fecha_nacimiento)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'cedula' => $datos['cedula'],
            'nombre' => $datos['nombre'],
            'sexo' => $datos['sexo'] ?? null,
            'cargo' => $datos['cargo'],
            'tipo_personal' => $datos['tipo_de_personal'] ?? null,
            'grupo' => $datos['grupo'] ?? null,
            'eps' => $datos['eps'] ?? null,
            'pension' => $datos['pension'] ?? null,
            'salario' => $datos['salario_basico'] ?? null,
            'bombero' => $datos['es_bombero_integral'],
            'contrato' => $datos['tipo_de_contrato'],
            'estado' => $datos['estado'],
            'celular' => $datos['celular'] ?: null,
            'correo' => $datos['correo'] ?: null,
            'fecha_nacimiento' => $datos['fecha_nacimiento'] ?: null,
        ]);
    }

    public static function existeCedula(string $cedula): bool
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT cedula FROM empleados WHERE cedula = :cedula");
        $stmt->execute(['cedula' => $cedula]);
        return (bool) $stmt->fetch();
    }

    public static function listar(string $busqueda = ''): array
    {
        $pdo = getPDO();

        $sql = "SELECT * FROM empleados";
        $params = [];

        if ($busqueda !== '') {
            $sql .= " WHERE cedula LIKE :b1 OR nombre LIKE :b2 OR cargo LIKE :b3 OR celular LIKE :b4 OR correo LIKE :b5";
            $valor = '%' . $busqueda . '%';
            $params = [
                'b1' => $valor,
                'b2' => $valor,
                'b3' => $valor,
                'b4' => $valor,
                'b5' => $valor,
            ];
        }

        $sql .= " ORDER BY nombre ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public static function obtenerPorCedula(string $cedula): ?array
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM empleados WHERE cedula = :cedula");
        $stmt->execute(['cedula' => $cedula]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public static function eliminar(string $cedula): void
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("DELETE FROM empleados WHERE cedula = :cedula");
        $stmt->execute(['cedula' => $cedula]);
    }

    /**
     * Calcula si el cumpleaños cae dentro de los próximos N días (ignorando el año).
     * Devuelve ['cumple' => bool, 'dias_faltantes' => int|null, 'fecha_texto' => string|null]
     */
    public static function infoCumpleanos(?string $fechaNacimiento, int $diasVentana = 7): array
    {
        if (!$fechaNacimiento) {
            return ['cumple' => false, 'dias_faltantes' => null, 'fecha_texto' => null];
        }

        $hoy = new DateTime('today');
        $nacimiento = new DateTime($fechaNacimiento);

        $proximoCumple = new DateTime($hoy->format('Y') . '-' . $nacimiento->format('m-d'));
        if ($proximoCumple < $hoy) {
            $proximoCumple->modify('+1 year');
        }

        $diff = $hoy->diff($proximoCumple)->days;

        return [
            'cumple' => $diff <= $diasVentana,
            'dias_faltantes' => $diff,
            'fecha_texto' => $nacimiento->format('d') . ' de ' . self::mesEnEspanol((int) $nacimiento->format('m')),
        ];
    }

    public static function mesEnEspanol(int $mes): string
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        return $meses[$mes] ?? '';
    }

    /**
     * Trae empleados cuyo cumpleaños (mes-día, ignorando año) cae dentro de los próximos $dias días.
     * Maneja el cruce de año (ej: hoy 28-dic, ventana llega hasta 04-ene).
     */
    public static function proximosCumpleanos(int $dias = 7): array
    {
        $pdo = getPDO();
        $sql = "SELECT *, DATE_FORMAT(fecha_nacimiento, '%m-%d') AS mes_dia
            FROM empleados
            WHERE fecha_nacimiento IS NOT NULL
              AND estado = 'activo'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $todos = $stmt->fetchAll();

        // Filtramos en PHP con la misma lógica de infoCumpleanos() para que sea 100% consistente
        // con lo que se muestra en la tabla de empleados (evita discrepancias por lógica SQL vs PHP distinta).
        $resultado = [];
        foreach ($todos as $emp) {
            $info = self::infoCumpleanos($emp['fecha_nacimiento'], $dias);
            if ($info['cumple']) {
                $emp['dias_faltantes'] = $info['dias_faltantes'];
                $emp['fecha_texto'] = $info['fecha_texto'];
                $resultado[] = $emp;
            }
        }

        // Ordenar por días faltantes ascendente (el más próximo primero)
        usort($resultado, fn($a, $b) => $a['dias_faltantes'] <=> $b['dias_faltantes']);

        return $resultado;
    }

    public static function actualizar(string $cedula, array $datos): void
    {
        $pdo = getPDO();
        $sql = "UPDATE empleados SET
              nombre = :nombre, sexo = :sexo, cargo = :cargo, tipo_de_personal = :tipo_personal,
              grupo = :grupo, eps = :eps, pension = :pension, salario_basico = :salario,
              es_bombero_integral = :bombero, tipo_de_contrato = :contrato, estado = :estado,
              celular = :celular, correo = :correo, fecha_nacimiento = :fecha_nacimiento
            WHERE cedula = :cedula";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $datos['nombre'],
            'sexo' => $datos['sexo'] ?? null,
            'cargo' => $datos['cargo'],
            'tipo_personal' => $datos['tipo_de_personal'] ?? null,
            'grupo' => $datos['grupo'] ?? null,
            'eps' => $datos['eps'] ?? null,
            'pension' => $datos['pension'] ?? null,
            'salario' => $datos['salario_basico'] ?? null,
            'bombero' => $datos['es_bombero_integral'],
            'contrato' => $datos['tipo_de_contrato'],
            'estado' => $datos['estado'],
            'celular' => $datos['celular'] ?: null,
            'correo' => $datos['correo'] ?: null,
            'fecha_nacimiento' => $datos['fecha_nacimiento'] ?: null,
            'cedula' => $cedula,
        ]);
    }

    /**
     * Cambia la PK cedula. Gracias a ON UPDATE CASCADE en bolsillos, documentos (vía bolsillo)
     * y usuarios_empleados, esto propaga automáticamente el cambio a esas tablas.
     */
    public static function actualizarCedula(string $cedulaActual, string $cedulaNueva): void
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("UPDATE empleados SET cedula = :nueva WHERE cedula = :actual");
        $stmt->execute(['nueva' => $cedulaNueva, 'actual' => $cedulaActual]);
    }

    public static function actualizarFoto(string $cedula, string $rutaFoto): void
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("UPDATE empleados SET foto = :foto WHERE cedula = :cedula");
        $stmt->execute(['foto' => $rutaFoto, 'cedula' => $cedula]);
    }

    public static function actualizarRutaFotoPorCambioCedula(string $cedulaAnterior, string $cedulaNueva): void
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "UPDATE empleados 
         SET foto = REPLACE(foto, CONCAT('hv_', :anterior, '/'), CONCAT('hv_', :nueva, '/'))
         WHERE cedula = :cedula AND foto IS NOT NULL"
        );
        $stmt->execute(['anterior' => $cedulaAnterior, 'nueva' => $cedulaNueva, 'cedula' => $cedulaNueva]);
    }
    /**
     * Formatea una fecha 'Y-m-d' como "13 de abril de 2003" (orden fijo: día, mes en letras, año).
     */
    public static function formatearFechaLarga(?string $fecha): string
    {
        if (!$fecha)
            return '-';
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$d)
            return $fecha;
        return (int) $d->format('d') . ' de ' . strtolower(self::mesEnEspanol((int) $d->format('m'))) . ' de ' . $d->format('Y');
    }

}