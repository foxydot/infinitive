<?php
/**
 * @package MSD Publication CPT
 * @version 0.1
 */

class MSDNewsCPT {

	public function MSDNewsCPT(){}
	
	function register_cpt_news() {
	
	    $labels = array( 
	        'name' => _x( 'News Items', 'news' ),
	        'singular_name' => _x( 'News Item', 'news' ),
	        'add_new' => _x( 'Add New', 'news' ),
	        'add_new_item' => _x( 'Add New News Item', 'news' ),
	        'edit_item' => _x( 'Edit News Item', 'news' ),
	        'new_item' => _x( 'New News Item', 'news' ),
	        'view_item' => _x( 'View News Item', 'news' ),
	        'search_items' => _x( 'Search News Items', 'news' ),
	        'not_found' => _x( 'No news items found', 'news' ),
	        'not_found_in_trash' => _x( 'No news items found in Trash', 'news' ),
	        'parent_item_colon' => _x( 'Parent News Item:', 'news' ),
	        'menu_name' => _x( 'News Items', 'news' ),
	    );
	
	    $args = array( 
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'Customer News Items',
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
	        'rewrite' => array('slug'=>'news-items','with_front'=>false),
	        'capability_type' => 'post'
	    );
	
	    register_post_type( 'msd_news', $args );
	}
		
	function list_news_stories( $atts ) {
		extract( shortcode_atts( array(
		), $atts ) );
		
		$args = array( 'post_type' => 'msd_news', 'numberposts' => 0, );

		$items = get_posts($args);
	    foreach($items AS $item){ 
	    	$excerpt = $item->post_excerpt?$item->post_excerpt:msd_trim_headline($item->post_content);
	     	$publication_list .= '
	     	<li>
				'.date('F j, Y',strtotime($item->post_date)).'
	     		<h3><a href="'.get_permalink($item->ID).'">'.$item->post_title.' ></a></h3>
				<div class="clear"></div>
			</li>';
	
	     }
		
		return '<ul class="publication-list news-items">'.$publication_list.'</ul><div class="clear"></div>';
	}	
}

	add_action( 'init', array('MSDNewsCPT','register_cpt_news') );
	add_shortcode( 'news-items', array('MSDNewsCPT','list_news_stories') );