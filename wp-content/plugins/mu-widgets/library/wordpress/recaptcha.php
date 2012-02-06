<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv46v_recaptcha extends bv46v_action {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $API_SERVER = "http://www.google.com/recaptcha/api";
	private $API_SECURE_SERVER = "https://www.google.com/recaptcha/api";
	private $VERIFY_SERVER = "http://www.google.com";
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function signup_url() {
		$http = new bv46v_http();
		return "https://www.google.com/recaptcha/admin/create?" . $http->data ( array ('domains' => get_option ( 'home' ), 'app' => $this->application ()->name ) );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function get_html() {
		$this->view->keys = $this->data('_keys')->recaptcha_keys;
		$this->view->pubkey = $this->view->keys ['public'];
		if ($this->view->pubkey === null || $this->view->pubkey == '') {
			echo 'here';
			return '';
		}
		$this->view->server = $this->API_SERVER;
		$page = $this->render_script('recaptcha/form.phtml');
		return $page;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private static $is_valid_cache = array();
	private $is_valid = null;
	public function is_valid($challenge, $response)
	{
		if(!isset(wv46v_recaptcha::$is_valid_cache[$challenge]))
		{
			$returned = $this->check_answer ( $_POST ['recaptcha_challenge_field'], $_POST ['recaptcha_response_field'] );
			wv46v_recaptcha::$is_valid_cache[$challenge] = $returned->is_valid;
		}
		return wv46v_recaptcha::$is_valid_cache[$challenge];
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function check_answer($challenge, $response) {
		$keys = $this->data('_keys')->recaptcha_keys;
		$privkey = $keys ['private'];
		$remoteip = $_SERVER ['REMOTE_ADDR'];
		//discard spam submissions
		if ($challenge == null || strlen ( $challenge ) == 0 || $response == null || strlen ( $response ) == 0) {
			$this->is_valid = false;
			$this->error = 'incorrect-captcha-sol';
			return $this;
		}
		$args = array();
		$args['method']='POST';
		$args['user-agent']=$this->application ()->name . '/' . $this->application ()->version;
		$args['body']=array ('privatekey' => $privkey, 'remoteip' => $remoteip, 'challenge' => $challenge, 'response' => $response );
		$url = $this->VERIFY_SERVER. '/recaptcha/api/verify';	
		$return = wp_remote_get($url,$args);
		if(!is_wp_error($return))
		{
			$answers = explode ( "\n", $return['body']  );
			$this->is_valid = (trim ( $answers [0] ) == 'true');
		}
		return $this;
	}
}