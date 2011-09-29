<?php
/**
 * @package MSD CPT Framework
 * @version 0.1
 */
/*
Plugin Name: MSD CPT Framework
Description: Plugin framework for custom post types defined programmatically.
Author: Catherine Sandrick
Version: 0.1
Author URI: http://madsciencedept.com
*/

$msd_cpt_path = plugin_dir_path(__FILE__);
$msd_cpt_url = plugin_dir_url(__FILE__);

if(!class_exists('WPAlchemy_MetaBox')){
	include_once ($msd_cpt_path.'/WPAlchemy/MetaBox.php');
}

//Include utility functions
include_once('includes/msd_publication_functions.php');

//Include CPT files
include_once('includes/msd_publication_cpt.php');
include_once('includes/msd_casestudy_cpt.php');
include_once('includes/msd_news_cpt.php');

//Include any metaboxes
include_once('includes/msd_cpt_metaboxes.php');

//Include widget files
include_once('includes/msd_publication_widget.php');
include_once('includes/msd_news_widget.php');