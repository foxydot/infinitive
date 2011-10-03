<?php
function infinitive_highlights($showposts = 4, $blogs = FALSE, $args = array()){
	$recent_posts = array();
	$orig_blog_id = get_current_blog_id();
	if($blogs == 'all'){
		$count = get_blog_count();
		for ($i = 0; $i < $count; $i++){
			switch_to_blog($i+1);
			$highlights = get_posts($args);
			for($j = 0; $j < count($highlights); $j++){
				$highlights[$j]->blog_id = $i+1;
				$highlights[$j]->blog_name = sanitize_title(get_blog_option( $highlights[$j]->blog_id, 'blogname' ));
				$highlights[$j]->featured_image = get_the_post_thumbnail($highlights[$j]->ID, 'gridthumb');
			}
			$recent_posts = array_merge_recursive($recent_posts,$highlights);
		}
		switch_to_blog($orig_blog_id);
	} else {
		if(!$blogs){ $blogs = array($orig_blog_id); }
		foreach($blogs AS $blog_id){
			switch_to_blog($blog_id);
			$highlights = get_posts($args);
			for($j = 0; $j < count($highlights); $j++){
				$highlights[$j]->blog_id = $blog_id;
				$highlights[$j]->blog_name = sanitize_title(get_blog_option( $highlights[$j]->blog_id, 'blogname' ));
				$highlights[$j]->featured_image = get_the_post_thumbnail($highlights[$j]->ID, 'gridthumb');
			}
			$recent_posts = array_merge_recursive($recent_posts,$highlights);
		}
		switch_to_blog($orig_blog_id);
	}
	usort($recent_posts,'infinitive_posts_sort');
	$recent_posts = array_slice($recent_posts, 0, $showposts);
	foreach($recent_posts AS $rp){
		$ret .= '<li class="widget-container highlight '.$rp->blog_name.'">
			'.$rp->featured_image.'
			<h4>'.$rp->post_title.'</h4>
			<p>'.$rp->post_excerpt.'</p>
			<a href="'.get_blog_permalink($rp->blog_id,$rp->ID).'"></a>
		</li>';
	}
	return $ret;
}

function infinitive_posts_sort( $a, $b ) {
	if(  $a->post_date ==  $b->post_date ){ return 0 ; }
	  return ($a->post_date < $b->post_date) ? 1 : -1;
}

function infinitive_grid_system($page_slug){
	switch($page_slug){
		case 'client-experience':
		case 'all-case-studies':
			$grid = '<div><h5>CRM ></h5><ul>'.infinitive_highlights(3,'all', array('post_type' => 'page', 'tax_query' => array(array('taxonomy' => 'msd_special', 'field' => 'slug', 'terms' => 'crm')), 'number_posts' => 12)).'</ul><div class="clear"></div></div>';
			$grid .= '<div><h5>Web Analytics &<br />Online Marketing ></h5><ul>'.infinitive_highlights(3,'all', array('post_type' => 'page', 'tax_query' => array(array('taxonomy' => 'msd_special', 'field' => 'slug', 'terms' => 'web-analytics-online-marketing')), 'number_posts' => 12)).'</ul><div class="clear"></div></div>';
			$grid .= '<div><h5>PMO ></h5><ul>'.infinitive_highlights(3,'all', array('post_type' => 'page', 'tax_query' => array(array('taxonomy' => 'msd_special', 'field' => 'slug', 'terms' => 'pmo')), 'number_posts' => 12)).'</ul><div class="clear"></div></div>';
			$grid .= '<div><h5>Risk Management ></h5><ul>'.infinitive_highlights(3,'all', array('post_type' => 'page', 'tax_query' => array(array('taxonomy' => 'msd_special', 'field' => 'slug', 'terms' => 'risk-management')), 'number_posts' => 12)).'</ul><div class="clear"></div></div>';
			break;
		case 'blogs':
			$grid = '<div><h5><a class="infinitive logo" href="'.get_site_url(1).'/blog" /><a href="'.get_site_url(1).'/blog">Visit Blog ></a><a href="#">Subscribe ></a></h5><ul>'.infinitive_highlights(3,array('1'), array('post_type' => 'post', 'number_posts' => 3)).'</ul><div class="clear"></div></div>';
			$grid .= '<div><h5><a class="analytics logo" href="'.get_site_url(4).'/blog" /><a href="'.get_site_url(4).'/blog">Visit Blog ></a><a href="#">Subscribe ></a></h5><ul>'.infinitive_highlights(3,array('4'), array('post_type' => 'post', 'number_posts' => 3)).'</ul><div class="clear"></div></div>';
			$grid .= '<div><h5><a class="insight logo" href="'.get_site_url(3).'/blog" /><a href="'.get_site_url(3).'/blog">Visit Blog ></a><a href="#">Subscribe ></a></h5><ul>'.infinitive_highlights(3,array('3'), array('post_type' => 'post', 'number_posts' => 3)).'</ul><div class="clear"></div></div>';
			$grid .= '<div><h5><a class="federal logo" href="'.get_site_url(2).'/blog" /><a href="'.get_site_url(2).'/blog">Visit Blog ></a><a href="#">Subscribe ></a></h5><ul>'.infinitive_highlights(3,array('2'), array('post_type' => 'post', 'number_posts' => 3)).'</ul><div class="clear"></div></div>';
			break;
		default;
			$grid = '';
			break;
	}
	return $grid;
}