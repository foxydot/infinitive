<?php
class PF_Connect_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_connect', 'description' => __('Social Media'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('connect', __('Connect'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$text = apply_filters( 'widget_text', $instance['text'], $instance );
		$themedir = $id == 'sidebar'?get_stylesheet_directory_uri():get_template_directory_uri();
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<div class="socnet_text"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
			 <div id="socnet_links" class="connect_widget socnet_links">
    <?php if(get_option("dm_ts_sn_twitter")) { ?>
        <a href="http://www.twitter.com/<?php echo get_option("dm_ts_sn_twitter"); ?>" class="tw" target="_BLANK"><img src="<?php echo $themedir; ?>/images/social/twitter.png" alt="Twitter" /> </a>
    <?php } ?>
    <?php if(get_option("dm_ts_sn_facebook")) { ?>
        <a href="<?php echo get_option("dm_ts_sn_facebook"); ?>" class="fb" target="_BLANK"><img src="<?php echo $themedir; ?>/images/social/face.png" alt="Facebook" /> </a>
    <?php } ?>
    <?php if(get_option("dm_ts_sn_digg")) { ?>
        <a href="http://www.digg.com/<?php echo get_option("dm_ts_sn_digg"); ?>" class="dg" target="_BLANK"><img src="<?php echo $themedir; ?>/images/social/digg.png" alt="Digg" /> </a>
    <?php } ?>
    <?php if(get_option("dm_ts_sn_vimeo")) { ?>
        <a href="http://www.vimeo.com/<?php echo get_option("dm_ts_sn_vimeo"); ?>" class="vm" target="_BLANK"><img src="<?php echo $themedir; ?>/images/social/vimeo.png" alt="Vimeo" /> </a>
    <?php } ?>
    <?php if(get_option("dm_ts_sn_youtube")) { ?>
        <a href="http://www.youtube.com/user/<?php echo get_option("dm_ts_sn_youtube"); ?>" class="yt" target="_BLANK"><img src="<?php echo $themedir; ?>/images/social/youtube.png" alt="YouTube" /> </a>
    <?php } ?>
    <?php if(get_option("dm_ts_sn_linkedin")) { ?>
        <a href="<?php echo get_option("dm_ts_sn_linkedin"); ?>" class="li" target="_BLANK"><img src="<?php echo $themedir; ?>/images/social/linkedin.png" alt="LinkedIn" /> </a>
    <?php } ?>
    </div>
    <div class="clear"></div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$text = esc_textarea($instance['text']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("PF_Connect_Widget");')); // register the custom search
?>