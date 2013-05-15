<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mobile extends CI_Controller {
    public function __construct() {
	parent::__construct();
	$this->load->model('mobile_model','mdb');
	$this->load->helper('array');
    }
    
    public function deleta_email(){
	$email = $this->input->get('email');
	$this->mdb->del_usuario($email);
	$user['exclui']['status'] = '200';
	$user['exclui']['msg'] = 'Excluido com sucesso';
	
	echo json_encode($user);
    }
    
    public function cadastro(){
	header("Content-type: application/json");
	$input = elements(array('token_face','nome_usuario','email_usuario','tokenpush'),$this->input->post());
	$input['senhaprov'] = sha1(md5($input['email_usuario']).":".md5('123456'));
        $ver = array(
	    'email' => $input['email_usuario'],
            'token' => $input['token_face'],
        );
	$tk = array(
	    'email' => $input['email_usuario'],
	    'tokenpush' => $input['tokenpush'],
	);
	if($ver['email'] != ""){
            $verify = $this->mdb->chk_usuario($ver['email'])->result();
	    $user['contagem'] = count($verify);
            if(count($verify) > 0){
                $user['status'] = '201';
                $user['msg'] = 'Email inexistente';
            }else{
                $this->mdb->set_usuario($input);
		$this->mdb->set_cleartokenpush($tk);
		$this->mdb->set_tokenpush($tk);
		$query = $this->mdb->get_loginuser($ver)->result_array();
		foreach($query as $v){
		    $user['login'] = $v;
		}
		
		$segue = $this->mdb->get_convites('s',$tk)->result_array();
		$meus = $this->mdb->get_meus($tk)->result_array();
		$conv = $this->mdb->get_convites('n',$tk)->result_array();

		if(count($segue) > 0){
		    foreach($segue as $k){
			if($k['privado'] == 's'){
			    $k['privado'] = 'true';
			}else{
			    $k['privado'] = 'false';
			}
			$user['seguindo']['counts'][] = $k;
		    }
		}else{
		    $user['seguindo']['counts'] = array();
		}

		if(count($meus) > 0){
		    foreach($meus as $m){
			if($m['id'] != null || $m['id'] != ''){
			    if($m['privado'] == 's'){
				$m['privado'] = 'true';
			    }else{
				$m['privado'] = 'false';
			    }
			    $user['projetos']['counts'][] = $m;
			}else{
			    $user['projetos']['counts'] = array();
			}
		    }
		}else{
		    $user['projetos']['counts'] = array();
		}

		if(count($conv) > 0){
		    foreach($conv as $c){
			if($c['privado'] == 's'){
			    $c['privado'] = 'true';
			}else{
			    $c['privado'] = 'false';
			}
			$user['convites']['counts'][] = $c;
		    }
		}else{
		    $user['convites']['counts'] = array();
		}
		
                $user['status'] = '200';
                $user['msg'] = 'ok';
            }
        }else{
            $user['status'] = '202';
            $user['msg'] = 'Email não Informado';
        }
	echo json_encode($user);
    }
    
    public function logout(){
	header("Content-type: application/json");
	$input = elements(array('email','tokenpush'),$this->input->post());
	if($input['email'] != ''){
	    
	    $query = $this->mdb->get_loginuser($input)->result_array();
	    if(count($query) > 0){
		$this->mdb->set_userlogout($input);
		$resp['status'] = '200';
		$resp['msg'] = 'Logout efetuado com sucesso';
	    }else{
		$resp['status'] = '201';
		$resp['msg'] = 'Email não localizado';
	    }
	}else{
	    $resp['status'] = '202';
	    $resp['msg'] = 'Email não informado';
	}
	
	echo json_encode($resp);
    }
    
    public function login(){
	header("Content-type: application/json");
	$input = elements(array('email','tokenpush'),$this->input->post());
	if($input['email'] != ""){
	    $query = $this->mdb->get_loginuser($input)->result_array();
	    if(count($query) > 0){
		$this->mdb->set_cleartokenpush($input);
		$this->mdb->set_tokenpush($input);
		foreach($query as $v){
		    $user['login'] = $v;
		}
		$segue = $this->mdb->get_convites('s',$input)->result_array();
		$meus = $this->mdb->get_meus($input)->result_array();
		$conv = $this->mdb->get_convites('n',$input)->result_array();

		if(count($segue) > 0){
		    foreach($segue as $k){
			if($k['privado'] == 's'){
			    $k['privado'] = 'true';
			}else{
			    $k['privado'] = 'false';
			}
			$user['seguindo']['counts'][] = $k;
		    }
		}else{
		    $user['seguindo']['counts'] = array();
		}

		if(count($meus) > 0){
		    foreach($meus as $m){
			if($m['id'] != null || $m['id'] != ''){
			    if($m['privado'] == 's'){
				$m['privado'] = 'true';
			    }else{
				$m['privado'] = 'false';
			    }
			    $user['projetos']['counts'][] = $m;
			}else{
			    $user['projetos']['counts'] = array();
			}
		    }
		}else{
		    $user['projetos']['counts'] = array();
		}

		if(count($conv) > 0){
		    foreach($conv as $c){
			if($c['privado'] == 's'){
			    $c['privado'] = 'true';
			}else{
			    $c['privado'] = 'false';
			}
			$user['convites']['counts'][] = $c;
		    }
		}else{
		    $user['convites']['counts'] = array();
		}
		
		$user['status'] = '200';
		$user['msg'] = 'ok';
	    }else{
		$user['status'] = '201';
		$user['msg'] = "Email não localizado";
	    }
	}else{
	    $user['status'] = '202';
	    $user['msg'] = 'Insira seu email';
	}
	echo json_encode($user);
    }
    
    private function geraTimestamp($dataini,$datafim) {
	$ini = explode('-', $dataini);
	$fim = explode('-', $datafim);
	
	$vini = mktime(0, 0, 0, $ini[1], $ini[2], $ini[0]);
	$vfim = mktime(0, 0, 0, $fim[1], $fim[2], $fim[0]);
	
	$diferenca = $vfim - $vini;
	$dias = (int)floor( $diferenca / (60 * 60 * 24));
	
	return strval(abs($dias));
    }
    
    public function count_public(){
	header("content-type: application/json");
	$input = array();
	if($this->input->post('busca') != ''){
	    $input['busca'] = $this->input->post('busca');
	}else{
	    $input['busca'] = '';
	}
	
	if($this->input->post('busca_id') != ''){
	    $input['busca_id'] = $this->input->post('busca_id');
	}else{
	    $input['busca_id'] = '';
	}
	$query = $this->mdb->get_countpublica($input)->result_array();
	if(count($query) > 0){
	    foreach($query as $k => $v){
		$ret['counts'][$k] = $v;
		if($ret['counts'][$k]['tags'] != ''){
		    $ex = explode(',',$ret['counts'][$k]['tags']);
		    if(is_array($ex)){
			unset($ret['counts'][$k]['tags']);
			foreach($ex as $e){
			    $ret['counts'][$k]['tags'][]['nome'] = $e;
			}
		    }else{
			$ret['counts'][$k]['tags'][]['nome'] = $ex;
		    }
		}else{
		    $ret['counts'][$k]['tags'] = array();
		}
		$ret['counts'][$k]['image'] = base_url().'capa/'.$ret['counts'][$k]['image'];
		if($ret['counts'][$k]['premium'] == 'n'){
		    $ret['counts'][$k]['premium'] = 'false';
		}else{
		    $ret['counts'][$k]['premium'] = 'true';
		}

		$ret['status'] = '200';
		$ret['msg'] = 'ok';

		if($ret['counts'][$k]['inicio'] != '0000-00-00' && $ret['counts'][$k]['inicio'] != '' && $ret['counts'][$k]['inicio'] <= date("Y-m-d")){
		    $dataini = date("Y-m-d");
		    $datafim = $ret['counts'][$k]['inicio'];
		    $ret['counts'][$k]['dias_passados'] = strval($this->geraTimestamp($dataini, $datafim) + 1);
		}else{
		    $ret['counts'][$k]['dias_passados'] = '0';
		}

		unset($ret['counts'][$k]['inicio']);

	    }
	}else{
	    $ret['counts'] = array();
	    $ret['status'] = '201';
	    $ret['msg'] = 'Nenhuma count encontrada';
	}

	echo json_encode($ret);
    }

    public function list_counts(){
	header("Content-type: application/json");
	$id = $this->input->post('codigo');
	$priv = $this->input->post('priv');
	
	if($priv == ""){
	    $priv = NULL;
	}
	$query = $this->mdb->get_allcount($priv,$id)->result_array();
	foreach($query as $k){
	    if($k['co_data_inicio'] != null || $k['co_data_inicio'] == ''){
		if($k['co_tags'] != ''){
		    $ex = explode(',',$k['co_tags']);
		    if(is_array($ex)){
			unset($k['co_tags']);
			foreach($ex as $e){
			    $k['co_tags'][] = $e;
			}
		    }else{
			$k['co_tags'] = $ex;
		    }
		}
	    }
	    $ret['counts'][] = $k;
	}

	echo json_encode($ret);
    }

    public function main_count(){
	header("Content-type: application/json");
	$id = elements(array('codigo','email'),$this->input->post());
	$query = $this->mdb->get_tipcount($id)->result_array();
	
	if(count($query) > 0){
	    $ret['count']['titulo'] = $query[0]['co_titulo'];
	    $ret['count']['data_inicio'] = $query[0]['co_data_inicio'];
	    $ret['count']['total_data'] = $query[0]['co_dias'];
	    $ret['count']['total_tips'] = strval(count($query));
	    if($query[0]['seguindo'] > 0){
		$ret['count']['follow'] = "true";
	    }else{
		$ret['count']['follow'] = "false";
	    }

	    foreach($query as $k => $v){
		$ret['count']['tips'][$k] = $v;
		
		if($ret['count']['tips'][$k]['ti_titulo'] != ""){
		    $ret['count']['tips'][$k]['ti_imagem'] = base_url().'tips/'.$ret['count']['tips'][$k]['ti_imagem'];
		    $ret['count']['tips'][$k]['ti_thumb'] = base_url().'tips/thumb_'.$v['ti_imagem'];
		}else{
		    unset($ret['count']['tips'][$k]);
		}
		
		unset($ret['count']['tips'][$k]['ti_imgposicao']);
		unset($ret['count']['tips'][$k]['ti_imgcentral']);
		unset($ret['count']['tips'][$k]['co_titulo']);
		unset($ret['count']['tips'][$k]['co_data_inicio']);
		unset($ret['count']['tips'][$k]['co_dias']);
		unset($ret['count']['tips'][$k]['ti_titulo']);
		unset($ret['count']['tips'][$k]['ti_subtitulo']);
		unset($ret['count']['tips'][$k]['ti_descricao']);
		
	    }
	    $ret['status'] = '200';
	    $ret['msg'] = 'ok';
	}else{
	    $ret['status'] = '201';
	    $ret['msg'] = 'Count não localizada';
	}

	echo json_encode($ret);
    }
    
    public function tips(){
	header("Content-type: application/json");
	$id = elements(array('codigo'),$this->input->post());
	$query = $this->mdb->get_tips($id)->result_array();
	if(count($query) > 0){
	    foreach($query as $k => $v){
		$ret['tips'] = $v;
		$ret['tips']['imagem'] = base_url().'tips/'.$ret['tips']['imagem'];
		$ret['tips']['descricao'] = str_replace("<br>", "\n\n", $ret['tips']['descricao']);
	    }
	    
	    
	    $ret['status'] = '200';
	    $ret['msg'] = 'ok';
	}else{
	    $ret['status'] = '201';
	    $ret['msg'] = 'Tip não localizada';
	}
	echo json_encode($ret);
    }
    
    public function menu(){
	header("Content-type: application/json");
	$email = elements(array('email'),$this->input->post());
	$segue = $this->mdb->get_convites('s',$email)->result_array();
	$meus = $this->mdb->get_meus($email)->result_array();
	$conv = $this->mdb->get_convites('n',$email)->result_array();
	
	if(count($segue) > 0){
	    foreach($segue as $k){
		if($k['privado'] == 's'){
		    $k['privado'] = 'true';
		}else{
		    $k['privado'] = 'false';
		}
		$ret['seguindo']['counts'][] = $k;
	    }
	}else{
	    $ret['seguindo']['counts'] = array();
	}
	
	if(count($meus) > 0){
	    foreach($meus as $m){
		if($m['id'] != null || $m['id'] != ''){
		    if($m['privado'] == 's'){
			$m['privado'] = 'true';
		    }else{
			$m['privado'] = 'false';
		    }
		    $ret['projetos']['counts'][] = $m;
		}else{
		    $ret['projetos']['counts'] = array();
		}
	    }
	}else{
	    $ret['projetos']['counts'] = array();
	}
	
	if(count($conv) > 0){
	    foreach($conv as $c){
		if($c['privado'] == 's'){
		    $c['privado'] = 'true';
		}else{
		    $c['privado'] = 'false';
		}
		$ret['convites']['counts'][] = $c;
	    }
	}else{
	    $ret['convites']['counts'] = array();
	}
	echo json_encode($ret);
    }
    
    public function seguir_count(){
	header("Content-type: application/json");
	$input = elements(array('email','id_count'),$this->input->post());
	$input['seguir'] = 's';
	$input['sair'] = 'n';
	$this->mdb->set_seguircount($input);
	
	$query = $this->mdb->get_count($input['id_count'])->result_array();
			
	$user['status'] = '200';
	$user['msg'] = 'Você começou a seguir a count '.$query[0]['co_titulo'];
	
	echo json_encode($user);
    }
    
    /*
     * Function @sair_count()
     * 
     */
    public function sair_count(){
	header("Content-type: application/json");
	$input = elements(array('email','id_count'),$this->input->post());
	$input['codigo'] = $input['id_count'];
	$input['seguir'] = 'n';
	$input['sair'] = 's';
	$this->mdb->set_seguircount($input);
	
	$query = $this->mdb->get_tipcount($input)->result_array();
	
	$user['status'] = '200';
	$user['msg'] = 'Você deixou de seguir a count '.$query[0]['co_titulo'];
		
	echo json_encode($user);
    }
}