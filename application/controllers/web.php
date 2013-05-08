<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('web_model','wdb');
        $this->load->helper('form');
        $this->load->helper('array');
	$this->load->helper('html');
	$this->load->helper('text');
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
    private function ver_conta(){
	if($this->session->userdata('us_codigo') == ''){
	    $this->session->sess_destroy();
	    redirect(index_page());
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
	$input = elements(array('proj','dias'),$this->input->post());
	
	if($input['proj'] !== '' && $input['dias'] !== ''){
	    $proj = str_replace(' ','',$input['proj']);
	    $dias = date("Y", strtotime("+".$input['dias']." days"));
	    $dados['result'] = strtolower(url_title(convert_accented_characters($proj))).$dias;
	    	    
	    $query = $this->wdb->get_nomeunico($dados['result'])->result_array();
	    
	    if(count($query) > 0){
		$dados['result'] = $dados['result']."_".count($query);
	    }
	    
	    echo json_encode($dados);
	}
    }
    
    public function disponivel(){
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
            'title' => 'TilTheDay',
            'tela' => 'home',
        );
        if($this->input->get('id')){
            $dados['usuario'] = $this->wdb->get_usuariobyid($this->input->get('id'))->result();
        }else if($this->input->get('email')){
            $dados['usuario'] = $this->wdb->get_usuariobyemail($this->input->get('email'))->result();
        }else{
            $dados['usuario'] = null;
        }
	
        $this->load->view('abertura',$dados);
    }
    
    public function cad_email(){
	$email = $this->input->post('email');
	if($this->wdb->veemail($email) > 0){
	    die("Esse email já está cadastrado");
	}
	$this->wdb->cademail($email);
    }
    
    public function quem_somos(){
	$dados = array(
	    'title' => 'TilTheDay &raquo; Quem Somos',
	    'tela' => 'quem_somos',
	);
	$this->load->view('web_view',$dados);
    }
    
    public function contato(){
	$this->load->helper('email');
	$this->load->library('email');
	$dados = array(
	    'title' => 'TilTheDay &raquo; Contato',
	    'tela' => 'contato',
	    'erro' => ''
	);
	
	$this->form_validation->set_rules('nome_contato','NOME','trim|required');
	$this->form_validation->set_rules('email_contato','EMAIL','trim|required|valid_email');
	$this->form_validation->set_rules('assunto_contato','ASSUNTO','trim|required');
	$this->form_validation->set_rules('mensagem_contato','MENSAGEM','trim|required');
	
	if($this->form_validation->run()){
	
	    $input = elements(array('nome_contato','email_contato','assunto_contato','mensagem_contato'),$this->input->post());
	    
	    $config['protocol']  = 'smtp';
	    $config['charset'] = 'utf8';
	    $config['wordwrap'] = TRUE;
	    $config['smtp_host'] = 'mail.tiltheday.com';
	    $config['smtp_user'] = 'contato@tiltheday.com';
	    $config['smtp_pass'] = 'dudinha09';
	    $config['smtp_port'] = 587;
	    $config['smtp_timeout'] = 20;
	    $config['mailtype'] = 'html';

	    $texto = "<p>Contato enviado por ".$input['nome_contato']."</p>";
	    $texto .= "<p>Email: ".$input['email_contato']."</p>";
	    $texto .= "<p>Mensagem: ".$input['mensagem_contato']."</p>";
	    $texto .= "<p><hr>Enviada em ".date("d/m/Y")." às ".date("H:i:s")."</p>";

	    $this->email->initialize($config);
	    $this->email->from($input['email_contato'], $input['nome_contato']);
	    $this->email->to('contato@tiltheday.com');
	    $this->email->subject($input['assunto_contato']);
	    $this->email->message($texto);
	    $em = $this->email->send();
	    
	    $dados['erro'] = '<small class="green">Mensagem enviada com sucesso.</small>';
	}
	
	$this->load->view('web_view',$dados);
    }
    
    public function esqueceu(){
	$this->load->helper('email');
	$this->load->library('email');
	
	$dados = array(
	    'title' => 'TilTheDay &raquo; Esqueceu a Senha',
	    'tela' => 'esqueceu',
	    'erro' => '',
	);
	
	$this->form_validation->set_rules('email','EMAIL','trim|required|valid_email');
	
	if($this->form_validation->run()){	
	    $input = elements(array('email'),$this->input->post());
	    $query = $this->wdb->get_usuariobyemail($input['email'])->result_array();

	    if(count($query) > 0){
		$novasenha = date("YmdHis");
		$input['snh'] = $this->crypt($input['email'], $novasenha);
		$this->wdb->troca_senha($input);
		
		$config['protocol']  = 'smtp';
		$config['charset'] = 'utf8';
		$config['wordwrap'] = TRUE;
		$config['smtp_host'] = 'mail.tiltheday.com';
		$config['smtp_user'] = 'noreply@tiltheday.com';
		$config['smtp_pass'] = 'dudinha09';
		$config['smtp_port'] = 587;
		$config['smtp_timeout'] = 20;
		$config['mailtype'] = 'html';
		
		$texto = "<p>Olá, ".$query[0]['us_nome']."</p>";
		$texto .= "<p>Essa é uma senha gerada pelo sistema</p>";
		$texto .= "<p><hr><strong>".$novasenha."</strong><hr></p>";
		$texto .= "<p>Faça seu login com essa senha e clique em Atualizar Dados para personaliza-la.</p>";
		$texto .= "<p>Equipe TilTheDay</p>";

		$this->email->initialize($config);
		$this->email->from('noreply@tiltheday.com', 'TilTheDay');
		$this->email->to($input['email']);
		$this->email->subject('Nova senha no TilTheDay');
		$this->email->message($texto);
		$em = $this->email->send();
		
		$dados['erro'] = '<small class="green">Sua nova senha foi enviada para seu email.</small>';
	    }else{
		$dados['erro'] = '<small class="red">Esse email não está cadastrado</small>';
	    }
	}
	
	$this->load->view('web_view',$dados);
    }
    
    public function login(){
	$dados = array(
	    'title' => 'TilTheDay &raquo; Login',
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
    
    public function sair_instagram(){
	if($this->uri->segment(3) == 'atualiza'){
	    $loc = 'atualiza_dados/ok';
	}else{
	    $ge = explode("_",$this->uri->segment(3));
	    
	    if($ge[0] == 'tips'){
		$loc = 'tips/'.$ge[1].'/tip_'.$ge[2];
	    }else{
		$loc = 'tips/'.$ge[1].'/capa';
	    }
	}
	$id = $this->session->userdata('us_codigo');
	$this->wdb->del_instagram($id);
	
	redirect('web/'.$loc);
    }
    
    public function atualiza_dados(){
	$this->ver_conta();
	$dados = array(
	    'title' => 'TilTheDay &raquo; Atualizar Dados',
	    'tela' => 'atualiza_dados',
	    'oauth' => $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result_array(),
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
            'title' => 'TilTheDay &raquo; Criar Novo TilTheDay',
            'tela' => 'cadprojeto',
        );
        
        $this->form_validation->set_rules('nome_usuario','Nome do Usuário','trim|required');
        $this->form_validation->set_rules('email_usuario','Email do Usuário','trim|required|valid_email|is_unique[tbl_usuario.us_email]');
        $this->form_validation->set_rules('senha_usuario','Senha','trim|required|min_length[6]|max_length[15]');
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
	$this->ver_conta();
	$dados = array(
            'title' => 'TilTheDay &raquo; Criar Novo TilTheDay',
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
    
    public function estatisticas(){
	$dados = array(
	    'title'	=> 'TilTheDay &raquo; Estatísticas',
	    'tela'	=> 'estatistica',
	);
	
	$this->load->view('web_view', $dados);
    }
    
    public function politica(){
	$dados = array(
	    'title'	=> 'TilTheDay &raquo; Política de Privacidade',
	    'tela'	=> 'privacidade',
	);
	
	$this->load->view('web_view', $dados);
    }
    
    public function edit_count(){
	$this->ver_conta();
	$id = $this->uri->segment(3);
	$dados = array(
	    'title'	=> 'TilTheDay &raquo; Editar Count',
	    'tela'	=> 'editar_count',
	    'edcount'	=> $this->wdb->get_count($id)->result_array(),
	);
	
	$this->form_validation->set_rules('nome_projeto','TITULO','trim|required');
	$this->form_validation->set_rules('ocasiao_projeto','OCASIAO','trim|required');
	
	if($this->form_validation->run()){
	    $input = elements(array('codigo','privado','nome_projeto','ocasiao_projeto'),$this->input->post());
	    $this->wdb->set_count($input);
	    redirect('web/counts');
	}
	
	$this->load->view('web_view',$dados);
    }
    
    public function excluir_count(){
	$this->ver_conta();
	$id = $this->uri->segment(3);
	$this->wdb->del_count($id);
	redirect('web/counts');
    }
    
    public function counts(){
	$this->ver_conta();
	$dados = array(
	    'title' => 'TilTheDay &raquo; Minhas Counts',
	    'tela' => 'counts',
	    'counts' => $this->wdb->get_counts($this->session->userdata('us_codigo'))->result(),
	);
	
	$this->load->view('web_view',$dados);
    }
    
    private function limpa_imagem($id){
	$qy = $this->wdb->get_onetip($id)->result_array();
	$img = realpath('tips/'.$qy[0]['ti_imagem']);
	if(file_exists($img)){
	    unlink($img);
	}
    }
    
    public function clean_tip(){
	$id = $this->input->post('id');
	$this->limpa_imagem($id);
	$this->wdb->clear_tip($id);
    }
    
    public function tips(){
	$this->ver_conta();
	if($this->uri->segment(3) != '' && $this->uri->segment(3) != 'ret'){
	    $id = $this->uri->segment(3);
	    $this->session->set_userdata('tips',$id);
	}else{
	    $id = $this->session->userdata('tips');
	}
	
	$dados = array(
	    'title'	=> 'TilTheDay &raquo; Tips',
	    'tela'	=> 'tips',
	    'count'	=> $this->wdb->get_tcount($id)->result(),
	    'tips'	=> $this->wdb->get_tips($id)->result(),
	    'totaltips'	=> $this->wdb->get_totaltips($id),
	);
	
	$auth = $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result_array();
	
	if(count($auth) > 0){
	    $dados['instagram'] = $auth[0]['oa_instagram_id'];
	}else{
	    $dados['instagram'] = 'oauth';
	}
	
	$dados['facebook'] = base_url().'facebook';
	
	$this->load->view('web_view',$dados);
    }
    
    public function upphoto(){
	$this->ver_conta();
	$dados = array(
	    'title' => 'TilTheDay &raquo; Fotos',
	    'tela' => 'sobefoto',
	    'instagram' => $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result(),
	);
	$this->load->view('up_view',$dados);
    }
    
    public function encodeimg(){
	$get = file_get_contents($this->input->post('img'));
	$img = base64_encode($get);
	$type = getimagesize($this->input->post('img'));
	$resp['img64'] = 'data:'.$type['mime'].';base64,'.$img;
	
	echo json_encode($resp);
    }
    
    public function img_instagram(){
	$this->ver_conta();
	$loca = $this->uri->segment(3);
	$img = $this->input->post('imgi');
	$local = $this->input->post('local');
	$exp = array_reverse(explode("/",$img));
	$loc = date("YmdHis").'_'.$exp[0];
	$im = 'n';
	
	$in=    fopen($img, "rb");
	$out=   fopen('./'.$loca.'/'.$loc, "wb");
	while ($chunk = fread($in,(8192 * 10))){
	    fwrite($out, $chunk, (8192 * 10));
	}
	fclose($in);
	fclose($out);
	
	if($loca === 'tips' && $local === 'instagram'){
	    $config = array(
		'image_library'	    => 'gd2',
		'source_image'	    => './'.$loca.'/'.$loc,
		'width'		    => 640,
		'height'	    => 640,
		'quality'	    => '100%',
		'maintain_ratio'    => TRUE,
	    );
	    $this->load->library('image_lib',$config);
	    $this->image_lib->resize();
	    $this->image_lib->clear();
	    $im = 's';
	}
	
	$tam = getimagesize('./'.$loca.'/'.$loc);
	
	if($local === 'facebook' && $loca === 'tip'){
	    if($tam[0] < 640 || $tam[1] < 570){
		unlink($loc);
		$data['erro'] = 'sim';
		echo json_encode($data);
		die();
	    }
	}
	
	if($local === 'facebook' && $loca === 'capa'){
	    if($tam[0] < 640 || $tam[1] < 200){
		//unlink($loc);
		$data['erro'] = 'sim';
		echo json_encode($data);
		die();
	    }
	}
	
	if($local === 'instagram' && $loca === 'capa'){	    
	    $config = array(
		'image_library'	    => 'gd2',
		'source_image'	    => './'.$loca.'/'.$loc,
		'new_image'	    => './'.$loca.'/'.'tmp_'.$loc,
		'width'		    => 320,
		'height'	    => 320,
		'quality'	    => '100%',
		'maintain_ratio'    => TRUE,
	    );
	}else{
	    $lar = $tam[0] / 2.5;
	    $alt = $tam[1] / 2.5;
	    
	    $config = array(
		'image_library' => 'gd2',
		'source_image' => './'.$loca.'/'.$loc,
		'new_image' => './'.$loca.'/'.'tmp_'.$loc,
		'width' => $lar,
		'height' => $alt,
		'quality' => '75',
		'maintain_ratio' => FALSE,
	    );
	}

	if($im === 'n'){
	    $this->load->library('image_lib',$config);
	}else{
	    $this->image_lib->initialize($config);
	}
	$this->image_lib->resize();
	$this->image_lib->clear();

	if($local === 'instagram' && $loca === 'tips'){
	    $data['width'] = 640;
	    $data['height'] = 640;
	}else{
	    $data['width'] = $tam[0];
	    $data['height'] = $tam[1];
	}
	$data['url'] = $loc;
	$data['erro'] = '';
	
	if($loca == 'capa'){
	    $input = array(
		'img' => $loc,
		'codigo' => $this->input->post('idcount'),
	    );
	    $this->wdb->up_count($input);
	}
	
	echo json_encode($data);
    }
    
    public function img_upload(){
	$this->ver_conta();
	$path = $this->uri->segment(3);
	if($path == ''){
	    $path = 'tips';
	}
	$this->load->helper('file');
	$config['upload_path']   = FCPATH.$path.'/';
	$config['allowed_types'] = 'jpg|jpeg|png';
	$config['file_name'] = md5(date("YmdHis"));
	if($path === 'tips'){
	    $config['min_width'] = 640;
	    $config['min_height'] = 570;
	}else if($path === 'capa'){
	    $config['min_width'] = 640;
	    $config['min_height'] = 200;
	}
	
	$this->load->library('upload', $config);
	
	if($this->upload->do_upload('imagem')){
	    $file = $this->upload->data();
	    
	    $data['arquivo'] = $file['file_name'];
	    $temp = 'tmp_'.$file['file_name'];
	    
	    if($path === 'tips'){
		$lar = ($file['image_width'] / 2.5);
		$alt = ($file['image_height'] / 2.5);
	    }else{
		$lar = ($file['image_width'] / 2);
		$alt = ($file['image_height'] / 2);
	    }
	    
	    $config = array(
		'image_library' => 'gd2',
		'source_image' => './'.$path.'/'.$file['file_name'],
		'new_image' => './'.$path.'/'.'tmp_'.$file['file_name'],
		'width' => $lar,
		'height' => $alt,
		'quality' => '75',
		'maintain_ratio' => FALSE,
	    );

	    $this->load->library('image_lib',$config);
	    $this->image_lib->resize();
	    
	    
	    if($path == 'tips'){
		$param = 'id="photo"';
	    }else{
		 $param = 'id="photoc"';
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
	    if($path == 'tips'){
		$data['msg'] = 'A imagem precisa ter no mínimo 640 x 570 pixels.';
	    }else{
		$data['msg'] = 'A imagem precisa ter no mínimo 640 x 200 pixels.';
	    }
	}
	
	echo json_encode($data);
    }
    
    public function grava_capa(){
	$this->ver_conta();
	$input = elements(array('codigo','img'), $this->input->post());
	
	$arq = $input['img'];
	
	$img = str_replace('data:image/jpeg;base64,', '', $arq);
	$img2 = str_replace(' ', '+', $img);
	$data = base64_decode($img2);

	$file = date("YmdHis")."_tip.jpg";

	file_put_contents("./capa/".$file, $data);
	
	$input['img'] = $file;
	
	/*$crd = explode('/',$input['posicao']);
	$posih = ($input['altura']);
	$posiw = ($input['largura']);
	
	if(file_exists('./capa/tmp_'.$input['img'])){
	    // FAZ O RESIZE DA IMAGEM
	    if($input['central'] === 's'){
		$config = array(
		    'image_library' => 'gd2',
		    'source_image' => './capa/tmp_'.$input['img'],
		    'width' => $posiw,
		    'height' => $posih,
		    'quality' => '75',
		    'maintain_ratio' => FALSE,
		);
		$this->load->library('image_lib',$config);
		$this->image_lib->resize();
		$this->image_lib->clear();

		$img = getimagesize('./capa/tmp_'.$input['img']);
		
		$tmpw = ($img[0] * 2);
		$tmph = ($img[1] * 2);

		$config = array(
		    'image_library' => 'gd2',
		    'source_image' => './capa/'.$input['img'],
		    'width' => $tmpw,
		    'height' => $tmph,
		    'quality' => '75',
		    'maintain_ratio' => FALSE,
		);
		$this->image_lib->initialize($config);
		$this->image_lib->resize();
		$this->image_lib->clear();
		
		$posl = abs($crd[0]);
		$post = abs($crd[1]);

		$config = array(
		    'image_library' => 'gd2',
		    'source_image' => './capa/'.$input['img'],
		    'x_axis' => ($posl * 2),
		    'y_axis' => ($post * 2),
		    'width'  => 640, //$input['largura'],
		    'height' => 200, //$input['altura'],
		    'quality' => '75',
		    'maintain_ratio' => FALSE,
		);

		$this->image_lib->initialize($config);
		$this->image_lib->crop();
		$this->image_lib->clear();
		
		// FAZ O CROP DA IMAGEM
	    }else{
		
		$posil = (abs($crd[0]) * 2);
		$posit = (abs($crd[1]) * 2);

		if($posil < 0){
		    $posil = 0;
		}
		if($posit < 0){
		    $posit = 0;
		}
		
		$config = array(
		    'image_library' => 'gd2',
		    'source_image' => './capa/'.$input['img'],
		    'x_axis' => $posil,
		    'y_axis' => $posit,
		    'width'  => 640,
		    'height' => 200,
		    'quality' => '75',
		    'maintain_ratio' => FALSE,
		);
		
		$this->load->library('image_lib',$config);
		$this->image_lib->crop();
		$this->image_lib->clear();
	    }*/
	    
	    $resp['msg'] = base_url().'capa/'.$input['img'];
	    $resp['erro'] = 'ok';

	    $ins = array(
		'img' => $input['img'],
		'codigo' => $input['codigo']
	    );
	    $this->wdb->up_count($ins);
	
	echo json_encode($resp);
    }
    
    public function grava_tip(){
	$this->ver_conta();
	$input = elements(array('id_tip','codigo','img','titulo','sub','mensagem','central','largura','altura','posicao'), $this->input->post());
	
	if($input['img'] != 'sem'){
	    $arq = $input['img'];

	    $img = str_replace('data:image/jpeg;base64,', '', $arq);
	    $img2 = str_replace(' ', '+', $img);
	    $data = base64_decode($img2);

	    $file = date("YmdHis")."_tip.jpg";

	    file_put_contents("./tips/".$file, $data);

	    $input['img'] = $file;

	    $crd = explode('/',$input['posicao']);
	    $posih = ($input['altura']);
	    $posiw = ($input['largura']);
	
	    $config['image_library'] = 'gd2';
	    $config['source_image'] = './tips/'.$input['img'];
	    $config['new_image'] = './tips/thumb_'.$input['img'];
	    $config['maintain_ratio'] = FALSE;
	    $config['width'] = 200;
	    $config['height'] = 200;
	    $this->load->library('image_lib',$config);
	    $this->image_lib->resize();
	    $resp['imagem'] = $file;
	}
	
	if($input['img'] == 'sem'){
	    $resp['imagem'] = 'nao';
	}
	
	$resp['msg'] = '';
	$resp['erro'] = 'ok';
	
	$this->wdb->set_tip($input);
	
	echo json_encode($resp);
    }
    
    private function inverte_data($data){
	$nova_data = implode("-",array_reverse(explode("/",$data)));
	return $nova_data;
    }
    
    public function gravadata(){
	$this->ver_conta();
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
    
    public function altdata(){
	$this->ver_conta();
	$input = elements(array('cd_count','dias_count','calendario'),$this->input->post());
	$c = 0;
	
	$input['calendario'] = $this->inverte_data($input['calendario']);
	
	$this->wdb->set_datacount($input);
	
	while($c < $input['dias_count']){
	    $data = date("Y-m-d", strtotime($input['calendario']." +".$c." days"));
	    $c++;
	    
	    $tp = array(
		'codigo' => $input['cd_count'],
		'data' => $data,
		'contagem' => $c
	    );
	    $this->wdb->up_tip($tp);
	}
    }
    
    public function gravatag(){
	$this->ver_conta();
	$input = elements(array('tags','codigo'),$this->input->post());
	$this->wdb->set_tag($input);
    }
    
    public function deltag(){
	$this->ver_conta();
	$input = elements(array('tag','codigo'),$this->input->post());
	$count = $this->wdb->get_count($input['codigo'])->result_array();
	
	$tags =  explode(",",$count[0]['co_tags']);
	$c = count($tags);
	
	$nova = '';
	
	for($i=0;$i<$c;$i++){
	    if($tags[$i] != $input['tag']){
		$nova .= $tags[$i].',';
	    }
	}
	$t = substr($nova,0,-1);
	$input['tags'] = $t;
	
	$this->wdb->set_tag($input);
    }
    
    public function computador(){
	$dados = array(
	    'title' => 'TilTheDay &raquo; Meu Computador',
	    'tela' => 'micro',
	);
	
	$this->load->view('up_view',$dados);
    }
    
    public function invites(){
	$this->ver_conta();
	$access = $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result_array();
	$dados = array(
	    'title' => 'TilTheDay &raquo; Invites',
	    'tela' => 'convites',
	    'counts' => $this->wdb->get_counts($this->session->userdata('us_codigo'))->result(),
	    'facebook' => base_url().'facebook',
	);
	
	if(count($access) === 0){
	    $dados['appID'] == '445876232159922';
	}else{
	    $dados['appID'] = '';
	}
	
	#echo "<pre>";
	#print_r($dados);
	#die();
	    
	$this->load->view('web_view',$dados);
    }
    
    public function convite_enviado(){
	$this->ver_conta();
	$saida = '<script src="http://code.jquery.com/jquery-1.9.1.js"></script>';
	$saida .= '<script src="'.base_url().'js/jquery.prettyPhoto.js" type="text/javascript"></script>';
	    $saida .= '<script>';
	    $saida .= "$(document).ready(function(){";
		$saida .= 'alert("Invite enviado com sucesso");';
		$saida .= 'window.parent.$.prettyPhoto.close();';
	    $saida .= "});";
	$saida .= "</script>";
	echo $saida;
    }
    
    public function convite_enviadonao(){
	$this->ver_conta();
	$saida = '<script src="http://code.jquery.com/jquery-1.9.1.js"></script>';
	$saida .= '<script src="'.base_url().'js/jquery.prettyPhoto.js" type="text/javascript"></script>';
	    $saida .= '<script>';
	    $saida .= "$(document).ready(function(){";
		$saida .= 'window.parent.$.prettyPhoto.close();';
	    $saida .= "});";
	$saida .= "</script>";
	echo $saida;
    }
    
    public function grava_convite(){
	$this->ver_conta();
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
		    $config['smtp_host'] = 'mail.tiltheday.com';
		    $config['smtp_user'] = 'invite@tiltheday.com';
		    $config['smtp_pass'] = 'dudinha09';
		    $config['smtp_port'] = 587;
		    $config['smtp_timeout'] = 20;
		    $config['mailtype'] = 'html';
		    
		    $this->email->initialize($config);
		    $this->email->from($this->session->userdata('us_email'), $this->session->userdata('nomecurto'));
		    $this->email->to($valid);
		    $this->email->subject('Você recebeu um invite de '.$this->session->userdata('nomecurto'));
		    $this->email->message($texto);
		    $em = $this->email->send();
		    
		    $c++;
		//}
	    }else{
		$em = false;
	    }
	}
	if(isset($em) && $em != false){
	    $this->session->set_flashdata('total','Email enviado com sucesso para '.$c.' invites');
	}else{
	    $saldo = count($arr) - $c;
	    $this->session->set_flashdata('total','Emails recebidos: '.count($arr).'. Enviados: '.$c.'. Inválidos: '.$saldo);
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
			$this->push($sg['push'],"Novas TIPS disponíveis",$idc);
		    }
		}else if($sg['total'] == 1){
		    if($sg['push'] != '(null)' && $sg['push'] != ''){
			$count = $this->wdb->get_countpush($sg['count'])->result_array();
			$this->push($sg['push'],$count[0]['co_titulo'], $idc);
		    }
		}
		$ver[] = $sg['push'];
	    }
	}
	
	echo "<pre>";
	print_r($ver);
    }
    
    public function expira_count(){
	$query = $this->wdb->get_all('tbl_count')->result_array();
	
	foreach($query as $k){
	    if($k['co_data_inicio'] && date("Y-m-d", strtotime($k['co_data_inicio']. '+ '.$k['co_dias'].' days')) < date("Y-m-d")){
		$this->wdb->set_expira($k['co_codigo']);
	    }
	}
    }
}