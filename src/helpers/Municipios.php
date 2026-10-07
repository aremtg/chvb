<?php
// src/helpers/Municipios.php
//
// Lista oficial de municipios de Colombia con el formato "Municipio, Departamento"
// (ej: "Yopal, Casanare", "Villanueva, Casanare", "Bogotá, D.C.").
//
// FUENTE ÚNICA: public/assets/data/municipios.json. El mismo archivo lo lee el
// combo del navegador (municipio_select.js) y esta clase para validar en el servidor,
// así que lo que se puede escoger y lo que se acepta nunca se desincroniza.

class Municipios
{
    private static ?array $lista = null;

    public static function todos(): array
    {
        if (self::$lista === null) {
            $ruta = __DIR__ . '/../../public/assets/data/municipios.json';
            $json = @file_get_contents($ruta);
            $datos = $json !== false ? json_decode($json, true) : null;
            self::$lista = is_array($datos) ? array_values($datos) : [];
        }
        return self::$lista;
    }

    /** ¿El texto es EXACTAMENTE uno de los municipios de la lista? */
    public static function esValido(string $valor): bool
    {
        return $valor !== '' && in_array($valor, self::todos(), true);
    }
}
