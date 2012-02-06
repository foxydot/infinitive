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
		<?php if(get_option('infinite_salesforce_oid')!=""){ ?>
		<div class="salesforce-form">
			<form action="https://www.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8" method="POST">
				<input type=hidden name="oid" value="<?php print get_option('infinite_salesforce_oid'); ?>">
				<input type=hidden name="retURL" value="<?php print get_option('infinite_salesforce_returnurl')!=""?get_option('infinite_salesforce_returnurl'):'http://infinitive.com'; ?>">
				<input id="email" maxlength="80" name="email" type="text" onfocus="if (this.value == 'email') {this.value = '';}" onblur="if (this.value == '') {this.value = 'email';}" value="email" /><br>
				<input id="first_name" maxlength="40" name="first_name" type="text"  onfocus="if (this.value == 'first name') {this.value = '';}" onblur="if (this.value == '') {this.value = 'first name';}" value="first name"/><br>
				<input id="last_name" maxlength="80" name="last_name" type="text"  onfocus="if (this.value == 'last name') {this.value = '';}" onblur="if (this.value == '') {this.value = 'last name';}" value="last name"/><br>
				<input type="submit" name="submit" value="signup" />
			</form>
		</div>
		<?php } ?>
		<div id="social-media" class="social-media">
		<?php if(get_option('infinite_google_link')!=""){ ?>
		<a href="<?php echo get_option('infinite_google_link'); ?>" class="gl" title="Google+" target="_blank">Google +</a>
		<?php }?>
		<?php if(get_option('infinite_facebook_link')!=""){ ?>
		<a href="<?php echo get_option('infinite_facebook_link'); ?>" class="fb" title="Join Us on Facebook!" target="_blank">Facebook</a>
		<?php }?>
		<?php if(get_option('infinite_twitter_user')!=""){ ?>
		<a href="http://www.twitter.com/<?php echo get_option('infinite_twitter_user'); ?>" class="tw" title="Follow Us on Twitter!" target="_blank">Twitter</a>
		<?php }?>
		<?php if(get_option('infinite_linkedin_link')!=""){ ?>
		<a href="<?php echo get_option('infinite_linkedin_link'); ?>" class="li" title="LinkedIn" target="_blank">LinkedIn</a>
		<?php }?>
		<?php if(get_option('infinite_flickr_link')!=""){ ?>
		<a href="<?php echo get_option('infinite_flickr_link'); ?>" class="fl" title="Flickr" target="_blank">Flickr</a>
		<?php }?>
		<?php if(get_option('infinite_youtube_link')!=""){ ?>
		<a href="<?php echo get_option('infinite_youtube_link'); ?>" class="yt" title="YouTube" target="_blank">YouTube</a>
		<?php }?>
		<?php if(get_option('infinite_sharethis_link')!=""){ ?>
		<a href="<?php echo get_option('infinite_sharethis_link'); ?>" class="st" title="ShareThis" target="_blank">ShareThis</a>
		<?php }?>
		<a href="<?php echo get_permalink(get_option( 'page_for_posts' )); ?>" title="Blog" class="wp">WordPress</a>
	</div>
	<div class="clear"></div>
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
