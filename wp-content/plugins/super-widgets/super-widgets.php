<?php
/*
Plugin Name: Super Widgets
Plugin URI: http://plugins.voceconnect.com
Description: Feature posts in your sidebar.  Select posts by Blog (multisite), individually, by Post Type, or by Tag/Category/Taxonomy.
Contributors: jeffstieler
Author: Jeff Stieler
Version: 0.1
Author URI: http://voceconnect.com
*/

require('walker-post-type-radio-list.php');
require('single-post-super-widget.php');
require('single-taxonomy-super-widget.php');
require('multi-post-super-widget.php');

if (!class_exists('Super_Widgets_Plugin')) {

	define('SUPER_WIDGETS_OPTIONS', 'super_widgets_options');

	class Super_Widgets_Plugin {
		function init() {
			wp_register_script('super_widgets', plugins_url('super-widgets.js', __FILE__), array('jquery'));
			wp_register_style('super_widgets', plugins_url('super-widgets.css', __FILE__));

			$widget_classes = array('Post_Super_Widget_Single', 'Taxonomy_Super_Widget_Single', 'Post_Super_Widget_Multi');
			foreach ($widget_classes as $widget_class) {
				register_widget($widget_class);
				add_action("wp_ajax_{$widget_class}-get-metabox", array($widget_class, 'handle_widget_admin_ajax'));
			}
		}

		function options_init() {
			register_setting( SUPER_WIDGETS_OPTIONS, SUPER_WIDGETS_OPTIONS, array($this, 'validate_options') );
		}

		function add_options_page() {
			add_options_page('Super Widget Options', 'Super Widgets', 'manage_options', SUPER_WIDGETS_OPTIONS, array($this, 'options_page') );
		}

		function validate_options($input) {
			$sanitized = array();
			foreach (array('blog_selection', 'title_override', 'excerpt_override') as $field)
				$sanitized[$field] = isset($input[$field]);
			return $sanitized;
		}

		function options_page() {
		?>
			<div class="wrap">
				<h2>Super Widget Options</h2>
				<form method="post" action="options.php">
					<?php
					settings_fields(SUPER_WIDGETS_OPTIONS);
					$options = get_option(SUPER_WIDGETS_OPTIONS);
					?>
					<table class="form-table">
						<?php if(function_exists('is_multisite') && is_multisite()): ?>
						<tr valign="top"><th scope="row">Allow Post and Taxonomy selection from other Sites on the Network</th>
							<td><input name="<?php echo SUPER_WIDGETS_OPTIONS; ?>[blog_selection]" type="checkbox" value="1" <?php checked($options['blog_selection']); ?> /></td>
						</tr>
						<?php endif; ?>
						<tr valign="top"><th scope="row">Allow override of Widget Title</th>
							<td><input name="<?php echo SUPER_WIDGETS_OPTIONS; ?>[title_override]" type="checkbox" value="1" <?php checked($options['title_override']); ?> /></td>
						</tr>
						<tr valign="top"><th scope="row">Allow override of Widget Excerpt (Single Post &amp; Single Taxonomy only)</th>
							<td><input name="<?php echo SUPER_WIDGETS_OPTIONS; ?>[excerpt_override]" type="checkbox" value="1" <?php checked($options['excerpt_override']); ?> /></td>
						</tr>
					</table>
					<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>
				</form>
			</div>
			<?php
		}
	}
}

$swp = new Super_Widgets_Plugin;
add_action('widgets_init', array($swp, 'init'));
add_action('admin_init', array($swp, 'options_init'));
add_action('admin_menu', array($swp, 'add_options_page'));
unset($swp);

?>