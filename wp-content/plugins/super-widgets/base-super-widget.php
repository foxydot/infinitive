<?php

abstract class Super_Widget_Base extends WP_Widget {

	var $object_type_label;

	function widget($args, $instance) {
		extract($args);

		$image = empty($instance['image']) ? false : $instance['image'];
		$title = empty($instance['title']) ? false : esc_attr($instance['title']);
		$excerpt = empty($instance['excerpt']) ? false : $instance['excerpt'];
		$link = empty($instance['link']) ? false : esc_attr($instance['link']);

		$link_html_begin = '';
		$link_html_end = '';
		$image_html = '';
		$image_class = '';
		$title_html = '';

		if ($link) {
			$link_html_begin = "<a href='$link'>";
			$link_html_end = '</a>';
		}
		if ($image) {
			$image_html = $link_html_begin . $image . $link_html_end;
		}
		if ($title)
			$title_html = $before_title . $link_html_begin . $title . $link_html_end . $after_title;

		echo $before_widget, $title_html, $image_html, '<p>', $excerpt, '</p>', $after_widget;

	}

	static function needs_blog_switch($instance) {
		global $wpdb;
		$blog_id = -1;
		$is_multisite = (function_exists('is_multisite') && is_multisite());
		if ($is_multisite && is_array($instance))
			$blog_id = array_key_exists('blog_id', $instance) ? intval($instance['blog_id']) : $blog_id;
		return ($is_multisite && ($blog_id > 0) && ($blog_id != $wpdb->blogid));
	}

	// Needs to return an associative array of objects types, value (likely ID) => label
	abstract function get_object_types();

	// Needs to return array of the following:
	// all objects, count of objects, max num pages of objects
	abstract function get_all_objects($args);

	abstract function get_all_objects_args($object_type_name, $per_page, $offset);

	abstract function get_walker();

	// Output the label above the object selection control
	abstract function object_selection_label($instance);

	// Output the most recent objects radio buttons
	abstract function most_recent_selection($instance, $args, $walker);

	// Outputs the all objects radio buttons
	abstract function all_objects_selection($all_objects, $instance, $args, $walker);

	abstract function get_search_results($searched, $object_type_name, $per_page);

	abstract function search_results_selection($search_results, $instance, $args, $walker);

