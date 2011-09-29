<?php
/*
Template Name: Landing Page
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

	<?php include('includes/home-landing.php'); ?>
	<?php get_footer(); ?>
