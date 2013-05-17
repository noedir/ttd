<?php
$pdo = new PDO("mysql:host=localhost; dbname=tiltheda_base", "tiltheda", "dudinha09");
$pds = new PDO("sqlite:push.db");

$query = $pdo->query("SELECT u.us_email email, (SELECT COUNT(con_email) FROM tbl_convidados c WHERE c.con_email = u.us_email AND c.con_aceitou = 's') AS total, u.us_tokenpush push, (SELECT c2.con_count FROM tbl_convidados c2 WHERE c2.con_email = u.us_email AND c2.con_aceitou = 's' LIMIT 1) AS count FROM tbl_usuario u WHERE u.us_tokenpush <> '' ORDER BY us_ultimologin DESC");
$row = $query->fetchAll();
$ver = array();

#$ins = $pds->prepare("INSERT INTO enviapush (push, mensagem, counts) VALUES (:ps, :ms, :co)");
#$ins->bindParam(':ps',$push);
#$ins->bindParam(':ms', $mensagem);
#$ins->bindParam(':co', $count);

foreach ($row as $sg){
    if(!in_array($sg['push'],$ver)){
	$idc = '';
	
	$c = $pdo->query("SELECT con_count FROM tbl_convidados WHERE con_email = '".$sg['email']."' AND con_aceitou = 's'")->fetchAll();
	
	if(count($c) > 0){
	    foreach($c as $co){
		$idc .= $co['con_count'].',';
	    }
	    $idc = substr($idc,0,-1);
	}else{
	    $idc = 0;
	}
	
	if($sg['total'] > 1){
	    if($sg['push'] != '(null)' && $sg['push'] != ''){
		
		$push = $sg['push'];
		$mensagem = utf8_encode('Novas TIPS disponíveis');
		$count = $idc;
	    }
	}else if($sg['total'] == 1){
	    if($sg['push'] != '(null)' && $sg['push'] != ''){
		
		$cont = $pdo->query("SELECT co_titulo FROM tbl_count WHERE co_codigo = ".$sg['count'])->fetch(PDO::FETCH_ASSOC);
		$push = $sg['push'];
		$mensagem =  utf8_encode($cont['co_titulo']);
		$count = $idc;
	    }
	}
	$pds->exec("INSERT INTO enviapush (push, mensagem, counts) VALUES ('".$push."', '".$mensagem."', '".$count."')");
	$ver[] = $sg['push'];
    }
}
echo "<pre>";
print_r($ver);