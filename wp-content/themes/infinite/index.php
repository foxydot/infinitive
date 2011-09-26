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
<div class="featured-header-area"></div>
<?php get_sidebar('blog'); ?>

<?php
/* Run the loop to output the page.
 * If you want to overload this in a child theme then include a file
 * called loop-page.php and that will be used instead.
 */
get_template_part( 'loop', 'index' );
?>


<?php get_footer(); ?>
