<?php
abstract class wv28v_controller_action_adminmenu extends wv28v_controller_action_abstract {
	//$menus = array ('sandbox', 'tools', 'superadmin', 'dashboard', 'posts', 'pages', 'appearance', 'comments', 'media', 'links', 'plugins', 'users', 'settings' );
	public function SupportActionMeta($return)
	{
		$return['title']='Support Forum';
		$return['url'] = "http://wordpress.org/tags/".$this->settings()->application['wpslug']."?forum_id=10";
		$return ['priority'] = 10;
		return $return;
	}
	public function SupportAction()
	{
	}
	public function PluginActionMeta($return)
	{
		$return['title']='Plugin Site';
		$return['url'] = $this->settings()->application['uri'];
		$return ['priority'] = 10;
		return $return;
	}
	public function PluginAction()
	{
	}
	public function DonateActionMeta($return)
	{
		$return['url'] = $this->settings()->application['donate_link'];
		$return ['priority'] = 10;
		return $return;
	}
	public function DonateAction()
	{
	}
	
	protected $adminMenuTitle = "";
	public function __construct($application) {
		parent::__construct ( $application );
		$this->set_type ( self::WP_DASHBOARD );
		$this->add_notices ();
	}
	public function add_notices() {
		foreach ( $this->actions () as $action ) {
			if (isset ( $action ['notice'] )) {
				add_action ( 'admin_notices', array ($this, 'doNotices' ) );
			}
		}
	}
	protected function read_notice() {
		$table = new wv28v_table_options ( 'DCoda_Notices' );
		$options = $table->get ();
		$notice = $this->notice_name ();
		if (! isset ( $options [$notice] )) {
			$options [$notice] = date ( 'Y-m-d H:i' );
			$table->set ( $options );
		}
	}
	public function clear_notice($name, $date) {
		$table = new wv28v_table_options ( 'DCoda_Notices' );
		$options = $table->get ();
		$notice = 'Settings_' . $this->settings ()->application ['name'] . '_' . $name;
		if (isset ( $options [$notice] )) {
			if ($date > $options [$notice]) {
				unset ( $options [$notice] );
				$table->set ( $options );
			}
		}
	}
	protected function notice_name() {
		$notice = '';
		if (isset ( $_GET ['page'] )) {
			$notice .= $_GET ['page'];
		}
		if (isset ( $_GET ['page2'] )) {
			$notice .= '_' . $_GET ['page2'];
		}
		return $notice;
	}
	public function doNotices() {
		foreach ( $this->actions () as $action ) {
			if (isset ( $action ['notice'] )) {
				$url = '/wp-admin/options-general.php?page=Settings_' . $this->application ()->settings ()->application ['name'] . '&page2=' . $action ['title'];
				if ($url != $_SERVER ['REQUEST_URI']) {
					$table = new wv28v_table_options ( 'DCoda_Notices' );
					$options = $table->get ();
					$notice = 'Settings_' . $this->settings ()->application ['name'] . '_' . $action ['title'];
					if (! isset ( $options [$notice] )) {
						$this->view->message = $this->settings ()->application ['name'] . ': ' . $action ['notice'] . ' <a href="' . $url . '">click here</a>.';
						$this->view->type = 'updated';
						$page = $this->RenderScript ( 'common/updated.phtml' );
						echo $page;
					}
				}
			}
		}
	}
	public function controller() {
		$args = func_get_args ();
		$this->view->_e ( call_user_func_array ( array ('parent', 'controller' ), $args ) );
	}
	protected static $sandbox_shown = false;
	public function setup() {
		$cm = $this->controller_meta ();		
		if($cm['menu']=='Sandbox' && !$this->dodebug ())
		{
			return;
		}
		global $menu;
		$page_title =  __($cm ['menu']);
		$mnu = $this->find_menu($page_title);
		//if ($cm ['title'] == $cm ['menu']) {
		$menu_title = $cm['title'];
		$capability = 'administrator';
		$function = array ($this, 'controller' );
		$menu_slug = $cm['slug'];
		if(null===$mnu)
		{
			/*
			 * positions in menu
			 * 0: $menu_title
			 * 1: $capability
			 * 2: $menu_slug
			 * 3: $page_title
			 * 4: class?
			 * 5: class?
			 * 6: icon_url
			 */
			$menu_slug = $page_title;
			$menu_title = $page_title;
			$icon_url = null;
			$position = null;
			add_menu_page ( $page_title, $menu_title, $capability, $menu_slug, $function,$icon_url,$position);
			$parent_slug =  $page_title;
		}
		else
		{
				$parent_slug = $mnu['2'];
		}
		/**
		 * position in sub menu
		 * 
		 * key: $parent_slug.
		 * 0: $menu_title
		 * 1: $capability
		 * 2: $menu_slug
		 * 3: $page_title
		 */
		//$this->debug($menu_title);
		//$menu_slug='admin.php?page=test';
		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
	}
	protected function pre_dispatch() {
		$cm = $this->controller_meta ();
		$this->view->args = array ();
		if (count ( func_get_args () ) > 0) {
			$this->view->args = func_get_args ();
		} else {
			$this->view->args [] = null;
		}
		if (isset ( $cm ['icon'] )) {
			$this->view->icon = $cm ['icon'];
		} else {
			$icons = array ('Dashboard' => 'icon-index', 'Posts' => 'icon-edit', 'Media' => 'icon-upload', 'Links' => 'icon-link-manager', 'Pages' => 'icon-edit-pages', 'Comments' => 'icon-edit-comments', 'Appearance' => 'icon-themes', 'Plugins' => 'icon-plugins', 'Users' => 'icon-users', 'Tools' => 'icon-tools', 'Settings' => 'icon-options-general' );
			if (isset ( $icons [$cm ['menu']] )) {
				$this->view->icon = $icons [$cm ['menu']];
			} else {
				$this->view->icon = $icons ['Settings'];
			}
		}
		$return = $this->render_script ( 'common/header.phtml' );
		if (null !== $return) {
			$this->view->args [0] .= $return;
		}
		$return = ($this->menu ());
		if (null !== $return) {
			$this->view->args [0] .= $return;
		}
		return $this->view->args [0];
	}
	protected function post_dispatch() {
		$this->view->args = array ();
		if (count ( func_get_args () ) > 0) {
			$this->view->args = func_get_args ();
		} else {
			$this->view->args [] = null;
		}
		$return = $this->render_script ( 'common/footer.phtml' );
		if (null !== $return) {
			$this->view->args [0] .= $return;
		}
		return $this->view->args [0];
	}
	public function menu() {
		$cm = $this->controller_meta ();
		$this->view->title = $cm ['title'];
		if($cm['title']!=$this->view->selected ['title'])
		{
			$this->view->title .= '&raquo;' . $this->view->selected ['title'];
		}
		$baseUrl =$this->dashboard_url($cm['menu'],$cm['title']);
		$this->view->items = $this->view->options;
		foreach ( $this->view->items as $key => $value ) {
			if ($value ['hide']) {
				unset ( $this->view->items [$key] );
			} elseif (! isset ( $value ['url'] )) {
				$this->view->items [$key] ['url'] = $baseUrl . '&page2=' . $value ['raw_title'];
			}
		}
		return $this->render_script ( 'common/menu.phtml' );
	}
}