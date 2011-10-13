<?php
abstract class wv28v_controller_action_action extends wv28v_controller_action_abstract {
	public function admin_headAction() {
		$this->wp_headAction ();
	}
	public function wp_headAction() {
		$this->view->url = $this->url ( 'public/style.css' );
		$this->view->_e ( $this->render_script ( 'common/head.phtml' ) );
		$this->view->url = $this->url ( 'public/wp-style.css' );
		$this->view->_e ( $this->render_script ( 'common/head.phtml' ) );
		$this->view->url = $this->url ( 'public/' . $this->application ()->settings ()->application['slug'] . '.css' );
		if ($this->view->url !== false) {
			echo $this->render_script ( 'common/head.phtml' ) ;
		}
	}
	protected function url($file) {
		$return = false;
		$file = $this->application ()->loader ()->find_file ( $file, true );
		if ($file !== false) {
			$return = wv28v_values::urlFromFileame ( $file );
		}
		return $return;
	}
	public function __construct($application) {
		$this->set_type ( self::WP_ACTION );
		parent::__construct ( $application );
	}
	public function setup() {
		foreach ( ( array ) $this->actions () as $action ) {
			add_action ( $action ['raw_title'], array ($this, "controller" ), $action ['priority'] );
		}
	}
}