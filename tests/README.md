# Pruebas del sistema de permisos

## Cómo correrlas
```
composer install        # instala PHPUnit, Dompdf y FPDI
composer test           # o: vendor/bin/phpunit
```

## Qué cubren
- `tests/Unit/JornadaHelperTest.php`: horas por jornada (administrativa 8,40 h, restringida, turnos), almuerzo, validación de horas.
- `tests/Pdf/PermisoPdfTest.php`: número de páginas y tamaño del PDF (1 y 2 permisos por hoja, 5 permisos, 16 y 18 días).

## Qué NO cubren todavía
- `calcularHorasPorDias()` y las transiciones de estado (enviar, firmar, anular) consultan la base de datos. Para probarlas sin BD hay que sacar el acceso a `PermisoModel` detrás de una interfaz.
