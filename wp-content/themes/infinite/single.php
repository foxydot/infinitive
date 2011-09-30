<?php
/**
 * The Template for displaying all single posts.
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
<?php
/* Run the loop to output the page.
 * If you want to overload this in a child theme then include a file
 * called loop-page.php and that will be used instead.
 */
get_template_part( 'loop', 'single' );
?>
<div class="clear"></div>
</div>
<?php get_sidebar('blog'); ?>

<?php get_footer(); ?>
