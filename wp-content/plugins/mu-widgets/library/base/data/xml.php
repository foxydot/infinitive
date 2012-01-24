<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class bv45v_data_xml implements Iterator, ArrayAccess, Countable {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public static function reduce(&$array) {
		if (! isset ( $array ['count'] )) {
			$new = array ();
			foreach ( $array as $key => $value ) {
				if ($value) {
					$new [$key] = $key;
				}
			}
			$array = $new;
		} else {
			$cnt = 0;
			foreach ( $array as $key => $value ) {
				if (is_numeric ( $key )) {
					$cnt ++;
					if ($cnt > $array ['count']) {
						unset ( $array [$key] );
					}
				}
			}
		}
		unset ( $array ['count'] );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/	
	public static function load($file) {
		if (! file_exists ( $file )) {
			return false;
		}
		$data = file_get_contents ( $file );
		$xml_parser = xml_parser_create ();
		xml_parser_set_option ( $xml_parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option ( $xml_parser, XML_OPTION_SKIP_WHITE, 0 );
		xml_parse_into_struct ( $xml_parser, $data, $vals, $index );
		xml_parser_free ( $xml_parser );
		foreach ( self::decode ( $vals ) as $return ) {
			return $return->regular ();
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private static function decode($xml) {
		$array = new self ();
		$sub = array ();
		$complete = new self ();
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
				$complete->true_keys = true;
				foreach ( $complete as $key => $value ) {
					$data [$key] = $value;
				}
				$complete->true_keys = false;
				$array [array ($tag, $id )] = $data;
				$tag = null;
				$level = null;
				$sub = array ();
				$complete = new self ();
			} elseif ($xml_elem ['type'] == 'complete' && $xml_elem ['level'] == $level + 1) {
				if (isset ( $xml_elem ['attributes'] ['xml_key_id'] )) {
					$xml_elem ['tag'] = $xml_elem ['attributes'] ['xml_key_id'];
					unset ( $xml_elem ['attributes'] ['xml_key_id'] );
				}
				if (array_key_exists ( 'value', $xml_elem )) {
					$complete [array ($xml_elem ['tag'], $index )] = $xml_elem ['value'];
				} else {
					$complete [array ($xml_elem ['tag'], $index )] = '';
				}
				if (array_key_exists ( 'attributes', $xml_elem )) {
				}
			} else {
				$sub [$index] = $xml_elem;
			}
		}
		return $array;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $values = array ();
	private $keys = array ();
	public $true_keys = false;
	public static function is($value) {
		return (is_object ( $value ) && get_class ( $value ) == __CLASS__);
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __construct($array = null) {
		if (null !== $array) {
			foreach ( $array as $key => $value ) {
				$this [$key] = $value;
			}
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function offset($offset) {
		if (null !== $offset) {
			if (is_array ( $offset )) {
				return serialize ( $offset );
			}
			return $offset;
		}
		return null;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function rewind() {
		reset ( $this->keys );
		return reset ( $this->values );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function current() {
		return current ( $this->values );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function key() {
		if ($this->true_keys) {
			return key ( $this->keys );
		} else {
			return current ( $this->keys );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function next() {
		next ( $this->keys );
		return next ( $this->values );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function valid() {
		return key ( $this->values ) !== null;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function true_key($key) {
		if (is_string ( $key )) {
			$new_key = unserialize ( $key );
			if (false !== $new_key) {
				return $new_key;
			}
		}
		return $key;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function offsetSet($offset, $value) {
		$offset = $this->true_key ( $offset );
		$key = $offset;
		$offset = $this->offset ( $offset );
		if (is_array ( $value ) && ! $attribute) {
			$value = new self ( $value );
		}
		if (null === $offset) {
			$this->values [] = $value;
			$ak = array_keys ( $this->values );
			$offset = $ak [count ( $ak ) - 1];
			$key = $offset;
		} else {
			$this->values [$offset] = $value;
		}
		if (is_array ( $key )) {
			$key = $key [0];
		}
		$this->keys [$offset] = $key;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function offsetExists($offset) {
		$offset = $this->offset ( $offset );
		return $this->values->offsetExists ( $offset );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function offsetUnset($offset) {
		$offset = $this->offset ( $offset );
		$this->values->offsetUnset ( $offest );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function offsetGet($offset) {
		$return = null;
		if (is_array ( $offset )) {
			$offset = $this->offset ( $offset );
			$return = $this->values->offsetGet ( $offset );
		} else {
			$keys = array_keys ( $this->keys, $offset, true );
			if (count ( $keys ) == 1) {
				$return = $this->values [$keys [0]];
			} else {
				$return = new self ();
				foreach ( $keys as $key ) {
					$return [] = $this->values [$key];
				}
			}
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function count() {
		return count ( $this->values );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function regular() {
		$keys = array_unique ( $this->keys );
		$return = array ();
		foreach ( $keys as $key ) {
			$value = $this [$key];
			if (self::is ( $value )) {
				$value = $value->regular ();
			}
			$return [$key] = $value;
		}
		return $return;
	}
}