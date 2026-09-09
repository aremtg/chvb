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
                'notificacion' => 'Notificación',
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
            mkdir($base, 0777, true);
        }

        foreach (self::bolsillosPorSeccion() as $seccion => $bolsillos) {
            $rutaSeccion = $base . '/' . $seccion;
            if (!is_dir($rutaSeccion)) {
                mkdir($rutaSeccion, 0777, true);
            }

            foreach ($bolsillos as $slug => $nombreCompleto) {
                $rutaBolsillo = $rutaSeccion . '/' . $slug . '_hv_' . $cedula;
                if (!is_dir($rutaBolsillo)) {
                    mkdir($rutaBolsillo, 0777, true);
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
}