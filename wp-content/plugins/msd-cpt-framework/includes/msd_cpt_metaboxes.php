<?php

	// add a custom meta box
	$documents = new WPAlchemy_MetaBox(array
	(
		'id' => '_documents',
		'title' => 'Documents',
		'types' => array('msd_publication','msd_success','msd_news'), // added only for pages and to custom post type "events"
		'context' => 'normal', // same as above, defaults to "normal"
		'priority' => 'high', // same as above, defaults to "high"
		'template' => $msd_cpt_path.'/template/attach_file.php'
	));
	// add a custom meta box
	$casestudy = new WPAlchemy_MetaBox(array
	(
		'id' => '_casestudy',
		'title' => 'Case Study Subtitle',
		'types' => array('msd_casestudy'), 
		'context' => 'normal', 
		'priority' => 'high', 
		'template' => $msd_cpt_path.'/template/casestudy.php'
	));