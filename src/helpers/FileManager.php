<?php
// src/helpers/FileManager.php

class FileManager {

    /**
     * Definición de los 32 bolsillos por sección.
     * clave = slug interno, valor = nombre visible
     */
    public static function bolsillosPorSeccion(): array {
        return [
            'hoja_de_vida' => [
                'hv_formal' => 'Hoja de Vida Formal',
                'cedula' => 'Cédula',
                'hv_libretaMilitar' => 'Libreta Militar',
                'licenciaDeConduccion' => 'Licencia de Conducción',
                'bachillerYotrosestudios' => 'Bachiller y Otros Estudios',
                'certificados' => 'Certificados',
                'formularioDeIngreso' => 'Formulario de Ingreso',
                'resolucionesDeAscenso' => 'Resoluciones de Ascenso',
                'hv_bomberil' => 'Hoja de Vida Bomberil',
                'autorizacion' => 'Autorización',
                'antecedentes' => 'Antecedentes',
                'vacunas' => 'Vacunas',
                'entregaDeDotacion' => 'Entrega de Dotación',
            ],
            'documentos_contractuales' => [
                'examenesMedicos' => 'Exámenes Médicos',
                'entrevistaDeSeleccion' => 'Entrevista de Selección',
                'certificadoEPS' => 'Certificado EPS',
                'certificadoFP' => 'Certificado FP',
                'certificadoARL' => 'Certificado ARL',
                'certificadoCCF' => 'Certificado CCF',
                'funcionesDelCargo' => 'Funciones del Cargo',
                'induccionSST' => 'Inducción SST',
                'induccionAlCargo' => 'Inducción al Cargo',
                'contratosFirmados' => 'Contratos Firmados',
                'notificacionDeTerminacion' => 'Notificación de Terminación',
                'renovacion' => 'Renovación',
                'evaluacionesDeDesempeno' => 'Evaluaciones de Desempeño',
                'planDeMejoramiento' => 'Plan de Mejoramiento',
                'felicitacionesYLlamadosDeAtencion' => 'Felicitaciones y Llamados de Atención',
                'novedadesEIncapacidades' => 'Novedades e Incapacidades',
                'pazYSalvos' => 'Paz y Salvos',
                'otros' => 'Otros',
            ],
        ];
    }

    public static function rutaBase(string $cedula): string {
        $uploadsPath = rtrim($_ENV['UPLOADS_PATH'], '/');
        return $uploadsPath . '/hv_' . $cedula;
    }

    /**
     * Crea la carpeta base, las 2 secciones, y las 32 subcarpetas de bolsillos.
     */
    public static function crearEstructuraEmpleado(string $cedula): void {
        $base = self::rutaBase($cedula);

        if (!is_dir($base)) {
            mkdir($base, 0755, true);
        }

        foreach (self::bolsillosPorSeccion() as $seccion => $bolsillos) {
            $rutaSeccion = $base . '/' . $seccion;
            if (!is_dir($rutaSeccion)) {
                mkdir($rutaSeccion, 0755, true);
            }

            foreach ($bolsillos as $slug => $nombreCompleto) {
                $rutaBolsillo = $rutaSeccion . '/' . $slug . '_hv_' . $cedula;
                if (!is_dir($rutaBolsillo)) {
                    mkdir($rutaBolsillo, 0755, true);
                }
            }
        }
    }

    /**
     * Borra recursivamente una carpeta y todo su contenido.
     */
    public static function rrmdir(string $dir): void {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $ruta = $dir . '/' . $item;
            if (is_dir($ruta)) {
                self::rrmdir($ruta);
            } else {
                unlink($ruta);
            }
        }

        rmdir($dir);
    }

    public static function borrarEstructuraEmpleado(string $cedula): void {
        self::rrmdir(self::rutaBase($cedula));
    }

    public static function rutaCarpetaPerfil(string $cedula): string {
    return self::rutaBase($cedula) . '/perfil';
}

/**
 * Guarda (o reemplaza) la foto de perfil de un empleado. Borra la anterior si existía.
 */
