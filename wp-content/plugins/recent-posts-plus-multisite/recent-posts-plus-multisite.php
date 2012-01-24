<?php
/*
Plugin Name: Recent Posts Plus Multisite
Description: Provides a replacement for the recent posts widget with advanced settings
*/

/*  Copyright 2011  Patrick Galbraith  (email : patrick.j.galbraith@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation. 

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
    die('Direct script access not allowed');
}

/**
 * Recent Posts Widget Class
 */
class RecentPostsPlusMultisite extends WP_Widget {
	
	private $default_config = array(
		'title' => '',
		'count' => 5,
		'include_post_thumbnail' => 'false',
		'include_post_excerpt' => 'false',
		'truncate_post_title' => '',
		'truncate_post_title_type' => 'char',
		'truncate_post_excerpt' => '',
		'truncate_post_excerpt_type' => 'char',
		'truncate_elipsis' => '...',
		'post_thumbnail_width' => '50',
		'post_thumbnail_height' => '50',
		'post_date_format' => 'M j',
		'wp_query_options' => '',
		'widget_output_template' => '<li>{THUMBNAIL}<a title="{TITLE_RAW}" href="{PERMALINK}">{TITLE}</a>{EXCERPT}</li>', //default format
		'show_expert_options' => 'false'
	);
	
	/** constructor */	
	function __construct() {
    	$widget_ops = array(
			'classname'   => 'widget_recent_entries', 
			'description' => __('The most recent posts on your site. Includes advanced options.')
		);
    	parent::__construct('recent-posts-plus-multisite', __('Recent Posts Plus Multisite'), $widget_ops);
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		
		$title = apply_filters( 'widget_title', empty($instance['title']) ? 'Recent Posts' : $instance['title'], $instance, $this->id_base);		
		$widget_output_template = (empty($instance['widget_output_template'])) ? $this->default_config['widget_output_template'] : $instance['widget_output_template'];
		
		echo $before_title . $title . $after_title;
		
		$output = $this->parse_output($instance);
		
		//if the first tag of the widget_output_template is a <li> tag then wrap it in <ul>
		if(stripos(ltrim($widget_output_template), '<li') === 0)
			echo '<ul>'.$output.'</ul>';
		else
			echo $output;
		
		echo $after_widget;
	}
	
