<?php
class wv28v_controller_front extends bv28v_controller_front {
	public static function getInstance($application) {
		$filename = $application->filename ();
		if (! array_key_exists ( $filename, self::$_instance )) {
			self::$_instance [$filename] = new self ( $application );
			self::$_instance [$filename]->setup ();
		}
		return self::$_instance [$filename];
	}
	protected function set_dispatcher() {
		parent::set_dispatcher ( new wv28v_controller_dispatcher ( $this->application () ) );
	}
}