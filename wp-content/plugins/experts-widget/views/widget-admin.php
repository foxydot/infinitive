<p><label for="<?php echo $this->get_field_id('firstname'); ?>"><?php _e('First Name:', $this->pluginDomain); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('firstname'); ?>" name="<?php echo $this->get_field_name('firstname'); ?>" type="text" value="<?php echo esc_attr(strip_tags($instance['firstname'])); ?>" /></p>

<p><label for="<?php echo $this->get_field_id('lastname'); ?>"><?php _e('Last Name:', $this->pluginDomain); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('lastname'); ?>" name="<?php echo $this->get_field_name('lastname'); ?>" type="text" value="<?php echo esc_attr(strip_tags($instance['lastname'])); ?>" /></p>

<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Job Title:', $this->pluginDomain); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr(strip_tags($instance['title'])); ?>" /></p>

<p><label for="<?php echo $this->get_field_id('image'); ?>"><?php _e('Image:', $this->pluginDomain); ?></label>
<?php
	$media_upload_iframe_src = "media-upload.php?type=image&widget_id=".$this->id; //NOTE #1: the widget id is added here to allow uploader to only return array if this is used with image widget so that all other uploads are not harmed.
	$image_upload_iframe_src = apply_filters('image_upload_iframe_src', "$media_upload_iframe_src");
	$image_title = __(($instance['image'] ? 'Change Image' : 'Add Image'), $this->pluginDomain);
?><br />
<a href="<?php echo $image_upload_iframe_src; ?>&TB_iframe=true" id="add_image-<?php echo $this->get_field_id('image'); ?>" class="thickbox-image-widget" title='<?php echo $image_title; ?>' onClick="set_active_widget('<?php echo $this->id; ?>');return false;" style="text-decoration:none"><img src='images/media-button-image.gif' alt='<?php echo $image_title; ?>' align="absmiddle" /> <?php echo $image_title; ?></a>
<div id="display-<?php echo $this->get_field_id('image'); ?>"><?php 
if ($instance['imageurl']) {
	echo "<img src=\"{$instance['imageurl']}\" alt=\"{$instance['title']}\" style=\"";
		if ($instance['width'] && is_numeric($instance['width'])) {
			echo "max-width: {$instance['width']}px;";
		}
		if ($instance['height'] && is_numeric($instance['height'])) {
			echo "max-height: {$instance['height']}px;";
		}
		echo "\"";
		echo " />";
}
?></div>
<br clear="all" />
<input id="<?php echo $this->get_field_id('image'); ?>" name="<?php echo $this->get_field_name('image'); ?>" type="hidden" value="<?php echo $instance['image']; ?>" />
</p>

<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Caption:', $this->pluginDomain); ?></label>
<textarea rows="8" class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo format_to_edit($instance['description']); ?></textarea></p>

<p><label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link:', $this->pluginDomain); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo esc_attr(strip_tags($instance['link'])); ?>" /><br />
<select name="<?php echo $this->get_field_name('linktarget'); ?>" id="<?php echo $this->get_field_id('linktarget'); ?>">
	<option value="_self"<?php selected( $instance['linktarget'], '_self' ); ?>><?php _e('Stay in Window', $this->pluginDomain); ?></option>
	<option value="_blank"<?php selected( $instance['linktarget'], '_blank' ); ?>><?php _e('Open New Window', $this->pluginDomain); ?></option>
</select></p>
<p><label for="<?php echo $this->get_field_id('linkedin'); ?>"><?php _e('LinkedIn:', $this->pluginDomain); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('linkedin'); ?>" name="<?php echo $this->get_field_name('linkedin'); ?>" type="text" value="<?php echo esc_attr(strip_tags($instance['linkedin'])); ?>" /><br />
</p>
<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:', $this->pluginDomain); ?></label>
<input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo esc_attr(strip_tags($instance['width'])); ?>" onchange="changeImgWidth('<?php echo $this->id; ?>')" /></p>

<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:', $this->pluginDomain); ?></label>
<input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo esc_attr(strip_tags($instance['height'])); ?>" onchange="changeImgHeight('<?php echo $this->id; ?>')" /></p>

<p><label for="<?php echo $this->get_field_id('alt'); ?>"><?php _e('Alternate Text:', $this->pluginDomain); ?></label>
<input id="<?php echo $this->get_field_id('alt'); ?>" name="<?php echo $this->get_field_name('alt'); ?>" type="text" value="<?php echo esc_attr(strip_tags($instance['alt'])); ?>" /></p>