	function parse_output($instance) {
		
		$output = '';
		
		foreach($this->default_config as $key => $val) {
			$$key = (empty($instance[$key])) ? $val : $instance[$key];
		}
		
		$default_query_args = array(
			'post_type' => 'post', 
			'posts_per_page' => $count, 
			'orderby' => 'date', 
			'order' => 'DESC'
		);
		$query_args = json_decode($wp_query_options, true);
		if($query_args == NULL) $query_args = array();
		
	$recent_posts = array();
	$orig_blog_id = get_current_blog_id();
		$c = get_blog_count();
		for ($i = 0; $i < $c; $i++){
			switch_to_blog($i+1);
			$highlights = get_posts(array_merge($default_query_args, $query_args));
			for($j = 0; $j < count($highlights); $j++){
				$highlights[$j]->blog_id = $i+1;
				$highlights[$j]->blog_name = sanitize_title(get_blog_option( $highlights[$j]->blog_id, 'blogname' ));
				$highlights[$j]->featured_image = get_the_post_thumbnail($highlights[$j]->ID, array($post_thumbnail_width, $post_thumbnail_height));
			}
			$recent_posts = array_merge_recursive($recent_posts,$highlights);
		}
		switch_to_blog($orig_blog_id);
	if(!empty($recent_posts)){
		//Deal with custom date formats, get a list of the custom tags ie {DATE[D \of j]}, {DATE[M j]}, etc...
		$date_matches = array();
		preg_match_all('/\{DATE\[(.*?)\]\}/', $widget_output_template, $date_matches);
		
		//Deal with custom meta tags, e.g. [META[key]]
		$meta_matches = array();
		preg_match_all('/\{META\[(.*?)\]\}/', $widget_output_template, $meta_matches);
		
		usort($recent_posts,'infinitive_posts_sort');
		$recent_posts = array_slice($recent_posts, 0, $count);
		foreach($recent_posts AS $rp){
			$blog_bug = '<img src="'.get_stylesheet_directory_uri().'/images/bugs/'.$rp->blog_name.'-sm.png" title="'.$rp->blog_name.'" alt="'.$rp->blog_name.'" class="attachment-50x36 wp-post-image" width="36" height="36" />';
			if($include_post_thumbnail == "false") {
					$POST_THUMBNAIL = '';
				} else {
					//$POST_THUMBNAIL = get_the_post_thumbnail($rp->ID, array($post_thumbnail_width, $post_thumbnail_height));
					$POST_THUMBNAIL = $rp->featured_image?$rp->featured_image:$blog_bug;
				}
				$POST_TITLE_RAW = strip_tags($rp->post_title);
				if(empty($truncate_post_title))
					$POST_TITLE = $POST_TITLE_RAW;
				else {
					if($truncate_post_title_type == "word")
						$POST_TITLE = $this->_truncate_words($POST_TITLE_RAW, $truncate_post_title, $truncate_elipsis);
					else
						$POST_TITLE = $this->_truncate_chars($POST_TITLE_RAW, $truncate_post_title, $truncate_elipsis);
				}
				if($include_post_excerpt == "false") {
					$POST_EXCERPT_RAW = $POST_EXCERPT = '';
				} else {
					$POST_EXCERPT_RAW = $this->_custom_trim_excerpt();
					if(empty($truncate_post_excerpt))
						$POST_EXCERPT = $POST_EXCERPT_RAW;
					else
						$POST_EXCERPT = $this->_custom_trim_excerpt($truncate_post_excerpt, $truncate_elipsis, $truncate_post_excerpt_type);
				} 
				$widget_ouput_template_params = array(
					'{ID}' => $rp->ID,
					'{THUMBNAIL}' => $POST_THUMBNAIL,
					'{TITLE_RAW}' => $POST_TITLE_RAW,
					'{TITLE}' => $POST_TITLE,
					'{EXCERPT_RAW}' => $POST_EXCERPT_RAW,
					'{EXCERPT}' => $POST_EXCERPT,
					'{PERMALINK}' => get_blog_permalink($rp->blog_id,$rp->ID),
					'{DATE}' => date($post_date_format,strtotime($rp->post_date)),
					'{AUTHOR}' => get_author_name($rp->post_author),
					'{AUTHOR_LINK}' => get_author_link(FALSE,$rp->post_author),
					'{COMMENT_COUNT}' => ((stripos($widget_output_template, '{COMMENT_COUNT}') !== FALSE) ? get_comments_number() : "") //Only load comment count if necessary since it might cause more db queries
				);
				//Deal with custom date formats, parse the custom tags and add the date value
				foreach($date_matches[0] as $key => $date_match) {
					if(!empty($date_matches[1][$key]))
						$widget_ouput_template_params[$date_match] = get_the_date($date_matches[1][$key]);
					else
						$widget_ouput_template_params[$date_match] = '';
				}
				
				//Deal with meta fields
				foreach($meta_matches[0] as $key => $meta_match) {
					if(!empty($meta_matches[1][$key]))
						$widget_ouput_template_params[$meta_match] = get_post_meta($rp->ID, $meta_matches[1][$key], true);
					else
						$widget_ouput_template_params[$meta_match] = '';
				}
				
				$output .= str_replace(array_keys($widget_ouput_template_params), array_values($widget_ouput_template_params), $widget_output_template);

		}
	}
		wp_reset_postdata();
		
		return $output;
	}
	
