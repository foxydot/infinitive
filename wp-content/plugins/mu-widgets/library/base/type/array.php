<?php
class bv28v_type_array extends bv28v_type_abstract implements Iterator, ArrayAccess, Serializable, Countable {
	public static function is($object) {
		return (is_object ( $object ) && __CLASS__ == get_class ( $object ));
	}
	private $convertAllTypes = null;
	public function __construct($array = null) {
		parent::__construct ();
		if (is_null ( $array )) {
			$array = array ();
		}
		$this->value ( $array );
	}
	/*
	 * allows array to be merged recursive , but unlike the php funciton will overight with later values rather than combing them.
	 * 
	 */
	
	public static function merge_replace_recursive() {
	    $params = func_get_args ();
		$arrays = array();
		foreach($params as $param)
		{
			if(is_array($param))
			{
				$arrays[]=$param;
			}
		}
		$return = array_shift($arrays);
		foreach($arrays as $array)
		{
			foreach($array as $key=>$value)
			{
				if(isset($return[$key]) && is_array($return[$key]))
				{
					$return[$key] =self::merge_replace_recursive($return[$key],(array)$value);
				}
				else
				{
					$return[$key]=$value;
				}
			}
		}
		return $return;
	}
	public function value($value = null) {
		if (! is_null ( $value )) {
			foreach ( (array)$value as $key => $avalue ) {
				if (is_array ( $avalue )) {
					$value [$key] = new self ( $avalue );
				}
			}
			parent::value ( $value );
		}
		return $this;
	}
	private function convert() {
		foreach ( $this->value as $key => $value ) {
			if (is_array ( $value )) {
				$this->value [$key] = new bv28v_type_array ( $this->application (), $value );
			}
		}
	}
	// iterator functions
	function rewind() {
		return reset ( $this->value );
	}
	function current() {
		return current ( $this->value );
	}
	function key() {
		return $this->xml_key ( key ( $this->value ) );
	}
	function next() {
		return next ( $this->value );
	}
	function valid() {
		return key ( $this->value ) !== null;
	}
	// (end)iterator functions
	// arrayaccess functions
	public function offsetSet($offset, $value) {
		$this->value [$offset] = $value;
	}
	public function offsetExists($offset) {
		return isset ( $this->value [$offset] );
	}
	public function offsetUnset($offset) {
		unset ( $this->value [$offset] );
	}
	public function offsetGet($offset) {
		return isset ( $this->value [$offset] ) ? $this->value [$offset] : null;
	}
	// (end)arrayaccess functions
	// Serializable functions
	public function serialize() {
		return serialize ( $this->value );
	}
	public function unserialize($data) {
		$this->data = unserialize ( $this->value );
	}
	public function getData() {
		return $this->data;
	}
	// (end)Serializable functions
	// Countable funcitons
	public function count() {
		return count ( $this->value );
	}
	// (end) Countable functions
	public static function xml_key($key, $array = null, $start = 0) {
		if (is_null ( $array )) {
			$colon = strrpos ( $key, ':' );
			if ($colon !== false) {
				$key = substr ( $key, 0, $colon );
			}
			return $key;
		} else {
			$return = null;
			foreach ( $array as $xml_key => $value ) {
				if (self::xml_key ( $xml_key ) == $key) {
					if ($start == 0) {
						return $xml_key;
					}
					$start --;
				}
			}
		}
		return false;
	}
/*
 * 
 * when doing a recursive merge of two arrays you have the problem that you may have a scenario where you have 2 arrays that you may want to override.
 * reduce array uses two methods.
 * 1. use the value as the key and a boolean to turn nit on and off in subsequent merged arrays. Reduce array will remove only return the keys that are marked with true.
 * 2. add a count attribute to limit the number of elements in the array, allowing merged arrays to reduce the size of the array from the original if required. It only applies to numeric keys 
 * so to add a 'safe' key just give it a not numeric key. In effect the count will give you back 'count' number of numeric fields + all the none numeric.
 * 
 */
   public static function reduce(&$array)
    {
    	if(!isset($array['count']))
    	{
			$new=array();
    		foreach($array as $key=>$value)
			{
				if($value)
				{
					$new[$key]=$key;
				}
			}
			$array=$new;
    	}
    	else
    	{
    		$cnt=0;
			foreach($array as $key=>$value)
			{
				if(is_numeric($key))
				{
					$cnt++;
					if($cnt>$array['count'])
					{
						unset($array[$key]);
					}
				}
			}
    	}
		unset($array['count']);
    }
	public static function xml_key_find($key, $array, $start = 0) {
		$return = null;
		$key = self::xml_key ( $key, $array, $start );
		if ($key !== FALSE) {
			$return = $array [$key];
		}
		return $return;
	}
	public function merge($array) {
		foreach ( $array as $key => $item ) {
			$this->array [$key] = $item;
		}
	}	
	
}