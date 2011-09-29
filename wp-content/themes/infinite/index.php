<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query. 
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
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
get_template_part( 'loop', 'index' );
?>
<div class="clear"></div>
</div>
<?php get_sidebar('blog'); ?>

<?php get_footer(); ?>