<?php
class bv28v_data_xml extends bv28v_data_abstract {
	public function load() {
		$this->array = $this->staticLoad ( $this->filename );
		return $this->array;
	}
	public function staticLoad($file) {
		$data =file_get_contents ( $this->findfile ( $file ) );
		$xml_parser = xml_parser_create ();
		xml_parser_set_option( $xml_parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option( $xml_parser, XML_OPTION_SKIP_WHITE, 0 );
		xml_parse_into_struct ( $xml_parser, $data, $vals, $index );
		xml_parser_free ( $xml_parser );
		//if($this->test)
		{
			$return=$this->decode ( $vals );
			$valid=false;
			foreach($return as $return)
			{
				$valid=true;
				break;
			}
			if($valid)
			{
				$return=$this->make_array($return);
			}
			else
			{
				throw new exception("$file is not valid XML");
			}
		}
//		else
//		{
//			$return=$this->old ( $vals );
//		}
		return $return;
	}
	public $test=false;
	private function decode($xml) {
		$array = array ();
		$sub = array ();
		$complete = array ();
		$tag = null;
		$level = null;
		$id = null;
		foreach ( $xml as $index => $xml_elem ) {
			if ($xml_elem ['type'] == 'open' && is_null ( $level ) && is_null ( $tag )) {
				$tag = $xml_elem ['tag'];
				$level = $xml_elem ['level'];
				$id = $index;
			} elseif ($xml_elem ['type'] == 'close' && $xml_elem ['level'] == $level && $xml_elem ['tag'] = $tag) {
				$data = self::decode ( $sub );
				foreach ( $complete as $key => $value ) {
					$data [$key] = $this->value($value);
				}
				$array [$tag . ':' . $id] = $data;
				$tag = null;
				$level = null;
				$sub = array ();
				$complete = array ();
			} elseif ($xml_elem ['type'] == 'complete' && $xml_elem ['level'] == $level + 1) {
				if(isset($xml_elem['attributes']['xml_key_id']))
				{
					$xml_elem['tag']=$xml_elem['attributes']['xml_key_id'];
					unset($xml_elem['attributes']['xml_key_id']);
				}
				if (array_key_exists ( 'value', $xml_elem )) {
					$complete [$xml_elem ['tag'] . ':' . $index] = $xml_elem ['value'];
				}else
				{
					$complete [$xml_elem ['tag'] . ':' . $index] = '';
				}
				if (array_key_exists ( 'attributes', $xml_elem )) {
					foreach ( $xml_elem ['attributes'] as $key => $value ) {
						$complete [$xml_elem ['tag'] . ':' . $index] [$key . ':' . $index] = $this->value($value);
					}
				}
			} else {
				$sub [$index] = $xml_elem;
			}
		}
		return $array;
	}
	public function make_array($array)
	{
		$return=array();
		foreach($array as $key=>$value)
		{
			if(is_array($value))
			{
				$value=$this->make_array($value);
			}
			$true_key=substr($key,0,strpos($key,':'));
			if(isset($return[$true_key]))
			{
				if(!is_array($return[$true_key]))
				{
					$old=$return[$true_key];
					$return[$true_key]=array();
					$return[$true_key][]=$old;
				}
				$return[$true_key][]=$value;
			}
			else
			{
				$return[$true_key]=$value;	
			}
		}
		return $return;
	}
	function value($value)
	{
/*		switch($value)
		{
			case 'null':
				$value=null;
				break;
			case 'true':
				$value=true;
				break;
			case 'false':
				$value=false;
				break;
		}
*/		return $value;
	}
}