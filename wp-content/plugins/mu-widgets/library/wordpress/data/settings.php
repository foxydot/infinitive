<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv46v_data_settings extends bv46v_data_settings {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __construct(&$application, $array = null)
	{
		parent::__construct($application,$array);
		if(is_array($array))
		{
			$this->set_slug($array['slug']);
		}
	}
	private $slug = '';
	public function set_slug($slug=null)
	{
		if(null===$slug)
		{
			$slug = $this->application()->slug;
		}
		$this->slug = $slug;		
	}
	public function slug()
	{
		return $this->slug;
	}
	private function read_post($option=null) {
		if($option===null)
		{
			$option = 'default';
		}
		$post = get_page_by_path ( md5($option.$this->slug()), OBJECT, 'dc_'.$this->slug() );
		$return = array ();
		if ($post) {
			$return = bv46v_data_json::decode ( $post->post_content, true );	
		}
		return $return;
	}

/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function options($show_hidden = false, $saved_only = false, $file_only=false) {
		$options = array ();
		if(!$file_only)
		{
		$sql = "
SELECT `post_title`
FROM `%s` 
WHERE `post_type` = 'dc_%s';
";
		$sql = sprintf ( $sql, $this->table ( 'posts' )->name (), $this->slug() );
		$return = $this->table ()->execute ( $sql );
		$exclude = array ();
		foreach ( $return as $option ) {
			if (! in_array ( $option ['post_title'], $exclude )) {
				$options [$option ['post_title']] = $option ['post_title'];
			}
		}
		}
		if (! $saved_only) {
			$options = parent::options ( $show_hidden, $options );
		}
		return $options;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __get($key) {
		$value = parent::__get ( $key );
		$value = apply_filters ( "{$this->application ()->slug}_read_{$key}", $value, $this->_option );
		$value = apply_filters ( "v46v_{$this->application ()->slug}_read_{$key}", $value, $this->_option );
		return $value;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __set($key, $value) {
		$data = $this->read_post ($this->_option);
		$data [$key] = $value;
		$this->write_post ( $data,$this->_option );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function write_post($data,$option=null) {
		if($option===null)
		{
			$option = 'default';
		}
		if(null!==$this->application()->write_only)
		{
			foreach(array_keys($data) as $key)
			{
				if(!in_array($key,$this->application()->write_only))
				{
					unset($data[$key]);
				}
			}	
		}
		$data = apply_filters ( "{$this->slug()}_write", $data ,$option);
		$data = apply_filters ( "v46v_{$this->slug()}_write", $data,$option );
		foreach ( $data as $key => &$value ) {
			$value = apply_filters ( "{$this->slug()}_write_{$key}", $value );
			$value = apply_filters ( "v46v_{$this->slug()}_write_{$key}", $value );
		}
		$data['__version__v46v__']='v46v';
		$post = array ();
		$id = get_page_by_path ( md5($option.$this->slug()), OBJECT, 'dc_'.$this->slug() );
		if (null !== $id) {
			$id = $id->ID;
		}
		$post ['ID'] = $id;
		$post ['post_title'] = $option;
		$post ['post_name'] = md5($option.$this->slug());
		$post ['post_content'] = addslashes ( json_encode ( $data ) );
		$post ['post_status'] = 'publish';
		$post ['post_type'] = 'dc_'.$this->slug();
		if (null === $post ['ID']) {
			$id = wp_insert_post ( $post );
		} else {
			$id = wp_update_post ( $post );
		}
		$this->_data = null;
		return $id;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function post($key = null) {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST') {		
			$this->write ( $_POST, $key );
		}
		if (null === $key) {
			return $this->data ();
		} else {
			return $this->$key;
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function write($data, $key = null) {
		//echo 'here ' . $key;
		if (null === $key) {
			$this->write_post ( $data,$this->_option );
		} else {
			if (isset ( $data [$key] )) {
				$this->__set ( $key, $data [$key] );
			} else {
				$this->__unset ( $key );
			}
		}
	}
	// only to be used to move data as it get only the save data and does not apply filters
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function copy($dst, $src = 'default') {
		$data = $this->read_post ( $src );
		$option = $this->_option;
		$this->_option = $dst;
		$this->write ( $data );
		$this->_option = $option;
	}
	// bare in mine that deleting a post deletes all meta too.
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function delete($option = 'default') {
		$sql = "
SELECT `ID` FROM `%s` WHERE `post_name` = '%s';
";
		$sql = sprintf($sql,$this->table('posts')->name(),md5($option.$this->slug()));
		$data = $this->table()->execute($sql);
		foreach($data as $datum)
		{
			wp_delete_post ( $datum['ID'], true );
		}
	}
	// unset works only on saved data. values may still much through from the hard settings
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __unset($key) {
		$data = $this->read_post ();
		unset ( $data [$key] );
		$this->write_post ( $data );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public static function setup(&$application,$show = false) {
		$labels = array ();
		$labels ['name'] = 'DCoda Settings';
		$labels ['singular_name'] = 'Setting';
		$labels ['add_new'] = 'Add New';
		$labels ['add_new_item'] = 'Add New Setting';
		$labels ['edit_item'] = 'Edit Setting';
		$labels ['new_item'] = 'New Setting';
		$labels ['view_item'] = 'View Setting';
		$labels ['search_items'] = 'Search Settings';
		$labels ['not_found'] = 'No Settings found';
		$labels ['not_found_in_trash'] = 'No Settings found in Trash';
		$labels ['parent_item_colon'] = '';
		$labels ['menu_name'] = $labels ['name'];
		
		$args = array ();
		$args ['labels'] = $labels;
		$args ['description'] = 'Exportable Settings';
		$args ['public'] = false;
		$args ['publicly_queryable'] = false; // final
		$args ['exclude_from_search'] = false; // final
		$args ['show_ui'] = true;
		$args ['show_in_menu'] = $show; //will depend on debug
		$args ['menu_position'] = 100;
		$args ['menu_icon'] = null;
		$args ['capability_type'] = 'page';
		$args ['hierarchical'] = true;
		$args ['query_var'] = true;
		$args ['has_archive'] = true;
		$args ['menu_position'] = 5;
		$args ['taxonomies'] = array ();
		//$args ['supports'] = array ('title', 'editor');
		//register_post_type ( 'dcoda_settings', $args );
		$labels ['name'] = $application->name.' Settings';
		$labels ['menu_name'] = $labels ['name'];
		$args ['labels'] = $labels;
		$args ['supports'] = array ('title', 'editor' );
		register_post_type ( 'dc_'.$application->slug, $args );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function refresh() {
		$settings = array ();
		$data = parent::refresh ();
		if (null !== $data) {
			$settings [] = $data;
		}
		$data = $this->read_post ($this->_option);
		if (null !== $data) {
			$settings [] = $data;
		}
		$this->_data = bv46v_data_array::merge ( $settings );
		$this->_data = apply_filters ( "{$this->application ()->slug}_read", $this->_data, $this->_option );
		$this->_data = apply_filters ( "v46v_{$this->application ()->slug}_read", $this->_data, $this->_option );
		return $this->_data;
	}


}
