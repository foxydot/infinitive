<?php
class wv44v_view extends bv44v_view {
	public function _e($text) {
		_e ( $text, $this->domain );
	}
	public function __($text) {
		return __ ( $text, $this->domain );
	}
	protected $domain = null;
	public function __construct(&$application) {
		$this->domain = get_class ( $application );
		parent::__construct ( $application );
	}
	public function info() {
		return $this->application ()->info ();
	}
}