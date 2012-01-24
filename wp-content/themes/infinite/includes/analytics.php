<?php
function infinite_analytics_logo(){
	if(get_blog_option(get_current_blog_id(),'blogname') == 'Analytics' && is_home()){
		$ret = '<div class="eye-on-analytics-logo">
		<img src="'.get_bloginfo('template_url').'/images/eyeonanalytics_logo.png" />
		</div>';
		print $ret;
	} else {
		$ret = '<img class="eye-on-analytics-logo logo" src="'.get_bloginfo('template_url').'/images/eyeonanalytics_logo.png" />';
		return $ret;
	}
}