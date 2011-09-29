<?php
/**
 * @package MSD Publication CPT
 * @version 0.1
 */

class MSDCaseStudyCPT {

	public function MSDCaseStudyCPT(){}
	
	function register_cpt_casestudy() {
	
	    $labels = array( 
	        'name' => _x( 'Case Studies', 'case-study' ),
	        'singular_name' => _x( 'Case Study', 'case-study' ),
	        'add_new' => _x( 'Add New', 'case-study' ),
	        'add_new_item' => _x( 'Add New Case Study', 'case-study' ),
	        'edit_item' => _x( 'Edit Case Study', 'case-study' ),
	        'new_item' => _x( 'New Case Study', 'case-study' ),
	        'view_item' => _x( 'View Case Study', 'case-study' ),
	        'search_items' => _x( 'Search Case Studies', 'case-study' ),
	        'not_found' => _x( 'No case studies found', 'case-study' ),
	        'not_found_in_trash' => _x( 'No case studies found in Trash', 'case-study' ),
	        'parent_item_colon' => _x( 'Parent Case Study:', 'case-study' ),
	        'menu_name' => _x( 'Case Studies', 'case-study' ),
	    );
	
	    $args = array( 
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'Case Studies',
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
	        'rewrite' => array('slug'=>'case-study','with_front'=>false),
	        'capability_type' => 'post'
	    );
	
	    register_post_type( 'msd_casestudy', $args );
	    flush_rewrite_rules();
	}
		
	function list_case_studies( $atts ) {
		global $casestudy;
		extract( shortcode_atts( array(
		), $atts ) );
		
		$args = array( 'post_type' => 'msd_casestudy', 'numberposts' => 0, );

		$items = get_posts($args);
	    foreach($items AS $item){ 
	    	$success->the_meta($item->ID);
	    	$excerpt = $item->post_excerpt?$item->post_excerpt:msd_trim_headline($item->post_content);
	     	$publication_list .= '
	     	<li>
				<h3><a href="'.get_permalink($item->ID).'">'.$item->post_title.'</a></h3>
				<h4><a href="'.get_permalink($item->ID).'">'.$success->get_the_value('subtitle').'</a></h4>
				<p>'.$excerpt.' <a href="'.get_permalink($item->ID).'">More ></a></p>
				<div class="clear"></div>
			</li>';
	
	     }
		
		return '<ul class="publication-list case-studies">'.$publication_list.'</ul><div class="clear"></div>';
	}	
}

	

	add_action( 'init', array('MSDCaseStudyCPT','register_cpt_casestudy') );
	add_shortcode( 'case-studies', array('MSDCaseStudyCPT','list_case_studies') );