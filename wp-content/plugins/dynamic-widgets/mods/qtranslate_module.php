<?php

/**
 *
 *
 * @version $Id: qtranslate_module.php 420121 2011-08-06 17:56:22Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	if ( defined('QTRANS_INIT') ) {
		$DW->qtranslate = TRUE;
		require_once(DW_PLUGIN . 'qt.php');

		$opt_qt = $DW->getDWOpt($_GET['id'], 'qt');

		$qt_langs = get_option('qtranslate_enabled_languages');
		if ( count($qt_langs) > DW_LIST_LIMIT ) {
			$qt_condition_select_style = DW_LIST_STYLE;
		}
?>

<h4><b><?php _e('Language (QT)', DW_L10N_DOMAIN); ?></b><?php echo ( $opt_qt->count > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on all languages?', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="<?php _e('Click to toggle info', DW_L10N_DOMAIN) ?>" onclick="divToggle('qt');" /><br /><br />
<?php $DW->dumpOpt($opt_qt); ?>
<div>
	<div id="qt" class="infotext">
	<?php _e('Using this option can override all other options.', DW_L10N_DOMAIN); ?><br />
	</div>
</div>
<input type="radio" name="qt" value="yes" id="qt-yes" <?php echo ( $opt_qt->selectYes() ) ? $opt_qt->checked : ''; ?> /> <label for="qt-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="qt" value="no" id="qt-no" <?php echo ( $opt_qt->selectNo() ) ? $opt_qt->checked : ''; ?> /> <label for="qt-no"><?php _e('No'); ?></label><br />
<?php _e('Except the languages', DW_L10N_DOMAIN); ?>:<br />
<div id="qt-select" class="condition-select" <?php echo ( isset($qt_condition_select_style) ? $qt_condition_select_style : '' ); ?>>
<?php		foreach ( $qt_langs as $code ) { ?>
<input type="checkbox" id="qt_act_<?php echo $code; ?>" name="qt_act[]" value="<?php echo $code; ?>" <?php echo ( $opt_qt->count > 0 && in_array($code, $opt_qt->act) ) ? 'checked="checked"' : ''; ?> /> <label for="qt_act_<?php echo $code; ?>"><?php echo dwGetQTLanguage($code); ?></label><br />
<?php 	} ?>
</div>
</div><!-- end dynwid_conf -->

<?php
	}
?>