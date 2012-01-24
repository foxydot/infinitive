<?php
class bv44v_data_legacy extends bv44v_base {
//	public function scope($true = true) {
//		$return = $this->_scope;
//		if (! $true && null === $return) {
//			$return = 'default';
//		}
//		return $return;
//	}
//	public function set_scope($scope = null) {
//		$this->_scope = $scope;
//	}
//	public function xml_scopes() {
//		if (isset ( $this->_xml [null] ['alternate'] )) {
//			$return = array ();
//			if (is_array ( $this->_xml [null] ['alternate'] )) {
//				$return = array_keys ( $this->_xml [null] ['alternate'] );
//			}
//			if (! in_array ( 'default', $return )) {
//				$return [] = 'default';
//			}
//			natsort ( $return );
//			return $return;
//		}
//		return array ();
//	}
//	public function scopes() {
//		return $this->xml_scopes ();
//	}
//	protected function legacy($data) {
//		return $data;
//	}
//	public function refresh() {
//		$this->all ();
//	}
//	
//	public function __get($key) {
//		if (isset ( $this->_config->$key )) {
//			return $this->_config->$key;
//		}
//		$method = '_' . $key;
//		if (method_exists ( $this, $method )) {
//			return $this->$method ();
//		}
//		$this->get_xml ( $key, $this->_scope );
//		if (isset ( $this->_xml [$this->_scope] [$key] )) {
//			return $this->_xml [$this->_scope] [$key];
//		}
//		return null;
//	}
//	private $global = array ();
//	public function __set($key, $value) {
//		$method = '_' . $key;
//		if (method_exists ( $this, $method )) {
//			$this->$method ( $value );
//			return;
//		}
//	}
//	public function __isset($key) {
//		$method = '_' . $key;
//		if (method_exists ( $this, $method )) {
//			return true;
//		}
//		$this->get_xml ( $key, $this->_scope );
//		return isset ( $this->_xml [$this->_scope] [$key] );
//	}
//	public function __unset($key) {
//		if (isset ( $this->_config->$key )) {
//			unset ( $this->_config->$key );
//		}
//	}
}