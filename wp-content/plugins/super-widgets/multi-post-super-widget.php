<?php

require_once('single-taxonomy-super-widget.php');

class Post_Super_Widget_Multi extends Taxonomy_Super_Widget_Single {

	public function Post_Super_Widget_Multi() {

		global $pagenow;
		if (is_admin() && ('widgets.php' == $pagenow)) {
			wp_enqueue_script('super_widgets');
			wp_enqueue_style('super_widgets');
		}

		$widget_ops = array('description' => __('Super Widget that displays most recent posts based on selected taxonomy'));
		$control_ops = array('width' => 400);

		$this->WP_Widget('multi_post_super_widget', 'Multi-Post Super Widget', $widget_ops, $control_ops);
	}

	function form($instance) {
		Super_Widget_Base::form($instance);
		$num_posts = $instance['number_posts'];
	?>
		<p>
			<label for="<?php echo $this->get_field_id('number_posts'); ?>">Number of Posts:</label>
			<input type="text" id="<?php echo $this->get_field_id('number_posts'); ?>" name="<?php echo $this->get_field_name('number_posts'); ?>" value="<?php echo $num_posts; ?>" />
		</p>
	<?php
	}

	function widget($args, $instance) {
		if (empty($instance['object_to_use']) || empty($instance['object_type']))
			return;
		extract($args);
		if ($switched_blogs = self::needs_blog_switch($instance))
			switch_to_blog(intval($instance['blog_id']));
		$the_term = get_term($instance['object_to_use'], $instance['object_type']);
		$title = empty($instance['title_override']) ? $the_term->name : $instance['title_override'];
		if (!is_null($the_term)) {
			query_posts(array(
				'taxonomy' => $instance['object_type'],
				'term' => $the_term->slug,
				'posts_per_page' => $instance['number_posts'],
				'caller_get_posts' => true,
			));
			if (have_posts()):
				$alt = false;
				$has_thumbnail_support = current_theme_supports('post-thumbnails', $instance['object_type']);
			?>
				<div class="super-widget-multi-post">
					<?php
						echo $before_title, $title, $after_title;
						while(have_posts()): the_post();
					?>
					<div class="super-widget-multi-post-item <?php if($alt) echo 'alt';?>">
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<?php if($has_thumbnail_support && has_post_thumbnail(get_the_ID())) echo get_the_post_thumbnail(get_the_ID()); ?>
						<?php the_excerpt(); ?>
					</div>
					<?php $alt = !$alt; endwhile; ?>
				</div>
			<?php endif;
			wp_reset_query();
		}
		if ($switched_blogs)
			restore_current_blog();
	}

	public static function handle_widget_admin_ajax() {
		$widget = new Post_Super_Widget_Multi();
		$widget->number = $_POST['number'];
		$widget->form($_POST);
		exit;
	}
}

?>