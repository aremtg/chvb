-- Empleados NO ACTIVOS: se desactiva el acceso (PIN) de los que ya estén así en la BD.
-- A partir de ahora esto lo hace el sistema solo al marcar a un empleado como "no activo".
-- Ejecutar UNA sola vez. No borra nada: solo pone activo = 0 en usuarios_empleados.
UPDATE usuarios_empleados ue
JOIN empleados e ON e.cedula = ue.cedula
SET ue.activo = 0
WHERE e.estado = 'no activo' AND ue.activo = 1;
