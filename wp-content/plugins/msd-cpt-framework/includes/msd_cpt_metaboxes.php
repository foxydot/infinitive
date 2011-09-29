<?php

	// add a custom meta box
	$subtitle = new WPAlchemy_MetaBox(array
	(
		'id' => '_subtitle',
		'title' => 'Subtitle',
		'types' => array('post','page','msd_casestudy','msd_news','msd_publiction'), 
		'context' => 'normal', 
		'priority' => 'high', 
		'template' => $msd_cpt_path.'/template/subtitle.php'
	));
	// add a custom meta box
	$documents = new WPAlchemy_MetaBox(array
	(
		'id' => '_documents',
		'title' => 'Documents',
		'types' => array('msd_publication','msd_casestudy','msd_news'), // added only for pages and to custom post type "events"
		'context' => 'normal', // same as above, defaults to "normal"
		'priority' => 'high', // same as above, defaults to "high"
		'template' => $msd_cpt_path.'/template/attach_file.php'
	));