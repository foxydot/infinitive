<?php
require_once(ABSPATH . '/wp-admin/includes/image.php');
echo $before_widget;
if ( !empty( $image ) ) {
	if ( $link ) {
		echo '<a class="'.$this->widget_options['classname'].'-image-link" href="'.$link.'" target="'.$linktarget.'">';
	}
	if ( $imageurl ) {
		$uploads = wp_upload_dir();
		if ( !empty( $width ) && is_numeric( $width ) ) {
			$widthstr = "max-width: {$width}px;";
		} 
		if ( !empty( $height ) && is_numeric( $height ) ) {
			$heightstr = "max-height: {$height}px;";
		} 
		if( empty($height) || empty($width) ){
			$height = 74;
			$width = 240;
			$imagepath = preg_replace('@'.$uploads['baseurl'].'@i',$uploads['basedir'],$imageurl);
			$imageurl = preg_replace('@'.$uploads['basedir'].'@i',$uploads['baseurl'],image_resize($imagepath,$width,$height,true));		
		}
		echo "<img src=\"{$imageurl}\" style=\"";
		echo $widthstr;
		echo $heightstr;
		echo "\"";
		if ( !empty( $align ) && $align != 'none' ) {
			echo " class=\"align{$align}\"";
		}
		if ( !empty( $alt ) ) {
			echo " alt=\"{$alt}\"";
		} else {
			echo " alt=\"{$title}\"";					
		}
		echo " />";
	}

	if ( $link ) { echo '</a>'; }
}
echo '<div class="widget-content">';
if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
if ( !empty( $description ) ) {
	$text = apply_filters( 'widget_text', $description );
	echo '<div class="'.$this->widget_options['classname'].'-description" >';
	echo wpautop( $text );			
	echo "</div>";
}
echo '</div>';
if ( $link ) {
		echo '<h4 class="link"><a class="'.$this->widget_options['classname'].'-link alignright" href="'.$link.'" target="'.$linktarget.'">Read More ></a><div class="clear"></div></h4>';
	}
	echo '<div class="clear"></div>';
echo $after_widget;
?>