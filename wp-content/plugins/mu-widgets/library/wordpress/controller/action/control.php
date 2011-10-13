<?php
abstract class wv28v_controller_action_control extends wv28v_controller_action_abstract {
	protected function selected_action() {
		return $this->selected_action_page ();
	}
	public function __construct($application) {
		$this->set_type ( self::WP_CONTROL );
		parent::__construct ( $application );
	}
	public function controller() {
		$args = func_get_args ();
		$page = call_user_func_array ( array ('parent', 'controller' ), $args );
		$this->view->_e ( $page );
	}
	public function setup() {
		add_action ( 'init', array ($this, 'init' ) );
		add_action ( 'generate_rewrite_rules', array ($this, 'generate_rewrite_rules' ) );
		add_filter ( 'query_vars', array ($this, 'query_vars' ) );
		add_filter ( 'template_redirect', array ($this, 'template_redirect' ) );
	}
	public function template_redirect() {
		global $wp_query;
		if ($wp_query->get ( 'view' )) {
			$cm=$this->controller_meta();
			if ($this->get_controller () == $cm['slug']) {
				if (null !== $this->selected_action ()) {
					$this->controller ();
					die ();
				}
			}
		}
	}
	public function query_vars($qvars) {
		$qvars [] = 'view';
		return $qvars;
	}
	public function init() {
		global $wp_rewrite;
		if (get_option ( 'permalink_structure' ) != '') {
			if (! in_array ( 'index.php?view=' . $this->get_page (), $wp_rewrite->wp_rewrite_rules () )) {
				$wp_rewrite->flush_rules ();
			}
		}
	}
	private function get_page() {
		$cm = $this->controller_meta();
		$class = $cm['slug'];
		return $class;
	}
	public function get_controller() {
		global $wp_query;
		$split = explode ( '/', $wp_query->get ( 'view' ) );
		$control = $split [0];
		return $control;
	}
	public function generate_rewrite_rules($wp_rewrite) {
		$class = $this->get_page ();
		$new_rules = array ();
		$new_rules [$class] = 'index.php?view=' . $class;
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}
}