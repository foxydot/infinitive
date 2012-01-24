<?php
function blog_logo($blog){
	if(get_blog_option(get_current_blog_id(),'blogname') == $blog && is_home()){
		$ret = '<div class="'.$blog.'-logo">
		<img src="'.get_bloginfo('template_url').'/images/blog-logos/'.$blog.'_blog.jpg" />
		</div>';
		print $ret;
	} else {
		$ret = '<img class="'.$blog.'-logo logo" src="'.get_bloginfo('template_url').'/images/blog-logos/'.$blog.'_blog.jpg" />';
		return $ret;
	}
}