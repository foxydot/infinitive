<?php /*
Plugin Name: Multisite Widgets
Plugin URI: http://muwidgets.dcoda.co.uk/
Description: Extends the standard WordPress widgets to be able to run on another blog on the site.
Author: dcoda
Author URI: 
Version: 1.2.44
License: GPLv2 or later
*/
@require_once  dirname ( __FILE__ ) . '/library/wordpress/application.php';
if (class_exists("wv44v_application"))
{
	new wv44v_application ( __FILE__);
}