<?php
// src/controllers/EmpleadoController.php
require_once __DIR__ . '/../models/EmpleadoModel.php';
require_once __DIR__ . '/../helpers/FileManager.php';
require_once __DIR__ . '/../models/BolsilloModel.php';
require_once __DIR__ . '/../models/DocumentoModel.php';

class EmpleadoController {

   public static array $cargosValidos = [
        'Auxiliar en Talento Humano','Director de talento humano','Auxiliar de Extintores',
        'Enfermero/a','Teniente','Practicante Sena','Practicante Fundetec','Practicante otra entidad',
        'Servicios Generales','Maquinista','Guardia','Recepcionista','Auxiliar administrativo',
        'Director Académico','Director de negocios','Tecnico en soporte sistemas','Tecnico archivista',
        'Coordinador SST','Auxiliar SST','Jefe de prensa','Contador','Auxiliar de contaduría',
        'Almacenista','Supervisor','Coordinador de banda','Conductor de ambulancia','Aspirante','Voluntario'
    ];
        private static function validarCamposComunes(array $datos): array {
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

    public static function crear(array $datos, ?array $archivoFoto = null): array {
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

    EmpleadoModel::crear([
        'cedula' => $cedula,
        'nombre' => trim($datos['nombre']),
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
        // Si la foto falla no revertimos la creación — la foto es opcional
    }

    return ['ok' => true];
}

public static function actualizar(string $cedulaActual, array $datos, ?array $archivoFoto = null): array {
    $errores = self::validarCamposComunes($datos);

    $cedulaNueva = trim($datos['cedula'] ?? '');
    if (strlen($cedulaNueva) > 10) {
        $errores[] = 'La cédula no puede tener más de 10 caracteres.';
    }
    if (!preg_match('/^[A-Za-z0-9]{5,10}$/', $cedulaNueva)) {
        $errores[] = 'La cédula no puede tener más de 10 caracteres y puede ser extranjera (letras y números permitidos).';
    }

    $cambioDeCedula = $cedulaNueva !== $cedulaActual;
    if ($cambioDeCedula && EmpleadoModel::existeCedula($cedulaNueva)) {
        $errores[] = 'Ya existe otro empleado con esa cédula.';
    }

    if (!empty($errores)) {
        return ['ok' => false, 'errores' => $errores];
    }

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
        FileManager::renombrarEstructuraEmpleado($cedulaNueva, $cedulaActual); // revertir disco si falla la BD
        return ['ok' => false, 'errores' => ['Error actualizando la base de datos: ' . $e->getMessage()]];
    }
    $cedulaFinal = $cedulaNueva;
}

    EmpleadoModel::actualizar($cedulaFinal, [
        'nombre' => trim($datos['nombre']),
        'cargo' => $datos['cargo'],
        'es_bombero_integral' => isset($datos['es_bombero_integral']) ? 1 : 0,
        'tipo_de_contrato' => $datos['tipo_de_contrato'],
        'estado' => $datos['estado'] ?? 'activo',
        'celular' => trim($datos['celular'] ?? ''),
        'correo' => trim($datos['correo'] ?? ''),
        'fecha_nacimiento' => trim($datos['fecha_nacimiento'] ?? ''),
    ]);

    if ($archivoFoto && isset($archivoFoto['error']) && $archivoFoto['error'] !== UPLOAD_ERR_NO_FILE) {
        $resultadoFoto = FileManager::guardarFoto($cedulaFinal, $archivoFoto);
        if ($resultadoFoto['ok']) {
            EmpleadoModel::actualizarFoto($cedulaFinal, $resultadoFoto['ruta']);
        }
    }

    return ['ok' => true, 'cedula' => $cedulaFinal];
}

    /**
     * Elimina un empleado, validando la contraseña de un superadmin_talento_humano.
     */
    public static function eliminar(string $cedula, string $passwordConfirmacion): array {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT password_hash FROM usuarios WHERE id = :id AND rol = 'superadmin_talento_humano'");
        $stmt->execute(['id' => $_SESSION['superadmin_id']]);
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
