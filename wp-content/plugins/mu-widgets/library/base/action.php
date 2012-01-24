<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class bv45v_action extends bv45v_base {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function controller() {
		$this->view->args = array ();
		if (count ( func_get_args () ) > 0) {
			$this->view->args = func_get_args ();
		} else {
			$this->view->args [] = null;
		}
		$return = call_user_func_array ( array ($this, 'dispatch' ), $this->view->args );
		if (null !== $return) {
			$this->view->args [0] = $return;
		}
		return $this->view->args [0];
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function dispatch() {
	}
	
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function render_script($script, $html = true) {
		$return = null;
		$this->view->action ( $this );
		$pi = pathinfo ( strtolower ( $script ) );
		$string = false;
		if (! isset ( $pi ['extension'] )) {
			throw new exception ( $script . ' need extension' );
		}
		if ($string === false) {
			$orig = $script;
			$exists = file_exists ( $script );
			$path = null;
			if ($script !== false) {
				$return = $this->view->render ( $script );
			}
		} else {
			$return = $this->view->render_string ( $string );
		}
		if ($html && null !== $return) {
			$return = str_replace ( "\n", '', $return );
			$return = str_replace ( "\r", '', $return );
		}
		return $return;
	}
	
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public $view = null;
	protected function set_view() {
		if (null === $this->view) {
			$this->view = new bv45v_view ( $this->application () );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_update_message=array('class'=>'normal','message'=>'Settings Saved');
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function update_message($message = null,$class=null)
	{
		if(null!==$message)
		{
			$this->_update_message['message'] = $message;
		}
		if(null!==$class)
		{
			$this->_update_message['class'] = $class;
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function updated($message = null,$force=false) {
		$return = '';
		if ($this->request()->is_post() || $force) {
			$this->update_message($message);
			$this->view->class = $this->_update_message['class'];
			$this->view->content = $this->_update_message['message'];
			$return = $this->render_script ( 'dashboard/notices.phtml' ,false);
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function marker($tag, $content) {
		$tagc = bv45v_tag::instance ();
		$matches = $tagc->get ( $tag, $content, true );
		foreach ( ( array ) $matches as $match ) {
			$new = call_user_func ( array ($this, $tag . '_Marker' ), $match );
			$content = str_replace ( $match ['match'], $new, $content );
		}
		return $content;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected $title = "";	
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function send_headers($file) {
		$pi = pathinfo ( $file );
		if (isset ( $pi ['extension'] )) {
			switch ($pi ['extension']) {
				case 'csv' :
					header ( "Content-type: application/csv" );
					if (null !== $pi ['filename']) {
						header ( "Content-Disposition: attachment; filename={$pi['filename']}.csv" );
					}
					header ( "Pragma: no-cache" );
					header ( "Expires: 0" );
					break;
				case 'xml' :
					header ( 'Content-type: text/xml' );
					break;
				case 'txt' :
					header ( 'Content-Type: text/plain' );
					break;
				case 'json' :
					header ( 'Content-Type: application/json' );
					break;
			}
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	/*******************************************************************
	 * Init Functions
	 *******************************************************************/
	public function __construct(&$application) {
		parent::__construct ( $application );
		$this->set_view ();
		$this->setup_action();
		//$this->add_action_type ( 'filter', 'Filter' );
		//$this->add_action_type ( 'controller', 'Controller' );
	}
	protected function setup_action()
	{
		$this->add_action_type ( 'action', 'Action' );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	/*******************************************************************
	 * Sort compares used in this class
	 *******************************************************************/
	protected function callback_filter($callback) {
		if (null === $callback [0]) {
			$callback [0] = $this;
		}
		return $callback;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	/*******************************************************************
	 * Sort compares used in this class
	 *******************************************************************/
	protected function sortcmp_action_priority($a, $b) {
		if ($a ['priority'] == $b ['priority']) {
			return $this->sortcmp_action_title ( $a, $b );
		}
		return ($a ['priority'] < $b ['priority']) ? - 1 : 1;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function sortcmp_action_title($a, $b) {
		if (strtolower ( $a ['title'] ) == strtolower ( $b ['title'] )) {
			return 0;
		}
		return (strtolower ( $a ['title'] ) < strtolower ( $b ['title'] )) ? - 1 : 1;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function sortcmp_action($a, $b) {
		if ($a ['hide'] == $b ['hide']) {
			return $this->sortcmp_action_priority ( $a, $b );
		}
		return ($b ['hide']) ? - 1 : 1;
	}
	/*******************************************************************
	 * Legacy stuff
	 *******************************************************************/
	/*******************************************************************
	 * Action
	 *******************************************************************/
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_cache_actions = null;
	protected function get_actions($get_type = 'action', $get_action = null) {
		if (null === $this->_cache_actions) {
			$return = array ();
			$methods = get_class_methods ( $this );
			$action_types = $this->action_types ();
			foreach ( $methods as $method ) {
				foreach ( $action_types as $type => $meta ) {
					if (strpos ( $method, $meta ['tag'] )) {
						if (substr ( $method, - 4 ) == 'Meta') {
							$method = substr ( $method, 0, strlen ( $method ) - 4 );
						}
						$return [$method] = $meta;
					}
				}
			}
			foreach ( $return as $method => $meta ) {
				$meta = $this->get_action_meta ( $method, $meta );
				if (false === $meta) {
					unset ( $return [$method] );
				} else {
					$return [$method] = $meta;
				}
			}
			$this->_cache_actions = $return;
			uasort ( $this->_cache_actions, array ($this, 'sortcmp_action' ) );
		}
		if (null === $get_type) {
			return $this->_cache_actions;
		} else {
			$return = $this->_cache_actions;
			foreach ( $return as $method => $meta ) {
				if (($meta ['type'] != $get_type) || (null !== $get_action && $meta ['raw_action_title'] != $get_action)) {
					unset ( $return [$method] );
				}
			}
			if (null !== $get_action) {
				foreach ( $return as $return ) {
					break;
				}
			}
			return $return;
		}
		return false;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function get_action_meta($method, $meta) {
		$meta ['action_callback'] = array (null, $method );
		$meta ['action'] = $method;
		$return ['meta'] = $method;
		$info = explode ( $meta ['tag'], $method );
		$meta ['raw_title'] = $info [0];
		$meta ['raw_action_title'] = $info [0];
		$info [0] = ucwords ( str_replace ( '_', ' ', $info [0] ) );
		$security = "";
		if (count ( $info ) < 2 || $info [1] == "") {
			$info [1] = 0;
		} else {
			$info2 = explode ( '__', $info [1] );
			$info [1] = str_replace ( '_', '-', $info2 [0] );
			if (count ( $info2 ) > 1 && $info2 [1] != "") {
				$security = $info2 [1];
			}
		}
		$meta ['title'] = $info [0];
		if (is_numeric ( $info [1] )) {
			$meta ['priority'] = $info [1];
		}
		$meta ['name'] = $meta ['title'];
		$meta ['link_name'] = '';
		$meta ['link_title'] = '';
		$meta ['url'] = '';
		$meta ['slug'] = str_replace ( ' ', '-', strtolower ( $meta ['title'] ) );
		$meta_func = $meta ['title'] . $meta ['tag'] . $meta ['meta'];
		$meta_class = $meta ['action_callback'] [0];
		if (null === $meta_class) {
			$meta_class = &$this;
		}
		if (method_exists ( $meta_class, $meta_func )) {
			$meta = $meta_class->$meta_func ( $meta );
		}
		$meta_func = $meta ['action_callback'] [1] . $meta ['meta'];
		if (method_exists ( $meta_class, $meta_func )) {
			$meta = $meta_class->$meta_func ( $meta );
		}
		if ($meta ['title'] === null || $meta ['title'] == '' || ($this->application ()->probono == 0 && $meta ['probono'] == true)) {
			return false;
		}
		return $meta;
	}
	/*******************************************************************
	 * Action Types
	 *******************************************************************/
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_action_types = array ();
	protected function add_action_type($action_type, $tag = null, $default_meta = array()) {
		if (null === $tag) {
			$tag = $action_type;
		}
		$meta = array ();
		$meta ['slug'] = null;
		$meta ['schedule'] = null;
		$meta ['schedule_start'] = null;
		$meta ['capability'] = 'administrator';
		$meta ['alert'] = 'normal';
		$meta ['menu'] = 'Settings';
		$meta ['name'] = null;
		$meta ['level'] = 'administrator';
		$meta ['title'] = null;
		$meta ['classes'] = array ();
		$meta ['hide'] = false;
		$meta ['priority'] = 0;
		$meta ['meta'] = 'Meta';
		$meta ['selected'] = false;
		//ft swap null/$this
		$meta ['action_callback'] = null;
		$meta ['action_title'] = null;
		$meta ['raw_action_title'] = null;
		// probono, true indicate somethinge to be included all fee plugins and removed for custom
		$meta ['probono'] = false;
		$meta = bv45v_data_array::merge ( $meta, $default_meta );
		$meta ['tag'] = $tag;
		$meta ['type'] = $action_type;
		$meta ['legacy'] = array ();
		$meta ['action'] = null;
		$meta ['legacy'] [] = 'action';
		$meta ['raw_title'] = '';
		$meta ['legacy'] [] = 'raw_title';
		$meta ['legacy'] [] = 'name?';
		$this->_action_types [$action_type] = $meta;
		$this->_cache_actions = null;
		return true;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function update_action_type($action_type, $default_meta = array()) {
		return $this->add_action_type ( $action_type, $default_meta );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function remove_action_type($action_type) {
		$return = false;
		if (isset ( $this->_action_types [$action_type] )) {
			unset ( $this->_action_types [$action_type] );
			$return = true;
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function action_types() {
		return $this->_action_types;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function callback_action($action_meta) {
		return true;
	}
}