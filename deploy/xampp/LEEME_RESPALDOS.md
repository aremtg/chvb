# Respaldos de CHVB (XAMPP en Windows)

Qué guarda cada respaldo: un `.zip` con `base_de_datos.sql` y, con `--con-uploads`, la carpeta `uploads/`
(hojas de vida, firmas, evidencias). El `.env` NO se incluye; guárdalo aparte en un lugar seguro.

## 1. Configurar el .env
Agrega (ver `.env.example`):

    BACKUP_PATH=D:\respaldos\chvb
    BACKUP_RETENTION_DAYS=30

`BACKUP_PATH` debe estar en **otro disco o en una carpeta de red**. Un respaldo en el mismo disco no sirve si el disco falla.
Si `mysqldump.exe` no está en `C:\xampp\mysql\bin\`, agrega `MYSQLDUMP_PATH=` con su ruta.

## 2. Probarlo a mano (abre CMD)

    C:\xampp\php\php.exe C:\xampp\htdocs\chvb\bin\respaldo.php --con-uploads

Debe terminar con una línea `OK Respaldo listo: ...` y crear el zip. Si falla verás `ERROR ...` con el motivo.
Todo queda también en `respaldo.log` dentro de la carpeta de destino.
(Ajusta `C:\xampp\htdocs\chvb` si tu proyecto está en otra ruta.)

## 3. Programarlo cada noche
En un CMD **como administrador**:

    schtasks /Create /TN "CHVB respaldo diario" /SC DAILY /ST 02:00 /RU SYSTEM /RL HIGHEST /TR "\"C:\xampp\php\php.exe\" \"C:\xampp\htdocs\chvb\bin\respaldo.php\" --con-uploads"

Ejecutarla ya para comprobar: `schtasks /Run /TN "CHVB respaldo diario"`.
Si el destino es una carpeta de red, la cuenta SYSTEM normalmente no tiene acceso: crea la tarea con un usuario que sí lo tenga
(`/RU DOMINIO\usuario /RP`) o usa un disco local adicional.
Si la base de datos pesa poco pero `uploads/` crece mucho, puedes hacer el respaldo con uploads solo una vez por semana
y el diario sin `--con-uploads`.

## 4. Revisar
Cada semana mira que haya zips nuevos y que `respaldo.log` no tenga líneas `ERROR`.
Además, copia de vez en cuando la carpeta de respaldos fuera del edificio (disco externo o nube de la empresa).

## 5. Restaurar (probarlo al menos una vez, con un zip de prueba)
1. Descomprime el zip en una carpeta temporal.
2. Base de datos (en CMD):

       C:\xampp\mysql\bin\mysql.exe -u root -p -e "CREATE DATABASE IF NOT EXISTS chvb CHARACTER SET utf8mb4"
       C:\xampp\mysql\bin\mysql.exe -u root -p chvb < base_de_datos.sql

   Esto reemplaza las tablas actuales con las del respaldo.
3. Archivos: copia el contenido de la carpeta `uploads\` del zip sobre la carpeta `uploads` del proyecto (o la de `UPLOADS_PATH`).
4. Entra al sistema y revisa que carguen empleados, fotos y permisos.

## Notas
- La contraseña de la base de datos no aparece en la línea de comandos: se pasa por un archivo temporal que se borra al terminar.
- Si el volcado queda cortado o vacío, el script falla (código 1) y no deja un zip engañoso.
- Probado con un `mysqldump` simulado en Linux; en tu servidor Windows haz la prueba del punto 2 antes de programarlo.
