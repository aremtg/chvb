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

                DocumentoModel::crear((int) $bolsillo['id'], $nombreArchivoFisico, $rutaRelativa, null, 'panel');
                $importados++;
            }
        }

        return $importados;
    }

    /**
     * Sincroniza la foto de perfil usando la referencia guardada en empleados.foto.
     *
     * Reglas:
     * - Si la BD apunta a una imagen válida dentro de uploads/, esa es la fuente de verdad.
     * - La foto final siempre queda como perfil/foto.{jpg|png|webp}.
     * - Antes de instalar la nueva foto se elimina cualquier foto.* anterior.
     * - Si la BD no tiene foto válida, pero ya existe foto.* en perfil/, se conserva una
     *   sola y se corrige la referencia de BD.
     * - Nunca acepta rutas fuera de uploads/.
     */
    public static function sincronizarFotoPerfil(string $cedula, ?string $rutaFotoBD): array {
        $carpetaPerfil = FileManager::rutaCarpetaPerfil($cedula);
        if (!is_dir($carpetaPerfil) && !mkdir($carpetaPerfil, 0755, true) && !is_dir($carpetaPerfil)) {
            throw new RuntimeException('No se pudo crear la carpeta de perfil.');
        }

        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        $fotosPerfil = glob($carpetaPerfil . '/foto.*') ?: [];
        $rutaFuente = null;
        $extension = null;

        // 1. La referencia de BD puede venir de una importación. Solo se acepta
        //    si resuelve a un archivo real dentro de uploads/.
        if ($rutaFotoBD !== null && trim($rutaFotoBD) !== '') {
            $rutaRelativa = ltrim(str_replace('\\', '/', trim($rutaFotoBD)), '/');
            $rutaUploads = realpath(FileManager::rutaUploads());

            if ($rutaUploads !== false && $rutaRelativa !== '' && !str_contains($rutaRelativa, '..')) {
                $candidata = $rutaUploads . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rutaRelativa);
                $real = realpath($candidata);
                $baseNorm = rtrim(str_replace('\\', '/', $rutaUploads), '/') . '/';
                $realNorm = $real !== false ? str_replace('\\', '/', $real) : '';

                if ($real !== false && is_file($real) && str_starts_with($realNorm, $baseNorm)) {
                    $ext = strtolower(pathinfo($real, PATHINFO_EXTENSION));
                    if (in_array($ext, $permitidas, true)) {
                        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($real);
                        $mimes = [
                            'image/jpeg' => true,
                            'image/png' => true,
                            'image/webp' => true,
                        ];
                        if (isset($mimes[$mime])) {
                            $rutaFuente = $real;
                            $extension = $ext === 'jpeg' ? 'jpg' : $ext;
                        }
                    }
                }
            }
        }

        // 2. Si no hay una fuente válida en BD, reutilizar una foto que ya exista
        //    en la carpeta de perfil.
        if ($rutaFuente === null && !empty($fotosPerfil)) {
            foreach ($fotosPerfil as $foto) {
                if (!is_file($foto)) continue;
                $ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
                if (!in_array($ext, $permitidas, true)) continue;

                $mime = (new finfo(FILEINFO_MIME_TYPE))->file($foto);
                if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                    $rutaFuente = realpath($foto);
                    $extension = $ext === 'jpeg' ? 'jpg' : $ext;
                    break;
                }
            }
        }

        if ($rutaFuente === null) {
            // No hay foto: limpiar referencias obsoletas y cualquier foto.* inválida.
            foreach ($fotosPerfil as $foto) {
                if (is_file($foto)) @unlink($foto);
            }
            return ['estado' => 'sin_foto', 'ruta' => null];
        }

        $destino = $carpetaPerfil . '/foto.' . $extension;
        $temporal = $carpetaPerfil . '/.foto_sync_' . bin2hex(random_bytes(8)) . '.' . $extension;

        // Si fuente y destino son el mismo archivo, no hace falta copiarlo.
        $mismaFuente = realpath($destino) !== false && realpath($destino) === $rutaFuente;

        if (!$mismaFuente) {
            if (!copy($rutaFuente, $temporal)) {
                throw new RuntimeException('No se pudo copiar la foto de perfil durante la sincronización.');
            }
            @chmod($temporal, 0644);

            // Ahora sí se puede borrar la foto anterior sin perder la nueva fuente.
            foreach ($fotosPerfil as $foto) {
                if (is_file($foto)) @unlink($foto);
            }

            if (!@rename($temporal, $destino)) {
                @unlink($temporal);
                throw new RuntimeException('No se pudo instalar la foto de perfil sincronizada.');
            }
        } else {
            // Aunque la fuente ya sea la correcta, elimina posibles duplicados foto.*.
            foreach ($fotosPerfil as $foto) {
                if (realpath($foto) !== $rutaFuente && is_file($foto)) @unlink($foto);
            }
        }

        return [
            'estado' => $mismaFuente ? 'ya_existia' : 'sincronizada',
            'ruta' => 'hv_' . $cedula . '/perfil/foto.' . $extension,
        ];
    }

    public static function detectarFotoExistente(string $cedula): ?string {
        $carpetaPerfil = FileManager::rutaCarpetaPerfil($cedula);
        if (!is_dir($carpetaPerfil)) return null;

        $candidatos = glob($carpetaPerfil . '/foto.*') ?: [];
        if (empty($candidatos)) return null;

        foreach ($candidatos as $candidato) {
            if (!is_file($candidato)) continue;
            $ext = strtolower(pathinfo($candidato, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                return 'hv_' . $cedula . '/perfil/' . basename($candidato);
            }
        }
        return null;
    }
}