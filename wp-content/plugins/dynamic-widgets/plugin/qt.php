<?php

/**
 *	QTranslate plugin
 * 	http://www.qianqin.de/qtranslate/
 *
 * @version $Id: qt.php 420121 2011-08-06 17:56:22Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	function dwGetQTLanguage($lang) {
		global $q_config;
		return $q_config['language_name'][$lang];
	}
?>