<?php
// src/controllers/EmpleadoController.php
require_once __DIR__ . '/../models/EmpleadoModel.php';
require_once __DIR__ . '/../helpers/FileManager.php';
require_once __DIR__ . '/../models/BolsilloModel.php';

class EmpleadoController {

   public static array $cargosValidos = [
        'Auxiliar en Talento Humano','Director de talento humano','Auxiliar de Extintores',
        'Enfermero/a','Teniente','Practicante Sena','Practicante Fundetec','Practicante otra entidad',
        'Servicios Generales','Maquinista','Guardia','Recepcionista','Auxiliar administrativo',
        'Director Académico','Director de negocios','Tecnico en soporte sistemas','Tecnico archivista',
        'Coordinador SST','Auxiliar SST','Jefe de prensa','Contador','Auxiliar de contaduría',
        'Almacenista','Supervisor','Coordinador de banda','Conductor de ambulancia','Aspirante','Voluntario'
    ];

    public static function crear(array $datos): array {
        $errores = [];

        $cedula = trim($datos['cedula'] ?? '');
        $nombre = trim($datos['nombre'] ?? '');
        $cargo = $datos['cargo'] ?? '';
        $esBomberoIntegral = isset($datos['es_bombero_integral']) ? 1 : 0;
        $tipoContrato = $datos['tipo_de_contrato'] ?? '';
        $estado = $datos['estado'] ?? 'activo';
        $celular = trim($datos['celular'] ?? '');
        $correo = trim($datos['correo'] ?? '');
        $fechaNacimiento = trim($datos['fecha_nacimiento'] ?? '');

        // --- Validación cédula (máx 10, alfanumérica, extranjera permitida) ---
        if (strlen($cedula) > 10) {
            $errores[] = 'La cédula no puede tener más de 10 caracteres.';
        }
        if (!preg_match('/^[A-Za-z0-9]{5,10}$/', $cedula)) {
            $errores[] = 'La cédula no puede tener más de 10 caracteres y puede ser extranjera (letras y números permitidos).';
        }
        if (EmpleadoModel::existeCedula($cedula)) {
            $errores[] = 'Ya existe un empleado con esa cédula.';
        }

        // --- Nombre ---
        if ($nombre === '' || strlen($nombre) > 150) {
            $errores[] = 'El nombre es obligatorio y debe tener máximo 150 caracteres.';
        }

        // --- Cargo ---
        if (!in_array($cargo, self::$cargosValidos, true)) {
            $errores[] = 'Debes seleccionar un cargo válido.';
        }

        // --- Tipo de contrato ---
        if (!in_array($tipoContrato, ['fijo', 'indefinido', 'ops'], true)) {
            $errores[] = 'Debes seleccionar un tipo de contrato válido.';
        }

        // --- Estado ---
        if (!in_array($estado, ['activo', 'no activo'], true)) {
            $errores[] = 'Estado inválido.';
        }

        // --- Celular (opcional, pero si viene debe ser 10 dígitos) ---
        if ($celular !== '' && !preg_match('/^[0-9]{10}$/', $celular)) {
            $errores[] = 'El celular debe tener 10 dígitos numéricos.';
        }

        // --- Correo (opcional, pero si viene debe ser válido) ---
        if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido.';
        }

        // --- Fecha de nacimiento (opcional, pero si viene debe ser fecha real y no futura) ---
        if ($fechaNacimiento !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $fechaNacimiento);
            if (!$d || $d->format('Y-m-d') !== $fechaNacimiento) {
                $errores[] = 'La fecha de nacimiento no es válida.';
            } elseif ($d > new DateTime('today')) {
                $errores[] = 'La fecha de nacimiento no puede ser futura.';
            }
        }

        if (!empty($errores)) {
            return ['ok' => false, 'errores' => $errores];
        }

        // --- Todo válido: crear en BD y luego en disco ---
        EmpleadoModel::crear([
            'cedula' => $cedula,
            'nombre' => $nombre,
            'cargo' => $cargo,
            'es_bombero_integral' => $esBomberoIntegral,
            'tipo_de_contrato' => $tipoContrato,
            'estado' => $estado,
            'celular' => $celular,
            'correo' => $correo,
            'fecha_nacimiento' => $fechaNacimiento,
        ]);

        try {
            FileManager::crearEstructuraEmpleado($cedula);
            try {
                FileManager::crearEstructuraEmpleado($cedula);
                BolsilloModel::crearBolsillosParaEmpleado($cedula);
            } catch (Exception $e) {
                EmpleadoModel::eliminar($cedula);
                FileManager::borrarEstructuraEmpleado($cedula); // por si alcanzó a crear algo físico
                return ['ok' => false, 'errores' => ['Error creando estructura del empleado: ' . $e->getMessage()]];
            }
        } catch (Exception $e) {
            // Si falla la creación física, revertimos el registro en BD para no dejar datos huérfanos
            EmpleadoModel::eliminar($cedula);
            return ['ok' => false, 'errores' => ['Error creando carpetas físicas: ' . $e->getMessage()]];
        }

        return ['ok' => true];
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
