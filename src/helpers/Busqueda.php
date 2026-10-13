<?php
// src/helpers/Busqueda.php
// Búsqueda de personas y permisos: texto con varias palabras y cédula con o sin puntos.
//   - "Angel Camargo" encuentra "Angel Gabriel Camargo Pezca" (cada palabra debe aparecer).
//   - "1.118.555.586" y "1118555586" encuentran la misma cédula (la base guarda solo dígitos).

final class Busqueda
{
    public const MAX_PALABRAS = 5;

    /** "Angel   Camargo" -> ["Angel", "Camargo"]. Como máximo MAX_PALABRAS. */
    public static function palabras(string $q): array
    {
        $partes = preg_split('/\s+/u', trim($q), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        return array_slice($partes, 0, self::MAX_PALABRAS);
    }

    /** Solo dígitos: "1.118.555.586" -> "1118555586". */
    public static function digitos(string $texto): string
    {
        return preg_replace('/\D+/', '', $texto) ?? '';
    }

    /** Escapa los comodines de LIKE (%, _ y la barra invertida). */
    public static function escapar(string $texto): string
    {
        return addcslashes($texto, '\\%_');
    }

    /**
     * Condición WHERE. Cada palabra debe aparecer en alguna de las columnas de texto
     * o, si la palabra tiene dígitos, en la cédula (comparada sin puntos).
     * Las columnas se pasan tal cual (pueden llevar alias o ser subconsultas).
     * Devuelve [sql, params]; si no hay palabras, sql es '' (el llamador decide qué hacer).
     */
    public static function condicion(string $q, array $columnasTexto, string $columnaCedula, string $prefijo = 'q'): array
    {
        $sqlPartes = [];
        $params = [];
        foreach (self::palabras($q) as $i => $palabra) {
            $ors = [];
            foreach ($columnasTexto as $j => $columna) {
                $clave = "{$prefijo}{$i}_{$j}";
                $ors[] = "{$columna} LIKE :{$clave}";
                $params[$clave] = '%' . self::escapar($palabra) . '%';
            }
            $digitos = self::digitos($palabra);
            if ($digitos !== '') {
                $clave = "{$prefijo}{$i}_c";
                $ors[] = "REPLACE({$columnaCedula}, '.', '') LIKE :{$clave}";
                $params[$clave] = '%' . $digitos . '%';
            }
            $sqlPartes[] = '(' . implode(' OR ', $ors) . ')';
        }
        return [implode(' AND ', $sqlPartes), $params];
    }
}
