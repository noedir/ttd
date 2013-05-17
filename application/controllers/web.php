<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web extends CI_Controller {
    
    /*
     * Função que inicias as libraries e helpers
     */
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
    
    /*
     * Faz a checagem do navegador do usuário para a página de TIPS
     */
    private function browser_check(){
        $brow = get_browser();
        $saida['browser'] = $brow->browser;
        $saida['version'] = $brow->version;
        return $saida;
    }
    
    /*
     * Função que verifica se está logado ou não.
     * Caso não esteja, é enviado para a página de abertura.
     */
    private function ver_conta(){
	if($this->session->userdata('us_codigo') == ''){
	    $this->session->sess_destroy();
	    redirect(index_page());
	}
	if(TUTORIAL == 's'){
	    if($this->session->userdata('us_tutorial') == 'n'){
		redirect('web/tutorial');
	    }
	}
    }
    
    private function mandapracount(){
	if($this->session->userdata('us_codigo') != ''){
	    redirect('web/counts');
	}
    }
    
    /*
     * Função que criptograma usuário e senha para gerar senha e gravar no BD
     */
    private function crypt($u=NULL,$s=NULL){
	if($u != NULL && $s != NULL){
	    $r = sha1(md5($u).':'.md5($s));
	    return $r;
	}
    }
    
    /*
     * Função que separa o nome do sobrenome
     */
    private function nome_curto($s){
	$n = explode(' ',$s);
	return $n[0];
    }
    
    /*
     * Função que cria o Identificador para a Count
     */
    public function nomeunico(){
	header('content-type: application/json');
	$input = elements(array('proj','dias'),$this->input->post());
	
	if($input['proj'] !== '' && $input['dias'] !== ''){
	    $proj = str_replace(' ','',$input['proj']);
	    $dias = date("Y", strtotime("+".$input['dias']." days"));
	    $dados['result'] = strtolower(url_title(convert_accented_characters($proj))).$dias;
	    
	    // Verifica se o nome já existe
	    $query = $this->wdb->get_nomeunico($dados['result'])->result_array();
	    
	    // Caso exista, acrescenta um número no final do Identificador
	    if(count($query) > 0){
		$dados['result'] = $dados['result']."_".count($query);
	    }
	    
	    echo json_encode($dados);
	}
    }
    
    /*
     * Função que verifica se um Identificador está disponível
     */
    public function disponivel(){
	$nome = $this->input->post('nome');
	$query = $this->wdb->get_disponivel($nome)->result_array();
	
	if(count($query) > 0){
	    echo "false";
	}else{
	    echo "true";
	}
    }
    
    /*
     * Troca de idioma no site
     */
    public function idioma(){
	$lng = $this->input->post('lng');
	$this->lang->load($lng,'idioma');
	$this->input->set_cookie(array(
	    'name'=>'idioma',
	    'value'=>$lng,
	    'expire'=>'86500',

	));
    }
    
    /*
     * Página Index do Site. Também a abertura
     * @dados Array: title - seta o título da página / tela: arquivo que é chamado da pasta telas dentro de application/views
     */
    public function index(){
	$this->mandapracount();
        $dados = array(
            'title' => TITLE_PAGE,
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
    
    /*
     * Função marca o tutorial como assistido
     */
    public function tuto(){
	$this->wdb->set_tutorial();
	$this->session->set_userdata('us_tutorial','s');
    }
    
    /*
     * Função que abre a página de tutorial
     */
    public function tutorial(){
	$dados = array(
	    'title' => TITLE_PAGE,
	    'tela' => 'tutorial'
	);
	
	$this->load->view('web_view', $dados);
    }
    
    /*
     * Função que verifica se email já está cadastrado na base de dados
     * Caso não esteja, faz o cadastro para a base de dados de email
     * Não tem relação com a tela de usuários. Somente para criar uma base de cadastros mesmo
     */
    public function cad_email(){
	$email = $this->input->post('email');
	if($this->wdb->veemail($email) > 0){
	    die("Esse email já está cadastrado");
	}
	$this->wdb->cademail($email);
    }
    
    /*
     * Função que abre a página quem_somos
     */
    public function quem_somos(){
	$dados = array(
	    'title' => TITLE_PAGE.' &raquo; Quem Somos',
	    'tela' => 'quem_somos',
	);
	$this->load->view('web_view',$dados);
    }
    
    /*
     * Função  que abre a página de contato
     * Também recebe os dados do formulário de contato e envia email
     */
    public function contato(){
	$this->load->helper('email');
	$this->load->library('email');
	$dados = array(
	    'title' => TITLE_PAGE.' &raquo; Contato',
	    'tela' => 'contato',
	    'erro' => ''
	);
	
	// Faz a validação do formulário
	$this->form_validation->set_rules('nome_contato','NOME','trim|required');
	$this->form_validation->set_rules('email_contato','EMAIL','trim|required|valid_email');
	$this->form_validation->set_rules('assunto_contato','ASSUNTO','trim|required');
	$this->form_validation->set_rules('mensagem_contato','MENSAGEM','trim|required');
	
	
	if($this->form_validation->run()){
	
	    $input = elements(array('nome_contato','email_contato','assunto_contato','mensagem_contato'),$this->input->post());
	    
	    // Seta os parâmetros de envio do email
	    $config['protocol']  = 'smtp'; // método SMTP
	    $config['charset'] = 'utf8'; // charset utilizado
	    $config['wordwrap'] = TRUE; // quebra de linha
	    $config['smtp_host'] = EMAIL_HOST; // host de envio (ver arquivo application/config/config.php
	    $config['smtp_user'] = EMAIL_CONTATO; // usuário de email
	    $config['smtp_pass'] = EMAIL_CONTATO_SENHA; // senha do email
	    $config['smtp_port'] = 587; // porta de envio
	    $config['smtp_timeout'] = 20; // timeout do serviço (20 segundos)
	    $config['mailtype'] = 'html'; // aceita HTML

	    $texto = "<p>Contato enviado por ".$input['nome_contato']."</p>";
	    $texto .= "<p>Email: ".$input['email_contato']."</p>";
	    $texto .= "<p>Mensagem: ".$input['mensagem_contato']."</p>";
	    $texto .= "<p><hr>Enviada em ".date("d/m/Y")." às ".date("H:i:s")."</p>";

	    // Inicializa o serviço de email e faz o envio
	    $this->email->initialize($config);
	    $this->email->from($input['email_contato'], $input['nome_contato']);
	    $this->email->to(EMAIL_CONTATO);
	    $this->email->subject($input['assunto_contato']);
	    $this->email->message($texto);
	    $this->email->send();
	    
	    $dados['erro'] = '<small class="green">Mensagem enviada com sucesso.</small>';
	}
	
	$this->load->view('web_view',$dados);
    }
    
    /*
     * Função que gera a página de esqueceu a senha
     * Faz a validação do formulário e envia para o cliente com uma nova senha
     */
    public function esqueceu(){
	$this->load->helper('email');
	$this->load->library('email');
	
	$dados = array(
	    'title' => TITLE_PAGE.' &raquo; Esqueceu a Senha',
	    'tela' => 'esqueceu',
	    'erro' => '',
	);
	
	$this->form_validation->set_rules('email','EMAIL','trim|required|valid_email');
	
	if($this->form_validation->run()){	
	    $input = elements(array('email'),$this->input->post());
	    
	    // Pega os dados do usuário baseado no email informado
	    $query = $this->wdb->get_usuariobyemail($input['email'])->result_array();
	    
	    // Caso exista, envia o email com a nova senha
	    // O padrão da nova senha é baseado no dia mes ano hora minuto segundo tudo jundo
	    if(count($query) > 0){
		$novasenha = date("YmdHis");
		$input['snh'] = $this->crypt($input['email'], $novasenha);
		$this->wdb->troca_senha($input);
		
		$config['protocol']  = 'smtp';
		$config['charset'] = 'utf8';
		$config['wordwrap'] = TRUE;
		$config['smtp_host'] = EMAIL_HOST;
		$config['smtp_user'] = EMAIL_NOREPLY;
		$config['smtp_pass'] = EMAIL_NOREPLY_SENHA;
		$config['smtp_port'] = 587;
		$config['smtp_timeout'] = 20;
		$config['mailtype'] = 'html';
		
		$texto = "<p>Olá, ".$query[0]['us_nome']."</p>";
		$texto .= "<p>Essa é uma senha gerada pelo sistema</p>";
		$texto .= "<p><hr><strong>".$novasenha."</strong><hr></p>";
		$texto .= "<p>Faça seu login com essa senha e clique em Atualizar Dados para personaliza-la.</p>";
		$texto .= "<p>Equipe ".TITLE_PAGE."</p>";

		$this->email->initialize($config);
		$this->email->from(EMAIL_NOREPLY, TITLE_PAGE);
		$this->email->to($input['email']);
		$this->email->subject('Nova senha no '.TITLE_PAGE);
		$this->email->message($texto);
		$em = $this->email->send();
		
		$dados['erro'] = '<small class="green">Sua nova senha foi enviada para seu email.</small>';
	    }else{
		$dados['erro'] = '<small class="red">Esse email não está cadastrado</small>';
	    }
	}
	
	$this->load->view('web_view',$dados);
    }
    
    /*
     * Função que gera a tela de login
     * Recebe e trata o formulário de login
     */
    public function login(){
	if($this->session->userdata('us_nome') != ''){
	    redirect('web/counts');
	}
	$dados = array(
	    'title' => TITLE_PAGE.' &raquo; Login',
	    'tela' => 'login',
	    'erro' => '',
	);
	
	$this->form_validation->set_rules('email','EMAIL','trim|required|valid_email');
	$this->form_validation->set_rules('senha','SENHA','trim|required');
	
	if($this->form_validation->run()){	
	    $input = elements(array('email','senha'),$this->input->post());
	    
	    // Criptografa a senha para comparar no bd
	    $input['snh'] = $this->crypt($input['email'],$input['senha']);
	    
	    // Busca as informações do usuário baseado no email e senha
	    $query = $this->wdb->get_loginuser($input)->result_array();
	    
	    // Caso exista e a senha esteja correta, monta a session
	    if(count($query) > 0){
		//if($query[0]['us_confirma'] == 's'){ // Descomentar para verificar confirmação de email
		    foreach($query[0] as $k => $v){
			$this->session->set_userdata(array($k=>$v));
		    }
		    $nc = $this->nome_curto($query[0]['us_nome']);
		    $this->session->set_userdata('nomecurto', $nc);
		    redirect('web/counts');
		    
		    // Descomentar para verificar confirmação de email
		/*}else{
		    $dados['erro'] = '<small class="red">Você ainda não confirmou seu e-mail</small>';
		}*/
	    }else{
		$dados['erro'] = '<small class="red">Email ou senha incorretos</small>';
	    }
	}
	
	$this->load->view('web_view',$dados);
    }
    
    /*
     * Função que faz o logout no Instagram
     */
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
	
	// Caso exista o arquivo de cache, é deletado
	$cache = './cachejson/instagram_'.$this->session->userdata('us_codigo').'.json';
	
	if(file_exists($cache)){
	    unlink($cache);
	}
	
	redirect('web/'.$loc);
    }
    
    /*
     * Função que atualiza os dados do usuário
     */
    public function atualiza_dados(){
	$this->ver_conta();
	$dados = array(
	    'title' => TITLE_PAGE.' &raquo; Atualizar Dados',
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
    
    /*
     * Função para fazer o logout da página
     */
    public function sair(){
	$this->session->sess_destroy();
	redirect('web');
    }
    
    /*
     * Função que cria um projeto e usuário
     * Faz a confirmação do formulário
     */
    public function criar_projeto(){
	$dados = array(
            'title' => TITLE_PAGE.' &raquo; Criar Novo '.TITLE_PAGE,
            'tela' => 'cadprojeto',
        );
	
	// Verifica se o email é da Dcanm para liberar + dias
        if($this->input->post('email_usuario') != ''){
	    $vem = strpos($this->input->post('email_usuario'),'@dcanm');
	}
	
        $this->form_validation->set_rules('nome_usuario','Nome do Usuário','trim|required');
        $this->form_validation->set_rules('email_usuario','Email do Usuário','trim|required|valid_email|is_unique[tbl_usuario.us_email]');
        $this->form_validation->set_rules('senha_usuario','Senha','trim|required|min_length[6]|max_length[15]');
        $this->form_validation->set_rules('confirma_senha','Confirmação','trim|required|matches[senha_usuario]');
        $this->form_validation->set_rules('nome_projeto','Projeto','trim|required');
        $this->form_validation->set_rules('ocasiao_projeto','Ocasião','trim|required');
	
	// Caso email não for @dcanm pode criar até 10 dias ou o limite na constante DIAS
	// Caso email seja @dcanm pode ser criado quantos dias desejar, independente da contantes DIAS
	if(isset($vem) && $vem === false){
	    $this->form_validation->set_rules('dias_projeto','Dias do Projeto','trim|required|numeric|less_than['.DIAS.']');
	}else{
	    $this->form_validation->set_rules('dias_projeto','Dias do Projeto','trim|required|numeric');
	}
        $this->form_validation->set_rules('nomeunico','Identificador','trim|required|is_unique[tbl_count.co_nomeunico]');
	
	if($this->form_validation->run()){
	    $in_user = elements(array('nome_usuario','email_usuario','gen_usuario','senha_usuario','confirma_senha'),$this->input->post());
	    
	    // Cria a senha criptografada que será gravada no banco
	    $in_user['senha_usuario'] = $this->crypt($in_user['email_usuario'],$in_user['senha_usuario']);
	    $in_user['codigo'] = 0; // Para mostrar ao Model Web que é para inserir um novo dado
	    
	    // Grava o usuário no banco e pega o ID gerado
	    $this->wdb->set_usuario($in_user);
            $idu = $this->db->insert_id();
	    
	    // Monta o array da Count
	    $in_count = elements(array('privado','nome_projeto','ocasiao_projeto','dias_projeto','valor_projeto','nomeunico'),$this->input->post());
	    
	    $in_count['user_id'] = $idu;
	    
	    // Grava os dados da Count no banco de dados
	    $this->wdb->set_count($in_count);
	    
	    // Pega os dados do usuário para já fazer login
	    $query = $this->wdb->get_usuariobyemail($in_user['email_usuario'])->result();
	    
	    // Monta a session do usuário recém cadastrado
	    foreach($query[0] as $k => $v){
		$this->session->set_userdata(array($k=>$v));
	    }
	    
	    // Gera o nome curto do usuário
	    $nc = $this->nome_curto($query[0]->us_nome);
	    $this->session->set_userdata('nomecurto',$nc);
	    $dados['nome'] = $query[0]->us_nome;
	    
	    // Envia email de bem vindo para o usuário
	    $this->load->helper('email');
	    $this->load->library('email');

	    $texto = '<img src="'.base_url().'img/logotipo_header.jpg">
		<p>Olá '.$in_user['email_usuario'].',</p>
		<p>Seja bem vindo ao TilTheDay!</p>
		<p>Você acabou de se cadastrar na ferramenta de contagem que vai estabelecer o vínculo entre você e seu melhores momentos futuros.</p>
		<p>A partir de agora você poderá criar uma contagem regressiva de até '.DIAS.' Dias para o evento ou acontecimento que você deseja.</p>
		<p><a href="'.base_url().'">Clique Aqui</a> e comece já a motivar a sua ansiedade.</p>
		<p>Caso o link acima não esteja funcionando, copie e cole o URL abaixo no seu Navegador:</p>
		<p>'.base_url().'</p>';

	    $config['protocol']  = 'smtp';
	    $config['charset'] = 'utf8';
	    $config['wordwrap'] = TRUE;
	    $config['smtp_host'] = EMAIL_HOST;
	    $config['smtp_user'] = EMAIL_NOREPLY;
	    $config['smtp_pass'] = EMAIL_NOREPLY_SENHA;
	    $config['smtp_port'] = 587;
	    $config['smtp_timeout'] = 20;
	    $config['mailtype'] = 'html';

	    $this->email->initialize($config);
	    $this->email->from(EMAIL_CONTATO);
	    $this->email->to($in_user['email_usuario']);
	    $this->email->subject('Bem vindo ao '.TITLE_PAGE);
	    $this->email->message($texto);
	    $em = $this->email->send();
	    
	    redirect('web/counts');
	}
	
	$this->load->view('web_view',$dados);
    }
    
    /*
     * Função para confirmação. Ainda não implantado no sistema
     */
    public function confirma(){
	$dados = array(
	    'title' => TITLE_PAGE.' &raquo; Confirmação de Email',
	    'tela' => 'confirma'
	);
	$email = $this->uri->segment(3);
	
	if($email != ''){
	    $query = $this->wdb->get_usuariobyemail($email)->result_array;
	    
	    if(count($query) > 0){
		$this->wdb->set_confirma($email);
		$dados['erro'] = '';
	    }else{
		$dados['erro'] = '<small class="red">Email não localizado</small>';
	    }
	}else{
	    $dados['erro'] = '<small class="red">Falta informar o e-mail</small>';
	}
	
	$this->load->view('web_view',$dados);
    }
    
    /*
     * Função que cria um novo count para um usuário já cadastrado no sistema
     */
    public function criar_novo_projeto(){
	$this->ver_conta();
	$dados = array(
            'title' => TITLE_PAGE.' &raquo; Criar Novo '.TITLE_PAGE,
            'tela' => 'novo_projeto'
        );
	
	// Verifica se o email é da Dcanm para liberar + dias
	if($this->input->post('email_usuario') != ''){
	    $vem = strpos($this->input->post('email_usuario'),'@dcanm');
	}
	
	$this->form_validation->set_rules('nome_projeto','Projeto','trim|required');
        $this->form_validation->set_rules('ocasiao_projeto','Ocasião','trim|required');
	
    	// Caso email não for @dcanm pode criar até 10 dias ou o limite na constante DIAS
	// Caso email seja @dcanm pode ser criado quantos dias desejar, independente da contantes DIAS
	if(isset($vem) && $vem === false){
	    $this->form_validation->set_rules('dias_projeto','Dias do Projeto','trim|required|numeric|less_than['.DIAS.']');
	}else{
	    $this->form_validation->set_rules('dias_projeto','Dias do Projeto','trim|required|numeric');
	}
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
    
    /*
     * Função que gera a página de estatística. Ainda não implementada
     */
    public function estatisticas(){
	$dados = array(
	    'title'	=> TITLE_PAGE.' &raquo; Estatísticas',
	    'tela'	=> 'estatistica',
	);
	
	$this->load->view('web_view', $dados);
    }
    
    /*
     * Função que gera a página de Política de Privacidade. Desabilitada temporariamente
     */
    public function politica(){
	$dados = array(
	    'title'	=> TITLE_PAGE.' &raquo; Política de Privacidade',
	    'tela'	=> 'privacidade',
	);
	
	$this->load->view('web_view', $dados);
    }
    
    /*
     * Função para editar uma conta. Permite editar somente o  Título da count
     */
    public function edit_count(){
	$this->ver_conta();
	$id = $this->uri->segment(3);
	$dados = array(
	    'title'	=> TITLE_PAGE.' &raquo; Editar Count',
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
    
    /*
     * Função que exclui uma count.
     */
    public function excluir_count(){
	$this->ver_conta();
	$id = $this->uri->segment(3);
	$this->wdb->del_count($id);
	redirect('web/counts');
    }
    
    /*
     * Função que gera a página das count, onde o usuário pode ver
     * as counts criadas por ele.
     */
    public function counts(){
	$this->ver_conta();
	$dados = array(
	    'title' => TITLE_PAGE.' &raquo; Minhas Counts',
	    'tela' => 'counts',
	    'counts' => $this->wdb->get_counts($this->session->userdata('us_codigo'))->result(),
	);
	
	$this->load->view('web_view',$dados);
    }
    
    /*
     * Função que apaga a imagens de uma tip que foi resetada
     */
    private function limpa_imagem($id){
	$qy = $this->wdb->get_onetip($id)->result_array();
	$img = realpath('tips/'.$qy[0]['ti_imagem']);
	if(file_exists($img)){
	    unlink($img);
	}
    }
    
    /*
     * Função que reseta uma tip
     */
    public function clean_tip(){
	$id = $this->input->post('id');
	$this->limpa_imagem($id);
	$this->wdb->clear_tip($id);
    }
    
    /*
     * Função que gera a página de edição das tips.
     */
    public function tips(){
	$this->ver_conta(); // Verifica se está logado
	
	/*
	 * Usada para retorno de Oauth.
	 */
	if($this->uri->segment(3) != '' && $this->uri->segment(3) != 'ret'){
	    $id = $this->uri->segment(3);
	    $this->session->set_userdata('tips',$id);
	}else{
	    $id = $this->session->userdata('tips');
	}
	
	
	$dados = array(
	    'title'	=> TITLE_PAGE.' &raquo; Tips',
	    'tela'	=> 'tips',
	    'count'	=> $this->wdb->get_tcount($id)->result(), // Dados da count
	    'tips'	=> $this->wdb->get_tips($id)->result(), // Dados das tips
	    'totaltips'	=> $this->wdb->get_totaltips($id), // Total de tips, baseado nos dias
            'browser'	=> $this->browser_check(), // Para direcionar o usuário para o sistema correto. No caso de navegador antigo, é carregado o arquivo javascript funcoes2.js
	);
	
	// Verifica Oauth para o Isntagram
	$auth = $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result_array();
	
	if(count($auth) > 0){
	    $dados['instagram'] = $auth[0]['oa_instagram_id'];
	}else{
	    $dados['instagram'] = 'oauth';
	}
	
	$dados['facebook'] = base_url().'facebook';
	
	$this->load->view('web_view',$dados);
    }
    
    /*
     * Função para upload de photo. Desativado
     */
    public function upphoto(){
	$this->ver_conta();
	$dados = array(
	    'title' => TITLE_PAGE.' &raquo; Fotos',
	    'tela' => 'sobefoto',
	    'instagram' => $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result(),
	);
	$this->load->view('up_view',$dados);
    }
    
    /*
     * Função para retornar uma imagem codificada para funcionar no Canvas
     */
    public function encodeimg(){
	$get = file_get_contents($this->input->post('img'));
	$img = base64_encode($get);
	$type = getimagesize($this->input->post('img'));
	$resp['img64'] = 'data:'.$type['mime'].';base64,'.$img;
	
	echo json_encode($resp);
    }
    
    /*
     * Mesma que função anterior
     */
    public function upimg(){
	$get = file_get_contents($this->input->post('img'));
	$img = base64_encode($get);
	$type = getimagesize($this->input->post('img'));
	$resp['img64'] = 'data:'.$type['mime'].';base64,'.$img;
	
	echo json_encode($resp);
    }
    
    /*
     * Recebe a imagem do instagram e grava no servidor.
     * Necessários para os navegadores mais antigos
     */
    public function img_instagram(){
	$this->ver_conta();
	$local = $this->input->post('loca');
	$img = $this->input->post('imgi');
	$loca = $this->input->post('local');
	$exp = array_reverse(explode("/",$img));
	$loc = date("YmdHis").'_'.$exp[0]; // nome do arquivo
	$im = 'n';
	
	// Grava no servidor
	$in=    fopen($img, "rb");
	$out=   fopen('./'.$loca.'/'.$loc, "wb");
	while ($chunk = fread($in,(8192 * 10))){
	    fwrite($out, $chunk, (8192 * 10));
	}
	fclose($in);
	fclose($out);
	
	// Faz a verificação do local da imagem para setar o tamanho
	if($loca === 'tips' && $local === 'instagram'){
	    $config = array(
		'image_library'	    => 'gd2',
		'source_image'	    => './'.$loca.'/'.$loc,
		'width'		    => 640,
		'height'	    => 640,
		'quality'	    => '100%',
		'maintain_ratio'    => FALSE,
	    );
	    $this->load->library('image_lib',$config);
	    $this->image_lib->resize();
	    $this->image_lib->clear();
	    $im = 's';
	}
	
	// Pega o tamanho da imagem no servidor
	$tam = getimagesize('./'.$loca.'/'.$loc);
	
	
	if($local === 'instagram' && $loca === 'capa'){	    
	    $config = array(
		'image_library'	    => 'gd2',
		'source_image'	    => './'.$loca.'/'.$loc,
		'new_image'	    => './'.$loca.'/'.'tmp_'.$loc,
		'width'		    => 320,
		'height'	    => 320,
		'quality'	    => '100%',
	    );
	}else{
	    
	    // Divide para caber na tela do iphone.
	    // NÃO MEXER NESSE CÁLCULO. RISCO DE NÃO DAR MAIS CERTO
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
	
	// Faz o resize na imagem
	$this->image_lib->resize();
	$this->image_lib->clear();

	if($local == 'instagram'){
	    $data['width'] = 320;
	    $data['height'] = 320;
	}else{
	    $data['width'] = $tam[0];
	    $data['height'] = $tam[1];
	}
	$data['url'] = $loc;
	$data['erro'] = '';
	
	// Se for na capa, já grava no banco de dados
	if($loca == 'capa'){
	    $input = array(
		'img' => $loc,
		'codigo' => $this->input->post('idcount'),
	    );
	    $this->wdb->up_count($input);
	}
	
	echo json_encode($data);
    }
    
    /*
     * Função para upload de imagem em navegadores antigos
     */
    public function img_upload_antigo(){
        $this->ver_conta();
        $path = $this->uri->segment(3);
	
	// Verifica se é para a TIP ou para a CAPA
        if($path == ''){
	    $path = 'tips';
        }else{
	    $path = 'capa';
        }
	
	// Faz o upload da imagem e verifica o tamanho mínimo que é para tela retina do iphone
        $this->load->helper('file');
        $config['upload_path'] = FCPATH.$path.'/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['file_name'] = md5(date("YmdHis"));
        if($path === 'tips'){
	    $config['min_width'] = 640;
	    $config['min_height'] = 570;
        }else if($path === 'capa'){
	    $config['min_width'] = 640;
	    $config['min_height'] = 200;
        }
	
	// Inicia biblioteca de upload
        $this->load->library('upload', $config);
	
	// Verifica se fez o upload
        if($this->upload->do_upload('imagem')){
	    $file = $this->upload->data(); // Pega todos os dados da imagem como altura, largura, etc
	    
	    $data['arquivo'] = $file['file_name'];
	    $temp = 'tmp_'.$file['file_name'];
	    
	    // Prepara a imagem para aparecer na TIPS ou na CAPA.
	    // NÃO MEXER NO 2.5
	    if($path === 'tips'){
		$lar = ($file['image_width'] / 2.5);
		$alt = ($file['image_height'] / 2.5);
	    }else{
		$lar = ($file['image_width'] / 2);
		$alt = ($file['image_height'] / 2);
	    }
	    
	    // Cria a imagem temporária que será enviada para o usuário trabalhar
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

	    // Cria tag img e a ID da tag
	    if($path == 'tips'){
		$param = 'id="photo"';
	    }else{
		$param = 'id="photoc"';
	    }
	    $data['img'] = '<img data-wi="'.$lar.'" data-he="'.$alt.'" '.$param.' src="'.base_url().$path.'/'.$temp.'">';
	    
	    // Se for para a capa, já grava no banco de dados o nome da imagem
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
		$data['msg'] = 'A imagem precisa ter no mínimo 512 x 456 pixels.';
	    }else{
		$data['msg'] = 'A imagem precisa ter no mínimo 640 x 200 pixels.';
	    }
        }

        echo json_encode($data);
    }
    
    /*
     * Função para upload de imagem em navegadores antigos
     * Os comentários são os mesmos que a função anterior img_upload_antigo
     */
    public function img_upload(){
	$this->ver_conta();
	$path = $this->uri->segment(3);
	if($path == ''){
	    $path = 'tips';
	}
	$this->load->helper('file');
	$config['upload_path']   = './'.$path.'/';
	$config['allowed_types'] = 'jpg|jpeg|png';
	$config['file_name'] = md5(date("YmdHis"));
	if($path == 'tips'){
	    $config['min_width'] = 640;
	    $config['min_height'] = 570;
	}else if($path == 'capa'){
	    $config['min_width'] = 640;
	    $config['min_height'] = 200;
	}
	
	$this->load->library('upload', $config);
	
	if($this->upload->do_upload('imagem')){
	    $file = $this->upload->data();
	    
	    $temp = 'tmp_'.$file['file_name'];
	    
	    if($path == 'tips'){
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
		$ca = '';
	    }else{
		 $param = 'id="photoc"';
		 $ca = 'c';
	    }
	    $mostra_img = '<img width="'.$lar.'" height="'.$alt.'" data-wi="'.$file['image_width'].'" data-he="'.$file['image_height'].'" '.$param.' src="'.base_url().$path.'/'.$temp.'"><input type="hidden" name="foto'.$ca.'" value="'.$file['file_name'].'">';
	    
	    if($path == 'capa'){
		$input = array(
		    'img' => $file['file_name'],
		    'codigo' => $this->input->get_post('cod_count'),
		);
		$this->wdb->up_count($input);
	    }
	}else{
	    if($path == 'tips'){
		//$data['msg'] = 'A imagem precisa ter no mínimo 640 x 570 pixels.';
		$mostra_img = 'erro1';
	    }else{
		//$data['msg'] = 'A imagem precisa ter no mínimo 640 x 200 pixels.';
		$mostra_img = 'erro2';
	    }
	}
	
	echo $mostra_img;
    }
    
    /*
     * Função que grava a imagem da capa no servidor
     * Para navegadores mais antigos
     */
    public function grava_capa_antigo(){
        $this->ver_conta();
        $input = elements(array('codigo','img','central','largura','altura','posicao'), $this->input->post());
	
        $crd = explode('/',$input['posicao']);
        $posih = ($input['altura']);
        $posiw = ($input['largura']);
	
	// Verifica se a imagem temporária existe
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
		
		// Pega o tamanho da imagem no servidor
		$img = getimagesize('./capa/tmp_'.$input['img']);
		
		// Pega a largura e altura e multiplica por 2 (dobro do tamanho)
		$tmpw = ($img[0] * 2);
		$tmph = ($img[1] * 2);
		
		// Baseado na imagem temporária, faz o resize na imagem original
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
		
		// Pega a posição da imagem LEFT x TOP
		$posl = abs($crd[0]);
		$post = abs($crd[1]);
		
		// Faz o crop da imagem original
		$config = array(
		    'image_library' => 'gd2',
		    'source_image' => './capa/'.$input['img'],
		    'x_axis' => ($posl * 2),
		    'y_axis' => ($post * 2),
		    'width' => 640, //$input['largura'],
		    'height' => 200, //$input['altura'],
		    'quality' => '75',
		    'maintain_ratio' => FALSE,
		);

		$this->image_lib->initialize($config);
		$this->image_lib->crop();
		$this->image_lib->clear();

		// FAZ O CROP DA IMAGEM
	    }else{
		
		// Pega a posição da imagem
		$posil = abs($crd[0]) * 2;
		$posit = abs($crd[1]) * 2;

		if($posil < 0){
		    $posil = 0;
		}
		if($posit < 0){
		    $posit = 0;
		}
		
		// Faz o crop.
		// O -28 foi um workaround para a capa não se descolar.
		// Isso porque a imagem original tem 612 e foi setada para 640
		$config = array(
		    'image_library' => 'gd2',
		    'source_image' => './capa/'.$input['img'],
		    'x_axis' => $posil,
		    'y_axis' => $posit,
		    'width' => 640 - 28,
		    'height' => 200 - 28,
		    'quality' => '75',
		    'maintain_ratio' => FALSE,
		);

		$this->load->library('image_lib',$config);
		$this->image_lib->crop();
		$this->image_lib->clear();
	    }
	    
	    // Apaga a imagem temporária, caso existir
	    if(file_exists('./capa/tmp_'.$input['img'])){
		unlink('./capa/tmp_'.$input['img']);
	    }

	    if($this->image_lib->display_errors()){
		$resp['msg'] = $this->image_lib->display_errors();
		$resp['erro'] = 'sim';
	    }

	    $resp['msg'] = $input['img'];
	    $resp['erro'] = 'ok';
	    
        }else{
	    $resp['msg'] = 'Arquivo não encontrado';
	    $resp['erro'] = 'sim';
        }
	
        echo json_encode($resp);
    }
    
    /*
     * Função que grava a capa no servidor
     * A imagem já vem nas dimensões corretas
     * Para navegadores mais atuais
     */
    public function grava_capa(){
	$this->ver_conta();
	$input = elements(array('codigo','img'), $this->input->post());
	
	$arq = $input['img'];
	
	// Recebe a imagem do canvas
	$img = str_replace('data:image/jpeg;base64,', '', $arq);
	$img2 = str_replace(' ', '+', $img);
	$data = base64_decode($img2);
	
	// Gera o nome do arquivo
	$file = date("YmdHis")."_tip.jpg";
	
	// Grava a imagem no servidor
	file_put_contents("./capa/".$file, $data);
	
	$input['img'] = $file;
	
	// Para a imagem aparecer na capa
        $resp['msg'] = base_url().'capa/'.$input['img'];
        $resp['erro'] = 'ok';

        $ins = array(
            'img' => $input['img'],
            'codigo' => $input['codigo']
        );
        $this->wdb->up_count($ins); // Grava no banco de dados
	
	echo json_encode($resp);
    }
    
    /*
     * Função que grava a uma tip em navegadores antigos
     */
    public function grava_tip_antigo(){
        $this->ver_conta();
        $input = elements(array('id_tip','codigo','img','titulo','sub','mensagem','central','largura','altura','posicao'), $this->input->post());
	
        $crd = explode('/',$input['posicao']);
        $posih = ($input['altura']);
        $posiw = ($input['largura']);
	
	// Verifica se existe uma imagem temporária. Importante para o caso de só fazer atualização de texto na tip
        if(file_exists('./tips/tmp_'.$input['img'])){
            // FAZ O RESIZE DA IMAGEM
            if($input['central'] === 's'){
                $config = array(
                    'image_library' => 'gd2',
                    'source_image' => './tips/tmp_'.$input['img'],
                    'width' => $posiw,
                    'height' => $posih,
                    'quality' => '75',
                    'maintain_ratio' => FALSE,
                );
                $this->load->library('image_lib',$config);
                $this->image_lib->resize();
                $this->image_lib->clear();
		
		// Pega o tamanho da imagem
                $img = getimagesize('./tips/tmp_'.$input['img']);
		
		// Multiplicar por 2.5 por é o fator que foi divido para mostrar no iphone
                $tmpw = ($img[0] * 2.5);
                $tmph = ($img[1] * 2.5);
		
		// Faz o resize
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
		
		// Pega a posição da imagem
                $posl = abs($crd[0]);
                $post = abs($crd[1]);
		
		
		// Faz o crop
                $config = array(
                    'image_library' => 'gd2',
                    'source_image' => './tips/'.$input['img'],
                    'x_axis' => ($posl * 2.5),
                    'y_axis' => ($post * 2.5),
                    'width' => 640, //$input['largura'],
                    'height' => 570, //$input['altura'],
                    'quality' => '75',
                    'maintain_ratio' => FALSE,
                );

                $this->image_lib->initialize($config);
                $this->image_lib->crop();
                $this->image_lib->clear();

            // FAZ O CROP DA IMAGEM
            }else{
		
		// Pega a posição da imagem multiplicada por 2.5
                $posil = abs($crd[0]) * 2.5;
                $posit = abs($crd[1]) * 2.5;

                if($posil < 0){
                    $posil = 0;
                }
                if($posit < 0){
                    $posit = 0;
                }
		
		// Faz o crop
                $config = array(
                    'image_library' => 'gd2',
                    'source_image' => './tips/'.$input['img'],
                    'x_axis' => $posil,
                    'y_axis' => $posit,
                    'width' => 640,
                    'height' => 570,
                    'quality' => '75',
                    'maintain_ratio' => FALSE,
                );	
                $this->load->library('image_lib',$config);
                $this->image_lib->crop();
                $this->image_lib->clear();
            }
	    
	    // Cria a thumb para mostrar no mozaico
            $config['image_library'] = 'gd2';
            $config['source_image'] = './tips/'.$input['img'];
            $config['new_image'] = './tips/thumb_'.$input['img'];
            $config['maintain_ratio'] = FALSE;
            $config['width'] = 200;
            $config['height'] = 200;
            $config['quality'] = '100%';
            $this->image_lib->initialize($config);
            $this->image_lib->resize();

            $resp['altura'] = $input['altura'];
            $resp['largura'] = $input['largura'];

            if(file_exists('./tips/tmp_'.$input['img'])){
                unlink('./tips/tmp_'.$input['img']);
            }

            if($this->image_lib->display_errors()){
                $resp['msg'] = $this->image_lib->display_errors();
            }
	    $resp['imagem'] = $input['img'];
            $resp['msg'] = '';
            $resp['erro'] = 'ok';
        }else{
	    $resp['imagem'] = $input['img'];
            $resp['msg'] = '';
            $resp['erro'] = 'ok';
        }
	
	// Grava a tip no banco de dados
        $this->wdb->set_tip($input);

        echo json_encode($resp);
    }
    
    /*
     * Função que salva a tip em navegadores atuais
     */
    public function grava_tip(){
	$this->ver_conta();
	$input = elements(array('id_tip','codigo','img','titulo','sub','mensagem','central','largura','altura','posicao'), $this->input->post());
	
	// Faz a verificação se vai atualizar com a imagem
	// Importante para quando é somente atualização de texto da TIP
	if($input['img'] != 'sem'){
	    $arq = $input['img'];
	    
	    // Recebe a imagem do canvas e grava no servidor
	    // A imagem já vem nas dimensões corretas
	    $img = str_replace('data:image/jpeg;base64,', '', $arq);
	    $img2 = str_replace(' ', '+', $img);
	    $data = base64_decode($img2);
	    
	    // Gera o nome do arquivo
	    $file = date("YmdHis")."_tip.jpg";
	    
	    // Grava a imagem na pasta tips
	    file_put_contents("./tips/".$file, $data);

	    $input['img'] = $file;
	    
	    // Gera a imagem thumb que aparece no mozaico
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
    
    /*
     * Função para mostrar a data em formato d/m/Y
     */
    private function inverte_data($data){
	$nova_data = implode("-",array_reverse(explode("/",$data)));
	return $nova_data;
    }
    
    /*
     * Função que grava as datas das tips
     */
    public function gravadata(){
	$this->ver_conta();
	$input = elements(array('cd_count','dias_count','calendario'),$this->input->post());
	$c = 0;
	
	// Pega o dia de início da count
	$input['calendario'] = $this->inverte_data($input['calendario']);
	
	// Grava a data na count
	$this->wdb->set_datacount($input);
	
	// Gera as tips com as datas corretas
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
    
    /*
     * Função que altera a data das tips e da count no banco de dados
     */
    public function altdata(){
	$this->ver_conta();
	$input = elements(array('cd_count','dias_count','calendario'),$this->input->post());
	$c = 0;
	
	// Pega a nova data
	$input['calendario'] = $this->inverte_data($input['calendario']);
	
	// Grava na tabela da count
	$this->wdb->set_datacount($input);
	
	// Atualiza as datas das tips
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
    
    /*
     * Função que grava as tags da count
     */
    public function gravatag(){
	$this->ver_conta();
	$input = elements(array('tags','codigo'),$this->input->post());
	$this->wdb->set_tag($input);
    }
    
    /*
     * Função que exclui as tags de uma count
     */
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
    
    /*
     * Função para subir fotos. Não é mais utilizada
     */
    public function computador(){
	$dados = array(
	    'title' => TITLE_PAGE.' &raquo; Meu Computador',
	    'tela' => 'micro',
	);
	
	$this->load->view('up_view',$dados);
    }
    
    /*
     * Função que gera a página de invites
     * 
     */
    public function invites(){
	$this->ver_conta();
	$access = $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result_array();
	$dados = array(
	    'title' => TITLE_PAGE.' &raquo; Invites',
	    'tela' => 'convites',
	    'counts' => $this->wdb->get_counts($this->session->userdata('us_codigo'))->result(),
	    'facebook' => base_url().'facebook',
	);
	
	if(count($access) === 0){
	    $dados['appID'] = FACEBOOK_ID;
	}else{
	    $dados['appID'] = '';
	}
	
	#echo "<pre>";
	#print_r($dados);
	#die();
	    
	$this->load->view('web_view',$dados);
    }
    
    /*
     * Função para mostrar ao usuário que os invites pelo facebook foram enviados
     */
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
    
    /*
     * Função que fecha a tela do invite, caso o usuário clique no botão cancelar
     */
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
    
    /*
     * Função que envia invite para um amigo do usuário por email
     */
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
	$html .= '<p>Se você já possui o aplicativo instalado, <a href="'.APP_ITUNES.'://###til###">clique aqui</a> para abrir o '.TITLE_PAGE.' e veja seu convite.</p>';
	$html .= '<p>Nos vemos lá.</p>';
	$html .= '<h5>###nome###</h5>';
	$html .= '<br><br>';
	$html .= '<p><em>Se você não conhece ###nome###, favor ignorar essa mensagem<em></p>';
	
	$co = $this->wdb->get_count($input['count'])->result_array();
	
	foreach($arr as $valid){
	    if(valid_email($valid)){ // Faz a validação do email
		$query =  $this->wdb->get_invite($valid,$input['count'])->result_array();
		//if(count($query) == 0){
		    $this->wdb->grava_invite($valid,$input['count']); // Grava na tela de convidados
		    $idinv = $this->db->insert_id() + (1024 * 4); // Gera um número para enviar por email para evitar fraude
		    
		    // Substitui os texto entre ### pelos dados do banco de dados
		    $texto = str_replace("###count###",$co[0]['co_titulo'],$html);
		    $texto = str_replace("###til###", $co[0]['co_codigo'],$texto);
		    $texto = str_replace("###id###",$idinv,$texto);
		    $texto = str_replace("###nome###",$this->session->userdata('nomecurto'),$texto);
		    
		    // Inicializa e envia o email
		    $config['protocol']  = 'smtp';
		    $config['charset'] = 'utf8';
		    $config['wordwrap'] = TRUE;
		    $config['smtp_host'] = EMAIL_HOST;
		    $config['smtp_user'] = EMAIL_INVITE;
		    $config['smtp_pass'] = EMAIL_INVITE_SENHA;
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
	// Caso nenhum email der erro
	if(isset($em) && $em != false){
	    $this->session->set_flashdata('total','Invite enviado com sucesso para '.$c.' emails');
	}else{
	    // Caso algum email der erro
	    $saldo = count($arr) - $c;
	    $this->session->set_flashdata('total','Emails recebidos: '.count($arr).'. Enviados: '.$c.'. Inválidos: '.$saldo);
	}
	redirect('web/invites/'.$input['count']);
    }
    
    /*
     * Função para o usuário aceitar o convite por email
     */
    public function aceitarconvite(){
	$id = $this->uri->segment(3) - (1024 * 4);
	
	$this->wdb->set_convite($id);
	redirect('https://itunes.apple.com/gb/app/nivea-sun-guide/id385271083?mt=8');
    }
    
    /*
     * Função para enviar push.
     * Agora é feito usando python
     */
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
    
    /*
     * Função que é usada no cron para alterar o status da count para finalizado
     * Roda todo dia às 00:10
     */
    public function expira_count(){
	$query = $this->wdb->get_all('tbl_count')->result_array();
	
	foreach($query as $k){
	    if($k['co_data_inicio'] && date("Y-m-d", strtotime($k['co_data_inicio']. '+ '.$k['co_dias'].' days')) < date("Y-m-d")){
		$this->wdb->set_expira($k['co_codigo']);
	    }
	}
    }
}