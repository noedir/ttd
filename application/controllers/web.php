<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('web_model','wdb');
        $this->load->helper('form');
        $this->load->helper('array');
	$this->load->helper('html');
	$this->load->library('form_validation');
	if($this->input->cookie('idioma') == ''){
	    $this->lang->load('pt','idioma');
	    $this->input->set_cookie(array(
		'name'=>'idioma',
		'value'=>'pt',
		'expire'=>'86500',
		
	    ));
	}else{
	    switch($this->input->cookie('idioma')){
		case 'pt':
		    $this->lang->load('pt','idioma');
		    break;
		case 'en':
		    $this->lang->load('en','idioma');
		    break;
		default:
		    $this->lang->load('pt','idioma');
		    break;
	    }
	}
    }
    private function crypt($u=NULL,$s=NULL){
	if($u != NULL && $s != NULL){
	    $r = sha1(md5($u).':'.md5($s));
	    return $r;
	}
    }
    private function nome_curto($s){
	$n = explode(' ',$s);
	return $n[0];
    }
    
    public function nomeunico(){
	header('content-type: application/json');
	$input = elements(array('nome','proj','dias'),$this->input->post());
	
	if($input['nome'] !== '' && $input['proj'] !== '' && $input['dias'] !== ''){
	    $nome = $this->nome_curto($input['nome']);
	    $proj = str_replace(' ','',$input['proj']);
	    $dias = date("Y", strtotime("+".$input['dias']." days"));
	    $dados['result'] = strtolower($proj).$dias;
	    	    
	    $query = $this->wdb->get_nomeunico($dados['result'])->result_array();
	    
	    if(count($query) > 0){
		$dados['result'] = $dados['result']."_".count($query);
	    }
	    
	    echo json_encode($dados);
	}
    }
    
    public function disponivel(){
	#header('content-type: application/json');
	$nome = $this->input->post('nome');
	$query = $this->wdb->get_disponivel($nome)->result_array();
	
	if(count($query) > 0){
	    echo "false";
	}else{
	    echo "true";
	}
    }
    
    public function idioma(){
	$lng = $this->input->post('lng');
	$this->lang->load($lng,'idioma');
	$this->input->set_cookie(array(
	    'name'=>'idioma',
	    'value'=>$lng,
	    'expire'=>'86500',

	));
    }
    
    public function index(){
        $dados = array(
            'title' => 'Projeto Count',
            'tela' => 'home',
        );
        if($this->input->get('id')){
            $dados['usuario'] = $this->wdb->get_usuariobyid($this->input->get('id'))->result();
        }else if($this->input->get('email')){
            $dados['usuario'] = $this->wdb->get_usuariobyemail($this->input->get('email'))->result();
        }else{
            $dados['usuario'] = null;
        }
	
        $this->load->view('web_view',$dados);
    }
    
    public function quem_somos(){
	$dados = array(
	    'title' => 'Projeto Count &raquo; Quem Somos',
	    'tela' => 'quem_somos',
	);
	$this->load->view('web_view',$dados);
    }
    
    public function contato(){
	$dados = array(
	    'title' => 'Projeto Count &raquo; Contato',
	    'tela' => 'contato',
	);
	$this->load->view('web_view',$dados);
    }
    
    public function login(){
	$dados = array(
	    'title' => 'Projeto Count &raquo; Login',
	    'tela' => 'login',
	    'erro' => '',
	);
	
	$this->form_validation->set_rules('email','EMAIL','trim|required|valid_email');
	$this->form_validation->set_rules('senha','SENHA','trim|required');
	
	if($this->form_validation->run()){	
	    $input = elements(array('email','senha'),$this->input->post());
	    $input['snh'] = $this->crypt($input['email'],$input['senha']);
	    $query = $this->wdb->get_loginuser($input)->result_array();

	    if(count($query) > 0){
		foreach($query[0] as $k => $v){
		    $this->session->set_userdata(array($k=>$v));
		}
		$nc = $this->nome_curto($query[0]['us_nome']);
		$this->session->set_userdata('nomecurto', $nc);
		redirect('web/counts');
	    }else{
		$dados['erro'] = '<small class="red">Email ou senha incorretos</small>';
	    }
	}
	
	$this->load->view('web_view',$dados);
    }
    
    public function atualiza_dados(){
	$dados = array(
	    'title' => 'Projeto Count &raquo; Atualizar Dados',
	    'tela' => 'atualiza_dados',
	);
	
	$this->form_validation->set_rules('nome_usuario','NOME','trim|required');
	$this->form_validation->set_rules('email_usuario','EMAIL','trim|required|valid_email');
	$this->form_validation->set_rules('senha_usuario','SENHA','trim');
	
	if($this->form_validation->run()){
	    $input = elements(array('nome_usuario','email_usuario','gen_usuario','senha_usuario','senhaatual'),$this->input->post());
	    if($input['senha_usuario'] == ''){
		$input['senha_usuario'] = $input['senhaatual'];
	    }else{
		$input['senha_usuario'] = $this->crypt($input['email_usuario'],$input['senha_usuario']);
	    }
	    $input['codigo'] = $this->session->userdata('us_codigo');
	    $this->wdb->set_usuario($input);
	    $query = $this->wdb->get_usuariobyid($this->session->userdata('us_codigo'))->result();
	    
	    if(count($query) > 0){
		foreach($query[0] as $k => $v){
		    $this->session->set_userdata(array($k=>$v));
		}
		$this->session->set_userdata('nomecurto',$this->nome_curto($query[0]->us_nome));
	    }
	    redirect('web/counts');
	}
	
	$this->load->view('web_view', $dados);
    }
    
    public function sair(){
	$this->session->sess_destroy();
	redirect('web');
    }
    
    public function criar_projeto(){
	$dados = array(
            'title' => 'Projeto Count &raquo; Criar Novo Projeto Count',
            'tela' => 'cadprojeto',
        );
        
        $this->form_validation->set_rules('nome_usuario','Nome do Usuário','trim|required');
        $this->form_validation->set_rules('email_usuario','Email do Usuário','trim|required|valid_email|is_unique[tbl_usuario.us_email]');
        $this->form_validation->set_rules('senha_usuario','Senha','trim|required');
        $this->form_validation->set_rules('confirma_senha','Confirmação','trim|required|matches[senha_usuario]');
        $this->form_validation->set_rules('nome_projeto','Projeto','trim|required');
        $this->form_validation->set_rules('ocasiao_projeto','Ocasião','trim|required');
        $this->form_validation->set_rules('dias_projeto','Dias do Projeto','trim|required|numeric');
        $this->form_validation->set_rules('nomeunico','Identificador','trim|required|is_unique[tbl_count.co_nomeunico]');
	
	if($this->form_validation->run()){
	    $in_user = elements(array('nome_usuario','email_usuario','gen_usuario','senha_usuario','confirma_senha'),$this->input->post());
	    
	    $in_user['senha_usuario'] = $this->crypt($in_user['email_usuario'],$in_user['senha_usuario']);
	    $in_user['codigo'] = 0;
	    
	    $this->wdb->set_usuario($in_user);
            $idu = $this->db->insert_id();
	    
	    $in_count = elements(array('privado','nome_projeto','ocasiao_projeto','dias_projeto','valor_projeto','nomeunico'),$this->input->post());
	    
	    $in_count['user_id'] = $idu;
	    
	    $this->wdb->set_count($in_count);
	    
	    $query = $this->wdb->get_usuariobyemail($in_user['email_usuario'])->result();
	    
	    foreach($query[0] as $k => $v){
		$this->session->set_userdata(array($k=>$v));
	    }
	    $nc = $this->nome_curto($query[0]->us_nome);
	    $this->session->set_userdata('nomecurto',$nc);
	    $dados['nome'] = $query[0]->us_nome;
	    
	    redirect('web/counts');
	}
	
	$this->load->view('web_view',$dados);
    }
    
    public function criar_novo_projeto(){
	$dados = array(
            'title' => 'Projeto Count &raquo; Criar Novo Projeto Count',
            'tela' => 'novo_projeto',
        );
	
	$this->form_validation->set_rules('nome_projeto','Projeto','trim|required');
        $this->form_validation->set_rules('ocasiao_projeto','Ocasião','trim|required');
        $this->form_validation->set_rules('dias_projeto','Dias do Projeto','trim|required|numeric');
        $this->form_validation->set_rules('nomeunico','Identificador','trim|required|is_unique[tbl_count.co_nomeunico]');
	
	if($this->form_validation->run()){
	    
	    $in_count = elements(array('privado','nome_projeto','ocasiao_projeto','dias_projeto','valor_projeto','nomeunico'),$this->input->post());
	    
	    $in_count['user_id'] = $this->session->userdata('us_codigo');
	    
	    if($in_count['privado'] == ''){
		$in_count['privado'] = 'n';
	    }
	    
	    $this->wdb->set_count($in_count);
	    
	    redirect('web/counts');
	}
	
	$this->load->view('web_view',$dados);
    }
    
    public function edit_count(){
	$id = $this->uri->segment(3);
	$dados = array(
	    'title'	=> 'Projeto Count &raquo; Editar Count',
	    'tela'	=> 'editar_count',
	    'edcount'	=> $this->wdb->get_count($id)->result_array(),
	);
	
	$this->form_validation->set_rules('nome_projeto','TITULO','trim|required');
	$this->form_validation->set_rules('ocasiao_projeto','OCASIAO','trim|required');
	$this->form_validation->set_rules('dias_projeto','DIAS','trim|required');
	
	if($this->form_validation->run()){
	    $input = elements(array('codigo','privado','nome_projeto','ocasiao_projeto','dias_projeto'),$this->input->post());
	    $this->wdb->set_count($input);
	    redirect('web/counts');
	}
	
	$this->load->view('web_view',$dados);
    }
    
    public function excluir_count(){
	$id = $this->uri->segment(3);
	$this->wdb->del_count($id);
	redirect('web/counts');
    }
    
    public function counts(){
	$dados = array(
	    'title' => 'Projeto Count &raquo; Minhas Counts',
	    'tela' => 'counts',
	    'counts' => $this->wdb->get_counts($this->session->userdata('us_codigo'))->result(),
	);
	
	$this->load->view('web_view',$dados);
    }
    
    public function tips(){
	if($this->uri->segment(3) != ''){
	    $id = $this->uri->segment(3);
	    $this->session->set_userdata('tips',$id);
	}else{
	    $id = $this->session->userdata('tips');
	}
	
	$dados = array(
	    'title'	=> 'Projeto Count &raquo; Tips',
	    'tela'	=> 'tips',
	    'count'	=> $this->wdb->get_tcount($id)->result(),
	    'tips'	=> $this->wdb->get_tips($id)->result(),
	    'totaltips'	=> $this->wdb->get_totaltips($id),
	    'oauth' => $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result_array(),
	);
	
	$dados['facebook'] = base_url().'facebook';
	
	$this->load->view('web_view',$dados);
    }
    
    public function upphoto(){
	$dados = array(
	    'title' => 'Projeto Count &raquo; Fotos',
	    'tela' => 'sobefoto',
	    'instagram' => $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result(),
	);
	$this->load->view('up_view',$dados);
    }
    
    public function img_instagram(){
	$img = $this->input->post('imgi');
	$local = $this->input->post('local');
	$exp = array_reverse(explode("/",$img));
	$loc = date("YmdHis").'_'.$exp[0];
	
	$in=    fopen($img, "rb");
	$out=   fopen('./tips/'.$loc, "wb");
	while ($chunk = fread($in,(8192 * 10))){
	    fwrite($out, $chunk, (8192 * 10));
	}
	fclose($in);
	fclose($out);
	
	$tam = getimagesize('./tips/'.$loc);
	
	if($local === 'facebook'){
	    if($tam[0] < 640 || $tam[1] < 570){
		unlink($loc);
		$data['erro'] = 'sim';
		echo json_encode($data);
		die();
	    }
	}
	
	$lar = $tam[0] / 2;
	$alt = $tam[1] / 2;

	$config = array(
	    'image_library' => 'gd2',
	    'source_image' => './tips/'.$loc,
	    'new_image' => './tips/'.'tmp_'.$loc,
	    'width' => $lar,
	    'height' => $alt,
	    'maintain_ratio' => FALSE,
	);

	$this->load->library('image_lib',$config);
	$this->image_lib->resize();

	$data['width'] = $tam[0];
	$data['height'] = $tam[1];
	$data['url'] = $loc;
	$data['erro'] = '';
	
	echo json_encode($data);
    }
    
    public function img_upload(){
	$path = $this->uri->segment(3);
	if($path == ''){
	    $path = 'tips';
	}
	$this->load->helper('file');
	$config['upload_path']   = FCPATH.$path.'/';
	$config['allowed_types'] = 'jpg|png';
	$config['file_name'] = md5(date("YmdHis"));
	$config['min_width'] = 640;
	$config['min_height'] = 570;
	$config['overwrite'] = TRUE;
	$this->load->library('upload', $config);
	
	if($this->upload->do_upload('imagem')){
	    $file = $this->upload->data();
	    
	    $data['arquivo'] = $file['file_name'];
	    $temp = 'tmp_'.$file['file_name'];
	    
	    $lar = $file['image_width'] / 2;
	    $alt = $file['image_height'] / 2;
	    
	    if($path == 'tips'){
		$config = array(
		    'image_library' => 'gd2',
		    'source_image' => './tips/'.$file['file_name'],
		    'new_image' => './tips/'.'tmp_'.$file['file_name'],
		    'width' => $lar,
		    'height' => $alt,
		    'maintain_ratio' => FALSE,
		);

		$this->load->library('image_lib',$config);
		$this->image_lib->resize();
		
		$param = 'id="photo"';
	    }else{
		 $param = 'width="335" height="120"';
	    }
	    $data['img'] = '<img data-wi="'.$lar.'" data-he="'.$alt.'" '.$param.' src="'.base_url().$path.'/'.$temp.'">';
	    
	    if($path == 'capa'){
		$input = array(
		    'img' => $data['arquivo'],
		    'codigo' => $this->input->get_post('cod_count'),
		);
		$this->wdb->up_count($input);
	    }
	    $data['status'] = '200';
	    $data['msg'] = 'OK';
	}else{
	    $data['status'] = '201';
	    $data['msg'] = 'A imagem precisa ter no mínimo 640 x 570 pixels.';
	}
	
	echo json_encode($data);
    }
    
    public function grava_tip(){
	$input = elements(array('id_tip','codigo','img','titulo','sub','mensagem','central','largura','altura','posicao'), $this->input->post());
	
	$crd = explode('/',$input['posicao']);
	$posih = ($input['altura']);
	$posiw = ($input['largura']);
	
	if(file_exists('./tips/tmp_'.$input['img'])){
	
	    if($input['central'] === 's'){
		$config = array(
		    'image_library' => 'gd2',
		    'source_image' => './tips/tmp_'.$input['img'],
		    'width' => ($posiw * 1.325),
		    'height' => ($posih * 1.20),
		    'maintain_ratio' => FALSE,
		);
		$this->load->library('image_lib',$config);
		$this->image_lib->resize();
		$this->image_lib->clear();

		$img = getimagesize('./tips/tmp_'.$input['img']);

		$tmpw = ($img[0] * 2);
		$tmph = ($img[1] * 2);

		$config = array(
		    'image_library' => 'gd2',
		    'source_image' => './tips/'.$input['img'],
		    'width' => $tmpw,
		    'height' => $tmph,
		    'maintain_ratio' => FALSE,
		);
		$this->image_lib->initialize($config);
		$this->image_lib->resize();
		$this->image_lib->clear();

		$posl = abs($crd[0]) * 3.25;
		$post = abs($crd[1]) * 2.70; 

		$config = array(
		    'image_library' => 'gd2',
		    'source_image' => './tips/'.$input['img'],
		    'x_axis' => $posl,
		    'y_axis' => $post,
		    'width'  => 640, //$input['largura'],
		    'height' => 570, //$input['altura'],
		    'maintain_ratio' => FALSE,
		);

		//$this->load->library('image_lib',$config);
		$this->image_lib->initialize($config);
		$this->image_lib->crop();
		$this->image_lib->clear();

	    }else{

		$lar = getimagesize('./tips/'.$input['img']);
		if($lar[0] >= 640){

		    $post = (abs($crd[1]) * 2) / 1.32;
		    $posl = (abs($crd[0]) * 2) / 1.20;

		    $config = array(
			'image_library' => 'gd2',
			'source_image' => './tips/'.$input['img'],
			'x_axis' => $posl,
			'y_axis' => $post,
			'width'  => 640, //$input['largura'],
			'height' => 570, //$input['altura'],
			'maintain_ratio' => FALSE,
		    );	    
		    $this->load->library('image_lib',$config);
		    $this->image_lib->crop();
		    $this->image_lib->clear();
		}else{
		    $config = array(
			'image_library' => 'gd2',
			'source_image' => './tips/'.$input['img'],
			'width' => 640, //$input['largura'];
			'height' => 570, //$input['altura'];
			'maintain_ratio' => FALSE,
		    );
		    $this->load->library('image_lib',$config);
		    $this->image_lib->resize();
		    $this->image_lib->clear();

		    $posit = (abs($crd[1]) * 2.70) / 1.30;
		    $posil = (abs($crd[0]) * 3.25) / 1.25;

		    $config = array(
			'image_library' => 'gd2',
			'source_image' => './tips/'.$input['img'],
			'x_axis' => $posil,
			'y_axis' => $posit,
			'width'  => 640, //$input['largura'],
			'height' => 570, //$input['altura'],
			'maintain_ratio' => FALSE,
		    );	    
		    $this->image_lib->initialize($config);
		    $this->image_lib->crop();
		    $this->image_lib->clear();
		}
	    }

	    $config['image_library'] = 'gd2';
	    $config['source_image'] = './tips/'.$input['img'];
	    $config['new_image'] = './tips/thumb_'.$input['img'];
	    $config['maintain_ratio'] = FALSE;
	    $config['width'] = 200;
	    $config['height'] = 200;
	    $config['quality'] = '75';
	    $this->image_lib->initialize($config);
	    $this->image_lib->resize();

	    $resp['altura'] = $input['altura'];
	    $resp['largura'] = $input['largura'];

	    $this->wdb->set_tip($input);
	    $resp['msg'] = 'Gravada com sucesso';
	    $resp['erro'] = 'ok';

	    if(file_exists('./tips/tmp_'.$input['img'])){
		unlink('./tips/tmp_'.$input['img']);
	    }

	    if($this->image_lib->display_errors()){
		$resp['msg'] = $this->image_lib->display_errors();
	    }
	}else{
	    $resp['msg'] = '';
	    $resp['erro'] = 'ok';
	}
	
	echo json_encode($resp);
    }
    
    private function inverte_data($data){
	$nova_data = implode("-",array_reverse(explode("/",$data)));
	return $nova_data;
    }
    
    public function gravadata(){
	$input = elements(array('cd_count','dias_count','calendario'),$this->input->post());
	$c = 0;
	
	$input['calendario'] = $this->inverte_data($input['calendario']);
	
	$this->wdb->set_datacount($input);
	
	while($c < $input['dias_count']){
	    $data = date("Y-m-d", strtotime($input['calendario']." +".$c." days"));
	    $c++;
	    
	    $tp = array(
		'id_tip' => '0',
		'codigo' => $input['cd_count'],
		'data' => $data,
		'contagem' => $c,
	    );
	    $this->wdb->set_tip($tp);
	}
	redirect('web/tips');
    }
    
    public function gravatag(){
	$input = elements(array('tags','codigo'),$this->input->post());
	$this->wdb->set_tag($input);
	print_r($input);
    }
    
    public function computador(){
	$dados = array(
	    'title' => 'Projeto Count &raquo; Meu Computador',
	    'tela' => 'micro',
	);
	
	$this->load->view('up_view',$dados);
    }
    
    public function invites(){
	$access = $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result_array();
	$dados = array(
	    'title' => 'Projeto Count &raquo; Invites',
	    'tela' => 'convites',
	    'counts' => $this->wdb->get_counts($this->session->userdata('us_codigo'))->result(),
	    'facebook' => base_url().'facebook',
	);
	
	if($access[0]['oa_facebook_access_token'] != ''){
	    $dados['facebook'] = base_url().'facebook';
	}
	$this->load->view('web_view',$dados);
    }
    
    public function grava_convite(){
	$this->load->helper('email');
	$this->load->library('email');
	
	$input = elements(array('amigos','count'),$this->input->post());
	$arr = explode(',',$input['amigos']);
	$c = 0;
	$texto = '';
	
	$html = '<p><strong>Olá, amigo(a).</strong></p>';
	$html .= '<p>Eu criei essa contagem regressiva de <strong>###count###</strong> e estou te convidando a seguir e ficar por dentro de todas as novidades.</p>';
	$html .= '<p>Você só precisa aceitar esse convite clicando <a href="'.base_url().'web/aceitarconvite/###id###"><strong>aqui</strong></a> ou copiando e colando esse link no seu navegador: '.base_url().'web/aceitarconvite/###id###</p>';
	$html .= '<p>Caso não possua o aplicativo, <a href="https://itunes.apple.com/br/app/nivea-sun/id577311928?mt=8">clique aqui</a>.</p>';
	$html .= '<p>Se você já possui o aplicativo instalado, <a href="tiltheday://###til###">clique aqui</a> para abrir o Til The Day e veja seu convite.</p>';
	$html .= '<p>Nos vemos lá.</p>';
	$html .= '<h5>###nome###</h5>';
	$html .= '<br><br>';
	$html .= '<p><em>Se você não conhece ###nome###, favor ignorar essa mensagem<em></p>';
	
	$co = $this->wdb->get_count($input['count'])->result_array();
	
	foreach($arr as $valid){
	    if(valid_email($valid)){
		$query =  $this->wdb->get_invite($valid,$input['count'])->result_array();
		//if(count($query) == 0){
		    $this->wdb->grava_invite($valid,$input['count']);
		    $idinv = $this->db->insert_id() + (1024 * 4);
		    
		    $texto = str_replace("###count###",$co[0]['co_titulo'],$html);
		    $texto = str_replace("###til###", $co[0]['co_codigo'],$texto);
		    $texto = str_replace("###id###",$idinv,$texto);
		    $texto = str_replace("###nome###",$this->session->userdata('nomecurto'),$texto);
		    
		    $config['protocol']  = 'smtp';
		    $config['charset'] = 'utf8';
		    $config['wordwrap'] = TRUE;
		    $config['smtp_host'] = 'mail.dcanm.mobi';
		    $config['smtp_user'] = 'tiltheday@dcanm.mobi';
		    $config['smtp_pass'] = 'dudinha';
		    $config['smtp_port'] = 587;
		    $config['smtp_timeout'] = 20;
		    $config['mailtype'] = 'html';
		    
		    $this->email->initialize($config);
		    $this->email->from('tiltheday@dcanm.mobi', 'TilTheDay');
		    $this->email->to($valid);
		    $this->email->subject('Você está a receber um invite de '.$this->session->userdata('nomecurto'));
		    $this->email->message($texto);
		    $em = $this->email->send();
		    
		    $c++;
		//}
	    }
	}
	if(isset($em)){
	    $this->session->set_flashdata('total','Email enviado com sucesso para '.$c.' invites');
	}else{
	    $this->session->set_flashdata('total','Os emails entraram em uma fila para serem enviados.');
	}
	redirect('web/invites/'.$input['count']);
    }
    
    public function aceitarconvite(){
	$id = $this->uri->segment(3) - (1024 * 4);
	
	$this->wdb->set_convite($id);
	redirect('https://itunes.apple.com/gb/app/nivea-sun-guide/id385271083?mt=8');
    }
    
    private function push($token,$mensagem,$idcount=NULL){
	// Put your device token here (without spaces):
	//$deviceToken = '064e0cf07193b1baa888a5f589f3801f3348a847166dc3360d29dd3e4eb322f5';

	//token ipod preto
	//$deviceToken = '94b08e42aee29d453e4768176fef7fc98f940e97cdcc208f0ce938ee0d3b36ec';

	//token ipod branco
	//$deviceToken = '064e0cf07193b1baa888a5f589f3801f3348a847166dc3360d29dd3e4eb322f5';

	//token iphone Daniel
	//$deviceToken = '6a92e1672bcae29c1c34f953133e23487adb798c688057dd2dd82874ddcf90b4';

	// Put your private key's passphrase here:
	$passphrase = '200402';

	// Put your alert message here:
	//$message = 'ABRE LOGO ESSE NEGÓCIO.';
	
	$deviceToken = $token;
	$message = $mensagem;

	////////////////////////////////////////////////////////////////////////////////

	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', 'apns-dev.pem');
	stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

	// Open a connection to the APNS server
	$fp = stream_socket_client(
		'ssl://gateway.sandbox.push.apple.com:2195', $err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

	/*if (!$fp)
		exit("Failed to connect: $err $errstr" . PHP_EOL);
	 * 
	 */

	#echo 'Connected to APNS' . PHP_EOL;

	// Create the payload body
	$body['aps'] = array(
		'alert' => $message,
		'sound' => 'default',
		);
	if($idcount != NULL){
	    $body['aps']['counts'] = $idcount;
	}

	// Encode the payload as JSON
	$payload = json_encode($body);

	// Build the binary notification
	$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

	// Send it to the server
	$result = fwrite($fp, $msg, strlen($msg));

	/*if (!$result)
		echo 'Message not delivered' . PHP_EOL;
	else
		echo 'Message successfully delivered' . PHP_EOL;
	 * 
	 */

	// Close the connection to the server
	fclose($fp);
    }
    
    /**
     * @sendpuxi = Send Push - dispara os push's diariamente para os celulares cadastrados.
     */
    public function sendpuxi(){
	$segue = $this->wdb->get_seguepush()->result_array();
	
	foreach ($segue as $sg){
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
		    $this->push($sg['push'],"Novas TIPS disponíveis",$idc);
		}
	    }else if($sg['total'] == 1){
		if($sg['push'] != '(null)' && $sg['push'] != ''){
		    $count = $this->wdb->get_countpush($sg['count'])->result_array();
		    $this->push($sg['push'],$count[0]['co_titulo'], $idc);
		}
	    }
	}
    }
}