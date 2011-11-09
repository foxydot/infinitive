<?php
/**
 * Custom Post Type Module
 *
 * @version $Id: custompost_module.php 423465 2011-08-14 19:24:18Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	/* WordPress 3.0 and higher: Custom Post Types */
	if ( version_compare($GLOBALS['wp_version'], '3.0', '>=') ) {

		function getCPostChilds($type, $arr, $id, $i) {
			$post = get_posts('post_type=' . $type . '&post_parent=' . $id . '&posts_per_page=-1');

			foreach ($post as $p ) {
				if (! in_array($p->ID, $i) ) {
					$i[ ] = $p->ID;
					$arr[$p->ID] = array();
					getCPostChilds($type, &$arr[$p->ID], $p->ID, &$i);
				}
			}
			return $arr;
		}

		function prtCPost($type, $ctid, $posts, $posts_act, $posts_childs_act) {
			$DW = $GLOBALS['DW'];

			foreach ( $posts as $pid => $childs ) {
				$run = TRUE;

				if ( $DW->wpml ) {
					$wpml_id = dw_wpml_get_id($pid, 'post_' . $type);
					if ( $wpml_id > 0 && $wpml_id <> $pid ) {
						$run = FALSE;
					}
				}

				if ( $run ) {
					$post = get_post($pid);

					echo '<div style="position:relative;left:15px;">';
					echo '<input type="checkbox" id="' . $type . '_act_' . $post->ID . '" name="' . $type . '_act[]" value="' . $post->ID . '" ' . ( isset($posts_act) && count($posts_act) > 0 && in_array($post->ID, $posts_act) ? 'checked="checked"' : '' ) . ' onchange="chkCPChild(\'' . $type . '\',' . $pid . ')" /> <label for="' . $type . '_act_' . $post->ID . '">' . $post->post_title . '</label><br />';

					if ( $ctid->hierarchical ) {
						echo '<div style="position:relative;left:15px;">';
						echo '<input type="checkbox" id="' . $type . '_childs_act_' . $pid . '" name="' . $type . '_childs_act[]" value="' . $pid . '" ' . ( isset($posts_childs_act) && count($posts_childs_act) > 0 && in_array($pid, $posts_childs_act) ? 'checked="checked"' : '' ) . ' onchange="chkCPParent(\'' . $type . '\',' . $pid . ')" /> <label for="' . $type . '_childs_act_' . $pid . '"><em>' . __('All childs', DW_L10N_DOMAIN) . '</em></label><br />';
						echo '</div>';
					}

					if ( count($childs) > 0 ) {
						prtCPost($type, $ctid, $childs, $posts_act, $posts_childs_act);
					}
					echo '</div>';
				}
			}
		}

		function getTaxChilds($term, $arr, $id, $i) {
			$tax = get_terms($term, array('hide_empty' => FALSE, 'parent' => $id));
			foreach ($tax as $t ) {
				if (! in_array($t->term_id, $i) && $t->parent == $id ) {
					$i[ ] = $t->term_id;
					$arr[$t->term_id] = array();
					$a = &$arr[$t->term_id];
					$a = getTaxChilds($term, $a, $t->term_id, &$i);
				}
			}
			return $arr;
		}

		function prtTax($tax, $terms, $terms_act, $terms_childs_act, $prefix) {
			foreach ( $terms as $pid => $childs ) {
				$run = TRUE;

				if ( $DW->wpml ) {
					$wpml_id = dw_wpml_get_id($pid, 'tax_' . $tax);
					if ( $wpml_id > 0 && $wpml_id <> $pid ) {
						$run = FALSE;
					}
				}

				if ( $run ) {
					$term = get_term_by('id', $pid, $tax);

					echo '<div style="position:relative;left:15px;">';
					echo '<input type="checkbox" id="' . $prefix . '_act_' . $pid . '" name="' . $prefix . '_act[]" value="' . $pid . '" ' . ( isset($terms_act) && count($terms_act) > 0 && in_array($pid, $terms_act) ? 'checked="checked"' : '' ) . ' onchange="chkChild(\'' . $prefix . '\', ' . $pid . ')" /> <label for="' . $prefix . '_act_' . $pid . '">' . $term->name . '</label><br />';;

					echo '<div style="position:relative;left:15px;">';
					echo '<input type="checkbox" id="' . $prefix . '_childs_act_' . $pid . '" name="' . $prefix . '_childs_act[]" value="' . $pid . '" ' . ( isset($terms_childs_act) && count($terms_childs_act) > 0 && in_array($pid, $terms_childs_act) ? 'checked="checked"' : '' ) . ' onchange="chkParent(\'' . $prefix . '\', ' . $pid . ')" /> <label for="' . $prefix . '_childs_act_' . $pid . '"><em>' . __('All childs', DW_L10N_DOMAIN) . '</em></label><br />';
					echo '</div>';

					if ( count($childs) > 0 ) {
						prtTax($tax, $childs, $terms_act, $terms_childs_act, $prefix);
					}
					echo '</div>';
				}
			}
		}

		$args = array(
		'public'   => TRUE,
		'_builtin' => FALSE
	);

		// Custom Post Type
		$post_types = get_post_types($args, 'objects', 'and');
		foreach ( $post_types as $type => $ctid ) {
			// Prepare
			$opt_custom = $DW->getDWOpt($_GET['id'], $type);

			// -- Childs
			if ( $ctid->hierarchical ) {
				$opt_custom_childs = $DW->getDWOpt($_GET['id'], $type . '-childs');
			}

			$loop = new WP_Query( array('post_type' => $type, 'posts_per_page' => -1) );
			if ( $loop->post_count > DW_LIST_LIMIT ) {
				$custom_condition_select_style = DW_LIST_STYLE;
			}

			$cpmap = getCPostChilds($type, array(), 0, array());
			$tax_list = get_object_taxonomies($type, 'objects');

			// Output
			echo '<h4><b>' . $ctid->label . '</b> ' . ( $opt_custom->count > 0 ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . ( $DW->wpml ? $wpml_icon : '' ) . '</h4>';
			echo '<div class="dynwid_conf">';
			echo __('Show widget on', DW_L10N_DOMAIN) . ' ' . $ctid->label . '? ' . ( ($ctid->hierarchical || count($tax_list) > 0) ? '<img src="' . $DW->plugin_url . 'img/info.gif" alt="info" onclick="divToggle(\'custom_' . $type . '\');" />' : '' ) . '<br />';
			echo '<input type="hidden" name="post_types[]" value="' . $type . '" />';
			$DW->dumpOpt($opt_custom);
			if ( isset($opt_custom_childs) ) {
				$DW->dumpOpt($opt_custom_childs);
			}

			echo '<div>';
			echo '<div id="custom_' . $type . '" class="infotext">';
			echo ( $ctid->hierarchical ? '<p>' . $childs_infotext . '</p>' : '' );
			echo ( (count($tax_list) > 0) ? '<p>' . __('All exceptions (Titles and Taxonomies) work in a logical OR condition. That means when one of the exceptions is met, the exception rule is applied.', DW_L10N_DOMAIN) . '</p>' : '' );
			echo '</div>';
			echo '</div>';

			echo '<input type="radio" name="' . $type . '" value="yes" id="' . $type . '-yes" ' . ( $opt_custom->selectYes() ? $opt_custom->checked : '' ) . ' /> <label for="' . $type . '-yes">' . __('Yes') . '</label> ';
			echo '<input type="radio" name="' . $type . '" value="no" id="' . $type . '-no" ' . ( $opt_custom->selectNo() ? $opt_custom->checked : '' ) . ' /> <label for="' . $type . '-no">' . __('No') . '</label><br />';

			echo __('Except for', DW_L10N_DOMAIN) . ':<br />';
			echo '<div id="' . $type . '-select" class="condition-select" ' . ( (isset($custom_condition_select_style)) ? $custom_condition_select_style : '' ) . '>';

			echo '<div style="position:relative;left:-15px">';

			if ( isset($opt_custom_childs) ) {
				$childs = $opt_custom_childs->act;
			} else {
				$childs = array();
			}
			prtCPost($type, $ctid, $cpmap, $opt_custom->act, $childs);

			echo '</div>';
			echo '</div>';

			// Taxonomy in Custom Post Type
			foreach ( $tax_list as $tax_type ) {
				// Prepare
				$opt_tax = $DW->getDWOpt($_GET['id'], $type . '-tax_' . $tax_type->name);
				if ( $tax_type->hierarchical ) {
					$opt_tax_childs = $DW->getDWOpt($_GET['id'], $type . '-tax_' . $tax_type->name . '-childs');
				}

				$tax = get_terms($tax_type->name, array('get' => 'all'));
				if ( count($tax) > 0 ) {
					if ( count($tax) > DW_LIST_LIMIT ) {
						$tax_condition_select_style = DW_LIST_STYLE;
					}

					$tree = getTaxChilds($tax_type->name, array(), 0, array());

					echo '<br />';
					$DW->dumpOpt($opt_tax);
					if ( isset($opt_tax_childs) ) {
						$DW->dumpOpt($opt_tax_childs);
					}
					echo __('Except for', DW_L10N_DOMAIN) . ' ' . $tax_type->label . ':<br />';
					echo '<div id="' . $type . '-tax_' . $tax_type->name . '-select" class="condition-select" ' . ( (isset($tax_condition_select_style)) ? $tax_condition_select_style : '' ) . '>';
					echo '<div style="position:relative;left:-15px">';
					if (! isset($opt_tax_childs) ) {
						$childs = array();
					} else {
						$childs = $opt_tax_childs->act;
					}
					prtTax($tax_type->name, $tree, $opt_tax->act, $childs, $type . '-tax_' . $tax_type->name);
					echo '</div>';
					echo '</div>';
				}
			}

			echo '</div><!-- end dynwid_conf -->';
		}

		// Custom Taxonomy Archive
		if ( function_exists('is_tax') ) {
			$taxlist = get_taxonomies($args, 'objects', 'and');

			if ( count($taxlist) > 0 ) {
				foreach ( $taxlist as $tax_id => $tax ) {
					$ct = 'tax_' . $tax_id;
					$ct_archive_yes_selected = 'checked="checked"';
					$opt_ct_archive = $DW->getDWOpt($_GET['id'], $ct);
					if ( $tax->hierarchical ) {
						$opt_ct_archive_childs = $DW->getDWOpt($_GET['id'], $ct . '-childs');
					}

					$t = get_terms($tax->name, array('get' => 'all'));
					if ( count($t) > DW_LIST_LIMIT ) {
						$ct_archive_condition_select_style = DW_LIST_STYLE;
					}

					$tree = getTaxChilds($tax->name, array(), 0, array());

					$cpt_label = array();
					foreach ( $tax->object_type as $obj ) {
						$cpt_label[ ] = $post_types[$obj]->label;
					}

					echo '<h4><b>' . $tax->label . '</b> (<em>' . implode(', ', $cpt_label) . '</em>)' . ( ($opt_ct_archive->count > 0) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . '</h4>';
					echo '<div class="dynwid_conf">';
					echo __('Show widget on', DW_L10N_DOMAIN) . ' ' . $tax->label . '?' . ( ($tax->hierarchical || count($t) > 0) ? ' <img src="' . $DW->plugin_url . 'img/info.gif" alt="info" onclick="divToggle(\'custom_' . $ct . '\');" />' : '' ) . '<br />';
					echo '<input type="hidden" name="taxonomy[]" value="' . $tax_id . '" />';
					$DW->dumpOpt($opt_ct_archive);
					if ( isset($opt_ct_archive_childs) ) {
						$DW->dumpOpt($opt_ct_archive_childs);
					}

					echo '<div>';
					echo '<div id="custom_' . $ct . '" class="infotext">';
					echo ( $tax->hierarchical ? '<p>' . $childs_infotext . '</p>' : '' );
					echo ( (count($t) > 0) ? '<p>' . __('All exceptions work in a logical OR condition. That means when one of the exceptions is met, the exception rule is applied.', DW_L10N_DOMAIN) . '</p>' : '' );
					echo '</div>';
					echo '</div>';

					echo '<input type="radio" name="' . $ct . '" value="yes" id="' . $ct . '-yes" ' . ( ($opt_ct_archive->selectYes()) ? $opt_ct_archive->checked : '' ) . ' /> <label for="' . $ct . '-yes">' . __('Yes') . '</label> ';
					echo '<input type="radio" name="' . $ct . '" value="no" id="' . $ct . '-no" ' . ( ($opt_ct_archive->selectNo()) ? $opt_ct_archive->checked : '' ) . ' /> <label for="' . $ct . '-no">' . __('No') . '</label><br />';

					if ( count($t) > 0 ) {
						echo __('Except for', DW_L10N_DOMAIN) . ':<br />';
						echo '<div id="' . $ct . '-select" class="condition-select" ' . ( (isset($ct_archive_condition_select_style)) ? $ct_archive_condition_select_style : '' ) . '>';
						echo '<div style="position:relative;left:-15px">';
						if (! isset($opt_ct_archive_childs) ) {
							$childs = array();
						} else {
							$childs = $opt_ct_archive_childs->act;
						}
						prtTax($tax->name, $tree, $opt_ct_archive->act, $childs, $ct);
						echo '</div>';
						echo '</div>';
					}
					echo '</div><!-- end dynwid_conf -->';
				}
			}
		}

		// Custom Post Type Archives
		if ( function_exists('is_post_type_archive') && count($post_types) > 0 ) {
			$opt_cp_archive = $DW->getDWOpt($_GET['id'], 'cp_archive');

			if ( count($post_types) > DW_LIST_LIMIT ) {
				$cp_archive_condition_select_style = DW_LIST_STYLE;
			}

			echo '<h4><b>' . __('Custom Post Type Archives', DW_L10N_DOMAIN) . '</b> ' . ( ($opt_cp_archive->count > 0)  ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . '</h4>';
			echo '<div class="dynwid_conf">';
			echo __('Show widget on Custom Post Type Archives', DW_L10N_DOMAIN) . '?<br />';
			$DW->dumpOpt($opt_cp_archive);

			echo '<input type="radio" name="cp_archive" value="yes" id="cp_archive-yes" ' . ( ($opt_cp_archive->selectYes()) ? $opt_cp_archive->checked : '' ) . ' /> <label for="cp_archive-yes">' . __('Yes') . '</label> ';
			echo '<input type="radio" name="cp_archive" value="no" id="cp_archive-no" ' . ( ($opt_cp_archive->selectNo()) ? $opt_cp_archive->checked : '' ) . ' /> <label for="cp_archive-no">' . __('No') . '</label><br />';

			echo __('Except for', DW_L10N_DOMAIN) . ':<br />';
			echo '<div id="cp_archive-select" class="condition-select" ' . ( isset($cp_archive_condition_select_style) ? $cp_archive_condition_select_style : '' ) . '>';
			foreach ( $post_types as $type => $ctid ){
				echo '<input type="checkbox" id="cp_archive_act_' . $type . '" name="cp_archive_act[]" value="' . $type . '"' . ( ($opt_cp_archive->count > 0 && in_array($type, $opt_cp_archive->act)) ? 'checked="checked"' : '' ) . ' /> <label for="cp_archive_act_' . $type . '">' . $ctid->label . '</label><br />';
			}
			echo '</div>';
			echo '</div><!-- end dynwid_conf -->';
		}

	} // end version compare >= WP 3.0
?>

