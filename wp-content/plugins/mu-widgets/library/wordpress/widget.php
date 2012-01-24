<?php
abstract class wv44v_widget extends WP_Widget {
	private $_application;
	public $view;
	private $action;
	private static $application = null;
	public static function register_widgets($widgets, $application) {
		throw new exception ( 'register_widgets is not 5.2 complient. Do them individually' );
		foreach ( ( array ) $widgets as $widget ) {
			// just uncommenting the next line will break the plugin in 5.2
			// register all widgets invidulaly.
			//$widget::$application = $application;
			register_widget ( $widget );
		}
	}
	public function init(&$application) {
		$this->_application = $application;
		$this->action = new wv44v_action ( $application );
		$this->view = &$this->action->view;
	}
	public function application() {
		return $this->_application;
	}
	public function settings() {
		return $this->application ()->data ()->data ();
	}
	public function render_script($script, $html = true) {
		//return 'test';
		return $this->action->render_script ( $script, $html );
	}
}
	