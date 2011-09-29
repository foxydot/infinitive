<?php
echo $before_widget;
if ( !empty( $image ) ) {
	if ( $link ) {
		echo '<a class="'.$this->widget_options['classname'].'-image-link" href="'.$link.'" target="'.$linktarget.'">';
	}
	if ( $imageurl ) {
		echo "<img src=\"{$imageurl}\" style=\"";
		if ( !empty( $width ) && is_numeric( $width ) ) {
			echo "max-width: {$width}px;";
		}
		if ( !empty( $height ) && is_numeric( $height ) ) {
			echo "max-height: {$height}px;";
		}
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
		echo '<h4 class="link"><a class="'.$this->widget_options['classname'].'-link alignright" href="'.$link.'" target="'.$linktarget.'">Read More ></a></h4>';
	}
echo $after_widget;
?>