	/* Replacement to WordPress overly simplistic excerpt trimming function see http://aaronrussell.co.uk/legacy/improving-wordpress-the_excerpt/ */
	function _custom_trim_excerpt($excerpt_length = NULL, $excerpt_more = NULL, $truncate_type = "word") {
		
		global $post;
		
		//If post is password protected then return empty string
		if(!empty($post->post_password))
			return '';
		
		$text = $post->post_excerpt;
		if($text == '') {
			$text = $post->post_content;
			
			//Deal with more tag, if it exists then only grab the content before it
			if ( preg_match('/<!--more(.*?)?-->/', $text, $matches) ) {
				$text = explode($matches[0], $text, 2);
				$text = $text[0];
			}
		}
		
		//$text = apply_filters('the_content', $text);
		$text = strip_shortcodes($text);
		
		$text = str_replace(']]>', ']]&gt;', $text);
		//$text = str_replace('\]\]\>', ']]&gt;', $text);
		$text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text); // Strip out javascript
		$text = preg_replace('@<style[^>]*?>.*?</style>@siU', '', $text); // Strip style tags
		$text = preg_replace('@<![\s\S]*?--[ \t\n\r]*>@', '', $text); // Strip multi-line comments including CDATA
		$text = strip_tags($text, '<p><a><br>');
		
		$excerpt_length = ($excerpt_length === NULL) ? apply_filters('excerpt_length', 55) : $excerpt_length; 
		$excerpt_more = ($excerpt_more === NULL) ? apply_filters('excerpt_more', ' ' . '[...]') : $excerpt_more; 
		
		if($truncate_type == "word") {
			$text = $this->_truncate_words($text, $excerpt_length, $excerpt_more);
		} else {
			$text = $this->_truncate_chars($text, $excerpt_length, $excerpt_more);
		}
		
