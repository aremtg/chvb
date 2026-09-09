<?php
// src/controllers/LibroController.php
require_once __DIR__ . '/../models/BolsilloModel.php';
require_once __DIR__ . '/../models/DocumentoModel.php';
require_once __DIR__ . '/../helpers/FileManager.php';

class LibroController {

    private const TIPOS_PERMITIDOS = ['application/pdf'];
    private const TAMANO_MAXIMO = 20 * 1024 * 1024; // 20MB, igual al php.ini

    /**
     * Sube un PDF a un bolsillo específico, tanto físico como en BD.
     */
    public static function subirDocumento(int $bolsilloId, string $cedula, array $archivo, bool $pendienteRevision = false): array {
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

    public static function eliminarDocumento(int $documentoId, string $cedula): array {
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

    public static function moverOrden(int $documentoId, string $direccion): array {
        $doc = DocumentoModel::obtenerPorId($documentoId);
        if (!$doc) {
            return ['ok' => false, 'error' => 'Documento no encontrado.'];
        }

        $docs = DocumentoModel::listarPorBolsillo((int)$doc['bolsillo_id']);
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

    public static function actualizarAlarma(int $bolsilloId, string $tipo, ?string $fechaCustom): array {
        $tiposValidos = ['2m', '3m', '6m', '1a', 'custom'];
        if (!in_array($tipo, $tiposValidos, true)) {
            return ['ok' => false, 'error' => 'Tipo de alarma inválido.'];
        }

        $fecha = self::calcularFechaAlarma($tipo, $fechaCustom);
        BolsilloModel::actualizarAlarma($bolsilloId, $tipo, $fecha, true);

        return ['ok' => true, 'fecha' => $fecha];
    }

    public static function desactivarAlarma(int $bolsilloId): array {
        BolsilloModel::actualizarAlarma($bolsilloId, null, null, false);
        return ['ok' => true];
    }

    private static function calcularFechaAlarma(string $tipo, ?string $fechaCustom): string {
        $hoy = new DateTime('today');

        return match ($tipo) {
            '2m' => $hoy->modify('+2 months')->format('Y-m-d'),
            '3m' => $hoy->modify('+3 months')->format('Y-m-d'),
            '6m' => $hoy->modify('+6 months')->format('Y-m-d'),
            '1a' => $hoy->modify('+1 year')->format('Y-m-d'),
            'custom' => $fechaCustom ?: $hoy->format('Y-m-d'),
            default => $hoy->format('Y-m-d'),
        };
    }
}