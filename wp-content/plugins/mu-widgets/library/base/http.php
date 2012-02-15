<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class bv47v_http extends bv47v_base {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $Timeout = 60;
	public function Timeout($value = null) {
		if (! is_null ( $value )) {
			$this->Timeout = $value;
		}
		return $this->Timeout;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $Port = null;
	public function Port($value = null) {
		if (! is_null ( $value )) {
			$this->Port = $value;
		}
		return $this->Port;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $Headers = array ();
	public function addHeader($key, $value) {
		$this->Headers [strtoupper ( $key )] = $value;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function removeHeader($key) {
		unset ( $this->Headers [strtoupper ( $key )] );
	}
	public function Headers($value = null) {
		if (! is_null ( $value )) {
			$this->Headers = $value;
		}
		return $this->Headers;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function logonBasic($username, $password) {
		$this->Headers ["Authorization"] = "Basic " . base64_encode ( $username . ":" . $password );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $url = null;
	public function url($value = null) {
		if (! is_null ( $value )) {
			$this->url = $value;
		}
		return $this->url;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $method = 'GET';
	public function method($value = null) {
		if (! is_null ( $value )) {
			$this->method = $value;
		}
		return $this->method;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function display() {
		$return = $this->get ();
		foreach ( $this->returnedHeaders () as $key => $value ) {
			header ( $key . ': ' . $value );
		}
		echo $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $data = null;
	public function data($data = null) {
		if (! is_null ( $data )) {
			if (is_array ( $data )) {
				foreach ( $data as $key => $value ) {
					$data [$key] = $key . '=' . urlencode ( $value );
				}
				$data = implode ( '&', $data );
			
			}
			$this->data = $data;
		}
		return $this->data;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_user = null;
	private $_password = null;
	public function set_user($value = null) {
		$this->_user = $value;
		return $this->_user;
	}
	public function set_password($value = null) {
		$this->_password = $value;
		return $this->_password;
	}
	public function user() {
		return $this->_user;
	}
	public function password() {
		return $this->_password;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __construct($url = null) {
		parent::__construct ();
		$this->url = $url;
		$this->dummyHeaders ();
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function get() {
		return $this->__request ( $this->url, $this->method, $this->data );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function dummyHeaders() {
		// stick in defaults
		$this->Headers ['USER-AGENT'] = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3';
		$this->Headers ['ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$this->Headers ['ACCEPT-LANGUAGE'] = 'en-us,en;q=0.5';
		$this->Headers ['ACCEPT-ENCODING'] = 'gzip,deflate';
		$this->Headers ['CONNECTION'] = 'close';
		// if possible stick in values from th users browser
		foreach ( $_SERVER as $key => $value ) {
			if (strpos ( $key, '-' ) === 0) {
				switch (strtoupper ( $key )) {
					// don't use these vaules as they cause problems
					case 'HTTP-KEEP_ALIVE' :
					case 'HTTP-CONNECTION' :
					case 'HTTP-COOKIE' :
					case 'HTTP-HOST' :
					case 'HTTP-REFERER' :
						break;
					default :
						$this->Headers [substr ( $key, 0, strlen ( 'http-' ) )] = $value;
				}
			}
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function __request($Url, $Method = 'GET', $Data = null) {
		$pURL = parse_url ( $Url );
		if (empty ( $pURL ['host'] )) {
			return false;
		}
		$Host = $pURL ['host'];
		$Path = (isset ( $pURL ['path'] )) ? $pURL ['path'] : '/';
		$Method = strtoupper ( $Method );
		switch ($pURL ['scheme']) {
			case 'https' :
				$scheme = 'ssl://';
				$port = 443;
				break;
			case 'http' :
			default :
				$scheme = '';
				$port = 80;
		}
		if (is_null ( $this->Port )) {
			$this->Port = $port;
		}
		if (! $Stream = fsockopen ( $scheme . $pURL ['host'], $this->Port, $Errno, $Errstr, $this->Timeout )) {
			return false;
		}
		if ($Method == 'GET' && $Data != null) {
			$Path .= '?' . $Data;
		}
		$Request = '';
		$Request .= "$Method $Path HTTP/1.1\r\n";
		$this->addHeader ( 'HOST', $Host );
		if ($Method == 'POST') {
			$this->addHeader ( 'Content-Type', "application/x-www-form-urlencoded" );
			$this->addHeader ( 'Content-Length', strlen ( $Data ) );
		}
		$this->addHeader ( 'CONNECTION', 'close' );
		foreach ( array_keys ( ( array ) $this->Headers ) as $key ) {
			$Request .= $key . ": " . $this->Headers [$key] . "\r\n";
		}
		$Request .= "\r\n";
		$Page = $this->_request ( $Stream, $Request, $Method, $Data );
		$parts = explode ( "\r\n\r\n", $Page, 2 );
		$headers = $parts [0];
		$content = $parts [1];
		unset ( $parts );
		$this->returnedHeaders = $this->_headers ( $headers );
		$content = $this->decode_body ( $this->returnedHeaders, $content );
		$this->returnedPageRaw = $Page;
		$this->returnedPage = $content;
		return $content;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $returnedPageRaw = '';
	public function ReturnedPageRaw() {
		return $this->returnedPageRaw;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $returnedHeaders = array ();
	public function returnedHeaders() {
		return $this->returnedHeaders;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $returnedPage = "";
	public function returnedPage() {
		return $this->returnedPage;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function _request($Stream, $Request, $Method = 'GET', $Data = "") {
		fwrite ( $Stream, $Request );
		if ($Method == 'POST') {
			fputs ( $Stream, $Data );
		}
		$Page = '';
		while ( ! feof ( $Stream ) ) {
			$Page .= fread ( $Stream, 128 );
		}
		fclose ( $Stream );
		return $Page;
	}
	//--------------------------------------------------------------------------------------------------------------------
	/*
	* Headers
	* explode headers
	* @param	string	$Headers
	* @return	array
	*/
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function _headers($Headers) {
		foreach ( ( array ) explode ( "\r\n", $Headers ) as $Header ) {
			$part = explode ( ": ", $Header, 2 );
			if (count ( $part ) == 1) {
				$part = explode ( " ", $Header, 2 );
			}
			$key = $part [0];
			$value = $part [1];
			$RetVal [strtoupper ( $key )] = $value;
		}
		return $RetVal;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function transfer_encoding_chunked_decode($in) {
		$out = '';
		while ( $in != '' ) {
			$lf_pos = strpos ( $in, "\012" );
			if ($lf_pos === false) {
				$out .= $in;
				break;
			}
			$chunk_hex = trim ( substr ( $in, 0, $lf_pos ) );
			$sc_pos = strpos ( $chunk_hex, ';' );
			if ($sc_pos !== false)
				$chunk_hex = substr ( $chunk_hex, 0, $sc_pos );
			if ($chunk_hex == '') {
				$out .= substr ( $in, 0, $lf_pos );
				$in = substr ( $in, $lf_pos + 1 );
				continue;
			}
			$chunk_len = hexdec ( $chunk_hex );
			if ($chunk_len) {
				$out .= substr ( $in, $lf_pos + 1, $chunk_len );
				$in = substr ( $in, $lf_pos + 2 + $chunk_len );
			} else {
				$in = '';
			}
		}
		return $out;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/	
	private function decode_body($info, $str, $eol = "\r\n") {
		$tmp = $str;
		$add = strlen ( $eol );
		$str = '';
		if (isset ( $info ['TRANSFER-ENCODING'] ) && $info ['TRANSFER-ENCODING'] == 'chunked') {
			$tmp = $this->transfer_encoding_chunked_decode ( $tmp );
		
		//			if (function_exists ( 'http_chunked_decode' )) {
		//				return http_chunked_decode ( $str );
		//			}
		//			do {
		//				$tmp = ltrim ( $tmp );
		//				$pos = strpos ( $tmp, $eol );
		//				$len = hexdec ( substr ( $tmp, 0, $pos ) );
		//				if (isset ( $info ['CONTENT-ENCODING'] )) {
		//					$inflated = @gzinflate ( substr ( $tmp, ($pos + $add), $len ) );
		//					$str .= $inflated;
		//				} else {
		//					$str .= substr ( $tmp, ($pos + $add), $len );
		//				}
		//				$tmp = substr ( $tmp, ($len + $pos + $add) );
		//				$check = trim ( $tmp );
		//			} while ( ! empty ( $check ) );
		}
		if (isset ( $info ['CONTENT-ENCODING'] )) {
			$str = gzinflate ( substr ( $tmp, 10 ) );
		} else {
			$str = $tmp;
		}
		return $str;
	}
}