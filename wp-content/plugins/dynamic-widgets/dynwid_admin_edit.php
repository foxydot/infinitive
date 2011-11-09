<?php
/**
 * dynwid_admin_edit.php - Options settings
 *
 * @version $Id: dynwid_admin_edit.php 432832 2011-09-03 10:41:24Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	// WPML Plugin support
	if ( defined('ICL_PLUGIN_PATH') && file_exists(ICL_PLUGIN_PATH . DW_WPML_API) ) {
		$DW->wpml = TRUE;
		require(DW_PLUGIN . 'wpml.php');
		$wpml_icon = '<img src="' . $DW->plugin_url . DW_WPML_ICON . '" alt="WMPL" title="Dynamic Widgets syncs with other languages of these pages via WPML" style="position:relative;top:2px;" />';
	}

	// Functions used in more modules
	function getCatChilds($arr, $id, $i) {
		$cat = get_categories( array('hide_empty' => FALSE, 'child_of' => $id) );
		foreach ($cat as $c ) {
			if (! in_array($c->cat_ID, $i) && $c->category_parent == $id ) {
				$i[ ] = $c->cat_ID;
				$arr[$c->cat_ID] = array();
				$a = &$arr[$c->cat_ID];
				$a = getCatChilds($a, $c->cat_ID, &$i);
			}
		}
		return $arr;
	}

	function prtCat($categories, $category_act, $single = FALSE) {
		$DW = $GLOBALS['DW'];

		foreach ( $categories as $pid => $childs ) {
			$run = TRUE;

			if ( $DW->wpml ) {
				$wpml_id = dw_wpml_get_id($pid, 'tax_category');
				if ( $wpml_id > 0 && $wpml_id <> $pid ) {
					$run = FALSE;
				}
			}

			if ( $run ) {
				$cat = get_category($pid);
				echo '<div style="position:relative;left:15px;">';
				echo '<input type="checkbox" id="' . ( $single ? 'single_' : '' ) . 'category_act_' . $cat->cat_ID . '" name="' . ( $single ? 'single_' : '' ) . 'category_act[]" value="' . $cat->cat_ID . '" ' . ( isset($category_act) && count($category_act) > 0 && in_array($cat->cat_ID, $category_act) ? 'checked="checked"' : '' ) . ( $single ? ' onclick="ci(\'single_category_act_' . $cat->cat_ID . '\')"' : '' ) . ' /> <label for="' . ( $single ? 'single_' : '' ) . 'category_act_' . $cat->cat_ID . '">' . $cat->name . '</label><br />';

				if ( count($childs) > 0 ) {
					prtCat($childs, $category_act, $single);
				}
				echo '</div>';
			}
		}
	}

	function dwGetAuthors() {
		global $wpdb;
		$query = "SELECT " . $wpdb->prefix . "users.ID, " . $wpdb->prefix . "users.display_name
							 FROM " . $wpdb->prefix . "users
							 JOIN " . $wpdb->prefix . "usermeta ON " . $wpdb->prefix . "users.ID = " . $wpdb->prefix . "usermeta.user_id
							 WHERE 1 AND " . $wpdb->prefix . "usermeta.meta_key = '" . $wpdb->prefix . "user_level'
							 	AND " . $wpdb->prefix . "usermeta.meta_value > '0'";
		return $wpdb->get_results($query);
	}

	// For JS exclOff
	$excl = array();
	foreach ( $DW->overrule_maintype as $m ) {
		$excl[ ] = "'" . $m . "'";
	}
?>

<style type="text/css">
label {
  cursor : default;
}

.condition-select {
  width : 300px;
  -moz-border-radius-topleft : 6px;
  -moz-border-radius-topright : 6px;
  -moz-border-radius-bottomleft : 6px;
  -moz-border-radius-bottomright : 6px;
  border-style : solid;
  border-width : 1px;
  border-color : #E3E3E3;
  padding : 5px;
}

.infotext {
  width : 98%;
  display : none;
  color : #666666;
  font-style : italic;
}

h4 {
	text-indent : 30px;
}

.hasoptions {
	color : #ff0000;
}

#dynwid {
	font-family : 'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif;
	font-size : 13px;
}

.ui-datepicker {
	font-size : 10px;
}
</style>

<?php
	if ( isset($_POST['dynwid_save']) && $_POST['dynwid_save'] == 'yes' ) {
		$mbox = new DWMessageBox();
		$lead = __('Widget options saved.', DW_L10N_DOMAIN);
		$msg = '<a href="themes.php?page=dynwid-config">' . __('Return', DW_L10N_DOMAIN) . '</a> ' . __('to Dynamic Widgets overview', DW_L10N_DOMAIN);
		$mbox->create($lead, $msg);
	} else if ( isset($_GET['work']) && $_GET['work'] == 'none' ) {
		$mbox = new DWMessageBox('error');
		$text = __('Dynamic does not mean static hiding of a widget.', DW_L10N_DOMAIN) . ' ' . __('Hint', DW_L10N_DOMAIN) . ': <a href="widgets.php">' . __('Remove', DW_L10N_DOMAIN) . '</a>' . __('the widget from the sidebar', DW_L10N_DOMAIN) . '.';
		$mbox->setMessage($text);
		$mbox->output();
	} else if ( isset($_GET['work']) && $_GET['work'] == 'nonedate' ) {
		$mbox = new DWMessageBox('error');
		$text = __('The From date can\'t be later than the To date.', DW_L10N_DOMAIN);
		$mbox->setMessage($text);
		$mbox->output();
	}
?>

<h3><?php _e('Edit options for the widget', DW_L10N_DOMAIN); ?>: <em><?php echo $DW->getName($_GET['id']); ?></em></h3>
<?php echo ( DW_DEBUG ) ? '<pre>ID = ' . $_GET['id'] . '</pre><br />' : ''; ?>

<div style="border-color: #E3E3E3;border-radius: 6px 6px 6px 6px;border-style: solid;border-width: 1px;padding: 5px;">
<b><?php _e('Quick settings', DW_L10N_DOMAIN); ?></b>
<p>
<a href="#" onclick="setOff(); return false;"><?php _e('Set all options to \'No\''); ?></a> (<?php _e('Except overriding options like Role, Date, etc.'); ?>)
</p>
</div><br />

<form action="<?php echo trailingslashit(admin_url()) . 'themes.php?page=dynwid-config&action=edit&id=' . $_GET['id']; ?>" method="post">
<?php wp_nonce_field('plugin-name-action_edit_' . $_GET['id']); ?>
<input type="hidden" name="dynwid_save" value="yes" />
<input type="hidden" name="widget_id" value="<?php echo $_GET['id']; ?>" />
<input type="hidden" name="returnurl" value="<?php echo ( isset($_GET['returnurl']) ? trailingslashit(admin_url()) . $_GET['returnurl'] : '' ); ?>" />

<div id="dynwid">
<?php
	$modules = array(
								'role' => 'Role',
								'date' => 'Date',
								'wpml' => 'Language (WPML)',
								'qtranslate' => 'Language (QT)',
								'useragent' => 'UserAgent',
								'tpl' => 'Templates',
								'frontpage' => 'Front Page',
								'single' => 'Single Posts',
								'attachment' => 'Attachment',
								'pages' => 'Pages',
								'author' => 'Author Pages',
								'category' => 'Category Pages',
								'archive' => 'Archive Pages',
								'error' => 'Error Page',
								'search' => 'Search Page',
								'custompost' => 'Custom Post Types',
								'wpec' => 'WPSC Category',
								'bp' => 'BuddyPress'
							);
	foreach ( $modules as $key => $mod ) {
		$modfile = DW_MODULES . $key . '_module.php';
		echo '<!-- ' . $mod . ' //-->';
		if ( file_exists($modfile) ) {
			include($modfile);
		}
	}
?>

</div><!-- end dynwid -->
<br /><br />

<!-- <div>
Save as a quick setting <input type="text" name="qsetting" value="" />
</div> //-->

<br />
<div style="float:left">
<input class="button-primary" type="submit" value="<?php _e('Save'); ?>" /> &nbsp;&nbsp;
</div>
<?php $url = (! empty($_GET['returnurl']) ? trailingslashit(admin_url()) . $_GET['returnurl'] : trailingslashit(admin_url()) . 'themes.php?page=dynwid-config' ); ?>
<div style="float:left">
<input class="button-secondary" type="button" value="<?php _e('Return', DW_L10N_DOMAIN); ?>" onclick="location.href='<?php echo $url; ?>'" />
</div>

</form>

<script type="text/javascript">
/* <![CDATA[ */
  function chkInPosts() {
    var posts = <?php echo $opt_single_post->count; ?>;
    var tags = <?php echo $opt_single_tag->count; ?>;

    if ( (posts > 0 || tags > 0) && jQuery('#individual').is(':checked') == false ) {
      if ( confirm('Are you sure you want to disable the exception rule for individual posts and tags?\nThis will remove the options set to individual posts and/or tags for this widget.\nOk = Yes; No = Cancel') ) {
        swChb(cAuthors, false);
        swChb(cCat, false);
      } else {
        jQuery('#individual').attr('checked', true);
      }
    } else if ( icount > 0 && jQuery('#individual').is(':checked') ) {
      if ( confirm('Are you sure you want to enable the exception rule for individual posts and tags?\nThis will remove the exceptions set for Author and/or Category on single posts for this widget.\nOk = Yes; No = Cancel') ) {
        swChb(cAuthors, true);
        swChb(cCat, true);
        icount = 0;
      } else {
        jQuery('#individual').attr('checked', false);
      }
    } else if ( jQuery('#individual').is(':checked') ) {
        swChb(cAuthors, true);
        swChb(cCat, true);
    } else {
        swChb(cAuthors, false);
        swChb(cCat, false);
    }
  }

  function chkChild(prefix, pid) {
  	if ( jQuery('#'+prefix+'_act_'+pid).is(':checked') == false ) {
  		jQuery('#'+prefix+'_childs_act_'+pid).attr('checked', false);
  	}
  }

  function chkParent(prefix, pid) {
  	if ( jQuery('#'+prefix+'_childs_act_'+pid).is(':checked') == true ) {
  		jQuery('#'+prefix+'_act_'+pid).attr('checked', true);
  	}
  }

  function chkCPChild(type, pid) {
  	if ( jQuery('#'+type+'_act_'+pid).is(':checked') == false ) {
  		jQuery('#'+type+'_childs_act_'+pid).attr('checked', false);
  	}
  }

  function chkCPParent(type, pid) {
  	if ( jQuery('#'+type+'_childs_act_'+pid).is(':checked') == true ) {
  		jQuery('#'+type+'_act_'+pid).attr('checked', true);
  	}
  }

  function ci(id) {
    if ( jQuery('#'+id).is(':checked') ) {
      icount++;
    } else {
      icount--;
    }
  }

  function divToggle(div) {
    var div = '#'+div;
    jQuery(div).slideToggle(400);
  }

  function showCalendar(id) {
    if ( jQuery('#date-no').is(':checked') ) {
      var id = '#'+id;
      jQuery(function() {
  		  jQuery(id).datepicker({
  		    dateFormat: 'yy-mm-dd',
  		    minDate: new Date(<?php echo date('Y, n - 1, j'); ?>),
  		    onClose: function() {
  		    	jQuery(id).datepicker('destroy');
  		    }
  		  });
        jQuery(id).datepicker('show');
    	});
    } else {
      jQuery('#date-no').attr('checked', true);
      swTxt(cDate, false);
      showCalendar(id);
    }
  }

  function swChb(c, s) {
  	for ( i = 0; i < c.length; i++ ) {
  	  if ( s == true ) {
  	    jQuery('#'+c[i]).attr('checked', false);
  	  }
      jQuery('#'+c[i]).attr('disabled', s);
    }
  }

  function swTxt(c, s) {
  	for ( i = 0; i < c.length; i++ ) {
  	  if ( s == true ) {
  	    jQuery('#'+c[i]).val('');
  	  }
      jQuery('#'+c[i]).attr('disabled', s);
    }
  }

  function setOff() {
  	jQuery(':radio').each( function() {
  		if ( jQuery(this).val() == 'no' && jQuery.inArray(jQuery(this).attr('name'), exclOff) == -1 ) {
  			jQuery(this).attr('checked', true);
  		};
  	});
  	alert('All options set to \'No\'.\nDon\'t forget to make changes, otherwise you\'ll receive an error when saving.');
  }

  var cAuthors = new Array(<?php echo implode(', ', $js_author_array); ?>);
  var cCat = new Array(<?php echo implode(', ', $js_category_array); ?>);
  var cRole = new Array(<?php echo implode(', ' , $jsroles); ?>);
  var cDate =  new Array('date_start', 'date_end');
  var icount = <?php echo $js_count; ?>;
  var exclOff = new Array(<?php echo implode(', ', $excl); ?>);

  if ( jQuery('#role-yes').is(':checked') ) {
  	swChb(cRole, true);
  }
  if ( jQuery('#date-yes').is(':checked') ) {
  	swTxt(cDate, true);
  }
  if ( jQuery('#individual').is(':checked') ) {
    swChb(cAuthors, true);
    swChb(cCat, true);
  }

  jQuery(document).ready(function() {
		jQuery('#dynwid').accordion({
			header: 'h4',
			autoHeight: false,
		});
	});
/* ]]> */
</script>