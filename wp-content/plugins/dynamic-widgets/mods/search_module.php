<?php

/**
 * Search Module
 *
 * @version $Id: search_module.php 402236 2011-06-28 20:46:55Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	$opt_search = $DW->getDWOpt($_GET['id'], 'search');
?>

<h4><b><?php _e('Search Page', DW_L10N_DOMAIN); ?></b><?php echo ( $opt_search->count > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget on the search page?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_search); ?>
<input type="radio" name="search" value="yes" id="search-yes" <?php echo ( $opt_search->selectYes() ) ? $opt_search->checked : ''; ?> /> <label for="search-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="search" value="no" id="search-no" <?php echo ( $opt_search->selectNo() ) ? $opt_search->checked : ''; ?> /> <label for="search-no"><?php _e('No'); ?></label>
</div><!-- end dynwid_conf -->
