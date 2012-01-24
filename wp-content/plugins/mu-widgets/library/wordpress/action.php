<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv45v_action extends bv45v_action {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
/***************************************************************************************
* default sub menu items
***************************************************************************************/
	private $show_default_items = true;
	public function show_default_items($show=null)
	{
		if(null!==$show)
		{
			$this->show_default_items=$show;
		}
		return $this->show_default_items;
	}
/*****************************************************************************************
* deschedule any action that are schedule to run, call on deactivation
*****************************************************************************************/
	public function deschedule()
	{
		foreach($this->get_actions('wpaction') as $action)
		{
			if(null!==$action['schedule'])
			{
				if(wp_next_scheduled($action ['raw_action_title'])!==false)
				{
					wp_clear_scheduled_hook($action ['raw_action_title']);
				}
			}
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function getting_startedActionMeta($return) {
		if ($this->help('gettingstarted')->url()=="#" || !$this->show_default_items()) {
			$return ['hide'] = 1;
		} else {
			$return ['title'] = 'Help';
			$return ['link_name'] = 'Getting Started';
			$return ['url'] = $this->help('gettingstarted')->url();
			$return ['classes'] [] = 'v45v_16x16_info';
			$return ['priority'] = 2;
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function multisiteActionMeta($return) {
		if(is_multisite() && $this->application()->multisite)
		{
			$return ['link_name'] = $return ['title'];
			$return ['classes'] [] = 'v45v_16x16_multisite';
			$return ['priority'] = 1;
		}
		else
		{
			$return['hide']=true;
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function multisiteAction()
	{
		if(!isset($this->view->rows))
		{
			$this->view->rows = array();
		}
		$this->view->title = $this->help('multisite')->render('Multisite Settings');
		$current_user = wp_get_current_user();
		$this->view->blogs = get_blogs_of_user($current_user->ID);
		$this->view->column_count=2;
		$this->view->multisite = $this->data()->post('multisite');
		$row = $this->render_script('dashboard/multisite.phtml',false);
		array_unshift($this->view->rows,$row);
		$page = $this->render_table();
		return $page;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function render_table()
	{
		if(!isset($this->view->title))
		{
			$this->view->title='';
		}
		if(!isset($this->view->rows))
		{
			$this->view->rows=array();
		}
		if(!isset($this->view->columns))
		{
			$this->view->columns='';
		}
		if(!isset($this->view->footer))
		{
			$this->view->footer='';
		}
		if(!isset($this->view->column_count))
		{
			$this->view->column_count=1;
		}
		if(!isset($this->view->table_type))
		{
			$this->view->table_type='standard';
		}
		if(!isset($this->view->apply))
		{
			$this->view->apply='Save Changes';
		}
		if(!isset($this->view->return_url))
		{
			$this->view->return_url=null;
		}
		$page = $this->render_script('dashboard/table.phtml',false);
		return $page;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function PluginActionMeta($return) {
		if (! isset ( $this->application ()->wordpress->uri ) || !$this->show_default_items()) {
			$return ['hide'] = 1;
		} else {
			$return ['probono'] = true;
			$return ['title'] = 'Plugin Site';
			$return ['url'] = $this->application ()->wordpress->uri;
			$return ['classes'] [] = 'v45v_16x16_home';
			$return ['priority'] = 10;
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function DonateActionMeta($return) {
		if (! isset ( $this->application ()->wordpress->donate_link ) || !$this->show_default_items()) {
			$return ['hide'] = 1;
		} else {
			$return ['link_name'] = $return ['title'];
			$return ['probono'] = true;
			$return ['url'] = $this->application ()->wordpress->donate_link;
			$return ['classes'] [] = 'v45v_16x16_donate';
			$return ['priority'] = 10;
		}
		return $return;
	}

/***************************************************************************************
* header stuff
***************************************************************************************/
	/**
	*	present basic authentication and validate against WordPress login
	**/
/*****************************************************************************************
* ??document??
*****************************************************************************************/
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
/***************************************************************************************
* dashboard layout
***************************************************************************************/
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function menu($page, $menu, $pages) {
		$this->view->title = $menu ['title'];
		if ($menu ['title'] != $page ['title']) {
			$this->view->title .= '&raquo;' . $page ['title'];
		}
		$baseUrl = $this->dashboard ( $menu ['menu'], $menu ['title'] )->url;
		// fix. try to find out cause
		if(substr($baseUrl,strlen($baseUrl)-1) == '=')
		{
			$baseUrl.= $_GET['page'];
		}
		$this->view->items = $pages;
		$current = false;
		foreach ( $this->view->items as $key => $value ) {
			if ($value ['hide']) {
				unset ( $this->view->items [$key] );
			} else {
				if (empty ( $value ['url'] )) {
					$this->view->items [$key] ['url'] = $baseUrl .'&page2=' . $value ['slug'];
				}
				if ((! isset ( $_GET ['page2'] ) && ! $current) || substr ( $_SERVER ['REQUEST_URI'], - strlen ( $this->view->items [$key] ['url'] ) ) == $this->view->items [$key] ['url']) {
					$this->view->items [$key] ['classes'] [] = 'v45v_current';
					$current = true;
				}
				$this->view->items [$key] ['classes'] = implode ( ' ', array_unique($this->view->items [$key] ['classes'] ));
			}
		}
		return $this->render_script ( 'dashboard/menu.phtml',false );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function icon($page, $menu) {
		$action = $page;
		if (! isset ( $action ['icon'] )) {
			$action = $menu;
		}
		if (isset ( $action ['icon'] )) {
			$this->view->icon = $action ['icon'];
		} else {
			$icons = array ('Dashboard' => 'icon-index', 'Posts' => 'icon-edit', 'Media' => 'icon-upload', 'Links' => 'icon-link-manager', 'Pages' => 'icon-edit-pages', 'Comments' => 'icon-edit-comments', 'Appearance' => 'icon-themes', 'Plugins' => 'icon-plugins', 'Users' => 'icon-users', 'Tools' => 'icon-tools', 'Settings' => 'icon-options-general' );
			if (isset ( $icons [$action ['menu']] )) {
				$this->view->icon = $icons [$action ['menu']];
			} else {
				$this->view->icon = $icons ['Settings'];
			}
		}
		return $this->render_script ( 'dashboard/icon.phtml',false );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function wrapper($page, $classes = array(), $attr = null, $tag = 'div') {
		$classes [] = 'wrap';
		$classes [] = 'v45v';
		if($this->dodebug())
		{
			$classes [] = 'v45v_debug';
		}
		$classes [] = 'v45v_'.$this->application ()->slug;
		$this->view->page = $page;
		$this->view->tag = $tag;
		$this->view->classes = implode ( ' ', $classes );
		return $this->render_script ( 'dashboard/wrapper.phtml',false );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function control_url($control) {
		$return = trim ( get_bloginfo ( 'url' ), '/' );
		if (get_option ( 'permalink_structure' ) == '') {
			$return .= '/?wppage=';
			$control = str_replace ( '?', '&', $control );
		} else {
			$return .= '/';
		}
		return $return . $control;
	
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function dashboard($srch_menu, $srch_sub = null) {
		global $menu;
		$srch_menu = array ($srch_menu, __ ( $srch_menu ) );
		$return = new stdClass ();
		$return->menu = false;
		$return->submenu = false;
		$return->url = false;
		foreach ( ( array ) $menu as $item ) {
			if (in_array ( $item [0], $srch_menu )) {
				$return->menu = $item;
				if (null !== $srch_sub) {
					global $submenu;
					$srch_sub = array ($srch_sub, __ ( $srch_sub ) );
					if(isset($submenu [$item [2]]))
					{
						foreach ( ( array ) $submenu [$item [2]] as $sub_item ) {
							if ((in_array ( $sub_item [0], $srch_sub ))) {
								$return->submenu = $sub_item;
							}
						}
					}
				}
			}
		}
		if (null !== $return->menu) {
			$return->url = $return->menu [2];
		}
		if (null !== $return->submenu) {
			$menu_a = explode ( '?', $return->submenu [2] );
			if (pathinfo ( $menu_a [0], PATHINFO_EXTENSION ) == 'php') {
				$return->url = $return->submenu [2];
			} else {
				$p_menu_a = explode ( '?', $return->menu [2] );
				if (pathinfo ( $p_menu_a [0], PATHINFO_EXTENSION ) == 'php') {
					$return->url = $return->menu [2];
					if (count ( $p_menu_a ) == 1) {
						$return->url .= '?';
					} else {
						$return->url .= '&';
					}
					$return->url .= 'page=' . $return->submenu [2];
				} else {
					$return->url = 'admin.php?page=' . $return->submenu [2];
				}
			}
		}
		return $return;
	}
/*****************************************************************************************
* ??document??used??
*****************************************************************************************/
	protected function set_view() {
		$this->view = new wv45v_view ( $this->application () );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function dispatch() {
		$this->view->args = array ();
		if (count ( func_get_args () ) > 0) {
			$this->view->args = func_get_args ();
		} else {
			$this->view->args [] = null;
		}
		if (is_array ( $this->view->selected )) {
			$args = $this->view->args;
			$return = call_user_func_array ( array ($this, $this->view->selected ['action'] ), $args );
			if (null !== $return) {
				$this->view->args [0] = $return;
			}
		}
		$return = $this->render_script ( $this->view->selected ['raw_title'] . '.phtml' ,false);
		if (null !== $return) {
			$this->view->args [0] .= $return;
		}
		return $this->view->args [0];
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	/*******************************************************************
	 * Init Functions
	 *******************************************************************/
	public function __construct(&$application) {
		parent::__construct ( $application );
		add_action($application->slug.'_deschedule',array($this,'deschedule'));
		$this->setup_wpactions ();
	}
	/*******************************************************************
	 * Setup Aciton Types
	 *******************************************************************/
	protected function setup_action() {
		// only setup for dashboard
		if (! is_admin ()) {
			return;
		}
		parent::setup_action ();
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function setup_wpactions() {
		$this->add_action_type ( 'wpaction', 'WPaction' );
		foreach ( ( array ) $this->get_actions ( 'wpaction' ) as $action ) {
			add_action ( $action ['raw_action_title'], $this->callback_filter ( $action ['action_callback'] ), $action ['priority'] );
			// check if the action is scheduled or that is is already scheduled
			if(null!==$action['schedule'])
			{
				if( null===$action ['schedule_start'])
				{
					$action ['schedule_start']=time();
				}
				$scheduled = wp_next_scheduled($action ['raw_action_title']);
				// if its not scheduled and you not clear the schedule schedule it.
				if ($scheduled===false && $action['schedule']!='clear') {
					wp_schedule_event($action ['schedule_start'], $action ['schedule'], $action ['raw_action_title']);
				}
				else
				{
					// if the action is set to clear the schedule, clear it.
					$scheduled = wp_get_schedule($action ['raw_action_title']);
					if($action['schedule']=='clear' && $scheduled!==false)
					{
						wp_clear_scheduled_hook($action ['raw_action_title']);
					}
					else
					{
						// if its already scheduled, check the frequency has not changed.
						if($scheduled!=$action['schedule'])
						{
							wp_clear_scheduled_hook($action ['raw_action_title']);
							wp_schedule_event($action ['schedule_start'], $action ['schedule'], $action ['raw_action_title']);
						}
					}
				}
			}
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function setup_wpfilters() {
		$this->add_action_type ( 'wpfilter', 'WPfilter' );
		foreach ( ( array ) $this->get_actions ( 'wpfilter' ) as $action ) {
			$numargs = 5;
			add_filter ( $action ['raw_action_title'], $this->callback_filter ( $action ['action_callback'] ), $action ['priority'], $numargs );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function setup_wpshortcodes() {
		$this->add_action_type ( 'wpshortcode', 'WPshortcode' );
		foreach ( ( array ) $this->get_actions ( 'wpshortcode' ) as $action ) {
			add_shortcode ( $action ['raw_action_title'], $this->callback_filter ( $action ['action_callback'] ) );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function setup_wppages() {
		// don't setup for dashboard
		if (is_admin ()) {
			return;
		}
		$this->add_action_type ( 'wppage', 'WPpage' );
		if (get_option ( 'permalink_structure' ) != '') {
			global $wp_rewrite;
			$flush = false;
			foreach ( ( array ) $this->get_actions ( 'wppage' ) as $action ) {
				if (! in_array ( 'index.php?wppage=' . $action ['slug'], $wp_rewrite->wp_rewrite_rules () )) {
					$flush = true;
				}
			}
			if ($flush) {
				$wp_rewrite->flush_rules ();
			}
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function setup_wpnotices() {
		// only setup for dashboard
		if (! is_admin ()) {
			return;
		}
		$this->add_action_type ( 'wpnotice', 'WPnotice' );
		$output = '';
		foreach ( ( array ) $this->get_actions ( 'wpnotice' ) as $action ) {
			$this->view->class = $action ['alert'];
			$this->view->slug = $action ['slug'];
			$this->view->content = call_user_func_array ( array ($this, $action ['action'] ), array ('' ) );
			$output .= $this->render_script ( 'dashboard/notices.phtml',false );
		}
		if (! empty ( $output )) {
			echo $this->wrapper ( $output );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function admin_noticesWPactionA() {
		$this->setup_wpnotices ();
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function generate_rewrite_rulesWPaction($wp_rewrite) {
		$new_rules = array ();
		foreach ( ( array ) $this->get_actions ( 'wppage' ) as $action ) {
			$new_rules [$action ['slug']] = 'index.php?wppage=' . $action ['slug'];
		}
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function query_varsWPfilter($qvars) {
		$qvars [] = 'wppage';
		return $qvars;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function template_redirectWPaction() {
		global $wp_query;
		global $wp_rewrite;
		if ($wp_query->get ( 'wppage' )) {
			foreach ( ( array ) $this->get_actions ( 'wppage' ) as $action ) {
				//$this->debug ( $wp_query->get ( 'wppage' ) );
				$pages = $this->pages ( $action ['slug'] );
				//$this->debug ( $pages );
				if ($pages !== false) {
					$return = call_user_func_array ( array ($this, $action ['action'] ), array ($pages ) );
					// if the action did not refuse the page then stop die
					if ($return !== false) {
						die ();
					}
				}
			}
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function pages($slug) {
		$siteurl = $this->application ()->siteuri ( true );
		$host = trim ( $_SERVER ['HTTP_HOST'], '/' );
		$request_uri = explode ( '?', $_SERVER ['REQUEST_URI'] );
		$request_uri = trim ( $request_uri [0], '/' );
		if (isset ( $_GET ['wppage'] )) {
			$request_uri .= '/' . $_GET ['wppage'];
			$request_uri = trim ( $request_uri, '/' );
		}
		$page = urldecode ( strtolower ( $host . '/' . $request_uri ) );
		//print_r($siteurl);
		$pages = null;
		if (strpos ( $page, $siteurl ['uri'] ) === 0) {
			$pages = trim ( substr ( $page, strlen ( $siteurl ['uri'] ) ), '/' );
			$pages = explode ( '/', $pages );
		}
		$slug = explode ( '/', trim ( $slug, '/' ) );
		// get a possible matchine part of the requested uri
		if (is_array ( $pages )) {
			$match = array_slice ( $pages, 0, count ( $slug ) );
			if ($slug != $match) {
				return false;
			}
		} else {
			return false;
		}
		// its a match so calculate the pages after the permalink
		$pages = array_slice ( $pages, count ( $slug ) );
		return $pages;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function setup() {
		foreach ( $this->get_actions ( 'wpmenu' ) as $menu ) {
			if ($menu ['menu'] != 'Sandbox' || $this->dodebug ()) {
				$page_title = __ ( $menu ['menu'] );
				$mnu = $this->dashboard ( $page_title )->menu;
				//if ($menu ['title'] == $menu ['menu']) {
				$menu_title = $menu ['title'];
				$capability = $menu ['capability'];
				$function = array ($this, 'callback' );
				$menu_slug = $menu ['slug'];
				if (false === $mnu) {
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
					add_menu_page ( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
					$parent_slug = $page_title;
				} else {
					$parent_slug = $mnu ['2'];
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
				//$menu_slug='admin.php?page=test';
				add_submenu_page ( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
			}
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function callback() {
		$page = urldecode ( $_GET ['page'] );
		$menu = false;
		foreach ( $this->get_actions ( 'wpmenu' ) as $item ) {
			if ($item ['slug'] = $page) {
				$menu = $item;
				break;
			}
		}
		if ($menu) {
			$pages = $this->get_actions ();
			$page = array('action'=>null,'classes'=>array());
			foreach ( $pages as $chk_page ) {
				if (! isset ( $_GET ['page2'] ) || $chk_page ['slug'] == strtolower($_GET ['page2'])) {
					$page = $chk_page;
					break;
				}
			}
			$output = '';
			if (method_exists ( $this, $page ['action'] )) {
				$output = call_user_func_array ( array ($this, $page ['action'] ), array ($page, $menu, $pages ) );
			}
			$return = $this->render_script ( "{$menu['slug']}/{$page ['slug']}.phtml" ,false);
			//$this->debug("{$menu['slug']}/{$page ['slug']}.phtml");
			if (null !== $return) {
				$output .= $return;
			}
			$this->view->classes = implode ( $page ['classes'] );
			$this->view->updated = $this->updated ();
			if(!isset($this->view->form_name))
			{
				$this->view->form_name = 'v45v_form';
			}
			$this->view->output = $output;
			
			$output = $this->render_script ( "dashboard/form.phtml",false );;
			$output = $this->icon ( $page, $menu ) . $this->menu ( $page, $menu, $pages ) . $output;
			echo $this->wrapper ( $output );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	/*******************************************************************
	 * Default actions of all types but only the ones that need to be done for all classes
	 *******************************************************************/
	public function plugins_loadedWPactionA() {
		$this->setup_wpfilters ();
		$this->setup_wpshortcodes ();
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function initWPactionA() {
		$this->setup_wppages ();
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function admin_menuWPactionA() {
		$this->setup_wpmenu ();
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function setup_wpmenu() {
		// only setup for dashboard
		if (! is_admin ()) {
			return;
		}
		$this->add_action_type ( 'wpmenu', 'WPmenu' );
		foreach ( ( array ) $this->get_actions ( 'wpmenu' ) as $action ) {
			$this->setup ( $action );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function plugin_action_linksWPfilter($links, $file) {
		if ($file != plugin_basename ( $this->application ()->filename )) {
			return $links;
		}
		foreach ( $this->get_actions ( 'wpmenu' ) as $menu ) {
			$baseUrl = $this->dashboard ( $menu ['menu'], $menu ['title'] )->url;
			$actions = array_reverse ( ( array ) $this->get_actions ( 'action' ) );
			foreach ( $actions as $action ) {
				if (! empty ( $action ['link_name'] ) && ! $action ['hide']) {
					$url = $action ['url'];
					if (empty ( $url )) {
						$url = $baseUrl . '&page2=' . $action ['slug'];
					}
					$classes = implode ( ' ', array_unique($action ['classes']) );
					$link_url = "<a href='{$url}' class='{$classes}' title='{$action ['link_title']}'>{$action ['link_name']}</a>";
					array_unshift ( $links, $link_url );
				}
			}
		}
		return $links;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
/*******************************************************************
 * General functions share by this class type
 *******************************************************************/
}