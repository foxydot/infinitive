<?php
abstract class bv28v_controller_action extends bv28v_base {
	protected function marker($tag, $content) {
		$tagc = bv28v_tag::instance ();
		$matches = $tagc->get ( $tag, $content, true );
		foreach ( ( array ) $matches as $match ) {
			$new = call_user_func ( array ($this, $tag . '_Marker' ), $match );
			$content = str_replace ( $match ['match'],  $new , $content );
		}
		return $content;
	}
	private $_type = null;
	
	public function getType() {
		return $this->_type;
	}
	protected function set_type($type) {
		$this->_type = $type;
	}
	protected $title = "";
	public function __construct($application) {
		parent::__construct ( $application );
		$this->get_type ();
		$cname = explode ( 'controller', get_class ( $this ) );
		$cname = $cname [0];
		$this->template_folders = array ($cname, '' );
	}
	public function updated($message = 'Settings Saved', $type = 'updated') {
		$return = '';
		if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
			$this->view->message = $message;
			$this->view->type = $type;
			$return = $this->render_script ( 'common/updated.phtml' );
		}
		return $return;
	}
	protected $_actions = null;
	
	protected function actions() {
		if (null === $this->_actions) {
			$this->_actions = $this->decode_actions ();
			uasort ( $this->_actions, array ($this, 'action_sort' ) );
		}
		return $this->_actions;
	}
	protected $view = null;
	
	protected function get_type() {
		$this->set_view ();
		return $this->view;
	}
	protected function set_view() {
		if (null === $this->view) {
			$this->view = new bv28v_view ( $this->application () );
		}
	}
	protected function decode_actions() {
		$return = array ();
		$methods = get_class_methods ( $this );
		$actions = array ();
		foreach ( $methods as $method ) {
			if (strpos ( $method, 'Virtual' ) !== 0) {
				if (strrpos ( $method, 'Meta' ) != strlen ( $method ) - 4) {
					if (strpos ( $method, 'Action' )) {
						$actions [] = $method;
					}
				}
			}
		}
		$actions = $this->VirtualActions ( $actions );
		foreach ( $actions as $method ) {
			$decoded = $this->decode_action ( $method );
			if ($decoded !== false) {
				$return [$method] = $decoded;
			}
		}
		return $return;
	}
	protected function action_sort($a, $b) {
		if ($a ['priority'] == $b ['priority']) {
			if (strtolower ( $a ['title'] ) == strtolower ( $b ['title'] )) {
				return 0;
			}
			return (strtolower ( $a ['title'] ) < strtolower ( $b ['title'] )) ? - 1 : 1;
		}
		return ($a ['priority'] < $b ['priority']) ? - 1 : 1;
	}
	protected function decode_action($method) {
		$return = array ();
		if (strpos ( $method, 'Action' )) {
			$return ['action'] = $method;
		} else {
			$return ['action'] = 'VirtualAction';
		}
		$return ['level'] = 'administrator';
		$return ['title'] = '';
		$return ['hide'] = false;
		$return ['raw_title'] = '';
		$return ['priority'] = 0;
		$info = explode ( 'Action', $method );
		$return ['raw_title'] = $info [0];
		$info [0] = ucwords(str_replace ( '_', ' ', $info [0] ));
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
		$return ['title'] = $info [0];
		$return ['priority'] = $info [1];
		$meta = $return ['action'] . 'Meta';
		if (method_exists ( $this, $meta )) {
			if ($meta == 'VirtualActionMeta') {
				$return = $this->$meta ( $return ['title'], $return );
			} else {
				$return = $this->$meta ( $return );
			}
		}
		if ($return ['title'] === null || $return ['title'] == '') {
			return false;
		}
		return $return;
	}
	protected $baseURL = "";
	protected function render_script($script, $html = true) {
		$return = null;
//		$this->DEbug($script);
//		$this->tdebug($this->view);
//		$this->trace();
		$this->view->action ( $this );
		$pi = pathinfo ( strtolower ( $script ) );
		$string = false;
		if (! isset ( $pi ['extension'] )) {
			throw new exception ( $script . ' need extension' );
		}
		if ($pi ['extension'] != 'xml') {
			$string = $this->check_settings ( $script );
		}
		if ($string === false) {
			$orig = $script;
			$exists = file_exists ( $script );
			$path = null;
			if (! $exists) {
				$script = $this->template_path ( $script );
			}
			if ($script !== false) {
				switch ($pi ['extension']) {
					case 'xml' :
						$data = new bv28v_data_xml ( $this->application (), $script, true );
						$return = $data->load ();
						$html = false;
						break;
					default :
						$return = $this->view->render ( $script );
				}
			}
		} else {
/*			if($script=="results/display/results.phtml")
			{
				echo "<code>\n===========\n".htmlentities($string)."\n===========\n</code>";
			}
*/			$return = $this->view->render_string ( $string );
		}
		if ($html && null !== $return) {
			$return = str_replace ( "\n", '', $return );
			$return = str_replace ( "\r", '', $return );
		}
		return $return;
	}
	private function check_settings($script) {
		$return = false;
		if (null !== $this->settings ()) {
			$settings = $this->settings ()->all ();
			if (is_array ( $settings )) {
				$script = strtolower ( $script );
				$pi = pathinfo ( $script );
				$script = substr ( $script, 0, strlen ( $script ) - strlen ( $pi ['extension'] ) - 1 );
				$script = str_replace ( ' ', '_', $script );
				$script = str_replace ( '.', '_', $script );
				$script = str_replace ( '-', '_', $script );
				$keys = explode ( '/', $script );
				array_unshift ( $keys, 'views' );
				$working = $settings;
				foreach ( $keys as $key ) {
					if (isset ( $working [$key] )) {
						$working = $working [$key];
					} else {
						$working = false;
						break;
					}
				}
				if ($working !== false) {
					if (isset ( $working [$pi ['extension']] ) && ! isset ( $working ['use_file'] )) {
						$return = $working [$pi ['extension']];
					}
				}
			}
		}
		return $return;
	}
	protected function load_script($script) {
		$filename = $this->template_path ( $script );
		if (! file_exists ( $filename )) {
			// reverse the directory order, in the case of view files allow files to be overriden
			$dirs = $this->application ()->loader ()->includepath ( array('views'), true );
			$filename = $this->application ()->loader ()->find_file ( $filename, true, $dirs );
		}
		return $this->application ()->loader ()->file ( $filename );
	}
	protected $template_folders = array ('' );
	protected function template_path($filename) {
		$dirs = $this->application ()->loader ()->includepath ( array('views'), true );
		$newdirs = array ();
		foreach ( $this->template_folders as $tp ) {
			foreach ( $dirs as $dir ) {
				$newdirs [] = rtrim ( $dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $tp;
			}
		}
		$fullpath = $this->application ()->loader ()->find_file ( ltrim ( $filename, DIRECTORY_SEPARATOR ), true, $newdirs );
		return $fullpath;
	}
	public function set_template_folders($folders) {
		$cm = $this->controller_meta();
		$folders [] = $cm['slug'];
		$folders [] = '';
		$this->template_folders = $folders;
	}
	public function VirtualActions($actions) {
		return $actions;
	}
	public function VirtualAction($action, $content) {
		return $content . $action;
	}
	public function template_files($types = "*") {
		$return = array ();
		$dirs = $this->application ()->loader ()->includepath ( array('views') );
		$newdirs = array ();
		foreach ( $this->template_folders as $tp ) {
			foreach ( $dirs as $dir ) {
				$newdirs [] = rtrim ( $dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $tp;
			}
		}
		if (! is_array ( $types )) {
			$types = ( array ) $types;
		}
		foreach ( $newdirs as $dir ) {
			$d = dir ( $dir );
			while ( false !== ($entry = $d->read ()) ) {
				if ($entry [0] != '.') {
					$fullpath = $d->path . DIRECTORY_SEPARATOR . $entry;
					$pi = pathinfo ( $fullpath );
					if (in_array ( '*', $types ) || in_array ( strtolower ( $pi ['extension'] ), $types )) {
						$return [] = $fullpath;
					}
				}
			}
			$d->close ();
		}
		return $return;
	}
	protected function pre_dispatch() {
	}
	protected function dispatch() {
	}
	protected function post_dispatch() {
	}
	public function controller() {
		$this->view->args = array ();
		if (count ( func_get_args () ) > 0) {
			$this->view->args = func_get_args ();
		} else {
			$this->view->args [] = null;
		}
		$return = call_user_func_array ( array ($this, 'pre_dispatch' ), $this->view->args );
		if (null !== $return) {
			$this->view->args [0] = $return;
		}
		$return = call_user_func_array ( array ($this, 'dispatch' ), $this->view->args );
		if (null !== $return) {
			$this->view->args [0] = $return;
		}
		$return = call_user_func_array ( array ($this, 'post_dispatch' ), $this->view->args );
		if (null !== $return) {
			$this->view->args [0] = $return;
		}
		return $this->view->args [0];
	}
	protected function controller_meta() {
		$name = get_class ( $this );
		$title =  $name;
		$slug = strtolower($title);
		$return = array();
		$return['name']=$name;
		$return['title']=ucwords($title);
		$return['slug']=$slug;
		return $return;
	}
	protected function selected_action_page() {
		$split = explode ( '/', $this->application ()->page () );
		if (count ( $split ) < 4 || $split [3] == "") {
			$act = 'indexAction';
		} else {
			$split [3] = urlencode ( $split [3] );
			$split [3] = str_replace ( '%', '_', $split [3] );
			$split [3] = str_replace ( '.', '_2E', $split [3] );
			$act = $split [3] . 'Action';
		}
		$actions = $this->actions ();
		if (array_key_exists ( $act, $actions )) {
			return $actions [$act];
		}
		if (method_exists ( $this, 'catchAllAction' )) {
			return $actions ['catchAllAction'];
		}
		return null;
	}
	protected function selected_action() {
		return $this->selected_action_page ();
	}
	protected function csv_headers($file = null) {
		header ( "Content-type: application/csv" );
		if (null !== $file) {
			header ( "Content-Disposition: attachment; filename=$file.csv" );
		}
		header ( "Pragma: no-cache" );
		header ( "Expires: 0" );
	}
	protected function xml_headers() {
		header ( 'Content-type: text/xml');
	}
	protected function txt_headers() {
		header ( 'Content-Type: text/plain' );
	}
}