<?php
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

get_header(); 
//this is the homepage, so we aren't looping here.
//insert slideshow
?>

<?php if(is_main_site()):  //this is the mainsite homepage?>
	<?php include('includes/home-main.php'); ?>
	<?php get_footer('home'); ?>
<?php else: //this is a subsite homepage?>
	<?php if(is_home()): //is the homepage dynamic? ?>
		<?php include('includes/home-blog.php') ?>
	<?php else: ?>
		<?php include('includes/home-landing.php'); ?>
		<?php get_footer(); ?>
	<?php endif; ?>
	<?php get_footer(); ?>
<?php endif; ?>
