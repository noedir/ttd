<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web_model extends CI_Model {
    public function set_oauth_instagram($dados=NULL){
	if($dados != NULL){
	    $ins = array(
		'oa_usuario' => $dados['usuario'],
		'oa_instagram_id' => $dados['id'],
		'oa_instagram_access_token' => $dados['access_token'],
		'oa_instagram_username' => $dados['user'],
	    );
	    $this->db->query("INSERT INTO tbl_oauth (oa_usuario,oa_instagram_id,oa_instagram_access_token,oa_instagram_username) VALUES('".$dados['usuario']."','".$dados['id']."','".$dados['access_token']."','".$dados['user']."') ON DUPLICATE KEY UPDATE oa_instagram_id = '".$dados['id']."', oa_instagram_access_token = '".$dados['access_token']."', oa_instagram_username = '".$dados['user']."'");
	}
    }
    
    public function cademail($email){
	if($email != ''){
	    $ins = array(
		'cad_email' => $email,
		'cad_datacadastro' => date("Y-m-d H:i:s")
	    );
	    $this->db->insert('tbl_cadastro',$ins);
	}
    }
    
    public function veemail($email){
	$this->db->from('tbl_cadastro');
	$this->db->where('cad_email',$email);
	return $this->db->count_all_results();
    }
    
    public function set_faceoauth($id,$access){
	if($access != NULL){
	    $conta = $this->db->query("SELECT COUNT(*) AS total FROM tbl_oauth WHERE oa_usuario = ".$this->session->userdata('us_codigo'))->result_array();
	    if($conta[0]['total'] > 0){
		$up = array(
		    'oa_facebook_access_token' => $access,
		    'oa_facebook_usuario' => $id,
		);
		$this->db->where('oa_usuario',$this->session->userdata('us_codigo'));
		$this->db->update('tbl_oauth',$up);
	    }else{
		$ins = array(
		    'oa_usuario' => $this->session->userdata('us_codigo'),
		    'oa_facebook_access_token' => $access,
		    'oa_facebook_usuario' => $id,
		);
		$this->db->insert('tbl_oauth',$ins);
	    }
	}
    }
    
    public function get_oauth($id){
	$this->db->where('oa_usuario',$id);
	return $this->db->get('tbl_oauth');
    }
    public function set_usuario($dados=NULL){
	if($dados != NULL){
	    
	    $ins = array(
		'us_nome'   => $dados['nome_usuario'],
		'us_email'  => $dados['email_usuario'],
		'us_senha'  => $dados['senha_usuario'],
		'us_genero' => $dados['gen_usuario'],
	    );
	    
	    if($dados['codigo'] > 0){
		$this->db->where(array('us_codigo'=>$dados['codigo']));
		$this->db->update('tbl_usuario',$ins);
	    }else{
		$ins['us_data_cadastro']= date("Y-m-d");
		$this->db->insert('tbl_usuario',$ins);
	    }
	}
    }
    
    public function get_count($id=NULL){
	if($id != NULL){
	    $this->db->where(array('co_codigo' => $id));
	    return $this->db->get('tbl_count');
	}
    }
    
    public function set_count($dados=NULL){
	if($dados != NULL){
	    if(isset($dados['codigo']) && $dados['codigo'] > 0){
		$upd = array(
		    'co_titulo'		=> $dados['nome_projeto'],
                    'co_descricao'      => $dados['ocasiao_projeto'],
		    'co_privado'	=> $dados['privado'],
		);
		$this->db->where(array('co_codigo' => $dados['codigo']));
		$this->db->update('tbl_count',$upd);
	    }else{
		$exp = date("Y-m-d", strtotime("+".$dados['dias_projeto']." days"));
		$ins = array(
		    'co_master'		=> $dados['user_id'],
		    'co_dias'		=> $dados['dias_projeto'],
		    'co_data_compra'	=> date("Y-m-d"),
		    'co_data_expira'	=> $exp,
		    'co_titulo'		=> $dados['nome_projeto'],
                    'co_descricao'      => $dados['ocasiao_projeto'],
		    'co_privado'	=> $dados['privado'],
		    'co_pago'		=> 's',
		    'co_nomeunico'	=> $dados['nomeunico'],
		);
		$this->db->insert('tbl_count',$ins);
	    }
	}
    }
    
    public function del_count($id){
	$up = array(
	    'co_excluido'=>'s',
	);
	$this->db->where(array('co_codigo'=>$id));
	$this->db->update('tbl_count',$up);
	
	$this->db->delete('tbl_tips',array('ti_count'=>$id));
    }
    
    public function get_counts($id){
	return $this->db->query("SELECT u.us_codigo, u.us_nome, c.* FROM tbl_count c INNER JOIN tbl_usuario u ON u.us_codigo = c.co_master WHERE u.us_codigo = $id AND c.co_excluido <> 's' AND c.co_finalizado <> 's' ORDER BY c.co_data_expira");
    }
    
    public function get_tcount($id){
	return $this->db->query("SELECT u.us_codigo, u.us_nome, c.co_codigo, c.co_pago, c.co_admin, c.co_titulo, c.co_descricao, c.co_dias, c.co_data_compra, c.co_data_expira, c.co_data_inicio, c.co_privado, c.co_tags, c.co_capa FROM tbl_count c INNER JOIN tbl_usuario u ON u.us_codigo = c.co_master WHERE c.co_codigo = $id AND c.co_excluido <> 's' AND c.co_finalizado <> 's' ORDER BY c.co_data_expira");
    }
    
    public function set_datacount($dados){
	$ins = array(
	    'co_data_inicio' => $dados['calendario'],
	);
	$this->db->where('co_codigo',$dados['cd_count']);
	$this->db->update('tbl_count',$ins);
    }
    
    public function up_count($dados){
	$ins = array('co_capa' => $dados['img']);
	$this->db->where('co_codigo',$dados['codigo']);
	$this->db->update('tbl_count',$ins);
    }
    
    public function get_loginuser($dados=NULL){
        if($dados != NULL){
            $this->db->where(array(
		'us_email'  => $dados['email'],
		'us_senha'  => $dados['snh'])
	    );
            return $this->db->get('tbl_usuario');
        }
    }
    
    public function get_allusuario(){
        return $this->db->get('tbl_usuario');
    }
    public function get_usuariobyemail($email){
        if($email != 'all'){
            $this->db->where(array('us_email'=>$email));
        }
        return $this->db->get('tbl_usuario');
    }
    
    public function get_usuariobyid($id){
        if($id != 'all'){
            $this->db->where(array('us_codigo'=>$id));
        }
        return $this->db->get('tbl_usuario');
    }
    
    public function get_all($tabela=NULL){
	if($tabela != NULL){
	    return $this->db->get($tabela);
	}
    }
    
    public function get_tips($id=NULL){
	if($id != NULL){
	    $this->db->where(array('ti_count'=>$id));
	    return $this->db->get('tbl_tips');
	}
    }
    
    public function troca_senha($dados=NULL){
	if($dados != NULL){
	    $this->db->where('us_email',$dados['email']);
	    $up = array(
		'us_senha' => $dados['snh']
	    );
	    $this->db->update('tbl_usuario',$up);
	}
    }
    
    public function get_totaltips($id=NULL){
	if($id != NULL){
	    $this->db->where(array('ti_count' => $id));
	    $this->db->from('tbl_tips');
	    return $this->db->count_all_results();
	}
    }
    
    public function set_tip($dados=NULL){
	if($dados != NULL){
	    if($dados['id_tip'] > 0){
		$ins = array(
		    'ti_count'	    => $dados['codigo'],
		    'ti_titulo'	    => $dados['titulo'],
		    'ti_subtitulo'  => $dados['sub'],
		    'ti_descricao'  => $dados['mensagem'],
		    'ti_imgcentral' => $dados['central'],
		);
		if(isset($dados['img']) && $dados['img'] != '' && $dados['img'] != 'sem'){
		    $ins['ti_imagem'] = $dados['img'];
		}
		$this->db->where(array('ti_codigo'=>$dados['id_tip']));
		$this->db->update('tbl_tips',$ins);
	    }else{
		$ins = array(
		    'ti_count' => $dados['codigo'],
		    'ti_data_mostra' => $dados['data'],
		    'ti_contagem' => $dados['contagem'],
		);
		$this->db->insert('tbl_tips',$ins);
	    }
	}
    }
    
    public function set_tag($dados){
	$ins = array(
	    'co_tags' => $dados['tags'],
	);
	$this->db->where('co_codigo',$dados['codigo']);
	$this->db->update('tbl_count',$ins);
    }
    
    public function get_invite($email,$count){
	return $this->db->get_where('tbl_convidados',array('con_count'=>$count,'con_email'=>$email));
    }
    
    public function grava_invite($email,$count){
	$this->db->query("INSERT IGNORE INTO tbl_convidados (con_count, con_email) VALUES('".$count."','".$email."')");
    }
    public function set_convite($id=NULL){
	if($id != NULL){
	    $set = array(
		'con_aceitou' => 's',
		'con_data_aceite' => date("Y-m-d"),
	    );
	    $this->db->set($set);
	    $this->db->where('con_codigo',$id);
	    $this->db->update('tbl_convidados');
	}
    }
    
    public function get_seguepush(){
	return $this->db->query("SELECT u.us_email email, (SELECT COUNT(*) FROM tbl_convidados c WHERE c.con_email = u.us_email AND c.con_aceitou = 's') AS total, u.us_tokenpush push, (SELECT c2.con_count FROM tbl_convidados c2 WHERE c2.con_email = u.us_email AND c2.con_aceitou = 's' LIMIT 1) AS count FROM tbl_usuario u WHERE u.us_tokenpush <> '' ORDER BY us_ultimologin DESC");
    }
    
    public function get_ultimotoken($tok){
	return $this->db->query("SELECT MAX(us_ultimologin) as ultimo FROM tbl_usuario WHERE us_tokenpush = '$tok'");
    }
    
    public function get_tokenpush($tok){
	$this->db->where('us_tokenpush',$tok);
	return $this->db->count_all_results('tbl_usuario');
    }
    
    public function get_idcount($email){
	$w = array(
	    'con_email' => $email,
	    'con_aceitou' => 's',
	);
	$this->db->select('con_count');
	return $this->db->get_where('tbl_convidados',$w);
    }
    
    public function get_countpush($id){
	return $this->db->get_where('tbl_count',array('co_codigo'=>$id));
    }
    
    public function get_nomeunico($nome){
	return $this->db->query("SELECT * FROM tbl_count WHERE co_nomeunico LIKE '".$nome."%'");
    }
    public function get_disponivel($nome){
	return $this->db->get_where('tbl_count',array('co_nomeunico'=>$nome));
    }
    
    public function set_expira($codigo){
	$ins = array(
	    'co_finalizado' => 's'
	);
	$this->db->where('co_codigo',$codigo);
	$this->db->update('tbl_count',$ins);
    }
}