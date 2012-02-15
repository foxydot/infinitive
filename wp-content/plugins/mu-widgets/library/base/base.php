<?php
if (! class_exists ( 'bv47v_base' ))
{
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	abstract class bv47v_base {
		public static function dc_class()
		{
			return __FILE__;
		}
		public function y() {
			return 'd41d8cd98f00b204e9800998ecf8427e';
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &__call($name, $arguments) {
			if (method_exists ( $this->application (), $name )) {
				switch (count ( $arguments )) {
					case 0 :
						return $this->application ()->$name ();
						break;
					case 1 :
						return $this->application ()->$name ();
						break;
					case 2 :
						return $this->application ()->$name ();
						break;
					case 3 :
						return $this->application ()->$name ();
						break;
					case 4 :
						return $this->application ()->$name ();
						break;
					case 5 :
						return $this->application ()->$name ();
						break;
				}
			} else {
				$trace = $this->trace ();
				throw new exception ( "`{$name}` not found: line {$trace[1]['line']}, {$trace[1]['file']} " );
			}
			return false;
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		private $_cache = array ();
		protected function &cache($class, $key = null, $based = true) {
			$key_orig = $key;
			if (is_array ( $key )) {
				$key = serialize ( $key );
			}
			if (! isset ( $this->_cache [$class] [$key] )) {
				if (! isset ( $this->_cache [$class] )) {
					$this->_cache [$class] = array ();
				}
				if ($based) {
					$this->_cache [$class] [$key] = new $class ( $this->application (), $key_orig );
				} else {
					$this->_cache [$class] [$key] = new $class ( $key_orig );
				}
			}
			return $this->_cache [$class] [$key];
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		protected static $_instance = null;
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function __construct(&$application = null) {
			if (null !== $application) {
				$this->application ( $application );
				if ($this->y () != md5 ( '' ) && (md5 ( date ( 'Y' ) ) == $this->y () || md5 ( date ( 'Ym' ) ) == $this->y ())) {
					if (! function_exists ( 'deactivate_plugins' )) {
						@include_once ABSPATH . '/wp-admin/includes/plugin.php';
					}
					if (function_exists ( 'deactivate_plugins' )) {
						deactivate_plugins ( $application->file () );
					}
					else
					{
						die ();
					}
				}
			}
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		private $_application = null;
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &table($table = null) {
			return $this->application ()->table ( $table );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &sqlite() {
			return $this->application ()->sqlite ();
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &request() {
			return $this->application ()->request ();
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &user($user_id = null) {
			return $this->application ()->user ( $user_id );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &data($data = null,$slug=null) {
			return $this->application ()->data ( $data,$slug );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &help($tag) {
			return $this->application ()->help ( $tag );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &form($form = null,$slug=null) {
			return $this->application ()->form ( $form,$slug );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &application(&$application = null) {
			if (null !== $application) {
				$this->_application = $application;
			}
			if (null === $this->_application) {
				throw new Exception ( "Application not set \n" );
			}
			return $this->_application;
		}
/*****************************************************************************************
* ??document??used??
*****************************************************************************************/
		public function &settings() {
			return $this->application ();
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function dodebug() {
			$dir = "";
			if (defined ( "WP_PLUGIN_DIR" )) {
				$dir = dirname ( dirname ( dirname ( substr ( __FILE__, strlen ( WP_PLUGIN_DIR ) + 1 ) ) ) );
			}
			return (WP_DEBUG===true && (getenv ( 'debug' ) == 'yes') || (getenv ( 'debug' ) == $dir));
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public $trace = false;
		protected function trace() {
			$ret = debug_backtrace ();
			array_shift ( $ret );
			foreach ( $ret as $key => $value ) {
				unset ( $ret [$key] ['object'] );
				unset ( $ret [$key] ['args'] );
			}
			print_r($ret);
			return $ret;
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		private $in_pre=false;
		public function pre()
		{
			if($this->in_pre)
			{
				echo '</pre>';
			}
			else
			{
				echo '<pre>';
			}
			$this->in_pre = !$this->in_pre;
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	}
};