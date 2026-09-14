$pdo->beginTransaction();
$stmt = $pdo->prepare("SELECT ultimo_numero FROM permisos_consecutivos WHERE anio = :a1 FOR UPDATE");
// si no existe la fila del año, INSERT con 0 primero
// luego UPDATE ultimo_numero = ultimo_numero + 1
// $consecutivo = "PER-{$anio}-" . str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);
$pdo->commit();