<?php

require_once('base-super-widget.php');

class Post_Super_Widget_Single extends Super_Widget_Base {

	var $object_type_label = 'Post';

	public function Post_Super_Widget_Single() {

		global $pagenow;
		if (is_admin() && ('widgets.php' == $pagenow)) {
			wp_enqueue_script('super_widgets');
			wp_enqueue_style('super_widgets');
		}

		$widget_ops = array('description' => __('Super Widget that displays a single post, filtered by post type'));
		$control_ops = array('width' => 400);

		$this->WP_Widget('single_post_super_widget', 'Single Post (Post Type) Super Widget', $widget_ops, $control_ops);
	}

	function get_all_objects_args($object_type_name, $per_page, $offset) {
		$args = array(
			'offset' => $offset,
			'order' => 'ASC',
			'orderby' => 'title',
			'posts_per_page' => $per_page,
			'post_type' => $object_type_name,
			'suppress_filters' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status' => 'publish'
		);
		return $args;
	}

	function get_all_objects($args) {
		// @todo transient caching of these results with proper invalidation on updating of a post of this type
		$get_posts = new WP_Query;
		$posts = $get_posts->query( $args );
		return array($posts, $get_posts->post_count, $get_posts->max_num_pages);
	}

	function get_walker() {
		$walker = new Walker_Post_Type_Radio_List;
		$walker->object_to_use = 'object_to_use';
		$walker->title_var = 'post_title';
		$walker->id_var = 'ID';
		return $walker;
	}

	function object_selection_label ( $instance ) {
	?>
		<p>
			<label>
				<?php _e('Post:'); ?>
				<?php if(($post_to_use = intval($instance['object_to_use'])) > 0): ?>
					<em><?php echo get_the_title($post_to_use); ?></em>
				<?php endif; ?>
			</label>
		</p>
	<?php
	}

	function most_recent_selection_label() {
		_e('Most Recent');
	}

	function most_recent_selection($instance, $args, $walker) {
		$recent_arg_defaults = array(
			'orderby' => 'post_date',
			'order' => 'DESC',
			'showposts' => 15,
			'posts_per_page' => 15,
			'offset' => 0,
		);
		$recent_args = array_merge( $args, $recent_arg_defaults );
		$get_most_recent = new WP_Query;
		$most_recent = $get_most_recent->query( $recent_args );
		$args['control_name'] = $this->get_field_name('object_to_use') . '[most-recent]';
		$args['instance'] = $instance;
		echo call_user_func_array(array(&$walker, 'walk'), array($most_recent, 0, $args));
	}

	function all_objects_selection($all_objects, $instance, $args, $walker) {
		$args['control_name'] = $this->get_field_name('object_to_use') . '[all]';

		// Output a hidden radio button if necessary to maintain selected posttype_object during paging
		$selected_post_id = $instance['object_to_use'];

		if (!empty($selected_post_id)) {
			$selected_posts_to_use = array_filter($all_objects, create_function('$p', 'return ($p->ID == '.$selected_post_id.');'));

			if (count($selected_posts_to_use) === 0) {
				echo '<input style="display: none;" name="', $args['control_name'], '" type="radio" value="', $selected_post_id, '" checked="checked" />';
			}
		}
		echo call_user_func_array(array(&$walker, 'walk'), array($all_objects, 0, $args));
	}

	function get_search_results($searched, $object_type_name, $per_page) {
		$search_args = array(
			's' => $searched,
			'post_type' => $object_type_name,
			'numposts' => $per_page,
			'showposts' => $per_page,
		);
		$search_results = get_posts( $search_args );
		return $search_results;
	}

	function search_results_selection($search_results, $instance, $args, $walker) {
		$args['control_name'] = $this->get_field_name('object_to_use') . '[search]';
		// Output a hidden radio button if necessary to maintain selected posttype_object during paging
		$selected_post_id = $instance['object_to_use'];
		if (!empty($selected_post_id)) {
			$selected_posts_to_use = array_filter($search_results, create_function('$p', 'return ($p->ID == '.$selected_post_id.');'));

			if (count($selected_posts_to_use) === 0) {
				echo '<input style="display: none;" name="', $args['control_name'], '" type="radio" value="', $selected_post_id, '" checked="checked" />';
			}
		}

		if ( ! empty( $search_results ) && ! is_wp_error( $search_results ) ):
			echo call_user_func_array(array(&$walker, 'walk'), array($search_results, 0, $args));
		elseif ( is_wp_error( $search_results ) ): ?>
			<li><?php echo $search_results->get_error_message(); ?></li>
		<?php elseif ( ! empty( $searched ) ): ?>
			<li><?php _e('No results found.'); ?></li>
		<?php endif;
	}

	function get_object_types() {
		$post_types = get_post_types( array('public' => true), 'object' );
		$return_types = array();
		foreach ($post_types as $post_type)
			$return_types[$post_type->name] = $post_type->labels->singular_name;
		return $return_types;
	}

	function update($new_instance, $old_instance) {
		// Use the post selected based on the current tab
		$tab = $new_instance['tab'];
		$new_instance['object_to_use'] = empty($new_instance['object_to_use'][$tab]) ? $old_instance['object_to_use'] : $new_instance['object_to_use'][$tab];

		return $new_instance;
	}

	function widget($args, $instance) {
		if (empty($instance['object_to_use']) || empty($instance['object_type']))
			return;
		$plugin_options = get_option(SUPER_WIDGETS_OPTIONS);
		if ($switched_blogs = self::needs_blog_switch($instance))
			switch_to_blog(intval($instance['blog_id']));
		$post_id = $instance['object_to_use'];
		$the_post = get_post($post_id);

		$instance['title'] = get_the_title($post_id);
		if ($plugin_options['title_override'] && !empty($instance['title_override']))
			$instance['title'] = $instance['title_override'];

		if ($plugin_options['excerpt_override'] && !empty($instance['excerpt_override']))
			$instance['excerpt'] = $instance['excerpt_override'];
		else if (!empty($the_post->post_excerpt))
			$instance['excerpt'] = $the_post->post_excerpt;
		else
			$instance['excerpt'] = self::trim_excerpt($the_post->post_content);
		$instance['link'] = get_permalink($post_id);

		if (current_theme_supports('post-thumbnails', $instance['object_type']) && has_post_thumbnail($post_id))
			$instance['image'] = get_the_post_thumbnail($post_id);

		if ($switched_blogs)
			restore_current_blog();
		parent::widget($args, $instance);
	}

	function form($instance) {
		parent::form($instance);
		$plugin_options = get_option(SUPER_WIDGETS_OPTIONS);
		if ($plugin_options['excerpt_override']):
	?>
	<p>
		<label for="<?php echo $this->get_field_id('excerpt_override'); ?>">
			Excerpt Override: <em>(optional)</em>
			<textarea class="widefat" rows="10" id="<?php echo $this->get_field_id('excerpt_override'); ?>" name="<?php echo $this->get_field_name('excerpt_override'); ?>"><?php echo format_to_edit($instance['excerpt_override']); ?></textarea>
		</label>
	</p>
	<?php
		endif;
	}

	public static function handle_widget_admin_ajax() {
		$widget = new Post_Super_Widget_Single();
		$widget->number = $_POST['number'];
		$widget->form($_POST);
		exit;
	}

}

?>