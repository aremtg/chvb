<?php
require_once __DIR__ . '/../../includes/session.php';
requireEmpleado();
validarCSRF();
require_once __DIR__ . '/../../src/controllers/PermisoController.php';
require_once __DIR__ . '/../../src/models/PermisoModel.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';
require_once __DIR__ . '/../../src/models/FirmaModel.php';
require_once __DIR__ . '/../../src/controllers/FirmaController.php';
require_once __DIR__ . '/../../src/helpers/FileManager.php';
require_once __DIR__ . '/../../src/models/NotificacionModel.php';
header('Content-Type: application/json'); ini_set('display_errors','0');
try {
    $id=(int)($_POST['id']??0); $version=(int)($_POST['version']??0); $cedula=$_SESSION['empleado_cedula'];
    $p=PermisoModel::obtenerPorId($id);
    if(!$p || $p['cedula_empleado']!==$cedula || $p['estado']!=='devuelto') { echo json_encode(['ok'=>false,'error'=>'Este permiso ya no está disponible para edición.']); exit; }
    if($version !== (int)$p['version']) { echo json_encode(['ok'=>false,'error'=>'El permiso cambió mientras lo tenías abierto. Recarga e inténtalo nuevamente.']); exit; }
    $empleado=EmpleadoModel::obtenerPorCedula($cedula); if(!$empleado) throw new Exception('Empleado no encontrado.');
    $tipo=$_POST['tipo_permiso']??''; $motivo=trim($_POST['motivo']??''); $jefe=trim($_POST['cedula_jefe']??'');
    $tiene=isset($_POST['tiene_reemplazo'])?1:0; $reemplazo=$tiene?trim($_POST['cedula_reemplazo']??''):null;
    $especial=isset($_POST['es_salida_pendiente_regreso']) && $tipo==='Permiso' ? 1:0;
    $errores=[];
    if(!in_array($tipo,['Permiso','Vacaciones','Licencia','Mision institucional'],true)) $errores[]='Tipo de permiso inválido.';
    if($motivo==='') $errores[]='El motivo es obligatorio.';
    if($jefe===''||$jefe===$cedula) $errores[]='Debes seleccionar un jefe inmediato válido.';
    if($tiene && (!$reemplazo||$reemplazo===$cedula)) $errores[]='Debes seleccionar un reemplazo válido.';
    if($especial && (empty($_POST['fecha_inicio'])||empty($_POST['hora_inicio']))) $errores[]='Indica fecha y hora de salida.';
    if(!$especial && empty($_POST['dias_confirmados'])) $errores[]='Debes indicar los días y horarios del permiso.';
    if($errores){ echo json_encode(['ok'=>false,'errores'=>$errores]); exit; }

    $campos=['tipo_permiso'=>$tipo,'motivo'=>$motivo,'tiene_reemplazo'=>$tiene,'cedula_reemplazo'=>$reemplazo,'cedula_jefe'=>$jefe,'es_salida_pendiente_regreso'=>$especial,
      'foto_reemplazo'=>null,'firma_reemplazo'=>null,'foto_jefe'=>null,'firma_jefe'=>null,'foto_jefe_prefirmado'=>null,'firma_jefe_prefirmado'=>null,
      'motivo_devolucion'=>null,'motivo_rechazo'=>null];
    $diasFinal=[];
    if($especial){
      $campos += ['fecha_inicio'=>trim($_POST['fecha_inicio']),'hora_inicio'=>trim($_POST['hora_inicio']),'fecha_fin'=>null,'hora_fin'=>null,'total_horas'=>null];
    } else {
      $dias=json_decode($_POST['dias_confirmados'],true);
      if(!is_array($dias)||!$dias) throw new Exception('El desglose de días es inválido.');
      $tipoPersonal=!empty($empleado['tipo_de_personal'])?$empleado['tipo_de_personal']:'Civil';
      $rec=PermisoController::recalcularConfirmado($dias,$tipoPersonal); $diasFinal=$rec['dias'];
      $campos += ['fecha_inicio'=>$diasFinal[0]['fecha'],'hora_inicio'=>$diasFinal[0]['hora_inicio'],'fecha_fin'=>$diasFinal[count($diasFinal)-1]['fecha'],'hora_fin'=>$diasFinal[count($diasFinal)-1]['hora_fin'],'total_horas'=>$rec['total_horas']];
    }
    $campos['remunerado']=isset($_POST['remunerado'])?1:0;
    $campos['es_compensatorio']=isset($_POST['es_compensatorio'])?1:0;
    $campos['fecha_horas_extra']=$campos['es_compensatorio']?($_POST['fecha_horas_extra']??null):null;
    $campos['es_devolucion']=isset($_POST['es_devolucion'])?1:0;
    if($campos['es_devolucion']){
      $devs=json_decode($_POST['devoluciones_json']??'[]',true)?:[]; $total=0;
      foreach($devs as $d){ $r=PermisoController::calcularHorasDevolucion($d['fecha']??'',$d['hora_inicio']??'',$d['hora_fin']??''); if(!$r['ok']) throw new Exception($r['error']); $d['total_horas']=$r['total_horas']; $total += $r['total_horas']; $devsFinal[]=$d; }
      $campos['devolucion_total_horas']=$total;
    } else { $devsFinal=[]; $campos['devolucion_total_horas']=null; }

    if(!empty($_POST['foto_solicitante_base64'])){ $rf=FileManager::guardarFotoPermisoBase64($cedula,$_POST['foto_solicitante_base64'],'permisos/fotos'); if(!$rf['ok']) throw new Exception($rf['error']); $campos['foto_solicitante']=$rf['ruta']; }
    if(isset($_POST['usar_firma_guardada'])){ $f=FirmaModel::obtenerPorCedula($cedula); if(!$f) throw new Exception('No tienes firma guardada.'); $campos['firma_solicitante']=$f['ruta_imagen']; }
    elseif(!empty($_POST['firma_solicitante_base64'])){ $f=FirmaController::guardarFirmaBase64($cedula,$_POST['firma_solicitante_base64']); if(!$f['ok']) throw new Exception($f['error']); $campos['firma_solicitante']=$f['ruta']; }
    elseif(!empty($_FILES['firma_solicitante_archivo'])){ $f=FirmaController::guardarFirmaArchivo($cedula,$_FILES['firma_solicitante_archivo']); if(!$f['ok']) throw new Exception($f['error']); $campos['firma_solicitante']=$f['ruta']; }
    if(!empty($_FILES['evidencia']) && $_FILES['evidencia']['error']===UPLOAD_ERR_OK){ $f=FileManager::guardarEvidenciaPermiso($cedula,$_FILES['evidencia']); if($f['ok']) $campos['evidencia_archivo']=$f['ruta']; }

    $nuevoReemplazo=$campos['cedula_reemplazo']; $anteriorReemplazo=$p['cedula_reemplazo'];
    $campos['estado']=$tiene?'por_firmar_reemplazo':'por_firmar_jefe';
    if(!PermisoModel::actualizarConVersion($id,$version,$campos)){ echo json_encode(['ok'=>false,'error'=>'conflicto_version']); exit; }
    if(!$especial) PermisoModel::reemplazarDias($id,$diasFinal); else PermisoModel::reemplazarDias($id,[]);
    $pdo=getPDO(); $pdo->prepare('DELETE FROM permisos_devoluciones WHERE permiso_id=:b1')->execute(['b1'=>$id]);
    if(!empty($devsFinal)) PermisoModel::crearDevoluciones($id,$devsFinal);

    $versionNueva=$version+1;
    PermisoModel::registrarHistorial($id,$version,'devuelto',$campos['estado'],'empleado',$cedula,'Permiso editado y reenviado; se conserva el mismo consecutivo.');
    if($anteriorReemplazo && $anteriorReemplazo!==$nuevoReemplazo){
      NotificacionModel::eliminarPendientesDePermisoParaEmpleado($anteriorReemplazo,$id);
      NotificacionModel::crearParaEmpleado($anteriorReemplazo,'Ya no eres reemplazo del permiso '.$p['consecutivo'],'/chvb/public/permiso_ver.php?id='.$id,'permiso');
      PermisoModel::registrarHistorial($id,$versionNueva,'devuelto','devuelto','reemplazo',$anteriorReemplazo,'Ya no eres reemplazo de este permiso.');
    }
    if($nuevoReemplazo) NotificacionModel::crearParaEmpleado($nuevoReemplazo,'Tienes un permiso pendiente de firma como reemplazo: '.$p['consecutivo'],'/chvb/public/permiso_ver.php?id='.$id,'permiso');
    NotificacionModel::crearParaEmpleado($jefe,'Tienes un permiso pendiente de firma: '.$p['consecutivo'],'/chvb/public/permiso_ver.php?id='.$id,'permiso');
    echo json_encode(['ok'=>true,'id'=>$id,'consecutivo'=>$p['consecutivo'],'estado'=>$campos['estado']]);
} catch(Throwable $e){ http_response_code(500); echo json_encode(['ok'=>false,'error'=>$e->getMessage()]); }
