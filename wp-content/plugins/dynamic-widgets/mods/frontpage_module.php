<?php
/**
 * Front Page Module
 *
 * @version $Id: frontpage_module.php 402236 2011-06-28 20:46:55Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	if ( get_option('show_on_front') != 'page' ) {
		$opt_frontpage = $DW->getDWOpt($_GET['id'], 'front-page');
?>

<h4><b><?php _e('Front Page', DW_L10N_DOMAIN); ?></b><?php echo ( $opt_frontpage->count > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget on the front page?', DW_L10N_DOMAIN) ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" onclick="divToggle('frontpage');" /><br />
<?php $DW->dumpOpt($opt_frontpage); ?>
<div>
	<div id="frontpage"  class="infotext">
	<?php _e('This option only applies when your front page is set to display your latest posts (See Settings &gt; Reading).<br />
						When a static page is set, you can use the options for the static pages below.
					', DW_L10N_DOMAIN); ?>
	</div>
</div>
<input type="radio" name="front-page" value="yes" id="front-page-yes" <?php echo ( $opt_frontpage->selectYes() ) ? $opt_frontpage->checked : ''; ?> /> <label for="front-page-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="front-page" value="no" id="front-page-no" <?php echo ( $opt_frontpage->selectNo() ) ? $opt_frontpage->checked : ''; ?> /> <label for="front-page-no"><?php _e('No'); ?></label>
</div><!-- end dynwid_conf -->

<?php } ?>
