<?php
/**
 * Archive Module
 *
 * @version $Id: archive_module.php 437634 2011-09-13 19:19:13Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Archive extends DWModule {
		protected static $info = 'This option does not include Author and Category Pages.';
		public static $option = array( 'archive' => 'Archive Pages' );
		protected static $question = 'Show widget on archive pages?';
	}
?>

