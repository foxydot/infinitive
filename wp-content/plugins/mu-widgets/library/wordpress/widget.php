<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
abstract class wv45v_widget extends WP_Widget {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_application;
	public $view;
	private $action;
	private static $application = null;
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public static function register_widgets($widgets, $application) {
		throw new exception ( 'register_widgets is not 5.2 complient. Do them individually' );
		foreach ( ( array ) $widgets as $widget ) {
			// just uncommenting the next line will break the plugin in 5.2
			// register all widgets invidulaly.
			//$widget::$application = $application;
			register_widget ( $widget );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function init(&$application) {
		$this->_application = $application;
		$this->action = new wv45v_action ( $application );
		$this->view = &$this->action->view;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function application() {
		return $this->_application;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function settings() {
		return $this->application ()->data ()->data ();
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function render_script($script, $html = true) {
		//return 'test';
		return $this->action->render_script ( $script, $html );
	}
}