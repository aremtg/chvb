<?php
// src/helpers/ReconciliadorArchivos.php
require_once __DIR__ . '/FileManager.php';
require_once __DIR__ . '/../models/BolsilloModel.php';
require_once __DIR__ . '/../models/DocumentoModel.php';

class ReconciliadorArchivos {

    public static function importarDocumentosExistentes(string $cedula): int {
        $bolsillos = BolsilloModel::listarPorEmpleado($cedula);
        $importados = 0;

        foreach ($bolsillos as $bolsillo) {
            $carpeta = FileManager::rutaBase($cedula) . '/' . $bolsillo['seccion'] . '/' . $bolsillo['nombre'] . '_hv_' . $cedula;
            if (!is_dir($carpeta)) continue;

            $archivosEnDisco = glob($carpeta . '/*.pdf');
            if (!$archivosEnDisco) continue;

            natsort($archivosEnDisco);

            $documentosExistentes = DocumentoModel::listarPorBolsillo((int) $bolsillo['id']);
            $rutasYaRegistradas = array_column($documentosExistentes, 'ruta');

            foreach ($archivosEnDisco as $rutaCompleta) {
                $nombreArchivoFisico = basename($rutaCompleta);
                $rutaRelativa = 'hv_' . $cedula . '/' . $bolsillo['seccion'] . '/' . $bolsillo['nombre'] . '_hv_' . $cedula . '/' . $nombreArchivoFisico;

                if (in_array($rutaRelativa, $rutasYaRegistradas, true)) {
                    continue;
                }

                DocumentoModel::crear((int) $bolsillo['id'], $nombreArchivoFisico, $rutaRelativa, false);
                $importados++;
            }
        }

        return $importados;
    }

    public static function detectarFotoExistente(string $cedula): ?string {
        $carpetaPerfil = FileManager::rutaCarpetaPerfil($cedula);
        if (!is_dir($carpetaPerfil)) return null;

        $candidatos = glob($carpetaPerfil . '/foto.*');
        if (empty($candidatos)) return null;

        return 'hv_' . $cedula . '/perfil/' . basename($candidatos[0]);
    }
}