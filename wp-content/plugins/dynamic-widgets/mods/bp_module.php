<?php
/**
 * BP module
 * http://buddypress.org/
 *
 * @version $Id: bp_module.php 403464 2011-07-01 20:30:55Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	if ( defined('BP_VERSION') ) {
		$DW->bp = TRUE;
		require_once(DW_PLUGIN . 'bp.php');

		$opt_bp = $DW->getDWOpt($_GET['id'], 'bp');
		$bp_components = dw_get_bp_components();
		if ( count($bp_components) > DW_LIST_LIMIT ) {
			$bp_condition_select_style = DW_LIST_STYLE;
		}
		unset($tmp);
?>

<h4><b><?php _e('BuddyPress', DW_L10N_DOMAIN); ?></b><?php echo ( $opt_bp->count > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on BuddyPress pages?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_bp); ?>
<input type="radio" name="bp" value="yes" id="bp-yes" <?php echo ( $opt_bp->selectYes() ) ? $opt_bp->checked : ''; ?> /> <label for="bp-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="bp" value="no" id="bp-no" <?php echo ( $opt_bp->selectNo() ) ? $opt_bp->checked : ''; ?> /> <label for="bp-no"><?php _e('No'); ?></label><br />
<?php _e('Except on the components pages', DW_L10N_DOMAIN); ?>:<br />
<div id="bp-select" class="condition-select" <?php echo ( isset($bp_condition_select_style) ) ? $bp_condition_select_style : ''; ?>>
<?php foreach ( $bp_components as $id => $component ) { ?>
<input type="checkbox" id="bp_act_<?php echo $id; ?>" name="bp_act[]" value="<?php echo $id; ?>" <?php echo ( $opt_bp->count > 0 && in_array($id, $opt_bp->act) ) ? 'checked="checked"' : ''; ?> /> <label for="bp_act_<?php echo $id; ?>"><?php echo $component; ?></label><br />
<?php } ?>
</div>
</div><!-- end dynwid_conf -->

<!-- BuddyPress Groups //-->
<?php
		if ( $DW->bp_groups ) {
			$opt_bp_group = $DW->getDWOpt($_GET['id'], 'bp-group');

			$bp_groups = dw_get_bp_groups();
			if ( count($bp_groups) > DW_LIST_LIMIT ) {
				$bp_group_condition_select_style = DW_LIST_STYLE;
			}

			$bp_group_pages = array(
				'forum_index' 	=> __('Forum Index', DW_L10N_DOMAIN),
				'forum_topic' 	=> __('Forum Topics', DW_L10N_DOMAIN),
				'members_index'	=> __('Members Index', DW_L10N_DOMAIN)
			);
?>
<h4><b><?php _e('BuddyPress Groups', DW_L10N_DOMAIN); ?></b><?php echo ( $opt_bp_group->count > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on BuddyPress Group pages?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_bp_group); ?>
<input type="radio" name="bp-group" value="yes" id="bp-group-yes" <?php echo ( $opt_bp_group->selectYes() ) ? $opt_bp_group->checked : ''; ?> /> <label for="bp-group-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="bp-group" value="no" id="bp-group-no" <?php echo ( $opt_bp_group->selectNo() ) ? $opt_bp_group->checked : ''; ?> /> <label for="bp-group-no"><?php _e('No'); ?></label><br />
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td valign="top">
		<?php _e('Except in the groups', DW_L10N_DOMAIN); ?>:<br />
		<div id="bp-group-select" class="condition-select" <?php echo ( isset($bp_group_condition_select_style) ) ? $bp_group_condition_select_style : ''; ?>>
		<?php foreach ( $bp_groups as $id => $group ) { ?>
		<input type="checkbox" id="bp_group_act_<?php echo $id; ?>" name="bp_group_act[]" value="<?php echo $id; ?>" <?php echo ( count($opt_bp_group->act) > 0 && in_array($id, $opt_bp_group->act) ) ? 'checked="checked"' : ''; ?> /> <label for="bp_group_act_<?php echo $id; ?>"><?php echo ucfirst($group); ?></label><br />
		<?php	 } ?>
		</div>
 </td>
  <td style="width:10px"></td>
  <td valign="top">
		<?php _e('Except in the group pages', DW_L10N_DOMAIN); ?>:<br />
		<div id="bp-group-select" class="condition-select" <?php echo ( isset($bp_group_condition_select_style) ) ? $bp_group_condition_select_style : ''; ?>>
		<?php foreach ( $bp_group_pages as $id => $group_pages ) { ?>
		<input type="checkbox" id="bp_group_act_<?php echo $id; ?>" name="bp_group_act[]" value="<?php echo $id; ?>" <?php echo ( count($opt_bp_group->act) > 0 && in_array($id, $opt_bp_group->act) ) ? 'checked="checked"' : ''; ?> /> <label for="bp_group_act_<?php echo $id; ?>"><?php echo $group_pages; ?></label><br />
		<?php	 } ?>
		</div>
  </td>
</tr>
</table>
</div><!-- end dynwid_conf -->

<?php
		}	// end $DW->bp_groups
	} // end $DW->bp;
?>