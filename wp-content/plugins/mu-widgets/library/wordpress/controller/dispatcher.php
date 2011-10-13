<?php
class wv28v_controller_dispatcher extends bv28v_controller_dispatcher {
	public function __construct($application) {
		parent::__construct ( $application );
		$this->setup ( true );
		add_action ( 'admin_menu', array ($this, 'setup' ) );
	}
	public function setup($notmenu = false) {
		$dirs = $this->application ()->loader ()->includepath ( array('controllers') );
		foreach ( $this->controllers () as $controller ) {
			$class = basename ( $controller, ".php" );
			$this->application ()->loader ()->load_class ( $class, $dirs );
			$controllerClass = new $class ( $this->application () );
			if ($notmenu) {
				switch ($controllerClass->getType ()) {
					case wv28v_controller_action_abstract::WP_FILTER :
					case wv28v_controller_action_abstract::WP_ACTION :
					case wv28v_controller_action_abstract::WP_CONTROL :
						$controllerClass->setup ();
				}
			} else {
				if ($controllerClass->getType () == wv28v_controller_action_abstract::WP_DASHBOARD) {
					$controllerClass->setup ();
				}
			}
		}
	}
}