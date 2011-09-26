<?php
/**
 * The Sidebar containing the primary and secondary widget areas.
 *
 * @package WordPress
 * @subpackage infinite
 * @since infinite 3.1
 */
?>

	<div id="sidebar-wrapper" class="sidebar-wrapper">
		<?php 
		global $pagelevel;
		if($pagelevel == 'content-page'){ print get_the_post_thumbnail( $post->ID, 'sidebar' ); }
		?>
		<div id="primary" class="widget-area" role="complementary">
			<ul>

<?php
	/* When we call the dynamic_sidebar() function, it'll spit out
	 * the widgets for that widget area. If it instead returns false,
	 * then the sidebar simply doesn't exist, so we'll hard-code in
	 * some default sidebar stuff just in case.
	 */
	if ( ! dynamic_sidebar( 'blog-widget-area' ) ) : ?>
	
			<li id="search" class="widget-container widget_search">
				<?php get_search_form(); ?>
			</li>

			<li id="archives" class="widget-container">
				<h3 class="widget-title"><?php _e( 'Archives', 'infinite' ); ?></h3>
				<ul>
					<?php wp_get_archives( 'type=monthly' ); ?>
				</ul>
			</li>

			<li id="meta" class="widget-container">
				<h3 class="widget-title"><?php _e( 'Meta', 'infinite' ); ?></h3>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
			</li>

		<?php endif; // end primary widget area ?>
			</ul>
		</div><!-- #primary .widget-area -->

	</div>