		return $text;
	}
	
	function _truncate_chars($text, $limit, $ellipsis = '...') {
		if($limit) {
			if( strlen($text) > $limit )
				$text = trim(substr($text, 0, $limit)).$ellipsis;
		}
		return $text;
	}
	
	function _truncate_words($text, $limit, $ellipsis = '...') {
		if($limit) {
			//$words = explode(' ', $text, $excerpt_length + 1);
			$words = preg_split("/[\n\r\t ]+/", $text, $limit + 1, PREG_SPLIT_NO_EMPTY);
			if (count($words) > $limit) {
				array_pop($words);
				$text = implode(' ', $words);
				$text = $text . $ellipsis;
			} else
				$text = implode(' ', $words);
		}
		return $text;
	}
	
	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = strip_tags($new_instance['count']);
		
		$instance['include_post_thumbnail'] = strip_tags( $new_instance[ 'include_post_thumbnail' ] );
		if( empty($instance['include_post_thumbnail']) ) $instance['include_post_thumbnail'] = 'false';
		 
		$instance['include_post_excerpt'] = strip_tags( $new_instance[ 'include_post_excerpt' ] );
		if( empty($instance['include_post_excerpt']) ) $instance['include_post_excerpt'] = 'false';
		
		$instance['truncate_post_title'] = strip_tags( $new_instance[ 'truncate_post_title' ] );
		$instance['truncate_post_title_type'] = strip_tags( $new_instance[ 'truncate_post_title_type' ] );
		$instance['truncate_post_excerpt'] = strip_tags( $new_instance[ 'truncate_post_excerpt' ] );
		$instance['truncate_post_excerpt_type'] = strip_tags( $new_instance[ 'truncate_post_excerpt_type' ] );
		$instance['truncate_elipsis'] = strip_tags( $new_instance[ 'truncate_elipsis' ] );
		$instance['post_thumbnail_width'] = strip_tags( $new_instance[ 'post_thumbnail_width' ] );
		$instance['post_thumbnail_height'] = strip_tags( $new_instance[ 'post_thumbnail_height' ] );
		$instance['wp_query_options'] = $new_instance[ 'wp_query_options' ];
		$instance['widget_output_template'] = $new_instance[ 'widget_output_template' ];
		$instance['show_expert_options'] = strip_tags( $new_instance[ 'show_expert_options' ] );
		$instance['post_date_format'] = strip_tags( $new_instance[ 'post_date_format' ] );
		
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {
			foreach($this->default_config as $key => $val) {
				$$key = esc_attr($instance[$key]);
			}			
		} else {
			/* DEFAULT OPTIONS */
			foreach($this->default_config as $key => $val) {
				$$key = $val;
			}
		}
		?>
        
        <script type="text/javascript">
			jQuery(document).ready(function($) {	
				$('.rpp_show-expert-options').trigger('change');
			});
		</script>
        
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
        <p>
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of posts to show:'); ?></label> 
			<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" size="3" value="<?php echo $count; ?>" />
		</p>
        
        <p>
        	<?php if( current_theme_supports('post-thumbnails') ): ?>
                <input id="<?php echo $this->get_field_id('include_post_thumbnail'); ?>" name="<?php echo $this->get_field_name('include_post_thumbnail'); ?>" type="checkbox" value="true"  class="checkbox" <?php echo ($include_post_thumbnail == 'true') ? 'checked="checked"' : '' ?> />
                <label for="<?php echo $this->get_field_id('include_post_thumbnail'); ?>"><?php _e('Include post thumbnail'); ?></label><br>
        	<?php endif; ?>
            
			<input id="<?php echo $this->get_field_id('include_post_excerpt'); ?>" name="<?php echo $this->get_field_name('include_post_excerpt'); ?>" type="checkbox" value="true" class="checkbox" <?php echo ($include_post_excerpt == 'true') ? 'checked="checked"' : '' ?> />
            <label for="<?php echo $this->get_field_id('include_post_excerpt'); ?>"><?php _e('Include post excerpt'); ?></label><br>
            
            <br>
            
            <input class="rpp_show-expert-options checkbox" id="<?php echo $this->get_field_name('show_expert_options'); ?>" name="<?php echo $this->get_field_name('show_expert_options'); ?>" type="checkbox" value="true" <?php echo ($show_expert_options == 'true') ? 'checked="checked"' : '' ?> /> 
        	<label for="<?php echo $this->get_field_id('show_expert_options'); ?>"><?php _e('Show expert options'); ?></label>
        </p>
        
        <div class="rpp_expert-panel" style="display:none; margin-top: 10px">
        
            <p>
                <label for="<?php echo $this->get_field_id('truncate_post_title'); ?>"><?php _e('Limit post title:'); ?></label> 
                <input id="<?php echo $this->get_field_id('truncate_post_title'); ?>" name="<?php echo $this->get_field_name('truncate_post_title'); ?>" type="text" size="2" value="<?php echo $truncate_post_title; ?>" />
                <select id="<?php echo $this->get_field_id('truncate_post_title_type'); ?>" name="<?php echo $this->get_field_name('truncate_post_title_type'); ?>">
  					<option value="char" <?php echo ($truncate_post_title_type == 'char') ? 'selected="selected"' : '' ?>>Chars</option>
  					<option value="word" <?php echo ($truncate_post_title_type == 'word') ? 'selected="selected"' : '' ?>>Words</option>
				</select>
                <br>
                
                <label for="<?php echo $this->get_field_id('truncate_post_excerpt'); ?>"><?php _e('Limit post excerpt:'); ?></label> 
                <input id="<?php echo $this->get_field_id('truncate_post_excerpt'); ?>" name="<?php echo $this->get_field_name('truncate_post_excerpt'); ?>" type="text" size="2" value="<?php echo $truncate_post_excerpt; ?>" />
                <select id="<?php echo $this->get_field_id('truncate_post_excerpt_type'); ?>" name="<?php echo $this->get_field_name('truncate_post_excerpt_type'); ?>">
  					<option value="char" <?php echo ($truncate_post_excerpt_type == 'char') ? 'selected="selected"' : '' ?>>Chars</option>
  					<option value="word" <?php echo ($truncate_post_excerpt_type == 'word') ? 'selected="selected"' : '' ?>>Words</option>
				</select>
                <br>
                
                <label for="<?php echo $this->get_field_id('truncate_elipsis'); ?>"><?php _e('Limit ellipsis:'); ?></label> 
                <input id="<?php echo $this->get_field_id('truncate_elipsis'); ?>" name="<?php echo $this->get_field_name('truncate_elipsis'); ?>" type="text" size="3" value="<?php echo $truncate_elipsis; ?>" /><br>
                
                <br>
                
                <label for="<?php echo $this->get_field_id('post_date_format'); ?>"><?php _e('Post date format:'); ?></label> <a title="View Documentation" target="_blank" href="http://www.pjgalbraith.com/2011/08/recent-posts-plus/">(?)</a>
                <input id="<?php echo $this->get_field_id('post_date_format'); ?>" name="<?php echo $this->get_field_name('post_date_format'); ?>" type="text" size="3" value="<?php echo $post_date_format; ?>" />
            </p>
            
            <?php if( current_theme_supports('post-thumbnails') ): ?>
                <p>
                    <label for="<?php echo $this->get_field_id('post_thumbnail_width'); ?>"><?php _e('Thumbnail size (WxH):'); ?></label> 
                    <input id="<?php echo $this->get_field_id('post_thumbnail_width'); ?>" name="<?php echo $this->get_field_name('post_thumbnail_width'); ?>" type="text" size="3" value="<?php echo $post_thumbnail_width; ?>" style="width: 40px" />
                    <input id="<?php echo $this->get_field_id('post_thumbnail_height'); ?>" name="<?php echo $this->get_field_name('post_thumbnail_height'); ?>" type="text" size="3" value="<?php echo $post_thumbnail_height; ?>" style="width: 40px" />
                </p>
            <?php endif; ?>
        
            <p>
                <label for="<?php echo $this->get_field_id('wp_query_options'); ?>"><?php _e('WP_Query Options'); ?></label> <a title="View Documentation" target="_blank" href="http://www.pjgalbraith.com/2011/08/recent-posts-plus/">(?)</a><br />
                <textarea id="<?php echo $this->get_field_id('wp_query_options'); ?>" name="<?php echo $this->get_field_name('wp_query_options'); ?>" style="width:222px; height: 100px; font-size: 80%"><?php echo $wp_query_options; ?></textarea>
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('widget_output_template'); ?>"><?php _e('Widget Output Template'); ?></label> <a title="View Documentation" target="_blank" href="http://www.pjgalbraith.com/2011/08/recent-posts-plus/">(?)</a><br />
                <textarea id="<?php echo $this->get_field_id('widget_output_template'); ?>" name="<?php echo $this->get_field_name('widget_output_template'); ?>" style="width:222px; height: 100px; font-size: 80%"><?php echo $widget_output_template; ?></textarea>
            </p>
        
        </div>
        
		<?php 
	}
	
	/** Appends the form script tag to the admin head to allow toggling the expert options panel */
	static function rpp_form_script() { ?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {	
				
				$('.rpp_show-expert-options').live('change', function(){
					if( $(this).is(':checked') ) {
						$(this).parent().parent().find('.rpp_expert-panel').show();
					} else {
						$(this).parent().parent().find('.rpp_expert-panel').hide();
					}
				});
				
				$('.rpp_show-expert-options').trigger('change');
			});
		</script>	
	<?php }

} // class RecentPostsPlus

add_action( 'admin_head', array('RecentPostsPlusMultisite', 'rpp_form_script') );

// register RecentPostsPlus widget
add_action( 'widgets_init', create_function( '', 'return register_widget("RecentPostsPlusMultisite");' ) );