<?php
if (! class_exists ( 'bv28v_application' )) :
	require_once dirname ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'base/application.php';
	class wv28v_application extends bv28v_application {
		protected function set_frontcontroller() {
			parent::set_frontcontroller ( wv28v_controller_front::getInstance ( $this->application () ) );
		}
		protected $passed_classes = null;
		public function __construct($filename = "", $classes = array(), $handler = 'wv28v_settings') {
			$this->passed_classes = $classes;
			add_action ( "plugins_loaded", array ($this, "setup" ) );
			parent::__construct ( $filename, $handler );
			$this->info = new wv28v_info ( $this );
			add_action ( "admin_menu", array ($this, "pages" ) );
		}
		public function pages() {
			$obj = new wv28v_controller_action_sandboxsandbox ( $this );
			$obj->setup ();
			$obj = new wv28v_controller_action_pluginsandbox ( $this );
			$obj->setup ();
		}
		public function relative_path($uri = null) {
			global $current_blog;
			if (null === $uri) {
				$uri = $_SERVER ['REQUEST_URI'];
			}
			//$uri = substr ( $uri , strlen ( $current_blog->path ) );
			$uri = substr ( $uri, strlen ( get_option ( 'site_url' ) ) );
			
			$uri = explode ( '?', $uri );
			$uri = $uri [0];
			$uri = rtrim ( $uri, '/' );
			$uri = '/' . rtrim ( $uri, '/' );
			return $uri;
		}
		public function setup() {
			load_plugin_textdomain ( get_class ( $this ), false, dirname ( plugin_basename ( $this->application ()->filename () ) ) . "/languages/" );
		}
		public function preload_classes($classes = array()) {
			$classes = ( array ) $classes;
			array_unshift ( $classes, 'wv28v_info', 'wv28v_values', 'wv28v_table', 'wv28v_table_sitemeta', 'wv28v_table_sites', 'wv28v_table_site', 'wv28v_table_posts', 'wv28v_table_postmeta', 'wv28v_table_blogs', 'wv28v_table_blog', 'wv28v_table_options', 'wv28v_table_users', 'wv28v_table_usermeta', 'wv28v_table_commentmeta', 'wv28v_view', 'wv28v_controller_action_abstract', 'wv28v_controller_action_action', 'wv28v_controller_action_adminmenu', 'wv28v_controller_action_control', 'wv28v_controller_action_filter', 'wv28v_controller_front', 'wv28v_controller_dispatcher', 'wv28v_table_comments', 'wv28v_settings', 'wv28v_controller_action_pluginsandbox', 'wv28v_controller_action_sandboxsandbox' );
			foreach ( $this->passed_classes as $class ) {
				$classes [] = $class;
			}
			parent::preload_classes ( $classes );
		}
		private $info = null;
		public function info() {
			return $this->info;
		}
	}


endif;