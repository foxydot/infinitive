<?php
/**
 * The Template for displaying all single posts.
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
get_template_part( 'loop', 'single' );
?>


<?php get_footer(); ?>
