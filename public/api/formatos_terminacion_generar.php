<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../src/models/FormatoModel.php';
require_once __DIR__ . '/../../src/models/EmpleadoModel.php';
header('Content-Type: application/json; charset=utf-8');
requireSuperAdmin();
if (!in_array($_SESSION['superadmin_rol'] ?? '', ['superadmin_talento_humano','auxiliar_talento_humano'], true)) {
 http_response_code(403); echo json_encode(['ok'=>false,'error'=>'No tienes permiso para usar Formatos.'],JSON_UNESCAPED_UNICODE); exit;
}
validarCSRF();
function termSexo(string $s): string { $s=mb_strtolower(trim($s),'UTF-8'); if(in_array($s,['f','femenino','femenina','mujer'],true))return 'F'; if(in_array($s,['m','masculino','hombre'],true))return 'M'; return ''; }
function termNombreArchivo(string $s): string { $s=mb_strtoupper(trim($s),'UTF-8'); $s=preg_replace('/[\\\\\/:*?"<>|]/u','',$s); return trim(preg_replace('/\s+/u',' ',$s)); }
function termReemplazarParrafoHistorial(string $docx,array $lineas): void {
 $zip=new ZipArchive(); if($zip->open($docx)!==true)throw new RuntimeException('No fue posible preparar el historial de renovaciones.');
 $xml=$zip->getFromName('word/document.xml'); if($xml===false){$zip->close();return;}
 $dom=new DOMDocument();$dom->preserveWhiteSpace=true;$dom->formatOutput=false;if(!@$dom->loadXML($xml)){$zip->close();return;}
 $xp=new DOMXPath($dom);$xp->registerNamespace('w','http://schemas.openxmlformats.org/wordprocessingml/2006/main');
 $target=null;foreach($xp->query('//w:body/w:p') as $p){$t='';foreach($xp->query('.//w:t',$p) as $n)$t.=$n->textContent;if(strpos($t,'HISTORIAL_TERMINACION_MARKER')!==false){$target=$p;break;}}
 if($target){
   $parent=$target->parentNode;$prototype=$target->cloneNode(true);
   $escribir=function($p,$texto)use($dom,$xp){$runs=$xp->query('.//w:r',$p);$first=$runs->item(0);if(!$first){$first=$dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main','w:r');$p->appendChild($first);}
     foreach(iterator_to_array($xp->query('.//w:t',$p)) as $t)$t->parentNode->removeChild($t);
     $t=$dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main','w:t',$texto);$t->setAttribute('xml:space','preserve');$first->appendChild($t);
     foreach($xp->query('.//w:r',$p) as $r){$texts=$xp->query('./w:t',$r);if($r!==$first&&$texts->length===0){/* keep formatting run empty */}}
   };
   if(!$lineas){$parent->removeChild($target);}else{
     $escribir($target,$lineas[0]);
     $ref=$target;
     for($i=1;$i<count($lineas);$i++){$clone=$prototype->cloneNode(true);$escribir($clone,$lineas[$i]);$parent->insertBefore($clone,$ref->nextSibling);$ref=$clone;}
   }
 }
 $zip->addFromString('word/document.xml',$dom->saveXML());$zip->close();
}
try {
 $cedula=trim((string)($_POST['cedula']??''));$fechaFin=trim((string)($_POST['fecha_fin']??''));$duraciones=json_decode((string)($_POST['duraciones']??'[]'),true);
 if($cedula===''||!is_array($duraciones))throw new InvalidArgumentException('Selecciona un empleado y las opciones del contrato.');
 $empleado=EmpleadoModel::obtenerPorCedula($cedula);if(!$empleado)throw new InvalidArgumentException('No se encontró el empleado.');
 foreach(['nombre','cedula','sexo','cargo','tipo_de_contrato','fecha_inicio_contrato','fecha_fin_contrato'] as $k)if(trim((string)($empleado[$k]??''))==='')throw new InvalidArgumentException('Falta el dato obligatorio en la hoja de vida: '.$k.'.');
 $sexo=termSexo((string)$empleado['sexo']);if(!$sexo)throw new InvalidArgumentException('El sexo del empleado no está registrado correctamente.');
 $inicio=(string)$empleado['fecha_inicio_contrato'];$finInicial=(string)$empleado['fecha_fin_contrato'];
 foreach([$inicio,$finInicial,$fechaFin] as $f){$d=DateTime::createFromFormat('!Y-m-d',$f);if(!$d||$d->format('Y-m-d')!==$f)throw new InvalidArgumentException('Hay una fecha inválida.');}
 if(count($duraciones)>20)throw new InvalidArgumentException('Puedes registrar máximo 20 renovaciones por formato.');
 $lineas=[];$cursor=(new DateTime($finInicial))->modify('+1 day');$finElegido=$finInicial;
 foreach($duraciones as $i=>$mRaw){$m=(int)$mRaw;$n=$i+1;if(!in_array($m,[1,2,3,6,12,18,24,36],true))throw new InvalidArgumentException("Duración inválida para RN{$n}.");if($n>=4&&$m<12)throw new InvalidArgumentException("La renovación RN{$n} debe ser de mínimo 12 meses.");
   $ini=$cursor->format('Y-m-d');$fin=FormatoModel::calcularFin($ini,$m);$lineas[]='Renovación N°'.$n.': del '.FormatoModel::fechaLarga($ini).' al '.FormatoModel::fechaLarga($fin).'.';$finElegido=$fin;$cursor=(new DateTime($fin))->modify('+1 day');
 }
 // La fecha seleccionada debe corresponder al contrato inicial o a una de las renovaciones calculadas.
 if($fechaFin!==$finInicial){
   $permitidas=[$finInicial];$cursor=(new DateTime($finInicial))->modify('+1 day');foreach($duraciones as $m){$f=FormatoModel::calcularFin($cursor->format('Y-m-d'),(int)$m);$permitidas[]=$f;$cursor=(new DateTime($f))->modify('+1 day');}
   if(!in_array($fechaFin,$permitidas,true))throw new InvalidArgumentException('La fecha final debe corresponder al contrato inicial o a una renovación calculada.');
 }
 $indice=array_search($fechaFin,[$finInicial],true);
 $cursor=(new DateTime($finInicial))->modify('+1 day');$hist=[];$fechaValida=$finInicial;
 foreach($duraciones as $i=>$m){$f=FormatoModel::calcularFin($cursor->format('Y-m-d'),(int)$m);$hist[]=['inicio'=>$cursor->format('Y-m-d'),'fin'=>$f];$cursor=(new DateTime($f))->modify('+1 day');if($f===$fechaFin){$indice=$i+1;$fechaValida=$f;break;}}
 if($fechaFin!==$finInicial&&$fechaValida!==$fechaFin)throw new InvalidArgumentException('La fecha final seleccionada no coincide con las renovaciones.');
 $historialLineas=[];foreach(array_slice($lineas,0,(int)$indice) as $linea)$historialLineas[]=$linea;
 $tipo=mb_strtolower(trim((string)$empleado['tipo_de_contrato']),'UTF-8');if(!in_array($tipo,['fijo','indefinido'],true))$tipo=trim((string)$empleado['tipo_de_contrato']);
 $plantilla=__DIR__.'/../../uploads/plantillas/AF-FT-02 NOTIFICACION TERMINACION CONTRATO.docx';if(!is_file($plantilla))throw new RuntimeException('Copia la plantilla AF-FT-02 NOTIFICACION TERMINACION CONTRATO.docx en uploads/plantillas/.');
 $generados=__DIR__.'/../../uploads/generados';if(!is_dir($generados)&&!mkdir($generados,0775,true)&&!is_dir($generados))throw new RuntimeException('No se pudo crear la carpeta de formatos generados.');
 if(!class_exists('PhpOffice\\PhpWord\\TemplateProcessor'))throw new RuntimeException('PHPWord no está instalado.');
 $nombre=mb_strtoupper(trim((string)$empleado['nombre']),'UTF-8');$cedulaFormateada=FormatoModel::formatearCedula((string)$empleado['cedula']);
 $archivo='AF-FT-02 NOTIFICACION DE TERMINACION CONTRATO '.$nombre.'_'.$cedulaFormateada.'.docx';$archivo=termNombreArchivo(pathinfo($archivo,PATHINFO_FILENAME)).'.docx';$salida=$generados.DIRECTORY_SEPARATOR.$archivo;
 $p=new \PhpOffice\PhpWord\TemplateProcessor($plantilla);
 $p->setValues(['fecha_actual'=>FormatoModel::fechaLarga(date('Y-m-d')),'tratamiento'=>$sexo==='F'?'Señora':'Señor','nombre_mayus'=>$nombre,'cargo'=>trim((string)$empleado['cargo']),'cedula'=>$cedulaFormateada,'tipo_contrato'=>$tipo,'fecha_inicio_inicial'=>FormatoModel::fechaLarga($inicio),'fecha_fin_inicial'=>FormatoModel::fechaLarga($finInicial),'fecha_fin'=>FormatoModel::fechaLarga($fechaFin),'historial_renovaciones'=>'HISTORIAL_TERMINACION_MARKER']);
 $p->saveAs($salida);termReemplazarParrafoHistorial($salida,$historialLineas);
 // Nombre y cédula: separar sus runs para poner en negrita únicamente esos datos.
 $zip=new ZipArchive();if($zip->open($salida)===true){$xml=$zip->getFromName('word/document.xml');if($xml!==false){$dom=new DOMDocument();$dom->preserveWhiteSpace=true;if(@$dom->loadXML($xml)){$xp=new DOMXPath($dom);$xp->registerNamespace('w','http://schemas.openxmlformats.org/wordprocessingml/2006/main');$ns='http://schemas.openxmlformats.org/wordprocessingml/2006/main';
 foreach(iterator_to_array($xp->query('//w:r')) as $r){if(!$r->parentNode)continue;$txt='';foreach($xp->query('./w:t',$r) as $t)$txt.=$t->textContent;if($txt===''||(!str_contains($txt,$nombre)&&!str_contains($txt,$cedulaFormateada)))continue;
   $rp=$xp->query('./w:rPr',$r)->item(0);$parts=[];$cursor=0;$len=mb_strlen($txt,'UTF-8');$targets=[$nombre,$cedulaFormateada];$matches=[];
   foreach($targets as $target){$pos=mb_strpos($txt,$target,0,'UTF-8');if($pos!==false)$matches[]=['pos'=>$pos,'len'=>mb_strlen($target,'UTF-8')];}
   usort($matches,fn($a,$b)=>$a['pos']<=>$b['pos']);foreach($matches as $m){if($m['pos']<$cursor)continue;if($m['pos']>$cursor)$parts[]=['t'=>mb_substr($txt,$cursor,$m['pos']-$cursor,'UTF-8'),'b'=>false];$parts[]=['t'=>mb_substr($txt,$m['pos'],$m['len'],'UTF-8'),'b'=>true];$cursor=$m['pos']+$m['len'];}if($cursor<$len)$parts[]=['t'=>mb_substr($txt,$cursor,null,'UTF-8'),'b'=>false];if(!$parts)continue;
   foreach($parts as $part){$nr=$dom->createElementNS($ns,'w:r');if($rp)$nr->appendChild($rp->cloneNode(true));$nrp=$xp->query('./w:rPr',$nr)->item(0);if($part['b']){if(!$nrp){$nrp=$dom->createElementNS($ns,'w:rPr');$nr->insertBefore($nrp,$nr->firstChild);}$nrp->appendChild($dom->createElementNS($ns,'w:b'));}else if($nrp){foreach(iterator_to_array((new DOMXPath($dom))->query('./w:b',$nrp)) as $b)$b->parentNode->removeChild($b);}
     $t=$dom->createElementNS($ns,'w:t');$t->setAttribute('xml:space','preserve');$t->appendChild($dom->createTextNode($part['t']));$nr->appendChild($t);$r->parentNode->insertBefore($nr,$r);}
   $r->parentNode->removeChild($r);
 }
 $zip->addFromString('word/document.xml',$dom->saveXML());}}$zip->close();}
 echo json_encode(['ok'=>true,'archivo'=>$archivo,'url'=>'./api/formato_archivo.php?f='.rawurlencode($archivo).'&accion=descargar'],JSON_UNESCAPED_UNICODE);
} catch(Throwable $e){http_response_code(400);echo json_encode(['ok'=>false,'error'=>$e->getMessage()],JSON_UNESCAPED_UNICODE);}
