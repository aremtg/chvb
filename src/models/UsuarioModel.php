<?php
// src/models/UsuarioModel.php
// Cuentas del panel (tabla usuarios): superadmin, auxiliar y teniente.
// OJO: NO se borran cuentas, se DESACTIVAN. Así el historial (quién firmó, anuló, creó...) no se rompe.
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/roles.php';

class UsuarioModel
{
    public const MAX_INTENTOS = 5;
    public const MINUTOS_BLOQUEO = 5;

    private const COLUMNAS_LISTA = 'id, username, nombre_completo, rol, activo, debe_cambiar_password,
        ultimo_acceso, created_at, bloqueado_hasta, creado_por, desactivado_at';

    public static function listar(): array
    {
        $roles = "'" . implode("','", ROLES_PANEL) . "'";
        return getPDO()->query(
            'SELECT ' . self::COLUMNAS_LISTA . " FROM usuarios
             ORDER BY activo DESC, FIELD(rol, $roles), username"
        )->fetchAll();
    }

    public static function obtener(int $id): ?array
    {
        $stmt = getPDO()->prepare('SELECT ' . self::COLUMNAS_LISTA . ' FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function obtenerPorUsername(string $username): ?array
    {
        $stmt = getPDO()->prepare('SELECT ' . self::COLUMNAS_LISTA . ' FROM usuarios WHERE username = :u');
        $stmt->execute(['u' => $username]);
        return $stmt->fetch() ?: null;
    }

    /** Crea la cuenta con contraseña TEMPORAL: la persona debe cambiarla en su primer ingreso. */
    public static function crear(string $username, ?string $nombre, string $rol, string $passwordTemporal, int $creadoPor): int
    {
        $pdo = getPDO();
        $pdo->prepare(
            'INSERT INTO usuarios (username, nombre_completo, password_hash, rol, activo, debe_cambiar_password, password_cambiada_at, creado_por)
             VALUES (:u, :n, :h, :r, 1, 1, NOW(), :c)'
        )->execute([
            'u' => $username, 'n' => $nombre, 'h' => password_hash($passwordTemporal, PASSWORD_DEFAULT),
            'r' => $rol, 'c' => $creadoPor,
        ]);
        return (int) $pdo->lastInsertId();
    }

    /**
     * Cambia el rol. Devuelve null si salió bien o el mensaje de error.
     * Nunca deja al sistema sin un superadmin activo.
     */
    public static function cambiarRol(int $id, string $rol): ?string
    {
        return self::modificarProtegiendoSuperadmin(
            $id,
            fn(array $objetivo): bool => $rol !== ROL_SUPERADMIN_TH,   // ¿deja de ser superadmin?
            'UPDATE usuarios SET rol = :v WHERE id = :id',
            $rol
        );
    }

    /** Activa o desactiva la cuenta (sin borrarla). Nunca deja al sistema sin superadmin activo. */
    public static function establecerActivo(int $id, bool $activo): ?string
    {
        // desactivado_at = cuándo se dio de baja (NULL si está activa): sirve para saber cuáles llevan años inactivas.
        $sql = $activo
            ? 'UPDATE usuarios SET activo = :v, desactivado_at = NULL, intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = :id'
            : 'UPDATE usuarios SET activo = :v, desactivado_at = NOW() WHERE id = :id';
        return self::modificarProtegiendoSuperadmin($id, fn(array $o): bool => !$activo, $sql, $activo ? 1 : 0);
    }

    /**
     * Transacción con bloqueo de filas: evita que dos superadmins se quiten el acceso a la vez
     * y el sistema quede sin ninguno.
     * @param callable $quitaSuperadmin recibe la fila objetivo; true si el cambio le quita el poder de superadmin
     */
    private static function modificarProtegiendoSuperadmin(int $id, callable $quitaSuperadmin, string $sql, $valor): ?string
    {
        $pdo = getPDO();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT id, rol, activo FROM usuarios WHERE id = :id OR rol = :sa FOR UPDATE');
            $stmt->execute(['id' => $id, 'sa' => ROL_SUPERADMIN_TH]);
            $filas = $stmt->fetchAll();

            $objetivo = null;
            $otrosSuperadminsActivos = 0;
            foreach ($filas as $f) {
                if ((int) $f['id'] === $id) {
                    $objetivo = $f;
                } elseif ($f['rol'] === ROL_SUPERADMIN_TH && (int) $f['activo'] === 1) {
                    $otrosSuperadminsActivos++;
                }
            }
            if (!$objetivo) {
                $pdo->rollBack();
                return 'El usuario no existe.';
            }
            $eraSuperadminActivo = $objetivo['rol'] === ROL_SUPERADMIN_TH && (int) $objetivo['activo'] === 1;
            if ($eraSuperadminActivo && $quitaSuperadmin($objetivo) && $otrosSuperadminsActivos === 0) {
                $pdo->rollBack();
                return 'No se puede: debe quedar al menos un superadmin activo.';
            }

            $pdo->prepare($sql)->execute(['v' => $valor, 'id' => $id]);
            $pdo->commit();
            return null;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Corrige username y nombre completo. Devuelve null si salió bien o el mensaje de error.
     * El UNIQUE de username es la garantía final contra duplicados (aunque dos superadmins lo intenten a la vez).
     */
    public static function actualizarDatos(int $id, string $username, string $nombre): ?string
    {
        try {
            getPDO()->prepare('UPDATE usuarios SET username = :u, nombre_completo = :n WHERE id = :id')
                ->execute(['u' => $username, 'n' => $nombre, 'id' => $id]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return 'Ese nombre de usuario ya existe.';
            }
            throw $e;
        }
        return null;
    }

    /** Contraseña temporal puesta por un superadmin: obliga a cambiarla y cierra las sesiones abiertas de esa cuenta. */
    public static function resetearPassword(int $id, string $passwordTemporal): void
    {
        getPDO()->prepare(
            'UPDATE usuarios SET password_hash = :h, debe_cambiar_password = 1, password_cambiada_at = NOW(),
                    intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = :id'
        )->execute(['h' => password_hash($passwordTemporal, PASSWORD_DEFAULT), 'id' => $id]);
    }

    /**
     * La persona cambia su propia contraseña.
     * Devuelve el momento (timestamp) que quedó guardado en password_cambiada_at, tomado de la BASE DE DATOS:
     * la sesión que hizo el cambio usa ese mismo valor como login_at, así que nunca se invalida a sí misma
     * aunque el reloj de MySQL y el de PHP no coincidan al segundo.
     */
    public static function cambiarPasswordPropia(int $id, string $passwordNueva): int
    {
        $pdo = getPDO();
        $pdo->prepare(
            'UPDATE usuarios SET password_hash = :h, debe_cambiar_password = 0, password_cambiada_at = NOW(),
                    intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = :id'
        )->execute(['h' => password_hash($passwordNueva, PASSWORD_DEFAULT), 'id' => $id]);

        $stmt = $pdo->prepare('SELECT password_cambiada_at FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return (int) strtotime((string) $stmt->fetchColumn());
    }

    public static function marcarAcceso(int $id): void
    {
        getPDO()->prepare('UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id')->execute(['id' => $id]);
    }

    // ---- Control de intentos (lo comparten el login y la re-confirmación de contraseña) ----

    /** Mensaje de bloqueo si la cuenta está bloqueada ahora mismo; null si no. */
    public static function mensajeBloqueo(array $usuario): ?string
    {
        if (!empty($usuario['bloqueado_hasta']) && strtotime($usuario['bloqueado_hasta']) > time()) {
            $min = (int) ceil((strtotime($usuario['bloqueado_hasta']) - time()) / 60);
            return "Cuenta bloqueada por intentos fallidos. Intenta de nuevo en $min minuto(s).";
        }
        return null;
    }

    public static function registrarIntentoFallido(array $usuario): void
    {
        $intentos = ((int) $usuario['intentos_fallidos']) + 1;
        if ($intentos >= self::MAX_INTENTOS) {
            $sql = 'UPDATE usuarios SET intentos_fallidos = :i, bloqueado_hasta = DATE_ADD(NOW(), INTERVAL ' . self::MINUTOS_BLOQUEO . ' MINUTE) WHERE id = :id';
        } else {
            $sql = 'UPDATE usuarios SET intentos_fallidos = :i WHERE id = :id';
        }
        getPDO()->prepare($sql)->execute(['i' => $intentos, 'id' => $usuario['id']]);
    }

    public static function limpiarIntentos(int $id): void
    {
        getPDO()->prepare('UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = :id')->execute(['id' => $id]);
    }

    /**
     * Comprueba la contraseña ACTUAL de una cuenta (para confirmar acciones sensibles o cambiarla).
     * Cuenta como intento: si se falla 5 veces, la cuenta se bloquea igual que en el login.
     * @return array{ok:bool, error?:string}
     */
    public static function verificarPasswordActual(int $id, string $password): array
    {
        $stmt = getPDO()->prepare('SELECT id, password_hash, intentos_fallidos, bloqueado_hasta FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $u = $stmt->fetch();
        if (!$u) {
            return ['ok' => false, 'error' => 'Usuario no encontrado.'];
        }
        if ($msg = self::mensajeBloqueo($u)) {
            return ['ok' => false, 'error' => $msg];
        }
        if ($password === '' || !password_verify($password, $u['password_hash'])) {
            self::registrarIntentoFallido($u);
            return ['ok' => false, 'error' => 'Tu contraseña es incorrecta.'];
        }
        self::limpiarIntentos($id);
        return ['ok' => true];
    }
}