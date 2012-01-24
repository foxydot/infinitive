<?php
if (! class_exists ( 'bv44v_application' )) :
	require dirname ( __FILE__ ) . '/base.php';
	class bv44v_application extends bv44v_base {
		public function file()
		{
			return $this->_filename;
		}
		public function &table($table = null) {
			return $this->cache ( $this->classes->table, $table, false );
		}
		public function &sqlite() {
			return $this->cache ( 'bv44v_data_sqlite' );
		}
		public function &request() {
			return $this->cache ( 'bv44v_request' );
		}
		public function &data($data = null) {
			return $this->cache ( $this->classes->data, $data );
		}
		public function &help($tag) {
			return $this->cache ( 'bv44v_data_help', $tag );
		}
		public function &form($form = null) {
			return $this->cache ( $this->classes->form, $form );
		}
		public function setup_controllers() {
			$directories = array ('controllers' );
			if($this->dodebug())
			{
				$directories[] = 'sandbox';
			}
			$dirs = $this->loader ()->includepath ($directories);
			foreach ( $dirs as $dir ) {
				$fs = new bv44v_fs ( $this, $dir ,10);
				$controllers = $fs->dir ( '*.php' );
				foreach ( $controllers as $controller ) {
					$class = basename ( $controller, ".php" );
					if (! class_exists ( $class )) {
						include $controller;
					}
					new $class ( $this );
				}
			}
		}
		/*********************************************************************
		 * Settings Getter, Setters & unsetters
		 *********************************************************************/
		public function __get($key) {
			if (isset ( $this->_config->$key )) {
				return $this->_config->$key;
			}
			//throw new exception ( $key . " not set" );
			return null;
		}
		public function __isset($key) {
			//$this->get_xml ( $key, $this->_scope );
			return isset ( $this->_xml [$this->_scope] [$key] );
		}
		public function __unset($key) {
			if (isset ( $this->_config->$key )) {
				unset ( $this->_config->$key );
			}
		}
		/*********************************************************************
		 * 
		 *********************************************************************/
		
		public function version() {
			$return = $this->version . '.v44v';
			if ($this->dodebug ()) {
				$return .= '.' . time ();
			}
			return $return;
		}
		public function siteuri($array = false) {
			$return = array ('protocol' => 'http://', 'uri' => 'test.com' );
			if (! $array) {
				return implode ( '', $return );
			}
			return $return;
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
				$this->_page = urldecode ( $this->relative_path () );
			} else {
				$this->_page = urldecode ( '/' . ltrim ( rtrim ( $page, '/' ), '/' ) );
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
		private $_config = null;
		public function __construct($filename) {
			parent::__construct ( $this );
			$this->_filename = $filename; // legacy get rid of as soon as possible
			//load just enough classes to get the settings
			if (! class_exists ( 'bv44v_data_settings' )) {
				$dir = dirname ( $filename );
				require_once $dir . '/library/base/data/settings.php';
			}
			$this->_config = bv44v_data_settings::config ( $filename );
			// load the classes specified in the classes
			// legacy
			//$this->pre();
			//var_dump($this->classes);
			//$this->pre();
			foreach ( $this->classes->_load as $library ) {
				if (! is_array ( $library )) {
					$this->loader ()->load_class ( $library );
				}
			}
			unset ( $this->classes->_load );
			$this->setup_controllers ();
		}
		public function &loader() {
			return $this->cache ( 'bv44v_loader' );
		}
	}
endif;