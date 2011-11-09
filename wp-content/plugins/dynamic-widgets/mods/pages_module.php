<?php
/**
 * Pages Module
 *
 * @version $Id: pages_module.php 426716 2011-08-21 14:48:48Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	// Abort function when timeout occurs
	register_shutdown_function('dw_abort');
	function dw_abort() {
		if ( connection_status() == CONNECTION_TIMEOUT ) {
			echo '<div class="error" id="message"><p><strong>';
			_e('The static page module failed to load.', DW_L10N_DOMAIN);
			echo '</strong><br />';
			_e('This is probably because building the hierarchical tree took too long.<br />Decrease the limit of number of pages in the advanced settings.', DW_L10N_DOMAIN);
			echo '</p></div>';
		}
		exit();
	}

	function getPageChilds($arr, $id, $i) {
		$pg = get_pages('child_of=' . $id);
		foreach ($pg as $p ) {
			if (! in_array($p->ID, $i) ) {
				$i[ ] = $p->ID;
				$arr[$p->ID] = array();
				$a = &$arr[$p->ID];
				$a = getPageChilds($a, $p->ID, &$i);
			}
		}
		return $arr;
	}

	function prtPgs($pages, $page_act, $page_childs_act, $static_page) {
		foreach ( $pages as $pid => $childs ) {
			$page = get_page($pid);

			echo '<div style="position:relative;left:15px;width:95%">';
			echo '<input type="checkbox" id="page_act_' . $page->ID . '" name="page_act[]" value="' . $page->ID . '" ' . ( isset($page_act) && count($page_act) > 0 && in_array($page->ID, $page_act) ? 'checked="checked"' : '' ) . ' onchange="chkChild(\'page\', ' . $pid . ')" /> <label for="page_act_' . $page->ID . '">' . $page->post_title . ' ' . ( get_option('show_on_front') == 'page' && isset($static_page[$page->ID]) ? '(' . $static_page[$page->ID] . ')' : '' ) . '</label><br />';

			echo '<div style="position:relative;left:15px;">';
			echo '<input type="checkbox" id="page_childs_act_' . $pid . '" name="page_childs_act[]" value="' . $pid . '" ' . ( isset($page_childs_act) && count($page_childs_act) > 0 && in_array($pid, $page_childs_act) ? 'checked="checked"' : '' ) . ' onchange="chkParent(\'page\', ' . $pid . ')" /> <label for="page_childs_act_' . $pid . '"><em>' . __('All childs', DW_L10N_DOMAIN) . '</em></label><br />';
			echo '</div>';

			if ( count($childs) > 0 ) {
				prtPgs($childs, $page_act, $page_childs_act, $static_page);
			}
			echo '</div>';
		}
	}

	function lsPages($pages, $static_page, $page_act) {
		echo '<div style="position:relative;left:15px;width:95%">';
		foreach ( $pages as $page ) {
			echo '<input type="checkbox" id="page_act_' . $page->ID . '" name="page_act[]" value="' . $page->ID . '" ' . ( count($page_act) > 0 && in_array($page->ID, $page_act) ? 'checked="checked"' : '' ) . ' /> <label for="page_act_' . $page->ID . '">' . $page->post_title . ' ' . ( get_option('show_on_front') == 'page' && isset($static_page[$page->ID]) ? '(' . $static_page[$page->ID] . ')' : '' ) . '</label><br />';
 		}
 		echo '</div>';
	}

	$opt_page = $DW->getDWOpt($_GET['id'], 'page');
	if ( $opt_page->count > 0 ) {
		$opt_page_childs = $DW->getDWOpt($_GET['id'], 'page-childs');
	}

	$pages = get_pages();
	$num_pages = count($pages);

	if ( $num_pages < DW_PAGE_LIMIT ) {
		$pagemap = getPageChilds(array(), 0, array());
	}

	// For childs we double the number of pages because of addition of 'All childs' option
	if ( (isset($pagemap) && ($num_pages * 2 > DW_LIST_LIMIT)) || ($num_pages  > DW_LIST_LIMIT) ) {
		$page_condition_select_style = DW_LIST_STYLE;
	}

	$static_page = array();
	if ( get_option('show_on_front') == 'page' ) {
		if ( get_option('page_on_front') == get_option('page_for_posts') ) {
			$id = get_option('page_on_front');
			$static_page[$id] = __('Front page', DW_L10N_DOMAIN) . ', ' . __('Posts page', DW_L10N_DOMAIN);
		} else {
			$id = get_option('page_on_front');
			$static_page[$id] = __('Front page', DW_L10N_DOMAIN);
			$id = get_option('page_for_posts');
			$static_page[$id] = __('Posts page', DW_L10N_DOMAIN);
		}
	}
?>
<h4><b><?php _e('Pages'); ?></b> <?php echo ( ($opt_page->count > 0) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . ( ($DW->wpml) ? $wpml_icon : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on static pages?', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="<?php _e('Click to toggle info', DW_L10N_DOMAIN); ?>" onclick="divToggle('pages');" /><br />
<?php $DW->dumpOpt($opt_page); ?>
<?php ( isset($opt_page_childs) ) ? $DW->dumpOpt($opt_page_childs) : ''; ?>
<div>
	<div id="pages" class="infotext">
	<?php
		if ( $num_pages < DW_PAGE_LIMIT ) {
			$childs_infotext = __('Checking the "All childs" option, makes the exception rule apply
					to the parent and all items under it in all levels. Also future items
					under the parent. It\'s not possible to apply an exception rule to
					"All childs" without the parent.', DW_L10N_DOMAIN);
		} else {
			$childs_infotext = __('Unfortunately the childs-function has been disabled
					because you have more than the limit of pages.', DW_L10N_DOMAIN) . '(' . DW_PAGE_LIMIT . ')';
		}
		echo $childs_infotext;
	?>
	</div>
</div>
<input type="radio" name="page" value="yes" id="page-yes" <?php echo ( ($opt_page->selectYes()) ? $opt_page->checked : '' ); ?> /> <label for="page-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="page" value="no" id="page-no" <?php echo ( ($opt_page->selectNo()) ? $opt_page->checked : '' ); ?> /> <label for="page-no"><?php _e('No'); ?></label><br />
<?php if ( $num_pages > 0 ) { ?>
<?php _e('Except the page(s)', DW_L10N_DOMAIN); ?>:<br />
<div id="page-select" class="condition-select" <?php echo ( (isset($page_condition_select_style)) ? $page_condition_select_style : '' ); ?>>
<div style="position:relative;left:-15px">
<?php
	if ( $num_pages < DW_PAGE_LIMIT ) {
		if (! isset($opt_page_childs) ) {
			$childs = array();
		} else {
			$childs = $opt_page_childs->act;
		}
		prtPgs($pagemap, $opt_page->act, $childs, $static_page);
	} else {
		lsPages($pages, $static_page, $opt_page->act);
	}
?>
</div>
</div>
<?php } ?>
</div><!-- end dynwid_conf -->
