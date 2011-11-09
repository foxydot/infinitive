<?php

/**
 * dynwid_init_worker.php
 *
 * @version $Id: dynwid_init_worker.php 423465 2011-08-14 19:24:18Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	$DW->message('Dynamic Widgets INIT');
	echo "\n" . '<!-- Dynamic Widgets v' . DW_VERSION . ' //-->' . "\n";

	// UserAgent detection
	$DW->message('UserAgent: ' . $DW->useragent);

	// WPML Plugin Support
	if ( defined('ICL_PLUGIN_PATH') ) {
		$wpml_api = ICL_PLUGIN_PATH . DW_WPML_API;

		if ( file_exists($wpml_api) ) {
			require_once($wpml_api);

			$wpmlang = wpml_get_default_language();
			$curlang = wpml_get_current_language();
			$DW->message('WPML language: ' . $curlang);

			if ( $wpmlang != $curlang ) {
				$DW->wpml = TRUE;
				$DW->message('WPML enabled');
				require_once(DW_PLUGIN . 'wpml.php');
			}
		}
	}

	// QT Plugin Support
	if ( defined('QTRANS_INIT') ) {
		$qtlang = get_option('qtranslate_default_language');
		$curlang = qtrans_getLanguage();
		$DW->message('QT language: ' . $curlang);

		if ( $qtlang != $curlang ) {
			$DW->qt = TRUE;
			$DW->message('QT enabled');
		}
	}

	$DW->message('User has role(s): ' . implode(', ', $DW->userrole));

//	$custom_post_type = FALSE;
	$DW->whereami = $DW->detectPage();
	$DW->message('Page is ' . $DW->whereami);

	if ( $DW->whereami == 'single' ) {
		$post = $GLOBALS['post'];
		$DW->message('post_id = ' . $post->ID);

		/* WordPress 3.0 and higher: Custom Post Types */
		if ( version_compare($GLOBALS['wp_version'], '3.0', '>=') ) {
			$post_type = get_post_type($post);
			$DW->message('Post Type = ' . $post_type);
			if ( $post_type != 'post' ) {
				$DW->custom_post_type = TRUE;
				$DW->whereami = $post_type;
				$DW->message('Custom Post Type detected, page changed to ' . $DW->whereami);
			}
		}
	}

	if ( $DW->whereami == 'page' ) {
		// WPSC/WPEC Plugin Support
		if ( defined('WPSC_VERSION') && version_compare(WPSC_VERSION, '3.8', '<') ) {
			$wpsc_query = &$GLOBALS['wpsc_query'];

			if ( $wpsc_query->category > 0 ) {
				$DW->wpsc = TRUE;
				$DW->whereami = 'wpsc';
				$DW->message('WPSC detected, page changed to ' . $DW->whereami . ', category: ' . $wpsc_query->category);

				require_once(DW_PLUGIN . 'wpsc.php');
			}
		} else if ( defined('BP_VERSION') ) {	// BuddyPress Plugin Support -- else if needed, otherwise WPEC pages are detected as BP
			require_once(DW_PLUGIN . 'bp.php');
			$bp = &$GLOBALS['bp'];

			/*
			   Array of BP components needed as a workaround for certain themes claiming an invalid BP component,
			   confusing DW by detecting BP, when it should be Page.
			*/
			$components = dw_get_bp_components();
			$bp_components = array_keys($components);

			if (! empty($bp->current_component) && in_array($bp->current_component, $bp_components) ) {
				if ( $bp->current_component == 'groups' && ! empty($bp->current_item) ) {
					$DW->bp_groups = TRUE;
					$DW->whereami = 'bp-group';
					$DW->message('BP detected, component: ' . $bp->current_component . '; Group: ' . $bp->current_item . ', Page changed to bp-group');
				} else {
					$DW->bp = TRUE;
					$DW->whereami = 'bp';
					$DW->message('BP detected, component: ' . $bp->current_component . ', Page changed to bp');
				}
			}
		}
	}

	if ( $DW->whereami == 'tax_archive' ) {
		$wp_query =  $GLOBALS['wp_query'];
		$taxonomy = $wp_query->get('taxonomy');

		$DW->custom_taxonomy = TRUE;
		$DW->whereami = 'tax_' . $taxonomy;
		$DW->message('Page changed to tax_'. $taxonomy. ' (term: ' . $wp_query->get_queried_object_id() . ')');
	}

	$DW->dwList($DW->whereami);
?>