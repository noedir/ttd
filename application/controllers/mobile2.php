<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classe do Controler do Mobile.
 * Usado para resgatar dados do Banco de Dados e transformar em json para
 * ser usado no celular.
 */

class Mobile extends CI_Controller {
    
    /*
     * Função que pré carrega as bibliotecas do CI
     */
    public function __construct() {
	parent::__construct();
	$this->load->model('mobile_model','mdb');
	$this->load->helper('array');
    }
    
    /*
     * Função utilizada para excluir email do usuário
     */
    public function deleta_email(){
	$email = $this->input->get('email');
	$this->mdb->del_usuario($email);
	$user['exclui']['status'] = '200';
	$user['exclui']['msg'] = 'Excluido com sucesso';
	
	echo json_encode($user);
    }
    
    /*
     * Função para fazer o cadastro que vem do celular.
     * Array: token_face, nome_usuario, email_usuario, tokenpush
     */
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
	// Verifica se foi enviado um email
	if($ver['email'] != ""){
	    
	    // Verifica se o email existe na base de dados
            $verify = $this->mdb->chk_usuario($ver['email'])->result();
	    $user['contagem'] = count($verify);
            if(count($verify) > 0){
                $user['status'] = '201';
                $user['msg'] = 'Email inexistente';
            }else{
		
		// Envia senha provisória para email do usuário		
		$this->load->helper('email');
		$this->load->library('email');
		
		$texto = '<html>
<head>
<title>Bem Vindo</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="650" height="517" border="0" align="center" cellpadding="0" cellspacing="0" id="Table_01">
	<tr>
		<td height="55">
			<img src="'.base_url().'email/images/email_cad_01.jpg" width="59" height="55" alt=""></td>
		<td colspan="2">
			<img src="'.base_url().'email/images/email_cad_02.jpg" alt="" width="145" height="55" border="0"></td>
		<td colspan="2">
			<img src="'.base_url().'email/images/email_cad_03.jpg" alt="" width="243" height="55" border="0"></td>
		<td colspan="2">
			<img src="'.base_url().'email/images/email_cad_04.jpg" alt="" width="144" height="55" border="0"></td>
		<td>
		  <img src="'.base_url().'email/images/email_cad_05.jpg" width="59" height="55" alt=""></td>
  </tr>
	<tr>
		<td style="background-color: #e9e9e9;" rowspan="6">&nbsp;</td>
		<td style="background-color: #fff;" rowspan="2">&nbsp;</td>
		<td height="18" colspan="4" style="background-color: #fff;">&nbsp;</td>
		<td style="background-color: #fff;" rowspan="2">&nbsp;</td>
		<td style="background-color: #e9e9e9;" rowspan="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="154" colspan="4" style="font-family: Arial, Helvetica, sans-serif; background-color: #fff;">
                <span style="font-size: 24px; font-weight: bold;">Olá '.$input['email_usuario'].',</span>
                <Br>
                <span style="font-size: 12px;">
                <strong>Seja bem vindo(a) ao '.TITLE_PAGE.'!</strong>
                <p style="font-size:11px; line-height: 12px;">
                Você acabou de se cadastrar na ferramenta de contagem que vai estabelecer o
                vínculo entre você e seu melhores momentos futuros. Aqui você terá a oportunidade de criar o conteúdo que irá motivar a sua
                ansiedade no período que antecede esta grande data.
                </p>
                <p style="font-size:11px; line-height: 12px;">
                A partir de agora você poderá criar uma contagem regressiva de
                até <strong>10 Dias</strong> para o evento ou acontecimento que você deseja.
          </p></span>
      </td>
	</tr>
	<tr>
		
	</tr>
	<tr>
		<td style="background-color: #fff;" colspan="2">&nbsp;</td>
		<td>
			<img src="'.base_url().'email/images/email_cad_14.jpg" width="242" height="48" alt=""></td>
		<td style="background-color: #fff;" colspan="3">&nbsp;</td>
	</tr>
	<tr>
		
	</tr>
	<tr>
		<td style="background-color: #fff;" height="154">&nbsp;</td>
			<td style="font-family: Arial, Helvetica, sans-serif; background-color: #fff; font-size: 11px;" colspan="4">
        
        	Nós estamos em período BETA, então se experienciar qualquer problema entre em contato conosco pelo e-mail:
		  	<br /><br /><strong style="font-size:14px;">'.EMAIL_CONTATO.'</strong>
		  	<br /><br />Teremos o prazer de fazer desta uma experiência incrível para você
		  	<br /><br />Obrigado,
	    	<br /><br />Equipe '.TITLE_PAGE.'
            
      </td>
		<td style="background-color: #fff;">&nbsp;</td>
  </tr>
	<tr>
		<td style="background-color: #e9e9e9;" colspan="8">
			<img src="'.base_url().'email/images/email_cad_20.jpg" width="650" height="53" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="'.base_url().'email/images/spacer.gif" width="59" height="1" alt=""></td>
		<td>
			<img src="'.base_url().'email/images/spacer.gif" width="27" height="1" alt=""></td>
		<td>
			<img src="'.base_url().'email/images/spacer.gif" width="118" height="1" alt=""></td>
		<td>
			<img src="'.base_url().'email/images/spacer.gif" width="242" height="1" alt=""></td>
		<td>
			<img src="'.base_url().'email/images/spacer.gif" width="1" height="1" alt=""></td>
		<td>
			<img src="'.base_url().'email/images/spacer.gif" width="116" height="1" alt=""></td>
		<td>
			<img src="'.base_url().'email/images/spacer.gif" width="28" height="1" alt=""></td>
		<td>
			<img src="'.base_url().'email/images/spacer.gif" width="59" height="1" alt=""></td>
	</tr>
</table>
</body>
</html>';
		
		$config['protocol']  = 'smtp';
		$config['charset'] = 'utf8';
		$config['wordwrap'] = TRUE;
		$config['smtp_host'] = EMAIL_HOST;
		$config['smtp_user'] = EMAIL_CONTATO;
		$config['smtp_pass'] = EMAIL_CONTATO_SENHA;
		$config['smtp_port'] = 587;
		$config['smtp_timeout'] = 20;
		$config['mailtype'] = 'html';

		$this->email->initialize($config);
		$this->email->from(EMAIL_CONTATO);
		$this->email->to($input['email_usuario']);
		$this->email->subject('Bem Vindo ao '.TITLE_PAGE);
		$this->email->message($texto);
		$em = $this->email->send();
		
		// Se usuário existir, atualiza, senão faz o cadastro
                $this->mdb->set_usuario($input);
		
		// Limpa o tokenpush baseado no email
		$this->mdb->set_cleartokenpush($tk);
		
		// Seta o novo tokenpush
		$this->mdb->set_tokenpush($tk);
		
		// Pega os dados de login
		$query = $this->mdb->get_loginuser($ver)->result_array();
		foreach($query as $v){
		    $user['login'] = $v;
		}
		
		// 's' indica os convites aceitos, então são os projetos que estão seguindo
		$segue = $this->mdb->get_convites('s',$tk)->result_array();
		
		// Pega os dados do projeto
		$meus = $this->mdb->get_meus($tk)->result_array();
		
		// Pega os convites que ainda não foram aceitos
		$conv = $this->mdb->get_convites('n',$tk)->result_array();

		// Verifica se está seguindo algum projeto, senão retorna um array vazio
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
		// Verifica se criou algum projeto, senão retorna um array vazio
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
		
		// Verifica se tem convite para algum projeto, senão retorna um array vazio
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
    
    /*
     * Função usada para fazer logout e limpar o tokenpush para não
     * receber nenhum, quando não estiver logado no projeto
     */
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
    
    /*
     * Função que faz o login e retorna os dados do usuário via json
     * @Array: email, tokenpush
     */
    public function login(){
	header("Content-type: application/json");
	$input = elements(array('email','tokenpush'),$this->input->post());
	
	// Verifica se o email foi informado
	if($input['email'] != ""){
	    
	    // Pega os dados do usuário
	    $query = $this->mdb->get_loginuser($input)->result_array();
	    
	    // se encontrado prossegue
	    if(count($query) > 0){
		
		// Limpa todos os tokens iguais
		$this->mdb->set_cleartokenpush($input);
		
		// Seta o token
		$this->mdb->set_tokenpush($input);
		foreach($query as $v){
		    $user['login'] = $v;
		}
		
		// Pega os projetos que está seguindo.
		$segue = $this->mdb->get_convites('s',$input)->result_array();
		
		// Pega os projetos criados
		$meus = $this->mdb->get_meus($input)->result_array();
		
		// Pega os convites ainda não aceitos
		$conv = $this->mdb->get_convites('n',$input)->result_array();
		
		// Caso não esteja seguingo, retorn um array vazio
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
		
		// Caso não tenha projeto criado, retorn um array vazio
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
		
		// Caso não tenha convites para aceitar, retorn um array vazio
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
    
    /*
     * Função para retornar número de dias
     */
    private function geraTimestamp($dataini,$datafim) {
	$ini = explode('-', $dataini);
	$fim = explode('-', $datafim);
	
	$vini = mktime(0, 0, 0, $ini[1], $ini[2], $ini[0]);
	$vfim = mktime(0, 0, 0, $fim[1], $fim[2], $fim[0]);
	
	$diferenca = $vfim - $vini;
	$dias = (int)floor( $diferenca / (60 * 60 * 24));
	
	return strval(abs($dias));
    }
    
    /*
     * Função que retorna todas as Counts que são públicas
     * Retorna um arquivo em json
     */
    public function count_public(){
	header("content-type: application/json");
	$input = array();
	
	// Verifica se foi feita uma busca.
	if($this->input->post('busca') != ''){
	    $input['busca'] = $this->input->post('busca');
	}else{
	    $input['busca'] = '';
	}
	
	// Verifica se foi enviado um array de counts para pesquisar
	if($this->input->post('busca_id') != ''){
	    $input['busca_id'] = $this->input->post('busca_id');
	}else{
	    $input['busca_id'] = '';
	}
	
	// Busca as Counts Públicas que não estejam finalizadas, excluídas, pagas
	// que não sejam privadas, que tenham uma capa gravada (diferente de no_image.jpg)
	$query = $this->mdb->get_countpublica($input)->result_array();
	if(count($query) > 0){
	    foreach($query as $k => $v){
		$ret['counts'][$k] = $v;
		if($ret['counts'][$k]['tags'] != ''){
		    
		    // Monta um array de tags
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
		
		// Monta os dias que a Count já começou
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
    
    /*
     * Função que retorna os dados de uma Count em json
     */
    public function list_counts(){
	header("Content-type: application/json");
	$id = $this->input->post('codigo');
	$priv = $this->input->post('priv');
	
	if($priv == ""){
	    $priv = NULL;
	}
	
	// Busca os dados da Count baseado no ID
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
    
    /*
     * Função que retorna todos os dados de uma Count e suas TIPS em json baseado no ID da Count
     */
    public function main_count(){
	header("Content-type: application/json");
	$id = elements(array('codigo','email'),$this->input->post());
	
	// Pega todas as tips da count que tenham pelo menos o título
	$query = $this->mdb->get_tipcount($id)->result_array();
	
	// Verifica se foram encontradas as tips
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
		
		// Monta o mozaico de tips (imagens)
		if($ret['count']['tips'][$k]['ti_titulo'] != ""){
		    $ret['count']['tips'][$k]['ti_imagem'] = base_url().'tips/'.$ret['count']['tips'][$k]['ti_imagem'];
		    $ret['count']['tips'][$k]['ti_thumb'] = base_url().'tips/thumb_'.$v['ti_imagem'];
		}else{
		    unset($ret['count']['tips'][$k]);
		}
		
		// Dados que não são interessantes enviar, são descartados
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
    
    /*
     * Função que retorna os dados de uma TIP baseado no código em json
     */
    public function tips(){
	header("Content-type: application/json");
	$id = elements(array('codigo'),$this->input->post());
	
	// Pega os dados da TIP
	$query = $this->mdb->get_tips($id)->result_array();
	
	// Caso exista, monta o array
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
    
    /*
     * Função que monta o menu do aplicativo
     */
    public function menu(){
	header("Content-type: application/json");
	$email = elements(array('email'),$this->input->post());
	
	// Se está seguindo algum projeto
	$segue = $this->mdb->get_convites('s',$email)->result_array();
	
	// Os projetos que criou
	$meus = $this->mdb->get_meus($email)->result_array();
	
	// Convites ainda não aceitos
	$conv = $this->mdb->get_convites('n',$email)->result_array();
	
	// Caso não esteja seguindo, gera um array vazio
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
	
	// Caso não tenha nenhum projeto, gera um array vazio
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
	
	// Caso não tenha nenhum convite, gera um array vazio
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
    
    /*
     * Função para aceitar um convite
     */
    public function seguir_count(){
	header("Content-type: application/json");
	$input = elements(array('email','id_count'),$this->input->post());
	$input['seguir'] = 's'; // Para seguir
	$input['sair'] = 'n'; // Não vai sair
	
	// Seta no banco como 's' (está seguindo)
	$this->mdb->set_seguircount($input);
	
	// Pega os nome da Count para retornar o aviso de qual count está seguindo
	$query = $this->mdb->get_count($input['id_count'])->result_array();
			
	$user['status'] = '200';
	$user['msg'] = 'Você começou a seguir a count '.$query[0]['co_titulo'];
	
	echo json_encode($user);
    }
    
    /*
     * Função para deixar de seguir uma count
     * @Array: email, id_count
     */
    public function sair_count(){
	header("Content-type: application/json");
	$input = elements(array('email','id_count'),$this->input->post());
	$input['codigo'] = $input['id_count'];
	$input['seguir'] = 'n'; // Não seguir
	$input['sair'] = 's'; // Sair
	
	// Excluir no banco o convite e dos dados
	$this->mdb->set_seguircount($input);
	
	$query = $this->mdb->get_tipcount($input)->result_array();
	
	$user['status'] = '200';
	$user['msg'] = 'Você deixou de seguir a count '.$query[0]['co_titulo'];
		
	echo json_encode($user);
    }
}