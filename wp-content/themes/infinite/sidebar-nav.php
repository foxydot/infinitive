<div id="nav-sidebar" class="nav-sidebar">
	<?php 
	global $section;
	set_section();
	switch ($section){
		case 'about':
			wp_nav_menu( array( 'container_class' => 'sideNav', 'menu' => 13 ) ); 
			break;
		case 'client-experience':
			wp_nav_menu( array( 'container_class' => 'sideNav', 'menu' => 14 ) ); 
			break;
		case 'hot-topics':
			wp_nav_menu( array( 'container_class' => 'sideNav', 'menu' => 17 ) ); 
			break;
		default:
			wp_nav_menu( array( 'container_class' => 'sideNav', 'theme_location' => 'side' ) ); 
			break;
	}
	?>
</div>