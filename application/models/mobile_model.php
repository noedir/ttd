<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mobile_model extends CI_Model {
    public function set_usuario($dados=NULL){
	if($dados != NULL){
	    $ins = array(
                'us_token'  => $dados['token_face'],
		'us_nome'   => $dados['nome_usuario'],
		'us_email'  => $dados['email_usuario'],
	    );
	    
	    if(isset($dados['codigo']) && $dados['codigo'] > 0){
		$this->db->where('us_codigo',$dados['codigo']);
		$this->db->update('tbl_usuario',$ins);
	    }else{
		$ins['us_senha']  = $dados['senhaprov'];
		$ins['us_data_cadastro']= date("Y-m-d H:i:s");
		$this->db->insert('tbl_usuario',$ins);
	    }
	}
    }
    
    public function chk_usuario($email){
	return $this->db->query("SELECT * FROM tbl_usuario WHERE us_email = '".$email."'");
    }
    
    public function del_usuario($email){
	$this->db->where("us_email",$email);
	$this->db->delete('tbl_usuario');
    }
    
    public function get_count($id=NULL){
	if($id != NULL){
	    $this->db->where(array('co_codigo'=>$id));
	    return $this->db->get('tbl_count');
	}
    }
    
    public function set_count($dados=NULL){
	if($dados != NULL){
	    $exp = date("Y-m-d", strtotime("+".$dados['dias_projeto']." days"));
	    if($dados['privado'] == ''){
		$priv = 'n';
	    }else{
		$priv = 's';
	    }
	    
	    if(isset($dados['codigo']) && $dados['codigo'] > 0){
		$upd = array(
		    'co_titulo'		=> $dados['nome_projeto'],
		    'co_descricao'	=> $dados['ocasiao_projeto'],
		    'co_privado'	=> $priv,
		    'co_dias'		=> $dados['dias_projeto'],
		    'co_data_expira'	=> $exp,
		    'co_tags'		=> $dados['tags'],
		);
		$this->db->where(array('co_codigo' => $dados['codigo']));
		$this->db->update('tbl_count',$upd);
	    }else{
		$ins = array(
		    'co_master'		=> $dados['user_id'],
		    'co_dias'		=> $dados['dias_projeto'],
		    'co_data_compra'	=> date("Y-m-d"),
		    'co_data_expira'	=> $exp,
		    'co_titulo'		=> $dados['nome_projeto'],
		    'co_descricao'	=> $dados['ocasiao_projeto'],
		    'co_privado'	=> $priv,
		    'co_tags'		=> $dados['tags'],
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
	return $this->db->query("SELECT u.us_codigo, u.us_nome, c.co_codigo, c.co_pago, c.co_admin, c.co_titulo, c.co_descricao, c.co_dias, c.co_data_compra, c.co_data_expira, c.co_privado FROM tbl_count c INNER JOIN tbl_usuario u ON u.us_codigo = c.co_master WHERE u.us_codigo = $id AND c.co_excluido != 's' AND c.co_finalizado != 's' ORDER BY c.co_data_expira");
    }
    
    public function get_tcount($id){
	return $this->db->query("SELECT u.us_codigo, u.us_nome, c.co_codigo, c.co_pago, c.co_admin, c.co_titulo, c.co_descricao, c.co_dias, c.co_data_compra, c.co_data_expira, c.co_privado FROM tbl_count c INNER JOIN tbl_usuario u ON u.us_codigo = c.co_master WHERE c.co_codigo = $id AND c.co_excluido != 's' AND c.co_finalizado != 's' ORDER BY c.co_data_expira");
    }
    
    public function set_cleartokenpush($dados){
	$this->db->query("UPDATE tbl_usuario SET us_tokenpush = '' WHERE us_email != '".$dados['email']."' AND us_tokenpush = '".$dados['tokenpush']."'");
    }
    
    public function set_tokenpush($dados){
	$ins = array(
	    'us_tokenpush' => $dados['tokenpush'],
	    'us_ultimologin' => date("Y-m-d H:i:s"),
	);
	$this->db->where('us_email',$dados['email']);
	$this->db->update('tbl_usuario',$ins);
    }
    
    public function get_loginuser($dados=NULL){
        if($dados != NULL){
	    return $this->db->query("SELECT * FROM tbl_usuario WHERE us_email = '".$dados['email']."'");
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
    
    public function get_countpublica($busca=''){
	if($busca['busca'] != ''){
	    $where = " AND (c.co_tags LIKE '%".$busca['busca']."%' OR c.co_nomeunico = '".$busca['busca']."') OR c.co_titulo LIKE '%".$busca['busca']."%'";
	}else{
	    $where = '';
	}
	
	if($busca['busca_id'] != ''){
	    $arr = " AND c.co_codigo IN (".$busca['busca_id'].") ";
	}else{
	    $arr = '';
	}
	return $this->db->query("SELECT c.co_codigo id, c.co_titulo nome, c.co_capa image, c.co_premium premium, c.co_tags tags, c.co_dias dias_total, c.co_data_inicio inicio, (SELECT COUNT(*) FROM tbl_convidados i WHERE i.con_count = c.co_codigo AND i.con_aceitou = 's') AS inscritos FROM tbl_count c WHERE c.co_privado = 'n' AND c.co_data_expira > '".date("Y-m-d")."' AND c.co_excluido = 'n' AND c.co_finalizado = 'n' AND c.co_pago = 's' AND c.co_data_inicio <= '".date("Y-m-d")."' $where $arr ORDER BY c.co_premium DESC");
    }
    
    public function get_allcount($priv=NULL,$id=NULL){
	if($id != NULL){
	    if($priv == NULL){
		$this->db->where(array('co_excluido'=>'n','co_finalizado'=>'n','co_pago'=>'s','co_master'=>$id['codigo']),'co_data_inicio >=',date("Y-m-d"));
	    }else{
		$this->db->where(array('co_excluido'=>'n','co_finalizado'=>'n','co_pago'=>'s','co_privado'=>$priv,'co_master'=>$id['codigo']),'co_data_inicio >=',date("Y-m-d"));
	    }
	    
	    $this->db->order_by('co_premium DESC');
	    return $this->db->get('tbl_count');
	}
    }
    
    public function get_tips($id=NULL){
	if($id != NULL){
	    return $this->db->query("SELECT c.co_titulo titulo, c.co_dias total_dias, t.ti_codigo id, t.ti_count count, t.ti_titulo nome, t.ti_subtitulo subtitulo, t.ti_descricao descricao, t.ti_imagem imagem, t.ti_data_mostra data, t.ti_contagem tip FROM tbl_tips t LEFT JOIN tbl_count c ON c.co_codigo = t.ti_count WHERE t.ti_codigo = ".$id['codigo']." AND t.ti_data_mostra <= '".date("Y-m-d")."'");
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
		    'ti_count'  => $dados['codigo'],
		    'ti_titulo' => $dados['titulo'],
		    'ti_subtitulo'	=> $dados['sub'],
		    'ti_descricao'	=> $dados['mensagem'],
		    'ti_imagem'	=> $dados['img'],
		);
		$this->db->where(array('ti_codigo'=>$dados['id_tip']));
		$this->db->update('tbl_tips',$ins);
	    }else{
		$ins = array(
		    'ti_count' => $dados['codigo'],
		);
		$this->db->insert('tbl_tips',$ins);
	    }
	}
    }
    
    public function get_tipcount($id=NULL){
	if($id != NULL){
	    return $this->db->query("SELECT c.co_titulo, c.co_dias, t.*,
		(SELECT COUNT(*) FROM tbl_convidados s WHERE s.con_email = '".$id['email']."' AND s.con_count = ".$id['codigo'].") as seguindo
		FROM tbl_tips t
		LEFT JOIN tbl_count c ON c.co_codigo = t.ti_count
		WHERE t.ti_count = ".$id['codigo']." AND t.ti_data_mostra <= '".date("Y-m-d")."' AND t.ti_titulo <> '' ORDER BY t.ti_data_mostra DESC");
	}
    }
    
    public function get_seguindo($email=NULL){
	if($email != NULL){
	    return $this->db->query("SELECT c.con_count, p.co_titulo, p.co_privado FROM tbl_convidados c INNER JOIN tbl_count p ON p.co_codigo = c.con_count WHERE c.con_aceitou = 's' AND c.con_email = '".$email['email']."'");
	}
    }
    
    public function get_meus($email=NULL){
	if($email != NULL){
	    return $this->db->query("SELECT c.co_codigo id, u.us_email email, c.co_titulo titulo, c.co_privado privado FROM tbl_usuario u LEFT JOIN tbl_count c ON c.co_master = u.us_codigo WHERE us_email = '".$email['email']."'");
	}
    }
    
    public function get_convites($aceite,$email=NULL){
	if($email != NULL){
	    
	    return $this->db->query("SELECT c.con_count id, p.co_titulo titulo, p.co_privado privado FROM tbl_convidados c INNER JOIN tbl_count p ON p.co_codigo = c.con_count WHERE c.con_aceitou = '$aceite' AND c.con_email = '".$email['email']."'");
	}
    }
    
    public function set_seguircount($dados){
	if($dados['sair'] == 'n'){
	    $query = "INSERT INTO tbl_convidados (con_count,con_email,con_aceitou,con_data_aceite) VALUES ('".$dados['id_count']."','".$dados['email']."','".$dados['seguir']."','".date("Y-m-d")."') ON DUPLICATE KEY UPDATE con_aceitou = '".$dados['seguir']."', con_data_aceite = '".date("Y-m-d")."'";
	    $this->db->query($query);
	}else{
	    $this->db->delete('tbl_convidados',array('con_count'=>$dados['id_count'],'con_email'=>$dados['email']));
	}
	
	
    }
}