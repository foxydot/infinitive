<?php
class bv28v_controller_front extends bv28v_base {
	protected $_dispatcher = null;
	public function get_dispatcher() {
		$this->set_dispatcher ();
		return $this->_dispatcher;
	}
	protected static $_instance = array ();
	public static function getInstance($application) {
		$filename = $application->filename ();
		if (! array_key_exists ( $filename, self::$_instance )) {
			self::$_instance [$filename] = new self ( $application );
			self::$_instance [$filename]->setup ();
		}
		return self::$_instance [$filename];
	}
	protected function set_dispatcher($dispatcher = null) {
		if (null === $this->_dispatcher) {
			if (null === $dispatcher) {
				$this->_dispatcher = new bv28v_controller_dispatcher ( $this->application () );
			} else {
				$this->_dispatcher = $dispatcher;
			}
		}
	}
	public function setup() {
		$this->set_dispatcher ();
	}
}