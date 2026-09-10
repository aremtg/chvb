<?php
// src/controllers/LibroController.php
require_once __DIR__ . '/../models/BolsilloModel.php';
require_once __DIR__ . '/../models/DocumentoModel.php';
require_once __DIR__ . '/../helpers/FileManager.php';
require_once __DIR__ . '/../models/NotificacionModel.php';

class LibroController
{

    private const TIPOS_PERMITIDOS = ['application/pdf'];
    private const TAMANO_MAXIMO = 20 * 1024 * 1024; // 20MB, igual al php.ini

    /**
     * Sube un PDF a un bolsillo específico, tanto físico como en BD.
     */
    public static function subirDocumento(int $bolsilloId, string $cedula, array $archivo, bool $pendienteRevision = false): array
    {
        $bolsillo = BolsilloModel::obtenerPorId($bolsilloId);
        if (!$bolsillo || $bolsillo['cedula_empleado'] !== $cedula) {
            return ['ok' => false, 'error' => 'Bolsillo no válido para este empleado.'];
        }

        if (!isset($archivo['error']) || $archivo['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Error al subir el archivo.'];
        }

        if ($archivo['size'] > self::TAMANO_MAXIMO) {
            return ['ok' => false, 'error' => 'El archivo supera el tamaño máximo permitido (20MB).'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, self::TIPOS_PERMITIDOS, true)) {
            return ['ok' => false, 'error' => 'Solo se permiten archivos PDF.'];
        }

        // Nombre seguro: quitamos caracteres raros y agregamos timestamp para evitar colisiones
        $nombreOriginal = pathinfo($archivo['name'], PATHINFO_FILENAME);
        $nombreLimpio = preg_replace('/[^A-Za-z0-9_\-]/', '_', $nombreOriginal);
        $nombreFinal = $nombreLimpio . '_' . time() . '.pdf';

        $rutaCarpeta = FileManager::rutaBase($cedula) . '/' . $bolsillo['seccion'] . '/' . $bolsillo['nombre'] . '_hv_' . $cedula;
        $rutaCompleta = $rutaCarpeta . '/' . $nombreFinal;

        if (!is_dir($rutaCarpeta)) {
            return ['ok' => false, 'error' => 'La carpeta física del bolsillo no existe. Contacta soporte.'];
        }

        if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return ['ok' => false, 'error' => 'No se pudo guardar el archivo en el servidor.'];
        }

        // Ruta relativa para guardar en BD (no la ruta absoluta de Windows)
        $rutaRelativa = 'hv_' . $cedula . '/' . $bolsillo['seccion'] . '/' . $bolsillo['nombre'] . '_hv_' . $cedula . '/' . $nombreFinal;

        $id = DocumentoModel::crear($bolsilloId, $archivo['name'], $rutaRelativa, $pendienteRevision);

