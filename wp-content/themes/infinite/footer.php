<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage infinite
 * @since infinite 3.1
 */
?>
<?php switch_to_blog(1); ?>
<div class="clear"></div>
		</div>
		</div><?php //close body wrapper?>
		
	<div id="footer-wrapper" class="footer-wrapper">
		<div id="footer" class="footer">
			<div id="footer-widget-area" class="widget-area" role="complementary">
				<ul>
					<?php dynamic_sidebar( 'footer-widget-area' ); ?>
				</ul>
			</div>
			<div class="site-info">
				<?php wp_nav_menu( array( 'container_class' => 'ftrNav', 'theme_location' => 'footer' ) ); ?>
				<?php infinite_copyright(FALSE); ?>
				<?php do_action( 'infinite_credits' ); ?>
			</div>
			<div class="clear"></div>
		</div>
	</div>
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
</body>
</html><!-- THIS IS THE PRODUCTION SERVER -->
<?php restore_current_blog(); ?>