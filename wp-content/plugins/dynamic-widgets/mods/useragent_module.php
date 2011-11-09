<?php
/**
 * UserAgent Module
 *
 * @version $Id: useragent_module.php 402236 2011-06-28 20:46:55Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	$useragents = array(
		'gecko'		=> 'Firefox' . ' ' . __('(and other Gecko based)', DW_L10N_DOMAIN),
		'msie'   	=> 'Internet Explorer',
		'msie6'		=> 'Internet Explorer 6',
		'opera'  	=> 'Opera',
		'ns'     	=> 'Netscape 4',
		'safari' 	=> 'Safari',
		'chrome' 	=> 'Chrome',
		'undef'  	=> __('Other / Unknown / Not detected', DW_L10N_DOMAIN)
);
	if ( count($useragents) > DW_LIST_LIMIT ) {
		$browser_condition_select_style = DW_LIST_STYLE;
	}

	$opt_browser = $DW->getDWOpt($_GET['id'], 'browser');
?>

<h4><b><?php _e('Browser', DW_L10N_DOMAIN); ?></b><?php echo ( $opt_browser->count > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget with all browsers?', DW_L10N_DOMAIN) ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="<?php _e('Click to toggle info', DW_L10N_DOMAIN) ?>" onclick="divToggle('browser');" /><br />
<?php $DW->dumpOpt($opt_browser); ?>
<div>
	<div id="browser"  class="infotext">
		<?php _e('Browser detection is never 100% accurate.', DW_L10N_DOMAIN); ?>
	</div>
</div>
<input type="radio" name="browser" value="yes" id="browser-yes" <?php echo ( $opt_browser->selectYes() ) ? $opt_browser->checked : ''; ?> /> <label for="browser-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="browser" value="no" id="browser-no" <?php echo ( $opt_browser->selectNo() ) ? $opt_browser->checked : ''; ?> /> <label for="browser-no"><?php _e('No'); ?></label><br />
<?php _e('Except the browser(s)', DW_L10N_DOMAIN); ?>:<br />
<div id="browser-select" class="condition-select" <?php echo ( isset($browser_condition_select_style) ) ? $browser_condition_select_style : ''; ?>>
<?php foreach ( $useragents as $code => $agent ) { ?>
	<input type="checkbox" id="browser_act_<?php echo $code; ?>" name="browser_act[]" value="<?php echo $code; ?>" <?php echo ( $opt_browser->count > 0 && in_array($code, $opt_browser->act) ? 'checked="checked"' : '' ); ?> /> <label for="browser_act_<?php echo $code; ?>"><?php echo $agent; ?></label><br />
<?php } ?>
</div>
</div><!-- end dynwid_conf -->
