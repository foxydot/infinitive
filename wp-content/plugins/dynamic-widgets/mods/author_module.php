<?php
/**
 * Author Module
 *
 * @version $Id: author_module.php 418888 2011-08-03 18:36:48Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	$opt_author = $DW->getDWOpt($_GET['id'], 'author');

	if ( function_exists('get_users') ) {
		$authors = get_users( array('who' => 'author') );
	} else {
		$authors = dwGetAuthors();
	}

	if ( count($authors) > DW_LIST_LIMIT ) {
		$author_condition_select_style = DW_LIST_STYLE;
	}
?>

<h4><b><?php _e('Author Pages', DW_L10N_DOMAIN); ?></b><?php echo ( $opt_author->count > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on author pages?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_author); ?>
<input type="radio" name="author" value="yes" id="author-yes" <?php echo ( $opt_author->selectYes() ) ? $opt_author->checked : ''; ?> /> <label for="author-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="author" value="no" id="author-no" <?php echo ( $opt_author->selectNo() ) ? $opt_author->checked : ''; ?> /> <label for="author-no"><?php _e('No'); ?></label><br />
<?php _e('Except the author(s)', DW_L10N_DOMAIN); ?>:<br />
<div id="author-select" class="condition-select" <?php echo ( isset($author_condition_select_style) ) ? $author_condition_select_style : ''; ?>>
<?php foreach ( $authors as $author ) { ?>
<input type="checkbox" id="author_act_<?php echo $author->ID; ?>" name="author_act[]" value="<?php echo $author->ID; ?>" <?php echo ( $opt_author->count > 0 && in_array($author->ID, $opt_author->act) ) ? $opt_author->checked : ''; ?> /> <label for="author_act_<?php echo $author->ID; ?>"><?php echo $author->display_name; ?></label><br />
<?php } ?></div>
</div><!-- end dynwid_conf -->
