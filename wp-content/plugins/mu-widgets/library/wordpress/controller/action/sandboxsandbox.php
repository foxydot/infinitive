<?php
class wv28v_controller_action_sandboxsandbox extends wv28v_controller_action_adminmenu {
	public function controller_meta() {
		$return = parent::controller_meta ();
		$return ['menu'] = 'Sandbox';
		$return ['title'] = 'Sandbox';
		return $return;
	}
	public function ApplicationsAction($content) {
		$page = '';
		foreach ( $this->application ()->applications () as $application ) {
			$page .= $application->settings ()->application ['name'] . "<br/>";
		}
		$page .= "<br><b>Controlling library is: </b>" . $this->application ()->settings ()->application ['name'];
		return $content . $page;
	}
	public function ts_testAction($content) {
		foreach ( $this->test as $test ) {
		
		}
		return $content;
	}
	public function PluginsAction($content) {
		$page = '';
		//$this->view->data = get_option ( 'plugins' );
		if (empty ( $this->view->data )) {
			$data = plugins_api ( 'query_plugins', array ('page' => 1, 'per_page' => 30, 'author' => 'dcoda' ) );
			$this->view->data = array ();
			foreach ( $data->plugins as $plugin ) {
				$this->view->data [] = plugins_api ( 'plugin_information', array ('slug' => $plugin->slug ) );
			
			}
			update_option('plugins',$this->view->data);
		}
		$page = $this->render_script ( 'common/plugins.phtml', false );
		return $content . $page;
	}
	public function xss_vulnAction($content) {
		$page = '<script>alert("test");</script><b onclick="alert(' . "'test'" . ')";>this is a test</b><br/><br/><br/><br/>';
		$page = $this->xss_clean($page);
		return $content . $page . htmlentities ( $page );
	}
	public function bugtestAction($content) {
		$file = 'survey.xml';
		$data = file_get_contents ( $this->application ()->loader ()->find_file ( $file ) );
		$this->sdebug ( $data );
		$fields = $this->settings ()->field_definitions ();
		foreach ( $fields as $field ) {
			$options = $field->options;
			$this->sdebug ( $options );
			$this->sdebug ( "\t this is a test \n\n\n" );
			$options = explode ( chr ( 13 ), $options );
			$this->debug ( $options );
			//break;
		}
		/*		$settings = $this->settings();
		$results = $settings->views['results']['display']['results']['phtml'];
		$this->debug($results);		
		$results = explode("\n",$results);
		$this->debug($results);		
*/		return $content;
	}
	function xss_clean($data) {
		// Fix &entity\n;
		$data = str_replace ( array ('&amp;', '&lt;', '&gt;' ), array ('&amp;amp;', '&amp;lt;', '&amp;gt;' ), $data );
		$data = preg_replace ( '/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data );
		$data = preg_replace ( '/(&#x*[0-9A-F]+);*/iu', '$1;', $data );
		$data = html_entity_decode ( $data, ENT_COMPAT, 'UTF-8' );
		
		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace ( '#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data );
		
		// Remove javascript: and vbscript: protocols
		$data = preg_replace ( '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data );
		$data = preg_replace ( '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data );
		$data = preg_replace ( '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data );
		
		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace ( '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data );
		$data = preg_replace ( '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data );
		$data = preg_replace ( '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data );
		
		// Remove namespaced elements (we do not need them)
		$data = preg_replace ( '#</*\w+:\w[^>]*+>#i', '', $data );
		
		do {
			// Remove really unwanted tags
			$old_data = $data;
			$data = preg_replace ( '#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data );
		} while ( $old_data !== $data );
		
		// we are done...
		return $data;
	}
	public function plexAction($content) {
		$file = '/Volumes/amber-users/damianmanifold/Library/Application Support/Plex Media Server/Plug-in Support/Databases/com.plexapp.plugins.library.db';
		//$file = '/Volumes/Users/com.plexapp.plugins.library.db';
		$conn = new PDO ( "sqlite:" . $file );
		$sql = "SELECT media_parts.file , media_items.library_section_id,metadata_item_id  FROM media_parts join media_items on media_parts.media_item_id = media_items.id join metadata_items on metadata_item_id = meta_data_items.idwhere file like '%shield%'";
		$files = array ();
		foreach ( $conn->query ( $sql ) as $row ) {
			if ($row ['library_section_id'] != '18') {
				$files [$row ['metadata_item_id']] [$row ['file']] = $row ['file'];
			}
		}
		$this->Debug ( $files );
		return $content;
	}
	public function pagesAction($content) {
		global $submenu;
		global $menu;
		global $_wp_real_parent_file;
		global $_wp_submenu_nopriv;
		global $_registered_pages;
		global $_parent_pages;
		
		$menu_slug = plugin_basename ( 'Settings' );
		$parent_slug = plugin_basename ( 'Settings' );
		
		$this->debug ( '$menu_slug', $menu_slug, '<br>' );
		$this->debug ( '$parent_slug', $parent_slug, '<br>' );
		$this->debug ( '$submenu', $submenu, '<br>' );
		$this->debug ( '$menu', $menu, '<br>' );
		$this->debug ( '$_wp_real_parent_file', $_wp_real_parent_file, '<br>' );
		$this->debug ( '$_wp_submenu_nopriv', $_wp_submenu_nopriv, '<br>' );
		$this->debug ( '$_registered_pages', $_registered_pages, '<br>' );
		$this->debug ( '$_parent_pages', $_parent_pages, '<br>' );
		return $content;
	}
	public function pathsAction($content) {
		$this->debug ( $this->application ()->loader ()->subfolders (), $this->application ()->loader ()->includepath () );
		return $content;
	}
	public function reWritesAction($content) {
		global $wp_rewrite;
		if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
			$wp_rewrite->flush_rules ();
		}
		$rules = $wp_rewrite->wp_rewrite_rules ();
		$return = "<pre>";
		$return .= print_r ( $rules, true );
		$return .= "</pre>";
		$return .= "<form method='post'><input type=submit value=Flush /></form>";
		return $content . $return;
	}
	public function TestingAction() {
		$this->Debug ( $this->settings ()->all () );
		return;
		$array = array ();
		$array ['one'] ['two'] = 'test';
		$array ['one'] ['three'] = 'test';
		$array ['four'] = 'test';
		$this->Debug ( 'here', $this->convert ( $array ) );
	}
	private function convert($array, $working = null, $keys = null) {
		$return = array ();
		foreach ( $this->map_array ( $array ) as $work ) {
			$key = array_shift ( $work ['keys'] );
			$post = implode ( '][', $work ['keys'] );
			if (! empty ( $post )) {
				$key .= "[$post]";
			}
			$return [$key] = $work ['value'];
		}
		return $return;
	}
	private function map_array($array, $working = null, $keys = null) {
		if ($working === null) {
			$this->working = array ();
		}
		foreach ( $array as $key => $value ) {
			$keys [] = $key;
			if (is_array ( $value )) {
				$working = $this->map_array ( $value, $working, $keys );
			} else {
				$working [] = array ('keys' => $keys, 'value' => $value );
			}
			array_pop ( $keys );
		}
		return $working;
	}
	private function TestingOptGroupAction($content) {
		$string = "option1\ncat1/option2\ncat2/option3\ncat2/scat1/option4\ncat2/scat1/option5\ncat3/option6\ncat2/scat2/option 7";
		$tmpa = explode ( "\n", $string );
		$array = $this->make_opt_array ( $tmpa, '' );
		$options = $this->options ( $array, '' );
		$this->debug ( $_POST );
		echo "<form method='post'>";
		echo "<select name='test'>{$options}</select>";
		echo "<input type=submit></form>";
	}
	
	public function make_opt_array($array, $sep = '/', $base = '') {
		$return = array ();
		foreach ( ( array ) $array as $value ) {
			$key = $base . $value;
			if (! empty ( $sep ) && strpos ( $value, $sep ) !== false) {
				$value = explode ( $sep, $value, 2 );
				$key = $base . $value [0];
				$value = $this->make_opt_array ( $value [1], $sep, $key . $sep );
			}
			if (isset ( $return [$key] )) {
				$return [$key] = array_merge_recursive ( ( array ) $return [$key], ( array ) $value );
			} else {
				$return [$key] = $value;
			}
		}
		return $return;
	}
	public function options($array, $sep = '/', $depth = 0) {
		$return = '';
		$lspaces = str_pad ( '', ($depth) * 4 * 6, '&nbsp;' );
		$ospaces = str_pad ( '', ($depth - 1) * 4 * 6, '&nbsp;' );
		$depth ++;
		$tabs = str_pad ( '', $depth, "\t" );
		foreach ( ( array ) $array as $key => $value ) {
			if (is_array ( $value )) {
				$label = explode ( $sep, $key );
				$label = $label [count ( $label ) - 1];
				$return .= "{$tabs}<optgroup label='{$lspaces}{$label}'>\n";
				$return .= $this->options ( $value, $sep, $depth );
				$return .= "{$tabs}</optgroup>\n";
			} else {
				$return .= "{$tabs}<option value='{$key}'>{$ospaces}{$value}</option>\n";
			}
		}
		return $return;
	}
	public function post_array($array) {
		$key = '';
		$first = true;
		foreach ( $array as $value ) {
			if ($first) {
				$key = $value;
				$first = false;
			} else {
				$key .= "[{$value}]";
			}
		}
		return $key;
	}
}