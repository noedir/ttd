<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:  Simple Facebook Codeigniter Login
 *
 * Author: Terry Matula
 *         terrymatula@gmail.com
 *         @terrymatula
 
 * Created:  03.31.2011
 *
 * Description:  An easy way to use Facebook to login
 *
 * Requirements: PHP5 or above
 *
 */
class Facebook extends CI_Controller {
 
    public $appid;
    public $apisecret;
 
    public function __construct(){
        parent::__construct();
	$this->load->helper('url');
        // replace these with Application ID and Application Secret.
        $this->appid = '445876232159922';
        $this->apisecret = '4e8db7c42234a9eac60854309e35a986';
	
	$this->load->model('web_model','wdb');
    }
 
    /**
     * if you have a Facebook login button on your site, link it here
     */
    public function index(){
	$this->session->set_userdata('return',$this->input->get('inv'));
        // set the page you want Facebook to send the user back to
        $callback = 'http://www.tiltheday.com/auth/token_facebook'; //base_url().'auth/token_facebook';
        // create the FB auth url to redirect the user to. 'scope' is
        // a comma sep list of the permissions you want. then direct them to it
        $url = "https://graph.facebook.com/oauth/authorize?client_id={$this->appid}&redirect_uri={$callback}&scope=email,friends_about_me,user_photos,publish_stream";
        redirect($url);
    }
 
    /**
     * Get tokens from FB then exchanges them for the User login tokens
     */
    public function confirm(){
        // get the code from the querystring
        $redirect = 'http://www.tiltheday.com/auth/token_facebook';
        $code = $this->input->get('code');
        if ($code){
            // now to get the auth token. '__getpage' is just a CURL method
            $gettoken = "https://graph.facebook.com/oauth/access_token?client_id={$this->appid}&redirect_uri={$redirect}&client_secret={$this->apisecret}&code={$code}";
            $return = $this->__getpage($gettoken);
            // if CURL didn't return a valid 200 http code, die
            if (!$return){
                die('Error getting token');
	    }
            // put the token into the $access_token variable
            parse_str($return);
            // now you can save the token to a database, and use it to access the user's graph
            // for example, this will return all their basic info.  check the FB Dev docs for more.
            $infourl = "https://graph.facebook.com/me?access_token=$access_token";
            $return = $this->__getpage($infourl);
            if (!$return)
                die('Error getting info');
            $info = json_decode($return);
	    
	    $this->wdb->set_faceoauth($info->id,$access_token);
        }
	redirect($this->session->userdata('return'));
    }
 
    /**
     * CURL method to interface with FB API
     * @param string $url
     * @return json
     */
    private function __getpage($url){
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