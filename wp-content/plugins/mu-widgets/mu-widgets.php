<?php /*
Plugin Name: MUWidgets
Plugin URI: http://dcoda.co.uk/wordpress/
Description: Extends the standard WordPress widgets to be able to run on another blog on the site
Author: dcoda
Author URI: http://dcoda.co.uk
Version: 1.2.28
 */ 
require_once  dirname ( __FILE__ ) . '/library/wordpress/application.php';
$muwidgets = new wv28v_application ( __FILE__,array('muwidgets') );
