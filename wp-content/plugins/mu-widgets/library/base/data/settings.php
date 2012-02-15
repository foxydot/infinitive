<?php
class bv47v_data_settings extends bv47v_base {
	/**
	 * option
	 *
	 * holds the form where the data is stored.
	 * @var string
	 */
	protected $_option=null;
	/**
	 * construct
	 *
	 * override construct to all settings of the option
	 *
	 * @param object $application pointer to the plugin running
	 * @param array $array holds the option, ie the form the data is stored in.
	 * @returns null
	 */
	public function __construct(&$application, $array = null) {
		parent::__construct ( $application );
		$option=null;
		$form='default';
		if(is_array($array))
		{
			$option=$array['data'];
			$form=$array['data'];
		}
		$this->_option = $option;
		$this->form = $form;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected $form = null;
	public function form() {
		return $this->form;
	}
	public function setForm($form)
	{
		$this->form=$form;
	}
	/**
	 * get
	 *
	 * direct missing properties to the data
	 *
	 * @param string $key the root of the data to load
	 * @returns mixed the dat to be returned
	 */
	public function __get($key) {
		//prime the data
		$this->data ();
		$return = null;
		if (isset ( $this->_data [$key] )) {
			$return = $this->_data [$key];
		}
		return $return;
	}
	/**
	 * forms
	 *
	 * get a list of files that are holding data
	 *
	 * @param bolean $show_hidden shows files starting $_ usually debug only
	 * @param array $options forms already passed from a child class
	 * @returns array list of forms
	 */
	public function forms($show_hidden = false, $options = array()) {
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
			if (strpos ( $option, '_' ) === 0 || (!$this->dodebug() && strpos ( $option, '$_' ) === 0)) {
				unset ( $options [$key] );
			}
		}
		natsort ( $options );
		// move default to the top of the sorted list 
		$default = array('default'=>$options['default']);
		unset($options['default']);
		$options = $default+$options;
		// ***** 
		return $options;
	}
	/**
	 * config
	 *
	 * load the basic classes and get the application settings
	 *
	 * @param string $filename used to find the location of the other files.
	 * @returns array the config data
	 */
	public static function config($filename) {
		$home = dirname ( $filename );
		if (! class_exists ( 'bv47v_data_array' )) {
			require_once $home . '/library/base/data/array.php';
		}
		if (! class_exists ( 'bv47v_data_xml' )) {
			require_once $home . '/library/base/data/xml.php';
		}
		if (! class_exists ( 'bv47v_loader' )) {
			require_once $home . '/library/base/loader.php';
		}
		if (! class_exists ( 'bv47v_data_json' )) {
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
		$data = bv47v_data_array::merge ( $data );
		if(isset($data->settings))
		{
			bv47v_data_array::objects_to_array($data->settings);
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
	/**
	 * load
	 *
	 * load in an individual file
	 *
	 * @param string $file he file to load
	 * @param boolean $legacy indicated the files is to be all array
	 * @returns array the config data
	 */
	private static function load($file, $legacy = false) {
		$return = false;
		if (file_exists ( $file )) {
			switch (pathinfo ( $file, PATHINFO_EXTENSION )) {
				case 'xml' :
					$return = bv47v_data_xml::load ( $file );
					break;
				case 'json' :
					$return = bv47v_data_json::decode ( file_get_contents ( $file ), $legacy );
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
		$data = bv47v_data_array::merge ( $data );
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
	public function data($refresh=false) {
		if (null === $this->_data || $refresh===false) {
			$settings = array ();
			$options = array ();
			$load = '/settings';
			if (null !== $this->_option) {
				$base = new bv47v_data_settings ( $this->application () );
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
			$this->_data = bv47v_data_array::merge ( $settings );
		}
		return $this->_data;
	}
}