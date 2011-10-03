<?php
/*
Template Name: Wide Page
*/

/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the wordpress construct of pages
 * and that other 'pages' on your wordpress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage infinite
 * @since infinite 3.1
 */
global $slug;
get_header(); ?>
<?php get_sidebar('breadcrumbs'); ?>
<div id="page-content-wrapper" class="page-content-wrapper">
<?php get_sidebar('nav'); ?>
<?php
/* Run the loop to output the page.
 * If you want to overload this in a child theme then include a file
 * called loop-page.php and that will be used instead.
 */
get_template_part( 'loop', 'wide' );
?>
<div class="clear"></div>
</div>

<?php get_footer(); ?>
