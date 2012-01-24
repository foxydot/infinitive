<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv45v_data_settings extends bv45v_data_settings {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function get_post($get_id = false) {
		$post = get_page_by_title ( $this->application ()->slug, OBJECT, 'dcoda_settings' );
		$return = null;
		if ($get_id) {
			if ($post) {
				$return = $post->ID;
			}
		} else {
			$return = array ();
			if ($post) {
				$return = bv45v_data_json::decode ( $post->post_content, true );
			
			}
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function options($show_hidden = false, $saved_only = false) {
		$sql = "
SELECT meta_key
FROM `%s` 
WHERE `post_id` = %d;
		";
		$sql = sprintf ( $sql, $this->table ( 'postmeta' )->name (), $this->get_post_id () );
		$return = $this->table ()->execute ( $sql );
		$exclude = array ('_edit_last', '_edit_lock' );
		$options = array ();
		foreach ( $return as $option ) {
			if (! in_array ( $option ['meta_key'], $exclude )) {
				$options [$option ['meta_key']] = $option ['meta_key'];
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
	private function get_post_id() {
		$id = $this->get_post ( true );
		if (null === $id) {
			$id = $this->write_post ( array () );
		}
		return $id;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __get($key) {
		$value = parent::__get ( $key );
		$value = apply_filters ( "{$this->application ()->slug}_read", $value, $key, $this->_option );
		$value = apply_filters ( "{$this->application ()->slug}_read_{$key}", $value, $this->_option );
		$value = apply_filters ( "v45v_{$this->application ()->slug}_read", $value, $key, $this->_option );
		$value = apply_filters ( "v45v_{$this->application ()->slug}_read_{$key}", $value, $this->_option );
		if (null !== $this->_option) {
			$value = apply_filters ( "{$this->application ()->slug}_read_{$key}_{$this->_option}", $value );
			$value = apply_filters ( "v45v_{$this->application ()->slug}_read_{$key}_{$this->_option}", $value );
		}
		return $value;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __set($key, $value) {
		$value = apply_filters ( "{$this->application ()->slug}_write", $value, $key, $this->_option );
		$value = apply_filters ( "{$this->application ()->slug}_write_{$key}", $value, $this->_option );
		$value = apply_filters ( "v45v_{$this->application ()->slug}_write", $value, $key, $this->_option );
		$value = apply_filters ( "v45v_{$this->application ()->slug}_write_{$key}", $value, $this->_option );
		if (null !== $this->_option) {
			$value = apply_filters ( "{$this->application ()->slug}_write_{$key}_{$this->_option}", $value );
			$value = apply_filters ( "v45v_{$this->application ()->slug}_write_{$key}_{$this->_option}", $value );
			$data = $this->get_meta ();
		} else {
			$data = $this->get_post ();
		}
		$data [$key] = $value;
		if (null !== $this->_option) {
			$this->write_meta ( $data );
		} else {
			$this->write_post ( $data );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function code_array(&$data) {
		//array_walk_recursive ( $data, array ($this, 'code' ) );
		$data = json_encode ( $data );
		$data = addcslashes ( $data );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function code(&$data) {
		$data = ($data);
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function write_meta($data) {
		//$this->code_array ( $data );
		//print_r ( $data );
		update_post_meta ( $this->get_post_id (), $this->_option, $data );
		$this->_data = null;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function get_meta($key = null) {
		if (null === $this->_option && null === $key) {
			return array ();
		}
		$option = $key;
		if (null===$option && null !== $this->_option) {
			$option = $this->_option;
		}
		$meta = get_post_meta ( $this->get_post_id (), $option, true );
		if (null === $meta) {
			$meta = array ();
			$this->write_meta ( $meta );
		} else {
			if ($meta === "") {
				$meta = array ();
			}
		
		//$meta = bv45v_data_json::decode ( $meta, true );
		}
		return $meta;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function write_post($data) {
		$post = array ();
		$id = get_page_by_title ( $this->application ()->slug, OBJECT, 'dcoda_settings' );
		if (null !== $id) {
			$id = $id->ID;
		}
		$post ['ID'] = $id;
		$post ['post_title'] = $this->application ()->slug;
		$post ['post_name'] = $this->application ()->slug;
		$post ['post_content'] = addslashes ( json_encode ( $data ) );
		$post ['post_status'] = 'publish';
		$post ['post_type'] = 'dcoda_settings';
		if (null === $post ['ID']) {
			$id = wp_insert_post ( $post );
		} else {
			$id = wp_update_post ( $post );
		}
		//update_post_meta($id,'default',$post['post_content']);
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
			foreach ( $data as $key => &$value ) {
				$value = apply_filters ( "{$this->application()->slug}_write", $value );
				$value = apply_filters ( "{$this->application()->slug}_write_{$key}", $value );
				$value = apply_filters ( "v45v_{$this->application()->slug}_write", $value );
				$value = apply_filters ( "v45v_{$this->application()->slug}_write_{$key}", $value );
				if (null !== $this->_option) {
					$value = apply_filters ( "{$this->application()->slug}_write_{$key}_{$this->_option}", $value );
					$value = apply_filters ( "v45v_{$this->application()->slug}_write_{$key}_{$this->_option}", $value );
				}
			}
			if (null !== $this->_option) {
				$this->write_meta ( $data );
			} else {
				$this->write_post ( $data );
			}
		
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
	private function get($key = null) {
		//echo 'here ' . $key;
		if (null === $key) {
			return $this->get_post ();
		} else {
			return $this->get_meta ( $key );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function copy($dst, $src = null) {
		$data = $this->get ( $src );
		$option = $this->_option;
		$this->_option = $dst;
		$this->write ( $data );
		$this->_option = $option;
	}
	// bare in mine that deleting a post deletes all meta too.
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function delete($key = null) {
		if (null === $key) {
			wp_delete_post ( $this->get_post_id (), true );
		} else {
			delete_post_meta ( $this->get_post_id (), $key );
		}
	}
	// unset works only on saved data. values may still much through from the hard settings
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __unset($key) {
		$data = $this->get_post ();
		unset ( $data [$key] );
		$this->write_post ( $data );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public static function setup($show = false) {
		$labels = array ();
		$labels ['name'] = _x ( "DCoda Settings", 'post type general name' );
		$labels ['singular_name'] = _x ( 'Setting', 'post type singular name' );
		$labels ['add_new'] = _x ( 'Add New', 'Settings' );
		$labels ['add_new_item'] = __ ( 'Add New Setting' );
		$labels ['edit_item'] = __ ( 'Edit Setting' );
		$labels ['new_item'] = __ ( 'New Setting' );
		$labels ['view_item'] = __ ( 'View Setting' );
		$labels ['search_items'] = __ ( 'Search Settings' );
		$labels ['not_found'] = __ ( 'No Settings found' );
		$labels ['not_found_in_trash'] = __ ( 'No Settings found in Trash' );
		$labels ['parent_item_colon'] = '';
		$labels ['menu_name'] = "DCoda Settings";
		
		$args = array ();
		$args ['labels'] = $labels;
		$args ['description'] = 'Exportable Settings';
		$args ['public'] = false;
		$args ['publicly_queryable'] = false; // final
		$args ['exclude_from_search'] = false; // final
		$args ['show_ui'] = true;
		$args ['show_in_menu'] = $show; //will depend on debug
		$args ['menu_position'] = 0;
		$args ['menu_icon'] = null;
		$args ['capability_type'] = 'page';
		$args ['hierarchical'] = true;
		$args ['query_var'] = true;
		$args ['has_archive'] = true;
		$args ['menu_position'] = 5;
		$args ['taxonomies'] = array ();
		$args ['supports'] = array ('title', 'editor', 'custom-fields' );
		register_post_type ( 'dcoda_settings', $args );
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
		$data = $this->get_post ();
		if (null !== $data) {
			$settings [] = $data;
		}
		$data = $this->get_meta ();
		if (null !== $data) {
			$settings [] = $data;
		}
		$this->_data = bv45v_data_array::merge ( $settings );
		return $this->_data;
	}
}