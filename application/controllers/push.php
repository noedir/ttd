<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Push extends CI_Controller {
    
    /*private function tratapush($token,$mensagem,$idcount=NULL){
	$config['hostname'] = 'sqlite:'.APPPATH.'db/push.db';
	$config['username'] = '';
	$config['password'] = '';
	$config['database'] = '';
	$config['dbdriver'] = 'sqlite';
	$config['dbprefix'] = '';
	$config['pconnect'] = TRUE;
	$config['db_debug'] = TRUE;
	$config['cache_on'] = FALSE;
	$config['cachedir'] = '';
	$config['char_set'] = 'utf8';
	$config['dbcollat'] = 'utf8_general_ci';
	$config['swap_pre'] = '';
	$config['autoinit'] = TRUE;
	$config['stricton'] = FALSE;
	$this->load->database('sqlpush');
	$this->load->model('push_model','pudb',$config);
	$array = array(
	    'push' => $token,
	    'mensagem' => $mensagem,
	    'counts' => $idcount
	);
	$this->db->query("INSERT INTO enviapush (push,mensagem,counts) VALUES ('".$token."','".$mensagem."','".$idcount."')");
	//$this->pudb->set_push($array);
    }*/
    
    public function index(){
	//$this->load->model('web_model','wdb');
	//$this->load->model('push_model','pudb',$config);
	$config['hostname'] = 'sqlite:'.APPPATH.'db/push.db';
	$config['username'] = '';
	$config['password'] = '';
	$config['database'] = '';
	$config['dbdriver'] = 'sqlite';
	$config['dbprefix'] = '';
	$config['pconnect'] = TRUE;
	$config['db_debug'] = TRUE;
	$config['cache_on'] = FALSE;
	$config['cachedir'] = '';
	$config['char_set'] = 'utf8';
	$config['dbcollat'] = 'utf8_general_ci';
	$config['swap_pre'] = '';
	$config['autoinit'] = TRUE;
	$config['stricton'] = FALSE;
	$this->load->database('sqlpush',true);
	$this->db->query("INSERT INTO enviapush (push,mensagem,counts) VALUES ('111233','Novas tips','10,11,12,13')");
	
	echo "gravou";
	die();
	
	$segue = $this->wdb->get_seguepush()->result_array();
	
	$ver = array();
	
	foreach ($segue as $sg){
	    if(!in_array($sg['push'],$ver)){
		$idc = '';
		$c = $this->wdb->get_idcount($sg['email'])->result_array();
		if(count($c) > 0){
		    foreach($c as $co){
			$idc .= $co['con_count'].',';
		    }
		    $idc = substr($idc,0,-1);
		}else{
		    $idc = NULL;
		}

		if($sg['total'] > 1){
		    if($sg['push'] != '(null)' && $sg['push'] != ''){
			$dd = array(
			    'push' => $sg['push'],
			    'mensagem' => 'Novas TIPS disponíveis',
			    'counts' => $idc,
			);
			$this->pudb->set_push($dd);
			//$this->tratapush($sg['push'],"Novas TIPS disponíveis",$idc);
		    }
		}else if($sg['total'] == 1){
		    if($sg['push'] != '(null)' && $sg['push'] != ''){
			$count = $this->wdb->get_countpush($sg['count'])->result_array();
			$dd = array(
			    'push' => $sg['push'],
			    'mensagem' => $count[0]['co_titulo'],
			    'counts' => $idc,
			);
			$this->pudb->set_push($dd);
			//$this->pudb->set_push("INSERT INTO enviapush (push,mensagem,counts) VALUES ('".$sg['push']."','".$count[0]['co_titulo']."','".$idc."')");
			//$this->tratapush($sg['push'],$count[0]['co_titulo'], $idc);
		    }
		}
		$ver[] = $sg['push'];
	    }
	}
	
	echo "<pre>";
	print_r($ver);
    }
}