<?php
// src/models/EmpleadoModel.php
require_once __DIR__ . '/../../config/database.php';

class EmpleadoModel
{

    public static function crear(array $datos): void
    {
        $pdo = getPDO();
        $sql = "INSERT INTO empleados 
            (cedula, lugar_expedicion, nombre, sexo, cargo, tipo_de_personal, eps, pension, arl, salario_basico,
             es_bombero_integral, tipo_jornada, jornada_hora_entrada, jornada_hora_salida,
             tipo_de_contrato, fecha_inicio_contrato, fecha_fin_contrato, estado, celular, correo, fecha_nacimiento)
            VALUES 
            (:cedula, :lugar_expedicion, :nombre, :sexo, :cargo, :tipo_personal, :eps, :pension, :arl, :salario,
             :bombero, :tipo_jornada, :jornada_entrada, :jornada_salida,
             :contrato, :fecha_inicio_contrato, :fecha_fin_contrato, :estado, :celular, :correo, :fecha_nacimiento)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'cedula' => $datos['cedula'],
            'lugar_expedicion' => $datos['lugar_expedicion'] ?? null,
            'nombre' => $datos['nombre'],
            'sexo' => $datos['sexo'] ?? null,
            'cargo' => $datos['cargo'],
            'tipo_personal' => $datos['tipo_de_personal'] ?? null,
            'eps' => $datos['eps'] ?? null,
            'pension' => $datos['pension'] ?? null,
            'arl' => $datos['arl'] ?? null,
            'salario' => $datos['salario_basico'] ?? null,
            'bombero' => $datos['es_bombero_integral'],
            'tipo_jornada' => $datos['tipo_jornada'],
            'jornada_entrada' => $datos['jornada_hora_entrada'] ?? null,
            'jornada_salida' => $datos['jornada_hora_salida'] ?? null,
            'contrato' => $datos['tipo_de_contrato'] ?: null,
            'fecha_inicio_contrato' => $datos['fecha_inicio_contrato'] ?? null,
            'fecha_fin_contrato' => $datos['fecha_fin_contrato'] ?? null,
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

    /**
     * Regla de negocio: a un empleado NO ACTIVO no se le generan formatos.
     * Los generadores la llaman justo después de cargar al empleado.
     */
    public static function exigirActivo(array $empleado): void
    {
        if (($empleado['estado'] ?? '') !== 'activo') {
            throw new InvalidArgumentException('El empleado está NO ACTIVO. Para generarle formatos primero actívalo en Empleados → Editar.');
        }
    }

    /** Arma WHERE + parámetros a partir de búsqueda y filtros (única fuente). */
    private static function construirWhere(string $busqueda, array $filtros): array
    {
        $condiciones = [];
        $params = [];

        if ($busqueda !== '') {
            $condiciones[] = '(cedula LIKE :b1 OR nombre LIKE :b2 OR cargo LIKE :b3 OR celular LIKE :b4 OR correo LIKE :b5)';
            $valor = '%' . $busqueda . '%';
            foreach (['b1', 'b2', 'b3', 'b4', 'b5'] as $k) {
                $params[$k] = $valor;
            }
        }
        if (!empty($filtros['contrato'])) {
            $condiciones[] = 'tipo_de_contrato = :f_contrato';
            $params['f_contrato'] = $filtros['contrato'];
        }
        if (!empty($filtros['cargo'])) {
            $condiciones[] = 'cargo = :f_cargo';
            $params['f_cargo'] = $filtros['cargo'];
        }
        if (!empty($filtros['estado'])) {
            $condiciones[] = 'estado = :f_estado';
            $params['f_estado'] = $filtros['estado'];
        }

        $where = $condiciones ? ' WHERE ' . implode(' AND ', $condiciones) : '';
        return [$where, $params];
    }

    public static function contar(string $busqueda = '', array $filtros = []): int
    {
        [$where, $params] = self::construirWhere($busqueda, $filtros);
        $stmt = getPDO()->prepare("SELECT COUNT(*) FROM empleados" . $where);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public static function listar(string $busqueda = '', int $limite = 10, int $offset = 0, array $filtros = []): array
    {
        $limite = max(1, min(100, $limite));
        $offset = max(0, $offset);
        [$where, $params] = self::construirWhere($busqueda, $filtros);

        $stmt = getPDO()->prepare("SELECT * FROM empleados" . $where . " ORDER BY nombre ASC LIMIT :limite OFFSET :offset");
        foreach ($params as $clave => $valor) {
            $stmt->bindValue(':' . $clave, $valor, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Todos los empleados que cumplen búsqueda + filtros, sin paginar (para Excel). */
    public static function listarParaExportar(string $busqueda = '', array $filtros = []): array
    {
        [$where, $params] = self::construirWhere($busqueda, $filtros);
        $stmt = getPDO()->prepare("SELECT * FROM empleados" . $where . " ORDER BY nombre ASC");
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
    public static function infoCumpleanos(?string $fechaNacimiento, int $diasVentana = 15): array
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
    public static function proximosCumpleanos(int $dias = 15): array
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

    /**
     * Devuelve todos los empleados activos que cumplen años en un mes/año concreto.
     * El año de nacimiento se ignora: solo se usa mes y día.
     */
    public static function cumpleanosDelMes(int $anio, int $mes): array
    {
        if ($mes < 1 || $mes > 12) {
            throw new InvalidArgumentException('Mes inválido.');
        }

        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "SELECT cedula, nombre, cargo, fecha_nacimiento
             FROM empleados
             WHERE fecha_nacimiento IS NOT NULL
               AND estado = 'activo'
               AND MONTH(fecha_nacimiento) = :mes
             ORDER BY DAY(fecha_nacimiento) ASC, nombre ASC"
        );
        $stmt->execute(['mes' => $mes]);

        $resultado = [];
        foreach ($stmt->fetchAll() as $emp) {
            $fecha = new DateTime($emp['fecha_nacimiento']);
            $emp['dia_cumpleanos'] = (int) $fecha->format('d');
            $emp['mes_cumpleanos'] = (int) $fecha->format('m');
            $resultado[] = $emp;
        }

        return $resultado;
    }

    public static function actualizar(string $cedula, array $datos): void
    {
        $pdo = getPDO();
        $sql = "UPDATE empleados SET
              lugar_expedicion = :lugar_expedicion,
              nombre = :nombre, sexo = :sexo, cargo = :cargo, tipo_de_personal = :tipo_personal,
              eps = :eps, pension = :pension, arl = :arl, salario_basico = :salario,
              es_bombero_integral = :bombero, tipo_jornada = :tipo_jornada,
              jornada_hora_entrada = :jornada_entrada, jornada_hora_salida = :jornada_salida,
              tipo_de_contrato = :contrato, fecha_inicio_contrato = :fecha_inicio_contrato, fecha_fin_contrato = :fecha_fin_contrato, estado = :estado,
              celular = :celular, correo = :correo, fecha_nacimiento = :fecha_nacimiento
            WHERE cedula = :cedula";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'lugar_expedicion' => $datos['lugar_expedicion'] ?? null,
            'nombre' => $datos['nombre'],
            'sexo' => $datos['sexo'] ?? null,
            'cargo' => $datos['cargo'],
            'tipo_personal' => $datos['tipo_de_personal'] ?? null,
            'eps' => $datos['eps'] ?? null,
            'pension' => $datos['pension'] ?? null,
            'arl' => $datos['arl'] ?? null,
            'salario' => $datos['salario_basico'] ?? null,
            'bombero' => $datos['es_bombero_integral'],
            'tipo_jornada' => $datos['tipo_jornada'],
            'jornada_entrada' => $datos['jornada_hora_entrada'] ?? null,
            'jornada_salida' => $datos['jornada_hora_salida'] ?? null,
            'contrato' => $datos['tipo_de_contrato'] ?: null,
            'fecha_inicio_contrato' => $datos['fecha_inicio_contrato'] ?? null,
            'fecha_fin_contrato' => $datos['fecha_fin_contrato'] ?? null,
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

    public static function actualizarFoto(string $cedula, ?string $rutaFoto): void
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