public static function guardarFoto(string $cedula, array $archivo): array {
    $tiposPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $tamanoMaximo = 5 * 1024 * 1024; // 5MB

    if (!isset($archivo['error']) || $archivo['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Error al subir la foto.'];
    }
    if ($archivo['size'] > $tamanoMaximo) {
        return ['ok' => false, 'error' => 'La foto no puede superar 5MB.'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $archivo['tmp_name']);
    finfo_close($finfo);

    if (!isset($tiposPermitidos[$mime])) {
        return ['ok' => false, 'error' => 'Solo se permiten imágenes JPG, PNG o WEBP.'];
    }

    $carpetaPerfil = self::rutaCarpetaPerfil($cedula);
    if (!is_dir($carpetaPerfil)) {
        mkdir($carpetaPerfil, 0755, true);
    }

    // Borrar foto anterior (cualquier extensión) antes de guardar la nueva
    foreach (glob($carpetaPerfil . '/foto.*') as $anterior) {
        unlink($anterior);
    }

    $extension = $tiposPermitidos[$mime];
    $rutaCompleta = $carpetaPerfil . '/foto.' . $extension;

    if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
        return ['ok' => false, 'error' => 'No se pudo guardar la foto.'];
    }

    return ['ok' => true, 'ruta' => 'hv_' . $cedula . '/perfil/foto.' . $extension];
}

    public static function renombrarEstructuraEmpleado(string $cedulaAnterior, string $cedulaNueva): bool {
        $rutaAnteriorBase = self::rutaBase($cedulaAnterior);
        $rutaNuevaBase = self::rutaBase($cedulaNueva);

        if (!is_dir($rutaAnteriorBase)) {
            return false;
        }

        // 1. Renombrar la carpeta raíz: hv_{anterior} -> hv_{nueva}
        if (!@rename($rutaAnteriorBase, $rutaNuevaBase)) {
            return false;
        }

        // 2. Renombrar cada subcarpeta de bolsillo: {slug}_hv_{anterior} -> {slug}_hv_{nueva}
        $renombradas = [];
        try {
            foreach (self::bolsillosPorSeccion() as $seccion => $bolsillos) {
                foreach ($bolsillos as $slug => $nombreCompleto) {
                    $rutaSeccion = $rutaNuevaBase . '/' . $seccion;
                    $rutaAnteriorBolsillo = $rutaSeccion . '/' . $slug . '_hv_' . $cedulaAnterior;
                    $rutaNuevaBolsillo = $rutaSeccion . '/' . $slug . '_hv_' . $cedulaNueva;

                    if (is_dir($rutaAnteriorBolsillo)) {
                        if (!@rename($rutaAnteriorBolsillo, $rutaNuevaBolsillo)) {
                            throw new Exception("No se pudo renombrar el bolsillo: $slug");
                        }
                        $renombradas[] = [$rutaNuevaBolsillo, $rutaAnteriorBolsillo];
                    }
                }
            }
        } catch (Exception $e) {
            // Revertir lo ya renombrado para no dejar una mezcla de nombres viejos/nuevos
            foreach ($renombradas as [$actual, $original]) {
                @rename($actual, $original);
            }
            @rename($rutaNuevaBase, $rutaAnteriorBase);
            return false;
        }

        return true;
    }


        public static function guardarFotoPermiso(string $cedula, array $archivo): array {
        return self::guardarImagenGenerica($cedula, $archivo, 'permisos/fotos');
    }

    public static function guardarEvidenciaPermiso(string $cedula, array $archivo): array {
        $carpeta = self::rutaBase($cedula) . '/permisos/evidencias';
        if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

        $tamanoMaximo = 20 * 1024 * 1024;
        if ($archivo['size'] > $tamanoMaximo) return ['ok' => false, 'error' => 'El archivo de evidencia supera 20MB.'];

        $mimesPermitidos = [
            'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'application/pdf' => 'pdf',
        ];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeReal = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);
        if (!isset($mimesPermitidos[$mimeReal])) {
            return ['ok' => false, 'error' => 'Solo se permiten imágenes o PDF como evidencia.'];
        }

        $extension = $mimesPermitidos[$mimeReal];
        $nombreFinal = 'evidencia_' . time() . '.' . $extension;
        $rutaCompleta = $carpeta . '/' . $nombreFinal;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return ['ok' => false, 'error' => 'No se pudo guardar la evidencia.'];
        }

        return ['ok' => true, 'ruta' => 'hv_' . $cedula . '/permisos/evidencias/' . $nombreFinal];
    }

    private static function guardarImagenGenerica(string $cedula, array $archivo, string $subcarpeta): array {
        $tiposPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $tamanoMaximo = 5 * 1024 * 1024;

        if (!isset($archivo['error']) || $archivo['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Error al subir la imagen.'];
        }
        if ($archivo['size'] > $tamanoMaximo) {
            return ['ok' => false, 'error' => 'La imagen no puede superar 5MB.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!isset($tiposPermitidos[$mime])) {
            return ['ok' => false, 'error' => 'Solo se permiten imágenes JPG, PNG o WEBP.'];
        }

        $carpeta = self::rutaBase($cedula) . '/' . $subcarpeta;
        if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

        $extension = $tiposPermitidos[$mime];
        $nombreFinal = 'foto_' . time() . '.' . $extension;
        $rutaCompleta = $carpeta . '/' . $nombreFinal;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return ['ok' => false, 'error' => 'No se pudo guardar la imagen.'];
        }

        return ['ok' => true, 'ruta' => 'hv_' . $cedula . '/' . $subcarpeta . '/' . $nombreFinal];
    }

    public static function guardarFotoPermisoBase64(string $cedula, string $dataUrl, string $subcarpeta): array {
        if (!preg_match('/^data:image\/(png|jpeg);base64,(.+)$/', $dataUrl, $m)) {
            return ['ok' => false, 'error' => 'Formato de foto inválido.'];
        }
        $extension = $m[1] === 'png' ? 'png' : 'jpg';
        $binario = base64_decode($m[2]);
        if ($binario === false) return ['ok' => false, 'error' => 'No se pudo decodificar la foto.'];

        $carpeta = self::rutaBase($cedula) . '/' . $subcarpeta;
        if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

        $nombreFinal = 'foto_' . time() . '.' . $extension;
        $rutaCompleta = $carpeta . '/' . $nombreFinal;

        if (file_put_contents($rutaCompleta, $binario) === false) {
            return ['ok' => false, 'error' => 'No se pudo guardar la foto.'];
        }

        return ['ok' => true, 'ruta' => 'hv_' . $cedula . '/' . $subcarpeta . '/' . $nombreFinal];
    }

}