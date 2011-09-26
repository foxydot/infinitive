<?php

class MSDPublicationWidget extends WP_Widget {
    /** constructor */
    function MSDPublicationWidget() {
		$widget_ops = array('classname' => 'msd_publication_widget', 'description' => __('Display the most recent publication of a particular genre'));
		$control_ops = array('width' => 400, 'height' => 350);
		$this->WP_Widget('msd_publication_widget', __('MSD Publication Widget'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
		$posttype = 'msd_publication';	
		$taxonomy = 'msd_genre';
		$category = strip_tags($instance['category']);	
		$numberposts = strip_tags($numberposts);
		$args = array();
		$args['post_type'] = $posttype; 
		if(!empty($category)){ 
			$args['tax_query'][0]['taxonomy'] = $taxonomy;
			$args['tax_query'][0]['field'] = 'slug';
			$args['tax_query'][0]['terms'] = $category;
		}
		$args['numberposts'] = !empty($numberposts)?$numberposts:1;
		
		$items = get_posts($args);
		global $documents;
		
		echo $before_widget;
		if ( !empty( $title ) ) { print $before_title.$title.$after_title; } 
		print '<ul class="publication-list">';
		foreach($items AS $item){ 
	    	$documents->the_meta($item->ID);
	    	$thumb = get_the_post_thumbnail($item->ID)?get_the_post_thumbnail($item->ID):'<img src="'.get_bloginfo('template_url').'/images/'.$category.'.png" />';
	    	$publication_list .= '
	     	<li>
	     		<div class="img">'.$thumb.'</div>
				<h3>'.$item->post_title.'</h3>';
				while($documents->have_fields('docs'))
					{
						$publication_list .= '<p><a href="'.$documents->get_the_value('upload_file').'">Download ></a></p>
						';
					}
				$publication_list .= '<div class="clear"></div>
			</li>';
	
	     }
	    print $publication_list;
		print '</ul>';
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['category'] = strip_tags($new_instance['category']);
		$instance['numberposts'] = strip_tags($new_instance['numberposts']);

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$category = strip_tags($instance['category']);	
		$numberposts = strip_tags($instance['numberposts']);	
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="narrow" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Genre:'); ?></label>
		<select class="narrow" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
				<option value="">All Genres</option>
				<?php $cats = get_terms('msd_genre',array('hide_empty' => 0)); foreach ($cats as $cat):?>
					<?php $in= ($cat->slug==$category)? " SELECTED":"";?>
  					<option value="<?php echo $cat->slug ?>"<?php echo $in?>><?php echo $cat->name?></option>
				<?php endforeach;?>
		</select></p>
		
		<p><label for="<?php echo $this->get_field_id('numberposts'); ?>"><?php _e('Number of Posts to Show:'); ?></label>
		<input class="narrow" id="<?php echo $this->get_field_id('numberposts'); ?>" name="<?php echo $this->get_field_name('numberposts'); ?>" type="text" value="<?php echo esc_attr($numberposts); ?>" /></p>
		
<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("MSDPublicationWidget");'));