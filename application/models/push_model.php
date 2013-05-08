<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Push_model extends CI_Model {
    public function set_push($dados){
	$ins = array(
	    'push' => $dados['push'],
	    'mensagem' => $dados['mensagem'],
	    'counts' => $dados['counts']
	);
	$this->db->insert('enviapush',$ins);
    }
}