<?php
abstract class wv28v_controller_action_abstract extends bv28v_controller_action {
	protected function find_menu($title) {
		global $menu;
		$selected = null;
		foreach ( $menu as $mnu ) {
			if ($title == $mnu [0]) {
				$selected = $mnu;
				break;
			}
		}
		return $selected;
	}
	protected function find_submenu($menu, $title) {
		global $submenu;
		$selected = null;
		if (! is_array ( $menu )) {
			$menu = $this->find_menu ( $menu );
		}
		if (isset ( $menu ['2'] )) {
			$menu_slug = $menu ['2'];
			if (isset ( $submenu [$menu_slug] )) {
				foreach ( $submenu [$menu_slug] as $mnu ) {
					if ($title == $mnu [0]) {
						$selected = $mnu;
						$selected['menu']=$menu;
						break;
					}
				}
			}
		}
		return $selected;
	}
	public function dashboard_url($menu,$submenu)
	{
		$menu = $this->find_submenu($menu,$submenu);
		$menu_a = explode('?',$menu[2]);
		if(strrpos($menu_a[0],'.php')===4)
		{
			$return = $menu[2];
		}
		else
		{
			$p_menu_a = explode('?',$menu['menu'][2]);
			if(strrpos($p_menu_a[0],'.php')===4)
			{
				$return = $menu['menu'][2];
				if(count($p_menu_a)==1)
				{
					$return .= '?';
				}
				else
				{
					$return .='&';
				}
				$return .= 'page='.$menu[2];
			}
			else
			{
				$return='admin.php?page='.$menu[2];
			}
		}
		return $return;
	}
	protected function controller_meta() {
		$return = parent::controller_meta ();
		$settings = $this->settings ();
		if (null !== $settings) {
			$return ['title'] = $settings->application ['name'];
		}
		$return ['menu'] = 'Settings';
		return $return;
	}
	protected function basic_auth() {
		$credentials = array ();
		if (array_key_exists ( 'PHP_AUTH_USER', $_SERVER ) && array_key_exists ( 'PHP_AUTH_PW', $_SERVER )) {
			$credentials ['user_login'] = $_SERVER ['PHP_AUTH_USER'];
			$credentials ['user_password'] = $_SERVER ['PHP_AUTH_PW'];
		}
		$user = wp_signon ( $credentials );
		if (is_wp_error ( $user )) {
			header ( 'WWW-Authenticate: Basic realm="' . $_SERVER ['SERVER_NAME'] . '"' );
			header ( 'HTTP/1.0 401 Unauthorized' );
			die ();
		}
	}
	const WP_FILTER = 2;
	const WP_ACTION = 4;
	const WP_CONTROL = 8;
	const WP_DASHBOARD = 16;
	protected function set_view() {
		$this->view = new wv28v_view ( $this->application () );
	}
	public function controller() {
		$this->view->title = $this->title;
		$this->view->options = $this->actions ();
		$this->view->selected = $this->selected_action ();
		$args = func_get_args ();
		return call_user_func_array ( array ('parent', 'controller' ), $args );
	}
	protected function selected_action_wp() {
		$filter = explode ( '_', current_filter () );
		if (in_array('page',$filter)) {
			$pages = $this->subpages ();
			if (empty ( $pages ['page2'] )) {
				foreach ( $this->actions () as $r ) {
					return $r;
				}
			} else {
				foreach ( $this->actions () as $r ) {
					if ($pages ['page2'] == $r ['raw_title']) {
						return $r;
					}
				}
			}
		} else {
			foreach ( ( array ) $this->actions () as $action ) {
				if (strpos ( $action ['raw_title'], current_filter () ) === 0) {
					return $action;
				}
			}
		}
		return null;
	}
	public function control_url($page) {
		$return = rtrim ( get_option ( 'siteurl' ), '/' );
		$page = ltrim ( $page, '/' );
		if (get_option ( 'permalink_structure' ) == '') {
			$return .= '/index.php?view=';
		} else {
			$return .= '/';
		}
		$return .= $page;
		return $return;
	}
	protected function selected_action() {
		return $this->selected_action_wp ();
	}
	
	protected function dispatch() {
		$this->view->args = array ();
		if (count ( func_get_args () ) > 0) {
			$this->view->args = func_get_args ();
		} else {
			$this->view->args [] = null;
		}
		if (is_array ( $this->view->selected )) {
			$args = $this->view->args;
			if ($this->view->selected ['action'] == 'VirtualAction') {
				array_unshift ( $args, $this->view->selected ['raw_title'] );
			}
			$return = call_user_func_array ( array ($this, $this->view->selected ['action'] ), $args );
			if (null !== $return) {
				$this->view->args [0] = $return;
			}
		}
		$return = $this->render_script ( $this->view->selected ['raw_title'] . '.phtml' );
		if (null !== $return) {
			$this->view->args [0] .= $return;
		}
		return $this->view->args [0];
	}
	protected function subpages() {
		$pages = array ();
		foreach ( ( array ) $_GET as $key => $value ) {
			if (bv28v_type_string::staticStartsWith ( $key, 'page' )) {
				$pages [$key] = $value;
			}
		}
		ksort ( $pages );
		return $pages;
	}
}