<?php
/**
 *	WPEC Module
 *  http://getshopped.org/
 *
 * @version $Id: wpec_module.php 422921 2011-08-13 14:34:41Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	if ( defined('WPSC_VERSION') && version_compare(WPSC_VERSION, '3.8', '<') ) {
		$DW->wpsc = TRUE;
		require_once(DW_PLUGIN . 'wpsc.php');

		$opt_wpsc = $DW->getDWOpt($_GET['id'], 'wpsc');

		$wpsc = dw_wpsc_get_categories();
		if ( count($wpsc) > DW_LIST_LIMIT ) {
			$wpsc_condition_select_style = DW_LIST_STYLE;
		}

		if ( count($wpsc) > 0 ) {
?>
<h4><b><?php _e('WPSC Category', DW_L10N_DOMAIN); ?></b><?php echo ( $opt_wpsc->count > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on WPSC categories?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_wpsc); ?>
<input type="radio" name="wpsc" value="yes" id="wpsc-yes" <?php echo ( $opt_wpsc->selectYes() ) ? $opt_wpsc->checked : ''; ?> /> <label for="wpsc-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="wpsc" value="no" id="wpsc-no" <?php echo ( $opt_wpsc->selectNo() ) ? $opt_wpsc->checked : ''; ?> /> <label for="wpsc-no"><?php _e('No'); ?></label><br />
<?php _e('Except the categories', DW_L10N_DOMAIN); ?>:<br />
<div id="wpsc-select" class="condition-select" <?php echo ( isset($wpsc_condition_select_style) ) ? $wpsc_condition_select_style : ''; ?>>
<?php
	foreach ( $wpsc as $id => $cat ) {
		echo '<input type="checkbox" id="wpsc_act_' . $id . '" name="wpsc_act[]" value="' . $id . '"' . ( $opt_wpsc->count > 0 && in_array($id, $opt_wpsc->act) ? 'checked="checked"' : ''  ) . ' /> <label for="wpsc_act_' . $id . '">' . $cat . '</label><br />';
	}
?>
</div>
</div><!-- end dynwid_conf -->
<?php
		}
	} // end DW->wpsc
?>