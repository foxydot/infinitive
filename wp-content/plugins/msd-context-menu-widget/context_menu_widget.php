<?php
/**
 * @package Context_Menu_Widget
 * @version 0.1
 */
/*
Plugin Name: Context Menu Widget
Description: This plugin makes a widget available that will create a menu for each section based entirely on page heiarchy. For posts and custom post types, it will not display (for now). Built off "Pages" by Wordpress.
Author: Catherine Sandrick
Version: 0.1
Author URI: http://madsciencedept.com
*/

class MSD_Widget_Context_Menu extends WP_Widget{
	function MSD_Widget_Context_Menu(){
		$widget_ops = array('classname' => 'msd_widget_context_menu', 'description' => __( 'Add a menu based on page structure to show subsections of site.') );
		$this->WP_Widget('msd_context_menu', __('Context Menu'), $widget_ops);
		wp_enqueue_style('msd_context_menu',WP_PLUGIN_URL.'/msd-context-menu-widget/context_menu_widget.css');
	}
		
	function widget( $args, $instance ) {
		global $wp_query;
		extract( $args );

		$sortby = empty( $instance['sortby'] ) ? 'menu_order' : $instance['sortby'];
		$exclude = empty( $instance['exclude'] ) ? '' : $instance['exclude'];
		$nofollow = empty( $instance['nofollow'] ) ? FALSE : TRUE;

		if ( $sortby == 'menu_order' )
			$sortby = 'menu_order, post_title';
		$my_widget_args = array('title_li' => '', 'echo' => 0, 'sort_column' => $sortby, 'exclude' => $exclude);
		if(!empty($wp_query->post->post_parent) && $nofollow){
			$adam = $this->get_adam();
			$my_widget_args['child_of'] = $adam;
			$title = get_post($adam)->post_title;
		} else {
			$my_widget_args['child_of'] = $wp_query->post->ID;
			$title = $wp_query->post->post_title;
		}
		$out = wp_list_pages( apply_filters('widget_pages_args', $my_widget_args ) );

		if ( !empty( $out ) ) {
			echo $before_widget;
			if ( $title)
				echo $before_title . $title . $after_title;
		?>
		<ul>
			<?php echo $out; ?>
		</ul>
		<?php
			echo $after_widget;
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		if ( in_array( $new_instance['sortby'], array( 'post_title', 'menu_order', 'ID' ) ) ) {
			$instance['sortby'] = $new_instance['sortby'];
		} else {
			$instance['sortby'] = 'menu_order';
		}

		$instance['exclude'] = strip_tags( $new_instance['exclude'] );
		$instance['nofollow'] = !empty($new_instance['nofollow']) ? 1 : 0;
		
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'sortby' => 'post_title', 'title' => '', 'exclude' => '', 'nofollow' =>'') );
		$exclude = esc_attr( $instance['exclude'] );
		$nofollow = esc_attr( $instance['nofollow'] );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('sortby'); ?>"><?php _e( 'Sort by:' ); ?></label>
			<select name="<?php echo $this->get_field_name('sortby'); ?>" id="<?php echo $this->get_field_id('sortby'); ?>" class="widefat">
				<option value="post_title"<?php selected( $instance['sortby'], 'post_title' ); ?>><?php _e('Page title'); ?></option>
				<option value="menu_order"<?php selected( $instance['sortby'], 'menu_order' ); ?>><?php _e('Page order'); ?></option>
				<option value="ID"<?php selected( $instance['sortby'], 'ID' ); ?>><?php _e( 'Page ID' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e( 'Exclude:' ); ?></label> <input type="text" value="<?php echo $exclude; ?>" name="<?php echo $this->get_field_name('exclude'); ?>" id="<?php echo $this->get_field_id('exclude'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Page IDs, separated by commas.' ); ?></small>
		</p>
		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('nofollow'); ?>" name="<?php echo $this->get_field_name('nofollow'); ?>"<?php checked( $nofollow ); ?> />
			<label for="<?php echo $this->get_field_id('nofollow'); ?>"><?php _e( 'Do Not Follow Context' ); ?></label>
			<br />
			<small><?php _e( 'Check to stay in parent category.' ); ?></small>
		</p>
<?php
	}
	
	function get_adam(){
		global $wp_query;
		$ancestors = $wp_query->post->ancestors;
		$adam = end($ancestors);
		return $adam;
	}
	
}

function msd_widgets_init() {
	if ( !is_blog_installed() )
		return;

	register_widget('MSD_Widget_Context_Menu');
}

add_action('init', 'msd_widgets_init', 1);