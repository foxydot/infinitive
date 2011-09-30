<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage infinite
 * @since infinite 3.1
 */

get_header(); ?>
<?php get_sidebar('breadcrumbs'); ?>
<?php get_sidebar('logo'); ?>
<div id="page-content-wrapper" class="page-content-wrapper">
<?php get_sidebar('nav'); ?>
				<h1><?php _e( 'Not Found', 'infinite' ); ?></h1>
				
				<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'infinite' ); ?></p>
				
				<?php get_search_form(); ?>
				

	<script type="text/javascript">
		// focus on search field after it has loaded
		document.getElementById('s') && document.getElementById('s').focus();
	</script>
<div class="clear"></div>
</div>
<?php get_sidebar('blog'); ?>

<?php get_footer(); ?>