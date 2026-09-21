<?php
// src/controllers/EmpleadoController.php
require_once __DIR__ . '/../models/EmpleadoModel.php';
require_once __DIR__ . '/../helpers/FileManager.php';
require_once __DIR__ . '/../models/BolsilloModel.php';
require_once __DIR__ . '/../models/DocumentoModel.php';
require_once __DIR__ . '/../models/PermisoModel.php';
require_once __DIR__ . '/../models/FirmaModel.php';
require_once __DIR__ . '/../models/NotificacionModel.php';
require_once __DIR__ . '/../helpers/ReconciliadorArchivos.php';

class EmpleadoController
{

    public static array $cargosValidos = [
        'Auxiliar en Talento Humano',
        'Director de talento humano',
        'Auxiliar de Extintores',
        'Enfermero/a',
        'Practicante Sena',
        'Practicante Fundetec',
        'Practicante otra entidad',
        'Servicios Generales',
        'Maquinista',
        'Guardia',
        'Recepcionista',
        'Administrativo',
        'Auxiliar administrativo',
        'Director Académico',
        'Director de negocios',
        'Directora administrativa y financiera',
        'Tecnico en soporte sistemas',
        'Tecnico archivista',
        'Coordinador SST',
        'Auxiliar SST',
        'Jefe de prensa',
        'Contador',
        'Auxiliar de contaduría',
        'Almacenista',
        'Supervisor',
        'Coordinador de banda',
        'Conductor de ambulancia',
        'Aspirante',
        'Voluntario',
        'Secretario recaudador',
        'Auxiliar de enfermería',
        'Docente de banda marcial',
        'PAMEC',
        'Revisor(a) fiscal',
        'Comandante de estación',
        'Bombero integral'
    ];

    public static array $tiposDePersonal = ['Bombero', 'Civil'];
    public static array $epsValidas = ['Sanitas', 'Nueva EPS', 'Capresoca', 'Salud Total'];
    public static array $pensionesValidas = ['Colfondos', 'Porvenir', 'Colpensiones', 'Protección', 'NA'];
    public static array $tiposDeContrato = ['Fijo', 'Indefinido', 'OPS', 'SENA', 'OPS SEMY', 'No aplica'];
    public static array $arlsValidas = ['Positiva', 'SURA', 'Colmena', 'AXA Colpatria', 'Seguros Bolívar'];

