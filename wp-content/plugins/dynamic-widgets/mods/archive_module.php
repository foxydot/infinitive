<?php
/**
 * Archive Module
 *
 * @version $Id: archive_module.php 402236 2011-06-28 20:46:55Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	$opt_archive = $DW->getDWOpt($_GET['id'], 'archive');
?>

<h4><b><?php _e('Archive Pages', DW_L10N_DOMAIN); ?></b><?php echo ( $opt_archive->count > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget on archive pages', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="<?php _e('Click to toggle info', DW_L10N_DOMAIN); ?>" onclick="divToggle('archive')" /><br />
<?php $DW->dumpOpt($opt_archive); ?>
<div>
<div id="archive" class="infotext">
  <?php _e('This option does not include Author and Category Pages.', DW_L10N_DOMAIN); ?>
</div>
</div>
<input type="radio" name="archive" value="yes" id="archive-yes" <?php echo ( $opt_archive->selectYes() ) ? $opt_archive->checked : ''; ?> /> <label for="archive-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="archive" value="no" id="archive-no" <?php echo ( $opt_archive->selectNo() ) ? $opt_archive->checked : ''; ?> /> <label for="archive-no"><?php _e('No'); ?></label>
</div><!-- end dynwid_conf -->
