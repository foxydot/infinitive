<?php
/**
 * Attachment Module
 *
 * @version $Id: attachment_module.php 402236 2011-06-28 20:46:55Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	$opt_attachment = $DW->getDWOpt($_GET['id'], 'attachment');
?>

<h4><b><?php _e('Attachments', DW_L10N_DOMAIN); ?></b><?php echo ( $opt_attachment->count > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget on attachment pages', DW_L10N_DOMAIN); ?>?<br />
<?php $DW->dumpOpt($opt_attachment); ?>
<input type="radio" name="attachment" value="yes" id="attachment-yes" <?php echo ( $opt_attachment->selectYes() ) ? $opt_attachment->checked : ''; ?> /> <label for="attachment-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="attachment" value="no" id="attachment-no" <?php echo ( $opt_attachment->selectNo() ) ? $opt_attachment->checked : ''; ?> /> <label for="attachment-no"><?php _e('No'); ?></label>
</div><!-- end dynwid_conf -->
