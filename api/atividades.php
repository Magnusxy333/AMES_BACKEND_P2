<?php
declare(strict_types=1); session_start();
header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store');
function out(int $s,array $v): never { http_response_code($s); echo json_encode($v,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function pdo_db(): PDO { static $p=null; if($p) return $p; $c=require __DIR__.'/config.php'; $p=new PDO("mysql:host={$c['host']};port={$c['port']};dbname={$c['database']};charset=utf8mb4",$c['username'],$c['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]); return $p; }
function uid(): int { return isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : (int)(getenv('AMES_USER_ID') ?: 0); }
try {
 $db=pdo_db(); $method=$_SERVER['REQUEST_METHOD']??'GET';
 if($method==='GET') { $q=$db->prepare('SELECT a.id_atividade,a.titulo,a.descricao,a.data_hora_criacao,a.data_hora_inicio,a.data_hora_fim,a.status,a.latitude,a.longitude,a.id_categoria,c.nome_categoria FROM atividade a LEFT JOIN categoria_atividade c ON c.id_categoria=a.id_categoria WHERE a.id_usuario=:uid ORDER BY a.data_hora_inicio IS NULL,a.data_hora_inicio,a.id_atividade DESC'); $q->execute(['uid'=>uid()]); out(200,['success'=>true,'atividades'=>$q->fetchAll()]); }
 if($method==='POST') {
  $b=json_decode(file_get_contents('php://input'),true); if(!is_array($b)) out(400,['success'=>false,'message'=>'JSON inválido.']);
  $title=trim((string)($b['titulo']??'')); $desc=trim((string)($b['descricao']??'')); $cat=(int)($b['id_categoria']??4); $status=(string)($b['status']??'pendente'); $lat=$b['latitude']??null; $lng=$b['longitude']??null;
  if($title===''||mb_strlen($title)>150) out(422,['success'=>false,'message'=>'Informe um título de até 150 caracteres.']);
  if(!in_array($status,['pendente','em andamento','concluida'],true)) $status='pendente';
  if($lat!==null&&(!is_numeric($lat)||$lat < -90||$lat>90)) out(422,['success'=>false,'message'=>'Latitude inválida.']);
  if($lng!==null&&(!is_numeric($lng)||$lng < -180||$lng>180)) out(422,['success'=>false,'message'=>'Longitude inválida.']);
  $c=$db->prepare('SELECT 1 FROM categoria_atividade WHERE id_categoria=?'); $c->execute([$cat]); if(!$c->fetchColumn()) $cat=4;
  $q=$db->prepare('INSERT INTO atividade(id_usuario,id_categoria,titulo,descricao,data_hora_inicio,status,latitude,longitude) VALUES(:uid,:cat,:t,:d,:dt,:s,:lat,:lng)');
  $q->execute(['uid'=>uid(),'cat'=>$cat,'t'=>$title,'d'=>$desc?:null,'dt'=>!empty($b['data_hora_inicio'])?str_replace('T',' ',$b['data_hora_inicio']).(strlen($b['data_hora_inicio'])===16?':00':''):null,'s'=>$status,'lat'=>$lat!==null&&$lat!==''?(float)$lat:null,'lng'=>$lng!==null&&$lng!==''?(float)$lng:null]);
  out(201,['success'=>true,'id_atividade'=>(int)$db->lastInsertId(),'message'=>'Atividade cadastrada.']);
 }
 out(405,['success'=>false,'message'=>'Método não permitido.']);
} catch(Throwable $e) { error_log((string)$e); out(500,['success'=>false,'message'=>'Falha ao acessar o banco. Confira a configuração do MySQL e as permissões do usuário.']); }
