<?php
function blog_logo($blog){
	if(empty($blog)){$blog='infinitive';}
	if(get_blog_option(get_current_blog_id(),'blogname') == $blog && is_home()){
		$ret = '<div class="'.$blog.'-logo">
		<img src="'.get_bloginfo('template_url').'/images/blog-logos/'.$blog.'_blog.png" />
		</div>';
		print $ret;
	} else {
		$ret = '<div class="blog-hdr"><img class="'.$blog.'-logo logo" src="'.get_bloginfo('template_url').'/images/blog-logos/'.$blog.'_blog.png" /></div>';
		return $ret;
	}
}