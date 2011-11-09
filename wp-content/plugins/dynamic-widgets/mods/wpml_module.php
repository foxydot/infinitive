<?php
/**
 * WPML Module
 *
 * @version $Id: wpml_module.php 408892 2011-07-12 20:11:47Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	if ( $DW->wpml ) {
		$wpml_api = ICL_PLUGIN_PATH . DW_WPML_API;
		require_once($wpml_api);

		$opt_wpml = $DW->getDWOpt($_GET['id'], 'wpml');
		
		$wpml_langs = wpml_get_active_languages();
		if ( count($wpml_langs) > DW_LIST_LIMIT ) {
			$wpml_condition_select_style = DW_LIST_STYLE;
		}
?>

<h4><b><?php _e('Language (WPML)', DW_L10N_DOMAIN); ?></b><?php echo ( $opt_wpml->count > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on all languages?', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="<?php _e('Click to toggle info', DW_L10N_DOMAIN) ?>" onclick="divToggle('wpml');" /><br /><br />
<?php $DW->dumpOpt($opt_wpml); ?>
<div>
	<div id="wpml" class="infotext">
	<?php _e('Using this option can override all other options.', DW_L10N_DOMAIN); ?><br />
	</div>
</div>
<input type="radio" name="wpml" value="yes" id="wpml-yes" <?php echo ( $opt_wpml->selectYes() ) ? $opt_wpml->checked : ''; ?> /> <label for="wpml-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="wpml" value="no" id="wpml-no" <?php echo ( $opt_wpml->selectNo() ) ? $opt_wpml->checked : ''; ?> /> <label for="wpml-no"><?php _e('No'); ?></label><br />
<?php _e('Except the languages', DW_L10N_DOMAIN); ?>:<br />
<div id="wpml-select" class="condition-select" <?php echo ( isset($wpml_condition_select_style) ? $wpml_condition_select_style : '' ); ?>>
<?php		foreach ( $wpml_langs as $code => $lang ) { ?>
<input type="checkbox" id="wpml_act_<?php echo $lang['code']; ?>" name="wpml_act[]" value="<?php echo $lang['code']; ?>" <?php echo ( $opt_wpml->count > 0 && in_array($lang['code'], $opt_wpml->act) ) ? 'checked="checked"' : ''; ?> /> <label for="wpml_act_<?php echo $lang['code']; ?>"><?php echo $lang['display_name']; ?></label><br />
<?php 	} ?>
</div>
</div><!-- end dynwid_conf -->
<?php } ?>