<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller{
    
    public function __construct() {
	parent::__construct();
	$this->load->helper('url');
	$this->load->helper('url_helper');
	$this->load->model('web_model','wdb');
    }
    
    private function id_secret($oauth){
	
	switch ($oauth){
	    case 'instagram':
		$provider = array(
		    'id' => '4df5f47cf2fa4da98b0d0f91beb158fb',
		    'secret' => '4664a8ef7e3142e4bb12fb19fd4aa4d3',
		    'redirect' => 'http://www.dcanm.mobi/count/auth/token',
		);
		break;
	    
	    case 'facebook':
		$provider = array(
		    'id' => '445876232159922',
		    'secret' => '4e8db7c42234a9eac60854309e35a986',
		    'redirect' => 'http://www.dcanm.mobi/count/auth/token_facebook',
		);
		break;
	}
	
	return $provider;
    }
    
    public function token(){
	
	$token = $this->id_secret('instagram');
	
	$post = array(
	    'client_id' => $token['id'],
	    'client_secret' => $token['secret'],
	    'grant_type' => 'authorization_code',
	    'redirect_uri' => $token['redirect'],
	    'code' => $this->input->get('code'),
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
	redirect('web/tips');
    }
    
    private function list_photo($url){
	$saida = '';
	$img = '';
	foreach($url as $k => $v){
	    $img = $v->images->standard_resolution->url;
	    $low = $v->images->thumbnail->url;
	    $hei = $v->images->standard_resolution->height;
	    $wid = $v->images->standard_resolution->width;
	    $saida .= '<div class="pfti"><img class="fti" data-wi="'.$wid.'" data-he="'.$hei.'" data-local="instagram" data-alta="'.$img.'" src="'.$low.'"></div>';
	}
	return $saida;
    }
    
    private function photo_face($url,$baixa,$hei,$wid){
	return '<div class="pfti"><img class="fti" data-he="'.$hei.'" data-wi="'.$wid.'" data-local="facebook" data-alta="'.$url.'" src="'.$baixa.'"><span class="size">'.$wid.'x'.$hei.'</span></div>';
    }
    
    public function fotos_instagram(){
	$query = $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result();
	$c = 0;
	$next = '';
	$photos = '';
	while($c <= 10){
	    if($next == ''){
		$url = 'https://api.instagram.com/v1/users/'.$query[0]->oa_instagram_id.'/media/recent/?access_token='.$query[0]->oa_instagram_access_token.'&count=32';
	    }else{
		$url .= '&max_id='.$next;
	    }
	
	    $get = file_get_contents($url);
	    $dados = json_decode($get);
	    
	    $photos .= $this->list_photo($dados->data);
	    
	    if(isset(json_decode($get)->pagination->next_max_id)){
		$next = json_decode($get)->pagination->next_max_id;
	    }else{
		break;
	    }
	    $c++;
	}
	
	echo $photos;
    }
    
    public function fotos_facebook(){
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
		$saida .= $this->photo_face($src->source,$low->source,$src->height,$src->width);
	    }
	}
	echo $saida;
    }
    
    
    public function facebook(){
	$token = $this->id_secret('facebook');
	$url = 'https://www.facebook.com/dialog/oauth?client_id='.$token['id'].'&redirect_uri='.$token['redirect'].'&scope=user_photos,email,read_friendlists&display=page';
	
	redirect($url,'location');
    }
        
    public function token_facebook(){
	if(isset($_GET['code']) && $_GET['code'] != ''){
	    redirect('http://www.dcanm.mobi/count/facebook/confirm?code='.$_GET['code']);
	}else
	if(isset($_GET['access_token']) && $_GET['access_token'] != ''){
	    redirect('http://www.dcanm.mobi/count/facebook/confirm?access_token='.$_GET['access_token']);
	}else	
	if(isset($_GET['post_id']) && $_GET['post_id'] != ''){
	    redirect(base_url().'/web/invites/'.$this->session->userdata('idcount').'/sim');
	}else{
	    redirect(base_url().'/web/invites/'.$this->session->userdata('idcount').'/sim');
	}
    }
    
    public function send_dialog_face(){
	$tk = $this->id_secret('facebook');
	$iduser = $this->uri->segment(3);
	$idcount = $this->uri->segment(4);
	
	$this->session->set_userdata('idcount',$idcount);
	
	$txt = 'O link para o aplicativo é tiltheday://'.$idcount;
		
	$url = 'https://www.facebook.com/dialog/feed?app_id='.$tk['id'].'&display=popup&name=People%20Argue%20Just%20to%20Win&link=http://www.dcanm.mobi/count/&picture=http://www.dcam.mobi/count/img/logotipo_header.jpg&name=TilTheDay&caption=Contagem%20Regressiva&description=fa%C3%A7a%20uma%20contagem%20dos%20seus%20eventos%20'.$txt.'&redirect_uri=http://www.dcanm.mobi/count/auth/token_facebook&to='.$iduser.'';
	
	redirect($url);
    }


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
    
    public function send_post(){
	$tk = $this->id_secret('facebook');
	$token = $this->wdb->get_oauth($this->session->userdata("us_codigo"))->result_array();
	$access = $token[0]['oa_facebook_access_token'];
	$usuario = $token[0]['oa_facebook_usuario'];
	#https://graph.facebook.com/100001933896128/feed?message=Mensagem%20da%20DCANM%20Mobil&link=http%3A%2F%2Fwww.dcanm.mobi%2Fcount
	$data['link'] = 'http://www.dcanm.mobi/count/';
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