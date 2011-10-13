<?php

require_once('base-super-widget.php');

class Taxonomy_Super_Widget_Single extends Super_Widget_Base {

	var $object_type_label = 'Taxonomy';

	public function Taxonomy_Super_Widget_Single() {

		global $pagenow;
		if (is_admin() && ('widgets.php' == $pagenow)) {
			wp_enqueue_script('super_widgets');
			wp_enqueue_style('super_widgets');
		}

		$widget_ops = array('description' => __('Super Widget that displays the most recent post/page based on selected taxonomy'));
		$control_ops = array('width' => 400);

		$this->WP_Widget('single_taxonomy_super_widget', 'Single Post (Taxonomy) Super Widget', $widget_ops, $control_ops);
	}

	function get_all_objects($args) {
		$object_type_name = $args['instance']['object_type'];
		// @todo transient caching of these results with proper invalidation on updating of a post of this type
		$count_all_objects = count(get_terms($object_type_name, array('fields' => 'ids')));
		$num_pages = $count_all_objects / $args['number'];
		$all_objects = get_terms($object_type_name, $args);
		return array($all_objects, $count_all_objects, $num_pages);
	}

	function get_all_objects_args($object_type_name, $per_page, $offset) {
		$args = array(
			'offset' => $offset,
			'order' => 'ASC',
			'orderby' => 'name',
			'number' => $per_page,
		);
		return $args;
	}

	function get_walker() {
		$walker = new Walker_Post_Type_Radio_List;
		$walker->object_to_use = 'object_to_use';
		$walker->title_var = 'name';
		$walker->db_fields = array(
			'parent' => 'parent',
			'id' => 'term_id',
		);
		return $walker;
	}

	function object_selection_label($instance) {
	?>
		<p>
			<label>
				<?php _e('Taxonomy:'); ?>
				<?php if(!empty($instance['object_to_use'])): ?>
					<?php $taxonomy = get_term($instance['object_to_use'], $instance['object_type']); ?>
					<em><?php echo $taxonomy->name; ?></em>
				<?php endif; ?>
			</label>
		</p>
	<?php
	}

	function most_recent_selection_label() {
		_e('Most Used');
	}

	function most_recent_selection($instance, $args, $walker) {
		$object_type_name = $instance['object_type'];
		$most_used_args = array_merge( $args, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 15 ) );
		$most_used = get_terms($object_type_name, $most_used_args);
		$args['control_name'] = $this->get_field_name('object_to_use') . '[most-recent]';
		$args['instance'] = $instance;
		echo call_user_func_array(array(&$walker, 'walk'), array($most_used, 0, $args));
	}

	function all_objects_selection($all_objects, $instance, $args, $walker) {
		$args['control_name'] = $this->get_field_name('object_to_use') . '[all]';

		// Output a hidden radio button if necessary to maintain selected taxonomytype_object during paging
		$selected_object_id = $instance['object_to_use'];
		if (!empty($selected_object_id)) {
			$selected_objects_to_use = array_filter($all_objects, create_function('$t', 'return ($t->term_id == '.$selected_object_id.');'));

			if (count($selected_objects_to_use) === 0) {
				echo '<input style="display: none;" name="', $args['control_name'], '" type="radio" value="', $selected_object_id, '" checked="checked" />';
			}
		}
		echo call_user_func_array(array(&$walker, 'walk'), array($all_objects, 0, $args));
	}

	function get_search_results($searched, $object_type_name, $per_page) {
		$search_args = array(
			'name__like' => $searched,
			'orderby' => 'count',
			'order' => 'DESC',
			'number' => $per_page,
		);
		$search_results = get_terms( $object_type_name, $search_args );
		return $search_results;
	}

	function search_results_selection($search_results, $instance, $args, $walker) {
		$args['control_name'] = $this->get_field_name('object_to_use') . '[search]';
		// Output a hidden radio button if necessary to maintain selected object during paging
		$selected_object_id = $instance['object_to_use'];
		if (!empty($selected_object_id)) {
			$selected_objects_to_use = array_filter($search_results, create_function('$t', 'return ($t->term_id == '.$selected_object_id.');'));

			if (count($selected_objecs_to_use) === 0) {
				echo '<input style="display: none;" name="', $args['control_name'], '" type="radio" value="', $selected_object_id, '" checked="checked" />';
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
		$taxonomy_types = get_object_taxonomies(array('page', 'post'), 'objects'); // should this be page/post?
		$return_types = array();
		foreach ($taxonomy_types as $taxonomy_type => $taxonomy) {
			$return_types[$taxonomy_type] = $taxonomy->labels->singular_name;
		}
		return $return_types;
	}


	function form($instance) {
		parent::form($instance);
	?>
	<p>
		<label for="<?php echo $this->get_field_id('use_post_title'); ?>">
			<input type="checkbox" id="<?php echo $this->get_field_id('use_post_title'); ?>" name="<?php echo $this->get_field_name('use_post_title'); ?>" <?php checked($instance['use_post_title']); ?> />
			Use post title (instead of taxonomy's title)
		</label>
	</p>
	<?php
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
		$the_term = get_term($instance['object_to_use'], $instance['object_type']);
		if (!is_null($the_term)) {
			$post_args = array(
				'taxonomy' => $instance['object_type'],
				'term' => $the_term->slug,
				'numberposts' => 1,
				'caller_get_posts' => true,
			);
			$the_post = array_shift(get_posts($post_args));
			if (!is_null($the_post)) {
				$instance['title'] = '';
				if (empty($instance['title_override']))
					$instance['title'] = ($instance['use_post_title']) ? get_the_title($the_post->ID) : $the_term->name;
				else
					$instance['title'] = $instance['title_override'];

				if (!empty($the_post->post_excerpt))
					$instance['excerpt'] = $the_post->post_excerpt;
				else
					$instance['excerpt'] = self::trim_excerpt($the_post->post_content);
				$instance['link'] = get_permalink($the_post->ID);

				if (current_theme_supports('post-thumbnails', $instance['object_type']) && has_post_thumbnail($the_post->ID))
					$instance['image'] = get_the_post_thumbnail($the_post->ID);
			}
		}
		if ($switched_blogs)
			restore_current_blog();
		parent::widget($args, $instance);
	}

	public static function handle_widget_admin_ajax() {
		$widget = new Taxonomy_Super_Widget_Single();
		$widget->number = $_POST['number'];
		$widget->form($_POST);
		exit;
	}
}

?>