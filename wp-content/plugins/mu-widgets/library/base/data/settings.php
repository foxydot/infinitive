<?php
class bv45v_data_settings extends bv45v_base {
	protected $_option;
	public function __construct(&$application, $option = null) {
		parent::__construct ( $application );
		$this->_option = $option;
	}
	public function __get($key) {
		$this->refresh ();
		if (isset ( $this->_data [$key] )) {
			//var_dump ( $this->_data [$key] );
			return $this->_data [$key];
		}
		return null;
	}
	public function __isset($key) {
		$this->refresh ();
		return isset ( $this->_data [$key] );
	}
	public function options($show_hidden = false, $options = array()) {
		foreach ( $this->application ()->folders as $folder ) {
			$folder .= '/settings';
			if (is_dir ( $folder )) {
				$files = scandir ( $folder );
				foreach ( $files as $file ) {
					$ext = pathinfo ( $file, PATHINFO_EXTENSION );
					if (! is_dir ( $folder . '/' . $file ) && in_array ( $ext, array ('json', 'xml' ) )) {
						$file = basename ( $file, '.' . $ext );
						$options [$file] = $file;
					}
				
				}
			}
		}
		foreach ( $options as $key => $option ) {
			if (strpos ( $option, '_' ) === 0 || (!$this->dodebug() && strpos ( $option, '$' ) === 0)) {
				unset ( $options [$key] );
			}
		}
		natsort ( $options );
		return $options;
	}
	private static function legacy_application($home) {
		$files = scandir ( $home . '/library' );
		foreach ( $files as $key => $value ) {
			if (! is_dir ( "{$home}/library/{$value}" ) || strpos ( $value, '.' ) === 0) {
				unset ( $files [$key] );
			} else {
				$files ["{$key}_json"] = "{$home}/library/{$value}/application.json";
			}
		
		}
		$files [] = "{$home}/application/application.json";
		$data = array ();
		foreach ( $files as $file ) {
			$datum = self::load ( $file, true );
			if (false !== $datum) {
				$data [$file] = $datum;
				if (! isset ( $data [$file] ['priority'] )) {
					$data [$file] ['priority'] = 2000;
					if ($file == "{$home}/application/application.json") {
						$data [$file] ['priority'] = 1000;
					}
				}
			}
		}
		uasort ( $data, array ('self', 'legacy_sort_xml_data' ) );
		$data = bv45v_data_array::merge ( $data );
		unset ( $data ['priority'] );
		return $data;
	}
	public static function config($filename) {
		$home = dirname ( $filename );
		if (! class_exists ( 'bv45v_data_array' )) {
			require_once $home . '/library/base/data/array.php';
		}
		if (! class_exists ( 'bv45v_data_xml' )) {
			require_once $home . '/library/base/data/xml.php';
		}
		if (! class_exists ( 'bv45v_loader' )) {
			require_once $home . '/library/base/loader.php';
		}
		if (! class_exists ( 'bv45v_data_json' )) {
			require_once $home . '/library/base/data/json.php';
		}
		$files = scandir ( $home . '/library' );
		foreach ( $files as $key => $value ) {
			if (! is_dir ( "{$home}/library/{$value}" ) || strpos ( $value, '.' ) === 0) {
				unset ( $files [$key] );
			} else {
				$files ["{$key}_json"] = "{$home}/library/{$value}/application.json";
			}
		
		}
		$files [] = "{$home}/application/application.json";
		$data = array ();
		foreach ( $files as $file ) {
			$datum = self::load ( $file );
			if (false !== $datum) {
				$data [$file] = $datum;
				if (! isset ( $data [$file]->priority )) {
					$data [$file]->priority = 2000;
					if ($file == "{$home}/application/application.json") {
						$data [$file]->priority = 1000;
					}
				}
			}
		}
		uasort ( $data, array ('self', 'priority_sort' ) );
		$data = bv45v_data_array::merge ( $data );
		if(isset($data->settings))
		{
			bv45v_data_array::objects_to_array($data->settings);
		}
		unset ( $data->priority );
		$data->folders->_1 = 'application';
		$data->folders->_3 = '';
		$data->directory = $home;
		$data->filename = $filename;
		foreach ( $data->folders as $key => &$folder ) {
			$folder = $home . '/' . $folder;
			if (! is_dir ( $folder )) {
				unset ( $data->folders->$key );
			}
		}
		return $data;
	}
	private static function load($file, $legacy = false) {
		$return = false;
		if (file_exists ( $file )) {
			switch (pathinfo ( $file, PATHINFO_EXTENSION )) {
				case 'xml' :
					$return = bv45v_data_xml::load ( $file );
					break;
				case 'json' :
					$return = bv45v_data_json::decode ( file_get_contents ( $file ), $legacy );
					break;
			}
		}
		return $return;
	}
	public static function _get($application) {
		$files = array ();
		foreach ( $application->folders as $key => $value ) {
			$files ["{$key}_xml"] = "{$value}/settings.xml";
			$files ["{$key}_json"] = "{$value}/settings.json";
		}
		$files [] = "{$application->directory}/application/settings.xml";
		$files [] = "{$application->directory}/application/settings.json";
		$data = array ();
		foreach ( $files as $file ) {
			$datum = self::load ( $file, true );
			if ($datum != false) {
				$data [$file] = $datum;
			}
		}
		$data = bv45v_data_array::merge ( $data );
		$data ['application'] = self::legacy_application ( $application->directory );
		return $data;
	}
	private static function legacy_sort_xml_data($a, $b) {
		if ($a ['priority'] == $b ['priority']) {
			return 0;
		}
		return ($a ['priority'] < $b ['priority']) ? - 1 : 1;
	}
	private static function priority_sort($a, $b) {
		if ($a->priority == $b->priority) {
			return 0;
		}
		return ($a->priority < $b->priority) ? - 1 : 1;
	}
	protected $_data = null;
	public function data() {
		if (null === $this->_data) {
			$this->refresh ();
		}
		return $this->_data;
	}
	public function refresh() {
		$settings = array ();
		$options = array ();
		$load = '/settings';
		if (null !== $this->_option) {
			$base = new bv45v_data_settings ( $this->application () );
			$settings [] = $base->data ();
			$load = '/settings/' . $this->_option;
		}
		foreach ( $this->application ()->folders as $folder ) {
			$folder = rtrim ( $folder, '\/' );
			foreach ( array ('.xml', '.json' ) as $type ) {
				$filename = $folder . $load . $type;
				$data = $this->load ( $filename, true );
				if ($data !== false) {
					$settings [] = $data;
				}
			}
		}
		$this->_data = bv45v_data_array::merge ( $settings );
		return $this->_data;
	}

}