<?php
abstract class bv28v_data_abstract extends bv28v_type_array {
	abstract public function staticLoad($file);
	abstract public function load();
	protected function findfile($file) {
		return $this->application ()->loader ()->find_file ( $file );
	}
	protected $filename = "";
	public function getArray() {
		$return = array ();
		foreach ( $this as $key => $value ) {
			$return [$key] = $value;
		}
		return $return;
	}
	public function __construct($application, $file, $array = null) {
		parent::__construct ( $array );
		$this->set_application ( $application );
		$this->filename = $file;
		$this->load ();
	}
}