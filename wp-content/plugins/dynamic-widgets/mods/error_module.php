<?php
/**
 * Error Module
 *
 * @version $Id: error_module.php 402236 2011-06-28 20:46:55Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	$opt_e404 = $DW->getDWOpt($_GET['id'], 'e404');
?>

<h4><b><?php _e('Error Page', DW_L10N_DOMAIN); ?></b><?php echo ( ($opt_e404->count) > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget on the error page?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_e404); ?>
<input type="radio" name="e404" value="yes" id="e404-yes" <?php echo ( $opt_e404->selectYes() ) ? $opt_e404->checked : ''; ?> /> <label for="e404-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="e404" value="no" id="e404-no" <?php echo ( $opt_e404->selectNo() ) ? $opt_e404->checked : ''; ?> /> <label for="e404-no"><?php _e('No'); ?></label>
</div><!-- end dynwid_conf -->
