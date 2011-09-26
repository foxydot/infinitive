<?php
/**
 * Connected Class
 */
class Connected extends WP_Widget {
    /** constructor */
    function Connected() {
		$widget_ops = array('classname' => 'connected', 'description' => __('Show social icons'));
		$control_ops = array('width' => 400, 'height' => 350);
		$this->WP_Widget('connected', __('Connected'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
		echo $before_widget;
		if ( !empty( $title ) ) { print $before_title.$title.$after_title; } 
		?>
		<div id="social-media" class="social-media">
		<?php if(get_option('infinite_google_link')!=""){ ?>
		<a href="<?php echo get_option('infinite_google_link'); ?>" class="gl" title="Google+" target="_blank"></a>
		<?php }?>
		<?php if(get_option('infinite_facebook_link')!=""){ ?>
		<a href="<?php echo get_option('infinite_facebook_link'); ?>" class="fb" title="Join Us on Facebook!" target="_blank"></a>
		<?php }?>
		<?php if(get_option('infinite_twitter_user')!=""){ ?>
		<a href="http://www.twitter.com/<?php echo get_option('infinite_twitter_user'); ?>" class="tw" title="Follow Us on Twitter!" target="_blank"></a>
		<?php }?>
		<?php if(get_option('infinite_linkedin_link')!=""){ ?>
		<a href="<?php echo get_option('infinite_linkedin_link'); ?>" class="li" title="LinkedIn" target="_blank"></a>
		<?php }?>
		<?php if(get_option('infinite_flickr_link')!=""){ ?>
		<a href="<?php echo get_option('infinite_flickr_link'); ?>" class="fl" title="Flickr" target="_blank"></a>
		<?php }?>
		<?php if(get_option('infinite_youtube_link')!=""){ ?>
		<a href="<?php echo get_option('infinite_youtube_link'); ?>" class="yt" title="YouTube" target="_blank"></a>
		<?php }?>
		<?php if(get_option('infinite_sharethis_link')!=""){ ?>
		<a href="<?php echo get_option('infinite_sharethis_link'); ?>" class="st" title="ShareThis" target="_blank"></a>
		<?php }?>
		<a href="<?php echo get_permalink(get_option( 'page_for_posts' )); ?>" title="Blog" class="wp"></a>
	</div>
		<?php 	
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>	
<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("Connected");'));
