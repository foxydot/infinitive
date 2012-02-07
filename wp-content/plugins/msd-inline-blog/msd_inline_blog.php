<?php
/*
Plugin Name: Inline Blog
Plugin URI: 
Description: Place blog "home page" type archive via a shortcode on any page.
Author: Catherine Sandrick
Version: 0.1
Author URI: http://MadScienceDept.com
*/   
   
/*  Copyright 2011  

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
function msd_inline_blog($atts){
	if(!is_user_logged_in()){
		return;
	}
	extract( shortcode_atts( array(
		'title' => 'Blog',
		'category' => FALSE,
		'numberposts' => 0,
		'order' => 'DESC'
	), $atts ) );
	$args = array(
    'numberposts'     => $numberposts,
    'offset'          => 0,
    'category'        => $category,
    'orderby'         => 'post_date',
    'order'           => 'DESC',
    'post_type'       => 'post',
    'post_status'     => 'publish',
	'suppress_filters' => false
	);
	$posts_array = get_posts( $args );
	$ret = '<h3>'.$title.'</h3>';
	foreach($posts_array AS $post){
			$ret .= '
		<div id="post-'.$post->ID.'" '.get_post_class('',$post->ID).'>
			<h2 class="entry-title"><a href="'.get_permalink($post->ID).'" title="'.sprintf( esc_attr__( 'Permalink to %s', 'foodbank' ), the_title_attribute( 'echo=0' ) ).'" rel="bookmark">'.get_the_title($post->ID).'</a></h2>

			<div class="entry-summary">
				'.$post->post_content.'
			</div><!-- .entry-summary -->

			<div class="clear"></div>
		</div><!-- #post-## -->
		<div class="clear"><br /><br /></div>';
	}
	return $ret;
}

add_shortcode('inline-blog','msd_inline_blog');