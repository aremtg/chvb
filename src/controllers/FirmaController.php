<?php
// src/controllers/FirmaController.php (nuevo archivo)
require_once __DIR__ . '/../helpers/FileManager.php';
require_once __DIR__ . '/../models/FirmaModel.php';

class FirmaController {

    /** Guarda la firma dibujada en canvas, recibida como data URL base64 (PNG). */
    public static function guardarFirmaBase64(string $cedula, string $dataUrl): array {
        if (!preg_match('/^data:image\/png;base64,(.+)$/', $dataUrl, $m)) {
            return ['ok' => false, 'error' => 'Formato de firma inválido.'];
        }
        $binario = base64_decode($m[1]);
        if ($binario === false) return ['ok' => false, 'error' => 'No se pudo decodificar la firma.'];

        $carpeta = FileManager::rutaBase($cedula) . '/firma';
        if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

        $nombreFinal = 'firma_' . time() . '.png';
        $rutaCompleta = $carpeta . '/' . $nombreFinal;

        if (file_put_contents($rutaCompleta, $binario) === false) {
            return ['ok' => false, 'error' => 'No se pudo guardar la firma.'];
        }

        $rutaRelativa = 'hv_' . $cedula . '/firma/' . $nombreFinal;
        FirmaModel::guardarComoActiva($cedula, $rutaRelativa);
        return ['ok' => true, 'ruta' => $rutaRelativa];
    }

    /** Guarda una firma subida como archivo PNG/JPG. */
    public static function guardarFirmaArchivo(string $cedula, array $archivo): array {
        $tiposPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        if (!isset($archivo['error']) || $archivo['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Error al subir la firma.'];
        }
        if ($archivo['size'] > 2 * 1024 * 1024) {
            return ['ok' => false, 'error' => 'La firma no puede superar 2MB.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!isset($tiposPermitidos[$mime])) {
            return ['ok' => false, 'error' => 'Solo se permiten firmas en PNG o JPG.'];
        }

        $carpeta = FileManager::rutaBase($cedula) . '/firma';
        if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

        $extension = $tiposPermitidos[$mime];
        $nombreFinal = 'firma_' . time() . '.' . $extension;
        $rutaCompleta = $carpeta . '/' . $nombreFinal;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return ['ok' => false, 'error' => 'No se pudo guardar la firma.'];
        }

        $rutaRelativa = 'hv_' . $cedula . '/firma/' . $nombreFinal;
        FirmaModel::guardarComoActiva($cedula, $rutaRelativa);
        return ['ok' => true, 'ruta' => $rutaRelativa];
    }
}