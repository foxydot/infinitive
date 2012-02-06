<?php get_sidebar('breadcrumbs'); ?>
<?php get_sidebar('logo'); ?>
<div id="page-content-wrapper" class="page-content-wrapper">
<?php get_sidebar('nav'); ?>
<?php 
	global $subdomain;
	print blog_logo($subdomain); ?>
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