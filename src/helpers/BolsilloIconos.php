<?php
// src/helpers/BolsilloIconos.php
// Icono Lucide (en assets/icons) de cada bolsillo. Lo usan el libro de Talento Humano y la hoja de vida del empleado.

final class BolsilloIconos
{
    public const MAPA = [
    'cedula' => 'id-card',
    'licenciaDeConduccion' => 'car',
    'hv_libretaMilitar' => 'shield',
    'bachillerYotrosestudios' => 'graduation-cap',
    'certificados' => 'file-check-2',
    'formularioDeIngreso' => 'clipboard-list',
    'resolucionesDeAscenso' => 'award',
    'hv_bomberil' => 'flame',
    'autorizacion' => 'file-signature',
    'antecedentes' => 'search',
    'vacunas' => 'syringe',
    'entregaDeDotacion' => 'shirt',
    'examenesMedicos' => 'stethoscope',
    'entrevistaDeSeleccion' => 'users',
    'funcionesDelCargo' => 'briefcase',
    'induccionSST' => 'hard-hat',
    'hv_formal' => 'file-text',
    'certificadoEPS' => 'file-check-2',
    'certificadoFP' => 'file-check-2',
    'certificadoARL' => 'file-check-2',
    'certificadoCCF' => 'file-check-2',
    ];

    /** Nombre del icono para un bolsillo; si no está en el mapa, una carpeta. */
    public static function de(string $slug): string
    {
        return self::MAPA[$slug] ?? 'folder';
    }
}
