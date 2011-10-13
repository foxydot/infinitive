<?php
if (! class_exists ( 'bv28v_application' )) :
	require dirname ( __FILE__ ) . '/base.php';
	class bv28v_application extends bv28v_base {
		private static $applications = array ();
		public function applications() {
			return self::$applications;
		}
		private $_page = null;
		public function page() {
			if (null === $this->_page) {
				$this->set_page ();
			}
			return $this->_page;
		}
		public function set_page($page = null) {
			if (null === $page) {
				$this->_page = $this->relative_path ();
			} else {
				$this->_page = '/' . ltrim ( rtrim ( $page, '/' ), '/' );
			}
		}
		public function relative_path($uri = null) {
			if (null === $uri) {
				$uri = $_SERVER ['REQUEST_URI'];
			}
			$uri = explode ( '?', $uri );
			$uri = $uri [0];
			$uri = rtrim ( $uri, '/' );
			$project = dirname ( $this->filename () );
			$root_uri = $uri;
			while ( strpos ( $project, $root_uri ) === false ) {
				$root_uri = substr ( $root_uri, 0, strrpos ( $root_uri, '/' ) );
			}
			$uri = '/' . ltrim ( rtrim ( substr ( $uri, strlen ( $root_uri ) ), '/' ), '/' );
			return $uri;
		}
		private $_filename = null;
		public function filename() {
			return $this->_filename;
		}
		public function plugin_directory() {
			return dirname($this->_filename);
		}
		protected $handler = null;
		public function __construct($filename = "",$handler='bv28v_settings') {
			if (! class_exists ( 'bv28v_loader' )) {
				require_once dirname ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'base/loader.php';
			}
			$this->_loader = new bv28v_loader ( $this );
			parent::__construct ( $this );
			$this->handler=$handler;
			$this->_filename = $filename;
			$this->preload_classes ();
			$class = $this->_class();
			$this->settings=new $class($this);
			$this->set_frontcontroller ();
			self::$applications [] = $this;
		}
		private $osettings = null;
		public function osettings() {
			if (null == $this->osettings) {
				$this->osettings = new bv28v_oSettings ( $this );
			}
			return $this->osettings;
		}
		private $_frontController;
		public function frontcontroller() {
			$this->set_frontcontroller ();
			return $this->_frontcontroller;
		}
		protected function set_frontcontroller($controller = null) {
			if (null === $controller) {
				$this->_frontcontroller = bv28v_controller_front::getInstance ( $this->application () );
			} else {
				$this->_frontcontroller = $controller;
			}
		}
		private $_loader = null;
		public function loader() {
			return $this->_loader;
		}
		private $settings = null;
		public function settings()
		{
			return $this->settings;
		}
		protected function _class()
		{
			return $this->handler;
		}
		protected function preload_classes($classes = array()) {
			$classes = ( array ) $classes;
			$loader = $this->loader ();
			array_unshift ( $classes, 'bv28v_info', 'bv28v_controller_action', 'bv28v_type_abstract', 'bv28v_type_string', 'bv28v_type_array', 'bv28v_fs', 'bv28v_view', 'bv28v_http', 'bv28v_tag', 'bv28v_data_abstract', 'bv28v_data_xml', 'bv28v_fs', 'bv28v_http', 'bv28v_controller_front', 'bv28v_controller_dispatcher', 'bv28v_table','bv28v_settings' );
			foreach ( $classes as $class ) {
				$loader->load_class ( $class );
			}
			$loader->load_class ( $this->_class() );
		}
	}


endif;