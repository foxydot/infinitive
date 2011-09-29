<?php
function infinitive_highlights($showposts = 4, $blogs = FALSE, $args = array()){
	$recent_posts = array();
	$orig_blog_id = get_current_blog_id();
	if($blogs = 'all'){
		$count = get_blog_count();
		for ($i = 0; $i < $count; $i++){
			switch_to_blog($i+1);
			$highlights = get_posts($args);
			for($j = 0; $j < count($highlights); $j++){
				$highlights[$j]->blog_id = $i+1;
				$highlights[$j]->blog_name = sanitize_title(get_blog_option( $highlights[$j]->blog_id, 'blogname' ));
				$highlights[$j]->featured_image = get_the_post_thumbnail($highlights[$j]->ID);
			}
			$recent_posts = array_merge_recursive($recent_posts,$highlights);
		}
		switch_to_blog($orig_blog_id);
	} else {
		$highlights = get_posts($args);
		for($j = 0; $j < count($highlights); $j++){
			$highlights[$j]->blog_id = $orig_blog_id;
			$highlights[$j]->blog_name = sanitize_title(get_blog_option( $highlights[$j]->blog_id, 'blogname' ));
			$highlights[$j]->featured_image = get_the_post_thumbnail($highlights[$j]->ID);
		}
		$recent_posts = array_merge_recursive($recent_posts,$highlights);
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
		default;
			$grid = 'WAH WAH WAH!';
			break;
	}
	return $grid;
}