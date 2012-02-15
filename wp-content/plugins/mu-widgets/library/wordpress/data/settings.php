<?php
/**
 * Settings
 *
 * Add WordPress functionality to settings
 *
 * @package library
 * @subpackage WordPress
 */
class wv47v_data_settings extends bv47v_data_settings {
	/**
	 * construct
	 *
	 * @access public
	 * @param object $application pointer to top instance of currently running plugin
	 * @param array $array optional parameters
	 * @returns null
	 */
	public function __construct(&$application, $array = null)
	{
		parent::__construct($application,$array);
		if(is_array($array))
		{
			$this->set_slug($array['slug']);
		}
	}
	/**
	 * slug
	 *
	 * The slug to be used to use in posttype
	 * @var string
	 */
	protected $slug = '';
	/**
	 * set slug
	 *
	 * set the slug to the supplied value or the applications slug
	 *
	 * @param string $slug the new slug
	 * @returns null
	 */
	public function set_slug($slug=null)
	{
		if(null===$slug)
		{
			$slug = $this->application()->slug;
		}
		$this->slug = $slug;		
	}
	/**
	 * slug
	 * 
	 * allow access to the the private slug
	 *
	 * @returns null
	 */  
	public function slug()
	{
		return $this->slug;
	}
	/**
	 * post name
	 * 
	 * uses the slug , title and md5 to make a unique post name to store the 
	 * data, has to be done has WordPress make the post_name unique across
	 * all post_types and not just within a post type. meaning you cannot use
	 * the same twice
	 *
	 * @param string $postTitle the title to encode
	 * @returns string
	 */  
	public function postName($postTitle)
	{
		if(is_array($postTitle))
		{
			$postTitle = array_pop($postTitle);
		}
		$return = md5($postTitle.$this->slug());
		return $return;
	}
	public function postPath($postTitles)
	{
		$return = array();
		if(is_string($postTitles))
		{
			$postTitles=array($postTitles);
		}
		foreach($postTitles as $postTitle)
		{
			$return[] = $this->postName($postTitle);
		}
		$return=implode('/',$return);
		return $return;
	}
	/**
	 * post type
	 * 
	 * get the post type of the current plugin 
	 *
	 * @returns string
	 */  
	public function postType()
	{
		$return = 'dc_'.$this->slug();
		return $return;
	}
	/**
	 * get post
	 * 
	 * returns the post holding the data 
	 *
	 * @param string $postTitle the title to of the post
	 * @returns OBJECT
	 */  
	public function getPost($postTitle=null) {
		if($postTitle===null)
		{
			$postTitle = 'default';
		}
		$post = get_page_by_path ( $this->postName($postTitle),
									ARRAY_A, $this->postType() );
		$return = false;
		if (null!==$post) {
			$return = $post;	
		}
		return $return;
	}
	/**
	 * get post data
	 * 
	 * returns the data held in the post 
	 *
	 * @param string $postTitle the title to of the post
	 * @returns array
	 */  
	private function getPostData($postTitle=null) {
		$post = $this->getPost($postTitle);
		$return = false;
		if ($post!==false) {
			$return = bv47v_data_json::decode ( $post['post_content'], true );	
			$this->table()->stripslashes($return);
		}
		return $return;
	}
	/**
	 * get post id
	 * 
	 * returns the id of the post the data is kept in 
	 *
	 * @param string $postTitle the title to of the post
	 * @returns integer the id of the post or false if not found
	 */  
	public function getPostID($postTitle=null) {
		$post = $this->getPost($postTitle);
		$return = false;
		if ($post!==false) {
			$return = $post['ID'];	
		}
		return $return;
	}
	/**
	 * filter name
	 * 
	 * enforces rules on the form name
	 *
	 * @param string $name the form name
	 * @returns string the filtered form name
	 */  
	public function filter_name($name)
	{
		$name = strtolower($name);
		$return = '';
		for($i=0;$i<strlen($name);$i++)
		{
			$letter = substr($name,$i,1);
			if($letter == '_' || ($i==0 && $letter=='$'))
			{
			}
			elseif(strpos('abcdefghijklmnopqrstuvwxyz1234567890',$letter)===false || $letter=='-')
			{
				$letter = '_';
			}
			$return.=$letter;
		}
		$return = trim($return,'_');
		if(!$this->dodebug())
		{
			$return = trim($return,'$');
		}
		return $return;
	}
	/**
	 * forms
	 * 
	 * returns info about the data that is stored and also any tables associated
	 * with them 
	 *
	 * @param boolean $show_hidden show files beginning $_ normally for debuggind
	 * @param boolean $saved_only show only forms saved in tables
	 * @param boolean $file_only show only data saved in files
	 * @returns integer the id of the post or false if not found
	 */  
	public function forms($show_hidden = false,$saved_only=false,$file_only=false) {
		$options = array ();
		if(!$file_only)
		{
			$sql = "
SELECT	`post_title`
FROM	`%s` 
WHERE	`post_type` = '%s' AND
		`post_parent` = 0;
";
		$sql = sprintf ( $sql, $this->table ( 'posts' )->name (), $this->postType() );
		$return = $this->table ()->execute ( $sql );
		$exclude = array ();
		foreach ( $return as $option ) {
			if (! in_array ( $option ['post_title'], $exclude )) {
				$options [$option ['post_title']] = $option ['post_title'];
			}
		}
		}
		if (! $saved_only) {
			$options = parent::forms ( $show_hidden, $options );
		}
		$tables = $this->table ()->show_tables ( "{$this->slug}_%" );
		$forms = array ();
		$new = array ('name' => null, 'table' => null, 'count' => '' );
		foreach ( $options as $option ) {
			$forms [$option] = $new;
			$forms [$option] ['name'] = $option;
		}
		foreach ( $tables as $table ) {
			$option = explode ( "{$this->slug}_", $table );
			$option = $option [1];
			if (! isset ( $forms [$option] )) {
				$forms [$option] = $new;
			}
			$forms [$option] ['table'] = $table;
			$forms [$option] ['count'] = $this->table ( $table )->count ();
		}
		return $forms;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	// legacy remove in a few versions
	public function options($show_hidden = false, $saved_only = false, $file_only=false) {
		$options = array_keys($this->forms($show_hidden,$saved_only,$file_only));
		return $options;
	}
	/**
	 * write post
	 * 
	 * save post to table 
	 *
	 * @param array $data the data
	 * @param string $option optional alternate name to write data to
	 * @param string $key optional, limits update to specified key only
	 * @returns integer the id of the post or false if not found
	 */  
	public function writePost($data,$option=null,$key = null,$post=null) {
		if (null !== $key) {
			$old = $this->data();
			if(isset($data[$key])){
				$old[$key] = $data[$key];
			} else {
				unset($old[$key]);
			}
			$data = $old;
		}
		if($option===null)
		{
			$option = $this->_option;
		}
		if($option===null)
		{
			$option = 'default';
		}
		$path = $this->postPath($option);
		if(is_array($option))
		{
			$option = array_pop($option);
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
		if(null===$post)
		{
			$data = apply_filters ( "{$this->slug()}_write", $data ,$option);
			$data['__version__v47v__']='v47v';
			$post = array ();
			$post ['post_content'] = addslashes ( json_encode ( $data ) );
			$post ['post_title'] = $option;
			$post ['post_name'] = $this->postName($option);
		}
		$id = get_page_by_path ( $path, OBJECT, $this->postType() );
		if (null !== $id) {
			$id = $id->ID;
		}
		$post ['post_status'] = 'publish';
		$post ['post_type'] = $this->postType();
		if (null === $id) {
			$id = wp_insert_post ( $post );
		} else {
			$post ['ID'] = $id;
			$id = wp_update_post ( $post );
		}
		$this->_data = null;
		return $id;
	}
	/**
	 * post
	 *
	 * quick way to check if the data is to be written and al get it
	 *
	 * @param string $key optionally limits update to that key
	 * @returns array they data
	 */
	public function post($key = null) {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST') {		
			$this->writePost ( $_POST, $this->_option,$key );
		}
		if (null === $key) {
			return $this->data ();
		} else {
			return $this->$key;
		}
	}
	/**
	 * copy
	 *
	 * move the data from one post to another
	 *
	 * @param string $dst title of the post to hold to copy
	 * @param string $src the title of the post holding the data 
	 * @returns null
	 */
	public function copy($dst, $src = 'default') {
		$data = $this->getPostData ( $src );
		$this->writePost ( $data,$dst );
	}
	/**
	 * delete
	 *
	 * delete the saved data
	 *
	 * @param string $postTitle title of the post to delete
	 * @returns bolean base on wether the post was found
	 */
	public function delete($postTitle = 'default') {
		$ID = $this->getPostID($postTitle);
		$return = false;
		if( false !== $ID )
		{
			wp_delete_post ( $ID, true );
			$return = true;
		}
		return $return;
	}
	/**
	 * setup
	 *
	 * setup custom post type
	 *
	 * @param OBJECT $application pointer to currently active plugin to look up values
	 * @returns null
	 */
	public static function setup(&$application,$show = false) {
		$args = array (
			'labels' => array (
				'name' 				 	=> $application->name.' Settings',
				'singular_name' 	 	=> 'Setting',
				'add_new' 			 	=> 'Add New',
				'add_new_item'		 	=> 'Add New Setting',
				'edit_item' 		 	=> 'Edit Setting',
				'new_item' 		 	 	=> 'New Setting',
				'view_item' 		 	=> 'View Setting',
				'search_items' 		 	=> 'Search Settings',
				'not_found' 		 	=> 'No Settings found',
				'not_found_in_trash' 	=> 'No Settings found in Trash',	
				'parent_item_colon'  	=> '',
				'menu_name' 		 	=> $application->name.' Settings'
			),
			'description' => 'Exportable Settings',
			'public' => false,
			'publicly_queryable' => false,
			'exclude_from_search' 	=> false,
			'show_ui' 				=> true,
			'show_in_menu' 			=> $show,
			'menu_position' 		=> 100,
			'menu_icon' 			=> null,
			'capability_type' 		=> 'page',
			'hierarchical' 			=> true,
			'query_var' 			=> true,
			'has_archive' 			=> true,
			'menu_position' 		=> 5,
			'taxonomies' 			=> array (),
			'supports'				=> array (
										'title',
										'editor',
										'excerpt',
										'custom-fields',
										'page-attributes'
									)
		);
		register_post_type ( 'dc_'.$application->slug, $args );
	}
	/**
	 * data
	 *
	 * a data from data saved in WordPress options to the existing data
	 *
	 * @param boolean $refresh ignore the cache and recreate the data
	 * @returns array combines data from json/xml files and stored in WordPress tables
	 */
	public function data($refresh=false) {
		if (null === $this->_data || $refresh === true) {
			$settings = array ();
			// get data stored in files
			$data = parent::data (true);
			if (null !== $data) {
				$settings [] = $data;
			}
			// get data stored in database
			$data = $this->getPostData ($this->_option);
			if (false !== $data) {
				$settings [] = $data;
			}
			// merge all data into one
			$this->_data = bv47v_data_array::merge ( $settings );
			// allow the data to be filters before it is cached.
			$this->_data = apply_filters ( "{$this->application ()->slug}_read",
				$this->_data, $this->_option );
		}
		return $this->_data;
	}
}
