<?php
class bv28v_view extends bv28v_base {
	private $_action=null;
	public function action($action=null)
	{
		if(!is_null($action))
		{
			$this->_action = $action;
		}
		return $this->_action;
	}
	public function disabled($value)
	{
		$return = "";
		if($value)
		{
			$return = " disabled ";
		}
		echo $return;
	}
	public function get_url_parts($url=null)
	{
		if(null===$url)
		{
			$url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}
		$url=explode('//',$url);
		$url=$url[1];
		$ru = explode('?',$url);
		if(count($ru)==1)
		{
			$ru[1]=array();
		}
		$uri=array();
		$uri['url']=explode('/',$ru[0]);
		$uri['query']=array();
		foreach(explode('&',$ru[1]) as $query)
		{
			$parts = explode('=',$query);
			$uri['query'][$parts[0]]=$parts[1];
		}
		return $uri;
	}
	public function url($url_parts=null)
	{
		if(null===$url_parts)
		{
			$url_parts=$this->get_url_parts();
		}
		$url='http://';
		$url.=implode('/',$url_parts['url']);
		if(count($url_parts['query'])>0)
		{
			foreach($url_parts['query'] as $key=>$value)
			{
				$url_parts['query'][$key]=$key.'='.$value;
			}
			$url.='?'.implode('&',$url_parts['query']);
		}
		return $url;
	}
	public function __call($name, $arguments) {
		if(method_exists($this->action(),$name))
		{
			$return = null;
			switch(count($arguments))
			{
				case 0:
					$return = $this->action()->$name();
					break;
				case 1:
					$return = call_user_func(array($this->action(),$name),$arguments[0]);
					break;
				case 2:
					$return = call_user_func(array($this->action(),$name),$arguments[0],$arguments[1]);
					break;
			}
			return $return;
		}
		else
		{
			throw new exception($name.' not found');
		}
    }
	
	public function selected($value, $check) {
		echo $this->checksel ( $value, $check, 'selected' );
	}
	public function checked($value, $check) {
		echo $this->checksel ( $value, $check, 'checked' );
	}
	private function checksel($value, $check, $output) {
		$selected = '';
		if (strtolower ( $value ) == strtolower ( $check )) {
			$selected = " $output ";
		
		}
		return $selected;
	}
	private $_alternate = false;
	public function alternate($reset = false) {
		if ($reset) {
			$this->_alternate = false;
		}
		$this->_alternate = ! $this->_alternate;
		if ($this->_alternate) {
			return ' alternate ';
		}
		return '';
	}
	function __($value) {
		return $value;
	}
	function _e($value) {
		echo $this->__ ( $value );
	}
	public function render_string($string)
	{
		ob_start ();
		eval ('?>'.$string);
		$return = ob_get_contents ();
		ob_end_clean ();
		return $return;
	}
	public function make_opt_array($array,$sep='/',$base='')
	{
		$return = array();
		foreach((array)$array as $value)
		{
			$key=$base.$value;
			if(!empty($sep) && strpos($value,$sep)!==false)
			{
				$value=explode($sep,$value,2);
				$key=$base.$value[0];
				$value=$this->make_opt_array($value[1],$sep,$key.$sep);
			}
			if(isset($return[$key]))
			{
				$return[$key]=array_merge_recursive((array)$return[$key],(array)$value);
			}
			else
			{
				$return[$key]=$value;
			}
		}
		return $return;
	}
	public function options($array,$post='',$sep='/',$depth=0)
	{
		$return = '';
		$lspaces=str_pad('',($depth)*4*6,'&nbsp;');
		$ospaces=str_pad('',($depth-1)*4*6,'&nbsp;');
		$depth++;
		
		// removed tabs as problems with convertsion in wp
		//$tabs=str_pad('',$depth,"\t");
		$tabs='';
		foreach((array)$array as $key=>$value)
		{
			if(is_array($value))
			{
				$label=$key;
				if(!empty($sep))
				{
					$label=explode($sep,$key);
					$label=$label[count($label)-1];
				}
				$return.="{$tabs}<optgroup label='{$lspaces}{$label}'>";
				$return.=$this->options($value,$post,$sep,$depth);
				$return.="{$tabs}</optgroup>";
			}
			else
			{
				$selected = $this->checksel($key,$post,'selected');
				$return.="{$tabs}<option {$selected} value='{$key}'>{$ospaces}{$value}</option>";
			}
		}
		return $return;
	}
	public function post_array($array)
	{
		$key='';
		$first=true;
		foreach($array as $value)
		{
			if($first)
			{
				$key=$value;
				$first=false;
			}
			else
			{	$key.="[{$value}]";
			}	
		}
		return $key;	
	}	
	public function render($filename) {
		$page = "";
		if (! file_exists ( $filename )) {
			// reverse the directory order, in the case of view files allow files to be overriden
			$dirs = $this->application ()->loader ()->includepath ( array('views'), true );
			$filename = $this->application ()->loader ()->find_file ( $filename, true, $dirs );
		}
		if ($filename !== false) {
			ob_start ();
			require $filename;
			$page = ob_get_contents ();
			ob_end_clean ();
			return $page;
		}
		return null;
	}
}