    private static function validarCamposComunes(array $datos): array
    {
        $errores = [];

        $nombre = trim($datos['nombre'] ?? '');
        $cargo = $datos['cargo'] ?? '';
        $tipoContrato = trim($datos['tipo_de_contrato'] ?? '');
        $estado = $datos['estado'] ?? 'activo';
        $celular = trim($datos['celular'] ?? '');
        $correo = trim($datos['correo'] ?? '');
        $fechaNacimiento = trim($datos['fecha_nacimiento'] ?? '');

        if ($nombre === '' || strlen($nombre) > 150) {
            $errores[] = 'El nombre es obligatorio y debe tener máximo 150 caracteres.';
        }
        if (!in_array($cargo, self::$cargosValidos, true)) {
            $errores[] = 'Debes seleccionar un cargo válido.';
        }
        if ($tipoContrato !== '' && !in_array($tipoContrato, self::$tiposDeContrato, true)) {
            $errores[] = 'Tipo de contrato inválido.';
        }

        $fechaInicio = trim($datos['fecha_inicio_contrato'] ?? '');
        $fechaFin = trim($datos['fecha_fin_contrato'] ?? '');
        $contratosConFin = ['Fijo', 'OPS', 'SENA', 'OPS SEMY'];
        if ($fechaInicio !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $fechaInicio);
            if (!$d || $d->format('Y-m-d') !== $fechaInicio) {
                $errores[] = 'La fecha de inicio del contrato no es válida.';
            }
        }
        if ($fechaFin !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $fechaFin);
            if (!$d || $d->format('Y-m-d') !== $fechaFin) {
                $errores[] = 'La fecha de fin del contrato no es válida.';
            }
        }
        if (in_array($tipoContrato, $contratosConFin, true) && $fechaFin !== '' && $fechaInicio !== '' && $fechaFin < $fechaInicio) {
            $errores[] = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
        }
        if (!in_array($estado, ['activo', 'no activo'], true)) {
            $errores[] = 'Estado inválido.';
        }
        if ($celular !== '' && !preg_match('/^[0-9]{10}$/', $celular)) {
            $errores[] = 'El celular debe tener 10 dígitos numéricos.';
        }
        if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido.';
        }
        if ($fechaNacimiento !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $fechaNacimiento);
            if (!$d || $d->format('Y-m-d') !== $fechaNacimiento) {
                $errores[] = 'La fecha de nacimiento no es válida.';
            } elseif ($d > new DateTime('today')) {
                $errores[] = 'La fecha de nacimiento no puede ser futura.';
            }
        }
        $sexo = $datos['sexo'] ?? '';
        $tipoPersonal = $datos['tipo_de_personal'] ?? '';
        $eps = $datos['eps'] ?? '';
        $pension = $datos['pension'] ?? '';
        $arl = $datos['arl'] ?? '';
        $salarioBasico = trim($datos['salario_basico'] ?? '');

        if ($sexo === '') {
            $errores[] = 'El sexo es obligatorio.';
        } elseif (!in_array($sexo, ['F', 'M'], true)) {
            $errores[] = 'El sexo debe ser F o M.';
        }
        if ($tipoPersonal === '') {
            $errores[] = 'El tipo de personal es obligatorio.';
        } elseif (!in_array($tipoPersonal, self::$tiposDePersonal, true)) {
            $errores[] = 'Tipo de personal inválido.';
        }
        if ($eps !== '' && !in_array($eps, self::$epsValidas, true)) {
            $errores[] = 'EPS inválida.';
        }
        if ($pension !== '' && !in_array($pension, self::$pensionesValidas, true)) {
            $errores[] = 'Fondo de pensión inválido.';
        }
        if ($arl !== '' && !in_array($arl, self::$arlsValidas, true)) {
            $errores[] = 'ARL inválida.';
        }
        if ($salarioBasico !== '' && (!is_numeric($salarioBasico) || (float) $salarioBasico < 0)) {
            $errores[] = 'El salario básico debe ser un número válido mayor o igual a 0.';
        }


        return $errores;
    }

    public static function crear(array $datos, ?array $archivoFoto = null): array
    {
        $errores = self::validarCamposComunes($datos);

        $cedula = trim($datos['cedula'] ?? '');
        if (strlen($cedula) > 10) {
            $errores[] = 'La cédula no puede tener más de 10 caracteres.';
        }
        if (!preg_match('/^[A-Za-z0-9]{5,10}$/', $cedula)) {
            $errores[] = 'La cédula no puede tener más de 10 caracteres y puede ser extranjera (letras y números permitidos).';
        }
        if (EmpleadoModel::existeCedula($cedula)) {
            $errores[] = 'Ya existe un empleado con esa cédula.';
        }

        if (!empty($errores)) {
            return ['ok' => false, 'errores' => $errores];
        }

        $nombreEmpleado = trim($datos['nombre']);

        EmpleadoModel::crear([
            'cedula' => $cedula,
            'nombre' => $nombreEmpleado,
            'sexo' => $datos['sexo'] ?: null,
            'cargo' => $datos['cargo'],
            'tipo_de_personal' => $datos['tipo_de_personal'] ?: null,
            'eps' => $datos['eps'] ?: null,
            'pension' => $datos['pension'] ?: null,
            'arl' => $datos['arl'] ?: null,
            'salario_basico' => $datos['salario_basico'] !== '' ? $datos['salario_basico'] : null,
            'es_bombero_integral' => isset($datos['es_bombero_integral']) ? 1 : 0,
            'tipo_de_contrato' => $datos['tipo_de_contrato'] ?: null,
            'fecha_inicio_contrato' => $datos['fecha_inicio_contrato'] ?: null,
            'fecha_fin_contrato' => $datos['fecha_fin_contrato'] ?: null,
            'estado' => $datos['estado'] ?? 'activo',
            'celular' => trim($datos['celular'] ?? ''),
            'correo' => trim($datos['correo'] ?? ''),
            'fecha_nacimiento' => trim($datos['fecha_nacimiento'] ?? ''),
        ]);

        try {
            FileManager::crearEstructuraEmpleado($cedula);
            BolsilloModel::crearBolsillosParaEmpleado($cedula);
            ReconciliadorArchivos::importarDocumentosExistentes($cedula);
        } catch (Exception $e) {
            EmpleadoModel::eliminar($cedula);
            FileManager::borrarEstructuraEmpleado($cedula);
            return ['ok' => false, 'errores' => ['Error creando estructura del empleado: ' . $e->getMessage()]];
        }

        if ($archivoFoto && isset($archivoFoto['error']) && $archivoFoto['error'] !== UPLOAD_ERR_NO_FILE) {
            $resultadoFoto = FileManager::guardarFoto($cedula, $archivoFoto);
            if ($resultadoFoto['ok']) {
                EmpleadoModel::actualizarFoto($cedula, $resultadoFoto['ruta']);
            }
        } else {
            // No subieron foto nueva: si la carpeta reutilizada ya tenía una, la detectamos y usamos
            $fotoExistente = ReconciliadorArchivos::detectarFotoExistente($cedula);
            if ($fotoExistente) {
                EmpleadoModel::actualizarFoto($cedula, $fotoExistente);
            }
        }
        $rolActor = $_SESSION['superadmin_rol'] ?? 'superadmin_talento_humano';
        if ($rolActor === 'auxiliar_talento_humano') {
            $usuarioNombre = $_SESSION['superadmin_username'];
            NotificacionModel::crear(
                $_SESSION['superadmin_id'],
                $usuarioNombre,
                $cedula,
                'creacion',
                "\"{$usuarioNombre}\" creó un nuevo empleado llamado {$nombreEmpleado}, con cédula {$cedula}",
                "/chvb/public/empleados.php?q=" . urlencode($cedula)
            );
        }

        return ['ok' => true];
    }

    public static function actualizar(string $cedulaActual, array $datos, ?array $archivoFoto = null): array
    {
        $rolActor = $_SESSION['superadmin_rol'] ?? 'superadmin_talento_humano';
        $esAuxiliar = $rolActor === 'auxiliar_talento_humano';

        $errores = self::validarCamposComunes($datos);

        $cedulaNueva = trim($datos['cedula'] ?? '');
        if (strlen($cedulaNueva) > 10) {
            $errores[] = 'La cédula no puede tener más de 10 caracteres.';
        }
        if (!preg_match('/^[A-Za-z0-9]{5,10}$/', $cedulaNueva)) {
            $errores[] = 'La cédula no puede tener más de 10 caracteres y puede ser extranjera (letras y números permitidos).';
        }

        $cambioDeCedula = $cedulaNueva !== $cedulaActual;

        if ($esAuxiliar && $cambioDeCedula) {
            return ['ok' => false, 'errores' => ['No tienes permiso para cambiar la cédula de un empleado. Contacta al Super Administrador.']];
        }

        if ($cambioDeCedula && EmpleadoModel::existeCedula($cedulaNueva)) {
            $errores[] = 'Ya existe otro empleado con esa cédula.';
        }

        if (!empty($errores)) {
            return ['ok' => false, 'errores' => $errores];
        }

        // Guardamos el estado ANTES de actualizar, para poder comparar y notificar
        $empleadoAnterior = EmpleadoModel::obtenerPorCedula($cedulaActual);

        $cedulaFinal = $cedulaActual;

        if ($cambioDeCedula) {
            if (!FileManager::renombrarEstructuraEmpleado($cedulaActual, $cedulaNueva)) {
                return ['ok' => false, 'errores' => ['No se pudo renombrar la carpeta física del empleado. No se guardó ningún cambio.']];
            }
            $pdo = getPDO();
            try {
                $pdo->beginTransaction();

                EmpleadoModel::actualizarCedula($cedulaActual, $cedulaNueva);
                DocumentoModel::actualizarRutasPorCambioCedula($cedulaActual, $cedulaNueva);
                PermisoModel::actualizarRutasPorCambioCedula($cedulaActual, $cedulaNueva);
                FirmaModel::actualizarRutaPorCambioCedula($cedulaActual, $cedulaNueva);
                EmpleadoModel::actualizarRutaFotoPorCambioCedula($cedulaActual, $cedulaNueva);

                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                // La BD queda con la cédula anterior y restauramos la carpeta física.
                FileManager::renombrarEstructuraEmpleado($cedulaNueva, $cedulaActual);
                return ['ok' => false, 'errores' => ['Error actualizando la base de datos: ' . $e->getMessage()]];
            }
            $cedulaFinal = $cedulaNueva;
        }

        $datosNuevos = [
            'nombre' => trim($datos['nombre']),
            'sexo' => $datos['sexo'] ?: null,
            'cargo' => $datos['cargo'],
            'tipo_de_personal' => $datos['tipo_de_personal'] ?: null,
            'eps' => $datos['eps'] ?: null,
            'pension' => $datos['pension'] ?: null,
            'arl' => $datos['arl'] ?: null,
            'salario_basico' => $datos['salario_basico'] !== '' ? $datos['salario_basico'] : null,
            'es_bombero_integral' => isset($datos['es_bombero_integral']) ? 1 : 0,
            'tipo_de_contrato' => $datos['tipo_de_contrato'] ?: null,
            'fecha_inicio_contrato' => $datos['fecha_inicio_contrato'] ?: null,
            'fecha_fin_contrato' => $datos['fecha_fin_contrato'] ?: null,
            'estado' => $datos['estado'] ?? 'activo',
            'celular' => trim($datos['celular'] ?? ''),
            'correo' => trim($datos['correo'] ?? ''),
            'fecha_nacimiento' => trim($datos['fecha_nacimiento'] ?? ''),
        ];
        EmpleadoModel::actualizar($cedulaFinal, $datosNuevos);

        if ($esAuxiliar && $empleadoAnterior) {
            self::registrarNotificacionesCambios($cedulaFinal, $empleadoAnterior, $datosNuevos);
        }

        if ($archivoFoto && isset($archivoFoto['error']) && $archivoFoto['error'] !== UPLOAD_ERR_NO_FILE) {
            $resultadoFoto = FileManager::guardarFoto($cedulaFinal, $archivoFoto);
            if ($resultadoFoto['ok']) {
                EmpleadoModel::actualizarFoto($cedulaFinal, $resultadoFoto['ruta']);

                if ($esAuxiliar) {
                    $usuarioNombre = $_SESSION['superadmin_username'];
                    NotificacionModel::crear(
                        $_SESSION['superadmin_id'],
                        $usuarioNombre,
                        $cedulaFinal,
                        'foto',
                        "\"{$usuarioNombre}\" cambió la foto de perfil de \"{$datosNuevos['nombre']}\"",
                        "/chvb/public/empleados.php?q=" . urlencode($cedulaFinal)
                    );
                }
            }
        }

        return ['ok' => true, 'cedula' => $cedulaFinal];
    }

    /**
     * Compara los valores viejos vs nuevos de los campos vigilados, y crea una notificación
     * por cada campo que haya cambiado, para que el Super Admin la revise.
     */
    private static function registrarNotificacionesCambios(string $cedula, array $anterior, array $nuevo): void
    {
        $usuarioId = $_SESSION['superadmin_id'];
        $usuarioNombre = $_SESSION['superadmin_username'];
        $nombreEmpleadoActual = $anterior['nombre'] ?? $cedula;
        $enlace = "/chvb/public/empleados.php?q=" . urlencode($cedula);

        $campos = [
            'nombre' => ['etiqueta' => 'el nombre', 'formato' => fn($v) => $v],
            'cargo' => ['etiqueta' => 'el cargo', 'formato' => fn($v) => $v],
            'correo' => ['etiqueta' => 'el correo', 'formato' => fn($v) => $v ?: 'sin correo'],
            'estado' => ['etiqueta' => 'el estado', 'formato' => fn($v) => $v],
            'es_bombero_integral' => ['etiqueta' => 'el campo bombero integral', 'formato' => fn($v) => $v == 1 ? 'Sí' : 'No'],
            'fecha_nacimiento' => ['etiqueta' => 'la fecha de nacimiento', 'formato' => fn($v) => EmpleadoModel::formatearFechaLarga($v)],
            'sexo' => ['etiqueta' => 'el sexo', 'formato' => fn($v) => $v ?: 'sin definir'],
            'tipo_de_personal' => ['etiqueta' => 'el tipo de personal', 'formato' => fn($v) => $v ?: 'sin definir'],
            'eps' => ['etiqueta' => 'la EPS', 'formato' => fn($v) => $v ?: 'sin definir'],
            'pension' => ['etiqueta' => 'el fondo de pensión', 'formato' => fn($v) => $v ?: 'sin definir'],
            'arl' => ['etiqueta' => 'la ARL', 'formato' => fn($v) => $v ?: 'sin definir'],
            'tipo_de_contrato' => ['etiqueta' => 'el tipo de contrato', 'formato' => fn($v) => $v ?: 'sin definir'],
            'fecha_inicio_contrato' => ['etiqueta' => 'la fecha de inicio del contrato', 'formato' => fn($v) => EmpleadoModel::formatearFechaLarga($v)],
            'fecha_fin_contrato' => ['etiqueta' => 'la fecha de fin del contrato', 'formato' => fn($v) => EmpleadoModel::formatearFechaLarga($v)],
            'salario_basico' => ['etiqueta' => 'el salario básico', 'formato' => fn($v) => $v !== null ? '$' . number_format((float) $v, 0, ',', '.') : 'sin definir'],
        ];

        foreach ($campos as $campo => $conf) {
            $valorAnterior = $anterior[$campo] ?? null;
            $valorNuevo = $nuevo[$campo] ?? null;

            if ((string) $valorAnterior === (string) $valorNuevo) {
                continue;
            }

            $textoAnterior = ($conf['formato'])($valorAnterior);
            $textoNuevo = ($conf['formato'])($valorNuevo);

            if ($campo === 'nombre') {
                $mensaje = "\"{$usuarioNombre}\" editó {$conf['etiqueta']} de {$cedula}: antes \"{$textoAnterior}\", ahora \"{$textoNuevo}\"";
            } else {
                $mensaje = "\"{$usuarioNombre}\" editó {$conf['etiqueta']} de \"{$nombreEmpleadoActual}\": antes \"{$textoAnterior}\", ahora \"{$textoNuevo}\"";
            }

            NotificacionModel::crear($usuarioId, $usuarioNombre, $cedula, $campo, $mensaje, $enlace);
        }
    }
    /**
     * Elimina un empleado, validando la contraseña de un superadmin_talento_humano.
     */
    public static function eliminar(string $cedula, string $passwordConfirmacion): array
    {
        $rolActor = $_SESSION['superadmin_rol'] ?? 'superadmin_talento_humano';
        if ($rolActor === 'auxiliar_talento_humano') {
            return ['ok' => false, 'error' => 'No tienes permiso para eliminar empleados. Contacta al Super Administrador.'];
        }

        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT password_hash FROM usuarios WHERE id = :id AND rol = :rol");
        $stmt->execute(['id' => $_SESSION['superadmin_id'], 'rol' => $rolActor]);
        $usuario = $stmt->fetch();

        if (!$usuario || !password_verify($passwordConfirmacion, $usuario['password_hash'])) {
            return ['ok' => false, 'error' => 'Contraseña de Talento Humano incorrecta.'];
        }

        if (!EmpleadoModel::obtenerPorCedula($cedula)) {
            return ['ok' => false, 'error' => 'El empleado no existe.'];
        }

        EmpleadoModel::eliminar($cedula);
        FileManager::borrarEstructuraEmpleado($cedula);

        return ['ok' => true];
    }
}
