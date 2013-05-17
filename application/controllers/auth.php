<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller{
    
    /*
     * Carrega o model.
     */
    public function __construct() {
	parent::__construct();
	$this->load->model('web_model','wdb');
    }
    
    /*
     * Método para pegar o id, secret e redirect do Instagram e do Facebook
     * 
     * @oauth String: instagram ou facebook
     * @provider Array: retorno das propriedades
     */
    private function id_secret($oauth){
	
	switch ($oauth){
	    case 'instagram':
		$provider = array(
		    'id' => INSTAGRAM_ID,
		    'secret' => INSTAGRAM_SECRET,
		    'redirect' => INSTAGRAM_REDIRECT,
		);
		break;
	    
	    case 'facebook':
		$provider = array(
		    'id' => FACEBOOK_ID,
		    'secret' => FACEBOOK_SECRET,
		    'redirect' => FACEBOOK_REDIRECT,
		);
		break;
	}
	
	return $provider;
    }
    
    /*
     * Método que faz a requisição via cUrl na api do Instagram
     * 
     * @token Array: chama o método id_secret com a propriedade requisitada
     * @post Array: array que será enviado para a api do Instagram
     * @get Json: retorno da API
     * @dados Array: tratamento da API
     * @put Array: dados que serão gravados na tabela tbl_oauth
     */
    public function token(){
	
	$token = $this->id_secret('instagram');
	
	$post = array(
	    'client_id' => $token['id'],
	    'client_secret' => $token['secret'],
	    'grant_type' => 'authorization_code',
	    'redirect_uri' => $token['redirect'],
	    'code' => $this->input->get_post('code'),
	);
	
	$access = 'https://api.instagram.com/oauth/access_token';
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $access);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, count($post));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$get = curl_exec($ch);
	curl_close ($ch);
	
	$dados = json_decode($get);
		
	$put = array(
	    'usuario' => $this->session->userdata('us_codigo'),
	    'id' => $dados->user->id,
	    'access_token' => $dados->access_token,
	    'user' => $dados->user->username,
	);
	
	$this->wdb->set_oauth_instagram($put);
	
	redirect('web/tips/ret');
    }
    
    /*
     * Método que prepara as imagens do Instagram para aparecer na tela
     * 
     * @url Array: dados
     * 
     */
    private function listjson($url, $loc){
	$saida = '';
	
	foreach($url['src'] as $k){
	    $imgbaixa = str_replace("_7","_5",$k);
	    $saida .= '<div class="pfti"><img class="fti'.$loc.'" data-wi="640" data-he="640" data-local="instagram" data-alta="'.$k.'" src="'.$imgbaixa.'"></div>';
	}
	return $saida;
    }
    
    /*
     * Função que prepara as imagens do Facebook para aparecer na tela.
     */
    private function photo_face($url,$baixa,$hei,$wid,$loc){
	return '<div class="pfti"><img class="fti'.$loc.'" data-he="'.$hei.'" data-wi="'.$wid.'" data-local="facebook" data-alta="'.$url.'" src="'.$baixa.'"><span class="size">'.$wid.'x'.$hei.'</span></div>';
    }
    
    /*
     * Função para pegar as imagens no Instagram.
     * Verifica se o arquivo instagram_X.json existe e se já faz um hora que foi criado.
     * X = código do usuário
     * 
     * Se arquivo existir e faz menos de uma hora, pega os dados, envia para a função
     * listjson e prepara para aparecer na tela.
     * 
     * se não existir ou faz mais de uma hora, cria o arquivo novamente e prepara
     * as imagens para aparecer na tela.
     */
    public function fotos_instagram(){
	$local = $this->input->post('local');
	
	// Pega os dados do Oauth no banco de dados
	$query = $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result();
	$l = 1;
	$next = '';
	$photo = '';
	
	// Monta o nome do arquivo 
	$cache = './cachejson/instagram_'.$this->session->userdata('us_codigo').'.json';
	
	if(file_exists($cache) && filemtime($cache) > (time() - (60 * 60))){
	    // If a cache file exists, and it is newer than 1 hour, use it
	    $photo = $this->listjson(json_decode(file_get_contents($cache),true),$local);
	    
	}else{
	    
	    if(file_exists($cache)){
		unlink($cache);
	    }
	
	    $images[] = array();
	    
	    /*
	     * Loop montado para pegar todos as imagen no Instagram, se houver ou não paginação no json de retorno.
	     */
	    for($i=0;$i<$l;++$i){
		if($next == ''){
		    $url = 'https://api.instagram.com/v1/users/'.$query[0]->oa_instagram_id.'/media/recent/?access_token='.$query[0]->oa_instagram_access_token.'&count=32';
		}else{
		    $url = $next;
		}

		$get = file_get_contents(urldecode($url));
		if($get !== false){
		
		    foreach(json_decode($get)->data as $item){

			$src = $item->images->standard_resolution->url;
			$low = $item->images->thumbnail->url;

			$images['src'][] = htmlspecialchars($src);
			$photo .= '<div class="pfti"><img class="fti'.$local.'" data-wi="640" data-he="640" data-local="instagram" data-alta="'.$src.'" src="'.$low.'"></div>';
		    }

		    file_put_contents($cache,json_encode($images)); //Save as json

		    if(isset(json_decode($get)->pagination->next_max_id)){
			$next = json_decode($get)->pagination->next_url;
			++$l;
		    }
		}else{
		    echo "falha";
		}
	    }
	}
	echo $photo;
    }
    
    /*
     * Pega as imagens do facebook
     */
    public function fotos_facebook(){
	$local = $this->uri->segment(3);
	$query = $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result_array();
	$saida = '';
	
	$album = $this->getpage('https://graph.facebook.com/'.$query[0]['oa_facebook_usuario'].'/albums?access_token='.$query[0]['oa_facebook_access_token']);
	
	$pg = json_decode($album)->data;
	
	foreach($pg as $al){
	    $photo = $this->getpage('https://graph.facebook.com/'.$al->id.'/photos?access_token='.$query[0]['oa_facebook_access_token']);
	    $dec = json_decode($photo)->data;
	    
	    foreach($dec as $ph){
		$src = $ph->images[0];
		$low = $ph->images[4];
		$saida .= $this->photo_face($src->source,$low->source,$src->height,$src->width,$local);
	    }
	}
	echo $saida;
    }
    
    /*
     * Faz o Oauth no facebook
     */
    public function facebook(){
	$token = $this->id_secret('facebook');
	$url = 'https://www.facebook.com/dialog/oauth?client_id='.$token['id'].'&redirect_uri='.$token['redirect'].'&scope=user_photos,email,read_friendlists&display=iframe';
	
	redirect($url,'location');
    }
    
    /*
     * Retorno de Oauth do facebook
     */
    public function token_facebook(){
	if($this->input->get('code') != ''){
		redirect('http://'.CAMINHO_SITE.'/facebook/confirm?code='.$this->input->get('code'));
	}else
	if($this->input->get('access_token') != ''){
	    redirect('http://'.CAMINHO_SITE.'/facebook/confirm?access_token='.$this->input->get('access_token'));
	}else	
	if($this->input->get('request') != ''){
	    redirect(base_url().'/web/convite_enviado/'.$this->input->get('post_id'));
	}else{
	    redirect(base_url().'/web/convite_enviadonao');
	}
    }
    
    /*
     * Texto que é enviado no invite.
     */
    public function send_dialog_face(){
	$tk = $this->id_secret('facebook');
	$query = $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result_array();
	$iduser = $this->uri->segment(3);
	$idcount = $this->uri->segment(4);
	
	$count = $this->wdb->get_count($idcount)->result_array();
	
	$this->session->set_userdata('idcount',$idcount);
	
	$txt = 'Estou te convidando para seguir meu evento '.$count[0]['co_titulo'].'. Instale o aplicativo '.TITLE_PAGE.' no seu Iphone ou Android. Caso já possua o aplicativo, basta clicar aqui '.APP_ITUNES.'://'.$idcount;
		
	$url = 'https://www.facebook.com/dialog/apprequests?app_id='.$tk['id'].'&link='.APP_ITUNES.'://'.$idcount.'&picture=http://'.CAMINHO_SITE.'/img/logotipo_header.jpg&name='.TITLE_PAGE.'&caption='.TITLE_PAGE.'&message='.$txt.'&redirect_uri='.$tk['redirect'].'&to='.$iduser.'&display=iframe&access_token='.$query[0]['oa_facebook_access_token'];
	
	redirect($url);
    }

    /*
     * Função para pegar os amigos no facebook
     * Retorna um arquivo em jSon
     */
    public function get_amigos(){
	header("content-type: application/json");
	$tkfb = $this->id_secret('facebook');
	$token = $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result_array();
	$access = $token[0]['oa_facebook_access_token'];
	$usuario = $token[0]['oa_facebook_usuario'];
	
	$pega = $this->getpage('https://graph.facebook.com/'.$usuario.'/?fields=friends.fields(first_name,link,picture)&access_token='.$access);
	$jsondec = json_decode($pega)->friends->data;
	$amigos = $jsondec;
	
	$saida = array();
	$c = 0;
	foreach($amigos as $fr){
	    $amg = (array)$fr;
	    $pict = (array)$amg['picture']->data;
	    $saida[$c]['appid'] = $tkfb['id'];
	    $saida[$c]['acctk'] = $access;
	    $saida[$c]['nome'] = $amg['first_name'];
	    $saida[$c]['link'] = $amg['link'];
	    $saida[$c]['id'] = $amg['id'];
	    $saida[$c]['pic'] = $pict['url'];
	    $c++;
	}
	sort($saida);
	
	echo json_encode($saida);
    }
    
    /*
     * Teste para envio de postagem no facebook
     */
    public function send_post(){
	$tk = $this->id_secret('facebook');
	$token = $this->wdb->get_oauth($this->session->userdata("us_codigo"))->result_array();
	$access = $token[0]['oa_facebook_access_token'];
	$usuario = $token[0]['oa_facebook_usuario'];
	#https://graph.facebook.com/100001933896128/feed?message=Mensagem%20da%20DCANM%20Mobil&link=http%3A%2F%2Fwww.dcanm.mobi%2Fcount
	$data['link'] = 'http://'.CAMINHO_SITE.'/';
	$data['message'] = 'Essa é uma mensagem de teste';
	$data['caption'] = 'Caption';
	$data['description'] = 'Description';
	$data['access_token'] = $access;
	
	$post_url = 'https://graph.facebook.com/100000174844022/feed';
	
	$manda = $this->sendpage($post_url,$data);
	
	print_r($manda);
    }
    
    private function sendpage($url,$post){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$return = curl_exec($ch);
	curl_close($ch);
	
	return $return;
    }
    
    /*
     * Usado para obter o json após o Oauth
     */
    private function getpage($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // check if it returns 200, or else return false
        if ($http_code === 200){
            curl_close($ch);
            return $return;
        }else{
            // store the error. I may want to return this instead of FALSE later
            $error = curl_error($ch);
            curl_close($ch);
            return FALSE;
        }
    }
}