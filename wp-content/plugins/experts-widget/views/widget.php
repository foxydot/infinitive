<?php
echo $before_widget;
echo '<div class="widget-content">';
echo $before_title . 'Meet Our Experts' . $after_title;
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
		echo " class=\"alignleft\"";
		
		if ( !empty( $alt ) ) {
			echo " alt=\"{$alt}\"";
		} else {
			echo " alt=\"{$title}\"";					
		}
		echo " />";
	}

	if ( $link ) { echo '</a>'; }
}
	echo '<div class="'.$this->widget_options['classname'].'-description" >';
if ( !empty( $description ) ) {
	$text = apply_filters( 'widget_text', $description );
	echo wpautop( $text );		
}
	if ( !empty( $firstname ) || !empty( $lastname ) ) { echo $firstname.' '.$lastname; }
	if ( !empty( $title ) ) { echo '<br /><em>' . $title . '</em>'; }	
	echo "</div>";

echo '</div>';
echo '<div class="clear"></div>';
echo '<div class="links">';
if ( $link ) {
		echo '<h4 class="link"><a class="'.$this->widget_options['classname'].'-link alignright" href="'.$link.'" target="'.$linktarget.'">Learn more about '.$firstname.' ></a></h4>';
	}
if ( $linkedin ) {
		echo '<h4 class="link linkedin"><a class="'.$this->widget_options['classname'].'-linkedin alignright" href="'.$linkedin.'" target="_blank">Connect with '.$firstname.'</a></h4>';
	}
echo '<div class="clear"></div>
</div>';
echo $after_widget;
?>