        return ['ok' => true, 'id' => $id];
    }

    public static function eliminarDocumento(int $documentoId, string $cedula): array
    {
        $doc = DocumentoModel::obtenerPorId($documentoId);
        if (!$doc) {
            return ['ok' => false, 'error' => 'Documento no encontrado.'];
        }

        $uploadsPath = rtrim($_ENV['UPLOADS_PATH'], '/');
        $rutaFisica = $uploadsPath . '/' . $doc['ruta'];

        if (is_file($rutaFisica)) {
            unlink($rutaFisica);
        }

        DocumentoModel::eliminar($documentoId);
        return ['ok' => true];
    }

    public static function renombrarDocumento(int $documentoId, string $cedula, string $nuevoNombre): array
    {
        $doc = DocumentoModel::obtenerPorId($documentoId);
        if (!$doc) {
            return ['ok' => false, 'error' => 'Documento no encontrado.'];
        }

        $bolsillo = BolsilloModel::obtenerPorId((int) $doc['bolsillo_id']);
        if (!$bolsillo || $bolsillo['cedula_empleado'] !== $cedula) {
            return ['ok' => false, 'error' => 'Este documento no pertenece a este empleado.'];
        }

        $nuevoNombre = trim($nuevoNombre);
        if ($nuevoNombre === '') {
            return ['ok' => false, 'error' => 'El nombre no puede estar vacío.'];
        }
        if (strlen($nuevoNombre) > 200) {
            return ['ok' => false, 'error' => 'El nombre es demasiado largo (máx. 200 caracteres).'];
        }

        // El nombre visible que verá el usuario (agregamos .pdf si no lo puso)
        $nombreVisible = preg_match('/\.pdf$/i', $nuevoNombre) ? $nuevoNombre : $nuevoNombre . '.pdf';

        // Nombre físico seguro en disco (sin espacios ni caracteres raros), conservando un sufijo único
        $nombreLimpio = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($nuevoNombre, PATHINFO_FILENAME));
        $nuevoNombreFisico = $nombreLimpio . '_' . time() . '.pdf';

        $uploadsPath = rtrim($_ENV['UPLOADS_PATH'], '/');
        $rutaFisicaActual = $uploadsPath . '/' . $doc['ruta'];

        if (!is_file($rutaFisicaActual)) {
            return ['ok' => false, 'error' => 'El archivo físico no existe en el servidor.'];
        }

        $carpetaContenedora = dirname($rutaFisicaActual);
        $rutaFisicaNueva = $carpetaContenedora . '/' . $nuevoNombreFisico;

        if (!rename($rutaFisicaActual, $rutaFisicaNueva)) {
            return ['ok' => false, 'error' => 'No se pudo renombrar el archivo en el servidor.'];
        }

        // Reconstruir la ruta relativa (mismo directorio, solo cambia el nombre del archivo)
        $rutaRelativaNueva = dirname($doc['ruta']) . '/' . $nuevoNombreFisico;

        DocumentoModel::actualizarNombreYRuta($documentoId, $nombreVisible, $rutaRelativaNueva);

        $rolActor = $_SESSION['superadmin_rol'] ?? 'superadmin_talento_humano';
        if ($rolActor === 'auxiliar_talento_humano') {
            $usuarioNombre = $_SESSION['superadmin_username'];
            NotificacionModel::crear(
                $_SESSION['superadmin_id'],
                $usuarioNombre,
                $cedula,
                'documento',
                "{$usuarioNombre} editó un pdf del bolsillo {$bolsillo['nombre_completo']}"
            );
        }

        return ['ok' => true, 'nombre' => $nombreVisible];
    }

    public static function moverOrden(int $documentoId, string $direccion): array
    {
        $doc = DocumentoModel::obtenerPorId($documentoId);
        if (!$doc) {
            return ['ok' => false, 'error' => 'Documento no encontrado.'];
        }

        $docs = DocumentoModel::listarPorBolsillo((int) $doc['bolsillo_id']);
        $index = array_search($doc['id'], array_column($docs, 'id'));

        if ($direccion === 'arriba' && $index > 0) {
            $vecino = $docs[$index - 1];
            DocumentoModel::intercambiarOrden($doc['id'], $doc['orden'], $vecino['id'], $vecino['orden']);
        } elseif ($direccion === 'abajo' && $index < count($docs) - 1) {
            $vecino = $docs[$index + 1];
            DocumentoModel::intercambiarOrden($doc['id'], $doc['orden'], $vecino['id'], $vecino['orden']);
        }

        return ['ok' => true];
    }

    public static function actualizarAlarma(
        int $bolsilloId,
        string $tipo,
        ?string $fechaInicio,
        ?int $valorCustom,
        ?string $unidadCustom
    ): array {
        $tiposValidos = ['1m', '2m', '6m', '1a', 'custom'];
        if (!in_array($tipo, $tiposValidos, true)) {
            return ['ok' => false, 'error' => 'Tipo de alarma inválido.'];
        }

        // Fecha desde la cual se cuenta el plazo: si no la envían, usamos HOY (fecha real del servidor PHP)
        $inicio = new DateTime('today');
        if (!empty($fechaInicio)) {
            $d = DateTime::createFromFormat('Y-m-d', $fechaInicio);
            if (!$d || $d->format('Y-m-d') !== $fechaInicio) {
                return ['ok' => false, 'error' => 'La fecha de inicio no es válida.'];
            }
            $inicio = $d;
        }

        // Días de aviso: 15 si es "1 mes", 35 para el resto
        $diasAviso = $tipo === '1m' ? 15 : 35;

        $fecha = clone $inicio;

        switch ($tipo) {
            case '1m':
                $fecha->modify('+1 month');
                break;
            case '2m':
                $fecha->modify('+2 months');
                break;
            case '6m':
                $fecha->modify('+6 months');
                break;
            case '1a':
                $fecha->modify('+1 year');
                break;
            case 'custom':
                if (!$valorCustom || $valorCustom < 1) {
                    return ['ok' => false, 'error' => 'Debes indicar una cantidad válida para el plazo personalizado.'];
                }
                $unidadesValidas = ['dias', 'meses', 'anios'];
                if (!in_array($unidadCustom, $unidadesValidas, true)) {
                    return ['ok' => false, 'error' => 'Debes seleccionar una unidad válida (días, meses o años).'];
                }
                $mapaUnidad = ['dias' => 'days', 'meses' => 'months', 'anios' => 'years'];
                $fecha->modify("+{$valorCustom} {$mapaUnidad[$unidadCustom]}");
                break;
        }

        BolsilloModel::actualizarAlarma(
            $bolsilloId,
            $tipo,
            $fecha->format('Y-m-d'),
            true,
            $tipo === 'custom' ? $valorCustom : null,
            $tipo === 'custom' ? $unidadCustom : null,
            $inicio->format('Y-m-d'),
            $diasAviso
        );

        return [
            'ok' => true,
            'fecha' => $fecha->format('Y-m-d'),
            'fecha_inicio' => $inicio->format('Y-m-d'),
            'dias_aviso' => $diasAviso,
        ];
    }

    public static function desactivarAlarma(int $bolsilloId): array
    {
        BolsilloModel::actualizarAlarma($bolsilloId, null, null, false);
        return ['ok' => true];
    }

}