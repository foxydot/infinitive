<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class bv45v_mail extends bv45v_base  {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	//--- ishtml
	private $_ishtml = false;
	protected function ishtml()
	{
		return $this->_ishtml;
	}
	protected function set_ishtml($ishtml)
	{
		$this->_ishtml =  $ishtml;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	//-- constructor
	public function __construct($ishtml = false)
	{
		parent::__construct();
		$this->set_ishtml($ishtml);
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	//-- headers
	private $_headers = null;
	protected function headers()
	{
		$this->set_headers();
		return $this->_headers;
	}
	protected function set_headers()
	{
		if(null === $this->_headers)
		{
			$this->_headers = "MIME-Version: 1.0\r\n";
			if($this->ishtml())
			{
				$this->_headers .= "Content-Type: text/html;\r\n";
			}
			else
			{
				$this->_headers .= "Content-Type: text/plain;\r\n";
			}
			if(null!==$this->from)
			{
				$this->_headers .= "From: ".$this->from."\r\n";
			}
			$this->_headers.=$this->headercharset();
		}
	}
	protected function headercharset()
	{
		return "";
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	//---
	protected function sendit($to, $subject, $message, $headers=null)
	{
		mail($to, $subject, $message, $headers);
	}
	private $from = null;
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function send($to,$subject,$message,$from=null)
	{
		$this->from = $from;
		$this->sendit($to, $subject, $message, $this->headers());
	}

}