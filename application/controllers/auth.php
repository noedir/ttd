<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller{
    
    public function __construct() {
	parent::__construct();
	$this->load->model('web_model','wdb');
    }
    
    private function id_secret($oauth){
	
	switch ($oauth){
	    case 'instagram':
		$provider = array(
		    'id' => '4df5f47cf2fa4da98b0d0f91beb158fb',
		    'secret' => '4664a8ef7e3142e4bb12fb19fd4aa4d3',
		    'redirect' => 'http://www.tiltheday.com/auth/token',
		);
		break;
	    
	    case 'facebook':
		$provider = array(
		    'id' => '445876232159922',
		    'secret' => '4e8db7c42234a9eac60854309e35a986',
		    'redirect' => 'http://www.tiltheday.com/auth/token_facebook',
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
	
	redirect('web/tips/ret');
    }
    
    private function list_photo($url,$loc){
	$saida = '';
	$img = '';
	foreach($url as $k => $v){
	    $img = $v->images->standard_resolution->url;
	    $low = $v->images->thumbnail->url;
	    $hei = 640;
	    $wid = 640;
	    $saida .= '<div class="pfti"><img class="fti'.$loc.'" data-wi="'.$wid.'" data-he="'.$hei.'" data-local="instagram" data-alta="'.$img.'" src="'.$low.'"></div>';
	}
	return $saida;
    }
    
    private function listjson($url, $loc){
	$saida = '';
	
	foreach($url['src'] as $k){
	    $imgbaixa = str_replace("_7","_5",$k);
	    $imgalta = $k;
	    $saida .= '<div class="pfti"><img class="fti'.$loc.'" data-wi="640" data-he="640" data-local="instagram" data-alta="'.$k.'" src="'.$imgbaixa.'"></div>';
	}
	return $saida;
    }
    
    private function photo_face($url,$baixa,$hei,$wid,$loc){
	return '<div class="pfti"><img class="fti'.$loc.'" data-he="'.$hei.'" data-wi="'.$wid.'" data-local="facebook" data-alta="'.$url.'" src="'.$baixa.'"><span class="size">'.$wid.'x'.$hei.'</span></div>';
    }
    
    public function fotos_instagram(){
	$local = $this->input->post('local');
	
	$query = $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result();
	$c = 0;
	$next = '';
	$photos = '';
	
	$cache = './cachejson/instagram_'.$this->session->userdata('us_codigo').'.json';
	
	if(file_exists($cache) && filemtime($cache) > time() - 60*30){
	    // If a cache file exists, and it is newer than 1 hour, use it
	    $photos = $this->listjson(json_decode(file_get_contents($cache),true),$local);
	    
	}else{
	
	    $images[] = array();
	    
	    while($c <= 10){
		if($next == ''){
		    $url = 'https://api.instagram.com/v1/users/'.$query[0]->oa_instagram_id.'/media/recent/?access_token='.$query[0]->oa_instagram_access_token.'&count=32';
		}else{
		    $url .= '&max_id='.$next;
		}

		$get = file_get_contents($url);
		$dados = json_decode($get);
		
		foreach(json_decode($get)->data as $item){

		    $src = $item->images->standard_resolution->url;
		    $low = $item->images->thumbnail->url;
		    
		    $images['src'][] = htmlspecialchars($src);
		}
		
		file_put_contents($cache,json_encode($images)); //Save as json

		$photos .= $this->list_photo($dados->data,$local);

		if(isset(json_decode($get)->pagination->next_max_id)){
		    $next = json_decode($get)->pagination->next_max_id;
		}else{
		    break;
		}
		$c++;
	    }
	}
	
	
	echo $photos;
    }
    
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
    
    
    public function facebook(){
	$token = $this->id_secret('facebook');
	$url = 'https://www.facebook.com/dialog/oauth?client_id='.$token['id'].'&redirect_uri='.$token['redirect'].'&scope=user_photos,email,read_friendlists&display=iframe';
	
	redirect($url,'location');
    }
        
    public function token_facebook(){
	if($this->input->get('code') != ''){
	    redirect('http://www.tiltheday.com/facebook/confirm?code='.$this->input->get('code'));
	}else
	if($this->input->get('access_token') != ''){
	    redirect('http://www.tiltheday.com/facebook/confirm?access_token='.$this->input->get('access_token'));
	}else	
	if($this->input->get('request') != ''){
	    redirect(base_url().'/web/convite_enviado/'.$this->input->get('post_id'));
	}else{
	    redirect(base_url().'/web/convite_enviadonao');
	}
    }
    
    public function send_dialog_face(){
	$tk = $this->id_secret('facebook');
	$query = $this->wdb->get_oauth($this->session->userdata('us_codigo'))->result_array();
	$iduser = $this->uri->segment(3);
	$idcount = $this->uri->segment(4);
	
	$count = $this->wdb->get_count($idcount)->result_array();
	
	$this->session->set_userdata('idcount',$idcount);
	
	$txt = 'Estou te convidando para seguir meu evento '.$count[0]['co_titulo'].'. Instale o aplicativo TilTheDay no seu Iphone ou Android. Caso já possua o aplicativo, basta clicar aqui tiltheday://'.$idcount;
		
	$url = 'https://www.facebook.com/dialog/apprequests?app_id='.$tk['id'].'&link=tiltheday://'.$idcount.'&picture=http://www.tiltheday.com/img/logotipo_header.jpg&name=TilTheDay&caption=TilTheDay&message='.$txt.'&redirect_uri='.$tk['redirect'].'&to='.$iduser.'&display=iframe&access_token='.$query[0]['oa_facebook_access_token'];
	
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
	$data['link'] = 'http://www.tiltheday.com/';
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