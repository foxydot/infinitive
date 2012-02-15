<?php
if (! class_exists ( 'bv47v_application' )) :
	require dirname ( __FILE__ ) . '/base.php';
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	class bv47v_application extends bv47v_base {
/*****************************************************************************************
* ??document??used??
*****************************************************************************************/
		public function file()
		{
			return $this->_filename;
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &table($table = null) {
			return $this->cache ( $this->classes->table, $table, false );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &sqlite() {
			return $this->cache ( 'bv47v_data_sqlite' );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &request() {
			return $this->cache ( 'bv47v_request' );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &data($data = null,$slug=null) {
			return $this->cache ( $this->classes->data, array('data'=>$data,'slug'=>$slug) );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &help($tag) {
			return $this->cache ( 'bv47v_data_help', $tag );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &form($form = null,$slug=null) {
			return $this->cache ( $this->classes->form, array('data'=>$form,'slug'=>$slug)  );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function setup_models() {
			$directories = array ('models' );
			$dirs = $this->loader ()->includepath ($directories);
			foreach ( $dirs as $dir ) {
				$fs = new bv47v_fs ( $this, $dir ,10);
				$controllers = $fs->dir ( '*.php' );
				foreach ( $controllers as $controller ) {
					$class = basename ( $controller, ".php" );
					// special case, models whos filename already begins with the slug will be loaded manually later
					if(strpos($class,$this->application()->slug)!==0)
					{
						$dir=str_replace('\\','/' , $controller);
						$prefix = $this->application()->slug.'_';
						foreach($this->folders as $key=>$value)
						{
							if(strpos($key,'_')===false)
							{
								if(strpos($dir, $value)!==false)
								{
									$prefix = $key.'v47v_';
									break;
								}
							}
						}
						$class=$prefix.$class;
						if (! class_exists ( $class )) {
							include $controller;
						}
						if (! class_exists ( $class ))
						{
							throw new exception('Model '.$class.' not loaded');
						}
					}
				}
			}
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function setup_controllers() {
			$directories = array ('controllers' );
			$dirs = $this->loader ()->includepath ($directories);
			foreach ( $dirs as $dir ) {
				$fs = new bv47v_fs ( $this, $dir ,10);
				$controllers = $fs->dir ( '*.php' );
				foreach ( $controllers as $controller ) {
					$class = basename ( $controller, ".php" );
					$dir=str_replace('\\','/' , $controller);
					$prefix = $this->application()->slug.'_';
					foreach($this->folders as $key=>$value)
					{
						if(strpos($key,'_')===false)
						{
							if(strpos($dir, $value)!==false)
							{
								$prefix = $key.'v47v_';
								break;
							}
						}
					}
					$class=$prefix.$class;
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
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function __get($key) {
			if (isset ( $this->_config->$key )) {
				return $this->_config->$key;
			}
			//throw new exception ( $key . " not set" );
			return null;
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function version() {
			$return = $this->version . '.v47v';
			if ($this->dodebug ()) {
				$return .= '.' . time ();
			}
			return $return;
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function siteuri($array = false) {
			$return = array ('protocol' => 'http://', 'uri' => 'test.com' );
			if (! $array) {
				return implode ( '', $return );
			}
			return $return;
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		private $_page = null;
		public function page() {
			if (null === $this->_page) {
				$this->set_page ();
			}
			return $this->_page;
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function set_page($page = null) {
			if (null === $page) {
				$this->_page = urldecode ( $this->relative_path () );
			} else {
				$this->_page = urldecode ( '/' . ltrim ( rtrim ( $page, '/' ), '/' ) );
			}
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
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
/*****************************************************************************************
* ??document??used??
*****************************************************************************************/
		private $_config = null;
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function __construct($filename) {
			parent::__construct ( $this );
			$this->_filename = $filename; // legacy get rid of as soon as possible
			//load just enough classes to get the settings
			if (! class_exists ( 'bv47v_data_settings' )) {
				$dir = dirname ( $filename );
				require_once $dir . '/library/base/data/settings.php';
			}
			$this->_config = bv47v_data_settings::config ( $filename );
			// load the classes specified in the classes
			// legacy
			foreach ( $this->classes->_load as $library ) {
				if (! is_array ( $library )) {
					$this->loader ()->load_class ( $library );
				}
			}
			unset ( $this->classes->_load );
			$this->setup_models ();
			$this->setup_controllers ();
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &loader() {
			return $this->cache ( 'bv47v_loader' );
		}
	}
endif;