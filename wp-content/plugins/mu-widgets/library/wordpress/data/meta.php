<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv45v_data_meta {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $name;
	private $slug;
	private $show;
	private $default = 'default';
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __construct($name, $slug, $show=false) {
		return;
		$this->name = $name;
		$this->slug = $slug;
		$this->show = false;
		add_action ( 'init', array (&$this, 'initWPaction' ) );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function initWPaction() {
		$this->add_type ( $this->name );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function post_type() {
		$slug = substr($this->slug,0,15);
		$return = "{$slug}_opt";
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function scopes()
	{
		$posts=get_pages(array('post_type'=>$this->post_type()));
		$return = array();
		foreach($posts as $post)
		{
			$return[]=$post->post_title;
		}
		natsort($return);
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function add_type($name) {
		$labels = array ();
		$labels ['name'] = _x ( "{$name} Options", 'post type general name' );
		$labels ['singular_name'] = _x ( 'Option', 'post type singular name' );
		$labels ['add_new'] = _x ( 'Add New', 'Options' );
		$labels ['add_new_item'] = __ ( 'Add New Option' );
		$labels ['edit_item'] = __ ( 'Edit Option' );
		$labels ['new_item'] = __ ( 'New Option' );
		$labels ['view_item'] = __ ( 'View Option' );
		$labels ['search_items'] = __ ( 'Search Options' );
		$labels ['not_found'] = __ ( 'No Options found' );
		$labels ['not_found_in_trash'] = __ ( 'No Options found in Trash' );
		$labels ['parent_item_colon'] = '';
		$labels ['menu_name'] = "{$name} Options";
		
		$args = array ();
		$args ['labels'] = $labels;
		$args ['description'] = 'Exportable options';
		$args ['public'] = false;
		$args ['publicly_queryable'] = false; // final
		$args ['exclude_from_search'] = false; // final
		$args ['show_ui'] = true;
		$args ['show_in_menu'] = $this->show; //will depend on debug
		$args ['menu_position'] = 0;
		$args ['menu_icon'] = null;
		$args ['capability_type'] = 'page';
		$args ['hierarchical'] = true;
		$args ['query_var'] = true;
		$args ['has_archive'] = true;
		$args ['menu_position'] = 5;
		$args ['taxonomies'] = array ();
		$args ['supports'] = array ('title', 'custom-fields' );
		register_post_type ( $this->post_type (), $args );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function option(&$name = null) {
		if (null === $name) {
			$name = $this->default;
		}
		$page = get_page_by_title ( $name, ARRAY_A, $this->post_type () );
		if (null === $page) {
			$name = wp_insert_post ( array ('post_title' => $name, 'post_type' => $this->post_type (), 'post_status' => 'publish' ) );
			return;
		}
		$name = $page ['ID'];
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function update($value, $key = null, $option = null) {
		$this->option ( $option );
		$value = ( array ) $value;
		if (null === $key) {
			$keys = get_post_custom_keys ( $option );
			foreach ( ( array ) $keys as $key ) {
				if (! isset ( $value [$key] )) {
					delete_post_meta ( $option, $key );
				}
			}
			foreach ( $value as $key => $item ) {
				$return = update_post_meta ( $option, $key, $item );
			}
		} else {
			$return = update_post_meta ( $option, $key, $value );
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function get($key = null, $option = null) {
		$this->option ( $option );
		$return = get_post_meta ( $option, $key );
		if (null ===  $key) {
			$return = array();
			foreach(get_post_custom($option) as $key=>$value)
			{
				$data = unserialize($value[0]);
				if($data===false)
				{
					$return[$key] = $value[0];
				}
				else
				{
					$return[$key] = $data;
				}
			}
			return $return;
		}
		else
		{
			foreach ( $return as $return ) {
				return $return;
			}
		}
		return null;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function delete($key = null, $option = null) {
		$this->option ( $option );
		if (null === $key) {
			wp_delete_post ( $option, true );
		} else {
			delete_post_meta ( $this->option, $key );
		}
	}
}