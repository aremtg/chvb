<?php
// src/controllers/EmpleadoController.php
require_once __DIR__ . '/../models/EmpleadoModel.php';
require_once __DIR__ . '/../helpers/FileManager.php';
require_once __DIR__ . '/../models/BolsilloModel.php';
require_once __DIR__ . '/../models/DocumentoModel.php';
require_once __DIR__ . '/../models/NotificacionModel.php';

class EmpleadoController
{

    public static array $cargosValidos = [
        'Auxiliar en Talento Humano',
        'Director de talento humano',
        'Auxiliar de Extintores',
        'Enfermero/a',
        'Teniente',
        'Practicante Sena',
        'Practicante Fundetec',
        'Practicante otra entidad',
        'Servicios Generales',
        'Maquinista',
        'Guardia',
        'Recepcionista',
        'Auxiliar administrativo',
        'Director Académico',
        'Director de negocios',
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
        'Voluntario'
    ];
    private static function validarCamposComunes(array $datos): array
    {
        $errores = [];

        $nombre = trim($datos['nombre'] ?? '');
        $cargo = $datos['cargo'] ?? '';
        $tipoContrato = $datos['tipo_de_contrato'] ?? '';
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
        if (!in_array($tipoContrato, ['fijo', 'indefinido', 'ops'], true)) {
            $errores[] = 'Debes seleccionar un tipo de contrato válido.';
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
            'cargo' => $datos['cargo'],
            'es_bombero_integral' => isset($datos['es_bombero_integral']) ? 1 : 0,
            'tipo_de_contrato' => $datos['tipo_de_contrato'],
            'estado' => $datos['estado'] ?? 'activo',
            'celular' => trim($datos['celular'] ?? ''),
            'correo' => trim($datos['correo'] ?? ''),
            'fecha_nacimiento' => trim($datos['fecha_nacimiento'] ?? ''),
        ]);

        try {
            FileManager::crearEstructuraEmpleado($cedula);
            BolsilloModel::crearBolsillosParaEmpleado($cedula);
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
        }

        $rolActor = $_SESSION['superadmin_rol'] ?? 'superadmin_talento_humano';
        if ($rolActor === 'auxiliar_talento_humano') {
            $usuarioNombre = $_SESSION['superadmin_username'];
            NotificacionModel::crear(
                $_SESSION['superadmin_id'],
                $usuarioNombre,
                $cedula,
                'creacion',
                "{$usuarioNombre} creó un nuevo empleado llamado {$nombreEmpleado}, con cédula {$cedula}"
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
            try {
                EmpleadoModel::actualizarCedula($cedulaActual, $cedulaNueva);
                DocumentoModel::actualizarRutasPorCambioCedula($cedulaActual, $cedulaNueva);
                EmpleadoModel::actualizarRutaFotoPorCambioCedula($cedulaActual, $cedulaNueva);
            } catch (Exception $e) {
                FileManager::renombrarEstructuraEmpleado($cedulaNueva, $cedulaActual);
                return ['ok' => false, 'errores' => ['Error actualizando la base de datos: ' . $e->getMessage()]];
            }
            $cedulaFinal = $cedulaNueva;
        }

        $datosNuevos = [
            'nombre' => trim($datos['nombre']),
            'cargo' => $datos['cargo'],
            'es_bombero_integral' => isset($datos['es_bombero_integral']) ? 1 : 0,
            'tipo_de_contrato' => $datos['tipo_de_contrato'],
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
                        "{$usuarioNombre} cambió la foto de perfil de {$cedulaFinal}"
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
        $formatearFecha = function (?string $fecha): string {
            if (!$fecha)
                return 'sin fecha';
            $d = DateTime::createFromFormat('Y-m-d', $fecha);
            if (!$d)
                return $fecha;
            return $d->format('d') . '/' . EmpleadoModel::mesEnEspanol((int) $d->format('m')) . '/' . $d->format('Y');
        };

        $campos = [
            'nombre' => ['etiqueta' => 'el nombre', 'formato' => fn($v) => $v],
            'cargo' => ['etiqueta' => 'el cargo', 'formato' => fn($v) => $v],
            'correo' => ['etiqueta' => 'el correo', 'formato' => fn($v) => $v ?: 'sin correo'],
            'estado' => ['etiqueta' => 'el estado', 'formato' => fn($v) => $v],
            'es_bombero_integral' => ['etiqueta' => 'el campo bombero integral', 'formato' => fn($v) => $v == 1 ? 'Sí' : 'No'],
            'fecha_nacimiento' => ['etiqueta' => 'la fecha de nacimiento', 'formato' => $formatearFecha],
        ];

        foreach ($campos as $campo => $conf) {
            $valorAnterior = $anterior[$campo] ?? null;
            $valorNuevo = $nuevo[$campo] ?? null;

            // Comparación como texto para detectar cambios reales, incluso entre "0"/0 o null/""
            if ((string) $valorAnterior === (string) $valorNuevo) {
                continue;
            }

            $textoAnterior = ($conf['formato'])($valorAnterior);
            $textoNuevo = ($conf['formato'])($valorNuevo);

           $mensaje = "{$usuarioNombre} editó {$conf['etiqueta']} de {$cedula}: antes \"{$textoAnterior}\", ahora \"{$textoNuevo}\"";

            NotificacionModel::crear($usuarioId, $usuarioNombre, $cedula, $campo, $mensaje);
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