	function object_selection_control ( $instance ) {

		if (!is_array($instance)) return;

		$object_type_name = array_key_exists('object_type', $instance) ? $instance['object_type'] : '';

		if (empty($object_type_name)) return;

		// paginate browsing for large numbers of objects
		$per_page = 50;
		$pagenum = isset( $instance['tab'] ) && isset( $instance['paged'] ) ? absint( $instance['paged'] ) : 1;
		$offset = 0 < $pagenum ? $per_page * ( $pagenum - 1 ) : 0;

		$args = $this->get_all_objects_args($object_type_name, $per_page, $offset);
		$args = array_merge(array('instance' => $instance), $args);
		list($all_objects, $all_objects_count, $num_pages) = $this->get_all_objects($args);

		if ( $all_objects_count <= 0 ) {
			echo '<p>' . __( 'No items.' ) . '</p>';
			return;
		}

		$page_links = paginate_links( array(
			'base' => '',
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => $num_pages,
			'current' => $pagenum
		));

		$walker = $this->get_walker();

		$valid_tabs = array('all', 'most-recent', 'search');
		$current_tab = 'most-recent';
		if ( isset( $instance['tab'] ) && in_array( $instance['tab'], $valid_tabs ) ) {
			$current_tab = $instance['tab'];
		}

		$this->object_selection_label($instance);
		$tab_id_base = sprintf('tabs-%d-%s-', $this->number, $object_type_name);
		?>
		<p>
		<div class="posttypediv">
			<ul class="posttype-tabs add-menu-item-tabs">
				<li <?php echo ( 'most-recent' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="nav-tab-link" href="#<?php echo $tab_id_base; ?>most-recent"><?php $this->most_recent_selection_label(); ?></a></li>
				<li <?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="nav-tab-link" href="#<?php echo $tab_id_base; ?>all"><?php _e('View All'); ?></a></li>
				<li <?php echo ( 'search' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="nav-tab-link" href="#<?php echo $tab_id_base; ?>search"><?php _e('Search'); ?></a></li>
			</ul>

			<div id="<?php echo $tab_id_base; ?>most-recent" class="tabs-panel <?php
				echo ( 'most-recent' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
			?>">
				<ul id="<?php echo $object_type_name; ?>checklist-most-recent" class="categorychecklist form-no-clear">
					<?php $this->most_recent_selection($instance, $args, $walker); ?>
				</ul>
			</div><!-- /.tabs-panel -->

			<div id="<?php echo $tab_id_base; ?>all" class="tabs-panel tabs-panel-view-all <?php
				echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
			?>">
				<?php if ( ! empty( $page_links ) ) : ?>
					<div class="add-menu-item-pagelinks">
						<?php echo $page_links; ?>
					</div>
				<?php endif; ?>
				<ul id="<?php echo $object_type_name; ?>checklist" class="list:<?php echo $object_type_name; ?> categorychecklist form-no-clear">
					<?php $this->all_objects_selection($all_objects, $instance, $args, $walker); ?>
				</ul>
				<?php if ( ! empty( $page_links ) ) : ?>
					<div class="add-menu-item-pagelinks">
						<?php echo $page_links; ?>
					</div>
				<?php endif; ?>
			</div><!-- /.tabs-panel -->

			<div class="tabs-panel <?php
				echo ( 'search' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
			?>" id="<?php echo $tab_id_base; ?>search">
				<?php
				if ( ( 'search' == $current_tab) && !empty($instance['searched']) ) {
					$searched = esc_attr( trim($instance['searched']) );
					$search_results = $this->get_search_results($searched, $object_type_name, $per_page);
				} else {
					$searched = '';
					$search_results = array();
				}
				?>
				<p class="quick-search-wrap">
					<input type="text" class="quick-search regular-text input-with-default-title" title="<?php esc_attr_e('Search'); ?>" value="<?php echo $searched; ?>" name="<?php echo $this->get_field_name('searched'); ?>" />
					<input type="submit" class="quick-search-submit button-secondary" value="<?php esc_attr_e('Search'); ?>" />
				</p>

				<ul id="<?php echo $object_type_name; ?>-search-checklist" class="list:<?php echo $object_type_name; ?> categorychecklist form-no-clear">
				<?php $this->search_results_selection($search_results, $instance, $args, $walker); ?>
				</ul>
			</div><!-- /.tabs-panel -->


		</div><!-- /.posttypediv -->
		<input class="hiddentab" type="hidden" id="<?php echo $this->get_field_id('tab'); ?>" name="<?php echo $this->get_field_name('tab'); ?>" value="<?php echo $current_tab; ?>" />
		<input class="hiddenpaged" type="hidden" id="<?php echo $this->get_field_id('paged'); ?>" name="<?php echo $this->get_field_name('paged'); ?>" value="<?php echo $pagenum; ?>" />
		</p>
		<br/>
		<?php
	}

	function form($instance) {
		$plugin_options = get_option(SUPER_WIDGETS_OPTIONS);
		$blog_id = -1;
		$is_multisite = (function_exists('is_multisite') && is_multisite());
		if ($is_multisite && $plugin_options['blog_selection']) {
			$blog_id_field = $this->get_field_id('blog_id');
			if (is_array($instance))
				$blog_id = intval($instance['blog_id']);
		}
		$object_type_field = $this->get_field_id('object_type');
	?>
	<input type="hidden" class="widget_id_base" value="<?php echo $this->get_field_id(''); ?>" />
	<input type="hidden" class="widget_number" value="<?php echo $this->number; ?>" />
	<input type="hidden" class="widget_class" value="<?php echo get_class($this); ?>" />
	<?php if($is_multisite && $plugin_options['blog_selection']): ?>
	<p>
		<label for="<?php echo $blog_id_field; ?>"><?php _e( 'Blog:' ); ?></label>
		<select class="super-widget-select widefat super-widget-blog-select" id="<?php echo $blog_id_field; ?>" name="<?php echo $this->get_field_name('blog_id'); ?>">
			<option value='-1'>--Select a Blog--</option>
		<?php $current_user = wp_get_current_user(); ?>
		<?php foreach(get_blogs_of_user($current_user->ID) as $blog): ?>
			<option value="<?php echo $blog->userblog_id; ?>" <?php selected($blog->userblog_id, $blog_id); ?>><?php echo $blog->blogname; ?></option>
		<?php endforeach; ?>
		</select>
	</p>
	<?php endif; ?>
	<?php
	if ($switched_blogs = self::needs_blog_switch($instance))
		switch_to_blog($blog_id);
	$object_types = $this->get_object_types($blog_id);
	?>
	<p>
		<label for="<?php echo $object_type_field; ?>"><?php echo $this->object_type_label; ?> Type:</label>
		<select class="super-widget-select widefat super-widget-object-type-select" id="<?php echo $object_type_field; ?>" name="<?php echo $this->get_field_name('object_type'); ?>">
			<option value=''>--Select a <?php echo $this->object_type_label; ?> Type--</option>
		<?php foreach($object_types as $object_type_value => $object_type_label): ?>
			<option value="<?php echo $object_type_value; ?>" <?php selected($object_type_value, $instance['object_type']); ?>><?php echo $object_type_label; ?></option>
		<?php endforeach; ?>
		</select>
	</p>
	<?php
	$this->object_selection_control($instance);
	if ($switched_blogs)
		restore_current_blog();
	if($plugin_options['title_override']):
	?>
	<p>
		<label for="<?php echo $this->get_field_id('title_override'); ?>">
			Title Override: <em>(optional)</em>
			<input class="widefat" id="<?php echo $this->get_field_id('title_override'); ?>" name="<?php echo $this->get_field_name('title_override'); ?>" type="text" value="<?php echo $instance['title_override']; ?>" />
		</label>
	</p>
	<?php
	endif;
	}

	// admin-ajax.php action
	// Use the following method, replacing new Super_Widget_Base(); with the subclass name
	// 	public static function handle_widget_admin_ajax() {
	// 		$widget = new Super_Widget_Base();
	// 		$widget->number = $_POST['number'];
	// 		$widget->form($_POST);
	// 		exit;
	// 	}
	abstract public static function handle_widget_admin_ajax();

	// Complete rip of formatting.php's wp_trim_excerpt()
	public static function trim_excerpt($text) {
		$text = strip_shortcodes( $text );

		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$excerpt_length = apply_filters('excerpt_length', 55);
		$excerpt_more = apply_filters('excerpt_more', ' ...');
		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode(' ', $words);
		}
		return $text;
	}
}

?>