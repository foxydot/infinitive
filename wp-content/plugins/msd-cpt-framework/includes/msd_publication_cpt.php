<?php
class MSDPublicationCPT {

	public function MSDPublicationCPT(){}
			
	public function register_taxonomy_genre() {
	
	    $labels = array( 
	        'name' => _x( 'Genres', 'genre' ),
	        'singular_name' => _x( 'Genre', 'genre' ),
	        'search_items' => _x( 'Search Genres', 'genre' ),
	        'popular_items' => _x( 'Popular Genres', 'genre' ),
	        'all_items' => _x( 'All Genres', 'genre' ),
	        'parent_item' => _x( 'Parent Genre', 'genre' ),
	        'parent_item_colon' => _x( 'Parent Genre:', 'genre' ),
	        'edit_item' => _x( 'Edit Genre', 'genre' ),
	        'update_item' => _x( 'Update Genre', 'genre' ),
	        'add_new_item' => _x( 'Add New Genre', 'genre' ),
	        'new_item_name' => _x( 'New Genre Name', 'genre' ),
	        'separate_items_with_commas' => _x( 'Separate genres with commas', 'genre' ),
	        'add_or_remove_items' => _x( 'Add or remove genres', 'genre' ),
	        'choose_from_most_used' => _x( 'Choose from the most used genres', 'genre' ),
	        'menu_name' => _x( 'Genres', 'genre' ),
	    );
	
	    $args = array( 
	        'labels' => $labels,
	        'public' => true,
	        'show_in_nav_menus' => true,
	        'show_ui' => true,
	        'show_tagcloud' => false,
	        'hierarchical' => true,
	
	        'rewrite' => true,
	        'query_var' => true
	    );
	
	    register_taxonomy( 'msd_genre', array('msd_publication','msd_news'), $args );
	}
	
	function register_cpt_publication() {
	
	    $labels = array( 
	        'name' => _x( 'Publications', 'publication' ),
	        'singular_name' => _x( 'Publication', 'publication' ),
	        'add_new' => _x( 'Add New', 'publication' ),
	        'add_new_item' => _x( 'Add New Publication', 'publication' ),
	        'edit_item' => _x( 'Edit Publication', 'publication' ),
	        'new_item' => _x( 'New Publication', 'publication' ),
	        'view_item' => _x( 'View Publication', 'publication' ),
	        'search_items' => _x( 'Search Publications', 'publication' ),
	        'not_found' => _x( 'No publications found', 'publication' ),
	        'not_found_in_trash' => _x( 'No publications found in Trash', 'publication' ),
	        'parent_item_colon' => _x( 'Parent Publication:', 'publication' ),
	        'menu_name' => _x( 'Publications', 'publication' ),
	    );
	
	    $args = array( 
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'Whitepapers, reports and other content that requires a downloadable PDF',
	        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail'),
	        'taxonomies' => array( 'genre' ),
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'menu_position' => 20,
	        
	        'show_in_nav_menus' => true,
	        'publicly_queryable' => true,
	        'exclude_from_search' => false,
	        'has_archive' => true,
	        'query_var' => true,
	        'can_export' => true,
	        'rewrite' => array('slug'=>'publications','with_front'=>false),
	        'capability_type' => 'post'
	    );
	
	    register_post_type( 'msd_publication', $args );
	}
	
	
	 
	function my_publications_admin_scripts() {
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_register_script('my-upload', plugin_dir_url(dirname(__FILE__)).'/js/msd-upload-file.js', array('jquery','media-upload','thickbox'),FALSE,TRUE);
		wp_enqueue_script('my-upload');
	}
	
	function my_publications_admin_styles() {
		wp_enqueue_style('thickbox');
		wp_enqueue_style('custom_meta_css',plugin_dir_url(dirname(__FILE__)).'/template/meta.css');
	}	
		
	function my_publications_print_footer_scripts()
	{
		print '<script type="text/javascript">/* <![CDATA[ */
			jQuery(function($)
			{
				var i=1;
				$(\'.customEditor textarea\').each(function(e)
				{
					var id = $(this).attr(\'id\');
	 
					if (!id)
					{
						id = \'customEditor-\' + i++;
						$(this).attr(\'id\',id);
					}
	 
					tinyMCE.execCommand(\'mceAddControl\', false, id);
	 
				});
			});
		/* ]]> */</script>';
	}
	
	function list_publications( $atts ) {
		global $documents;
		extract( shortcode_atts( array(
			'genre' => '',
		), $atts ) );
		
		$args = array( 'post_type' => 'msd_publication', 'numberposts' => 0, );
		if(!empty($genre)){
			$args['tax_query'][0]['taxonomy'] = 'msd_genre';
			$args['tax_query'][0]['field'] = 'slug';
			$args['tax_query'][0]['terms'] = $genre;
		}
		$items = get_posts($args);
	    foreach($items AS $item){ 
	    	$documents->the_meta($item->ID);
	    	$thumb = get_the_post_thumbnail($item->ID)?get_the_post_thumbnail($item->ID):'<img src="'.get_bloginfo('template_url').'/images/'.$genre.'.png" />';
	    	$excerpt = $item->post_excerpt?$item->post_excerpt:msd_trim_headline($item->post_content);
	     	$publication_list .= '
	     	<li>
	     		<div class="img">'.$thumb.'</div>
				<h3>'.$item->post_title.'</h3>
				<p>'.$excerpt.'</p>
				<p><a href="'.get_permalink($item->ID).'">Read More ></a></p>';
				while($documents->have_fields('docs'))
					{
						$publication_list .= '<p><a href="'.$documents->get_the_value('upload_file').'">Download PDF ></a></p>
						';
					}
				$publication_list .= '<div class="clear"></div>
			</li>';
	
	     }
		
		return '<ul class="publication-list">'.$publication_list.'</ul>';
	}
	
}

	add_action( 'init', array('MSDPublicationCPT','register_taxonomy_genre') );
	add_action( 'init', array('MSDPublicationCPT','register_cpt_publication') );
	add_action('admin_print_scripts', array('MSDPublicationCPT','my_publications_admin_scripts') );
	add_action('admin_print_styles', array('MSDPublicationCPT','my_publications_admin_styles') );
	// important: note the priority of 99, the js needs to be placed after tinymce loads
	add_action('admin_print_footer_scripts',array('MSDPublicationCPT','my_publications_print_footer_scripts'),99);
	add_shortcode( 'publications', array('MSDPublicationCPT','list_publications') );