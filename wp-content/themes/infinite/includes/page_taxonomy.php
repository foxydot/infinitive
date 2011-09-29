<?php
class MSDPageTaxonomy {

	public function MSDPageTaxonomy(){}
			
	public function register_taxonomy_special() {
	
	    $labels = array( 
	        'name' => _x( 'Special Statuses', 'special' ),
	        'singular_name' => _x( 'Special Status', 'special' ),
	        'search_items' => _x( 'Search Special Statuses', 'special' ),
	        'popular_items' => _x( 'Popular Special Statuses', 'special' ),
	        'all_items' => _x( 'All Special Statuses', 'special' ),
	        'parent_item' => _x( 'Parent Special Status', 'special' ),
	        'parent_item_colon' => _x( 'Parent Special Status:', 'special' ),
	        'edit_item' => _x( 'Edit Special Status', 'special' ),
	        'update_item' => _x( 'Update Special Status', 'special' ),
	        'add_new_item' => _x( 'Add New Special Status', 'special' ),
	        'new_item_name' => _x( 'New Special Status Name', 'special' ),
	        'separate_items_with_commas' => _x( 'Separate special statuses with commas', 'special' ),
	        'add_or_remove_items' => _x( 'Add or remove special statuses', 'special' ),
	        'choose_from_most_used' => _x( 'Choose from the most used specials', 'special' ),
	        'menu_name' => _x( 'Special Statuses', 'special' ),
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
	
	    register_taxonomy( 'msd_special', array('page','post'), $args );
	    flush_rewrite_rules();
	}
}

	add_action( 'init', array('MSDPageTaxonomy','register_taxonomy_special') );