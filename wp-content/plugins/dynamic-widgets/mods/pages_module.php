<?php
/**
 * Pages Module
 *
 * @version $Id: pages_module.php 488903 2012-01-12 18:17:27Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Page extends DWModule {
		protected static $info;
		public static $option = array( 'page' => 'Pages' );
		protected static $question = 'Show widget default on static pages?';
		protected static $type = 'custom';
		protected static $wpml = TRUE;

		public static function admin() {
			$DW = &$GLOBALS['DW'];

			// Abort function when timeout occurs
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
			register_shutdown_function('dw_abort');

			parent::admin();

			self::$opt = $DW->getDWOpt($_GET['id'], 'page');
			if ( self::$opt->count > 0 ) {
				$opt_page_childs = $DW->getDWOpt($_GET['id'], 'page-childs');
			}

			$pages = get_pages();
			$num_pages = count($pages);

			if ( $num_pages < DW_PAGE_LIMIT ) {
				$pagemap = self::getPageChilds(array(), 0, array());
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
					// $static_page[$id] = __('Posts page', DW_L10N_DOMAIN);
					unset($pages[$id]);
				}
			}

			if ( $num_pages < DW_PAGE_LIMIT ) {
				$childs_infotext = self::infoText();
			} else {
				$childs_infotext = __('Unfortunately the childs-function has been disabled
						because you have more than the limit of pages.', DW_L10N_DOMAIN) . '(' . DW_PAGE_LIMIT . ')';
			}
			self::$info = $childs_infotext;
			self::GUIHeader(self::$option[self::$name], self::$question, self::$info);
			self::GUIOption();

			if ( $num_pages > 0 ) {
				echo '<br />';
				_e('Except the page(s)', DW_L10N_DOMAIN);
				echo '<br />';
				echo '<div id="page-select" class="condition-select" ' . ( (isset($page_condition_select_style)) ? $page_condition_select_style : '' ) . ' />';
				echo '<div style="position:relative;left:-15px">';

				if ( $num_pages < DW_PAGE_LIMIT ) {
					if (! isset($opt_page_childs) ) {
						$childs = array();
					} else {
						$childs = $opt_page_childs->act;
					}
					self::prtPgs($pagemap, self::$opt->act, $childs, $static_page);
				} else {
					self::lsPages($pages, $static_page, self::$opt->act);
				}

				echo '</div></div>';
			}

			self::GUIFooter();
		}

		public static function getPageChilds($arr, $id, $i) {
			$pg = get_pages('child_of=' . $id);
			foreach ($pg as $p ) {
				if (! in_array($p->ID, $i) ) {
					$i[ ] = $p->ID;
					$arr[$p->ID] = array();
					$a = &$arr[$p->ID];
					$a = self::getPageChilds($a, $p->ID, $i);
				}
			}
			return $arr;
		}

		public static function infoText() {
			return __('Checking the "All childs" option, makes the exception rule apply
						to the parent and all items under it in all levels. Also future items
						under the parent. It\'s not possible to apply an exception rule to
						"All childs" without the parent.', DW_L10N_DOMAIN);
		}

		public static function prtPgs($pages, $page_act, $page_childs_act, $static_page) {
			foreach ( $pages as $pid => $childs ) {
				$page = get_page($pid);

				echo '<div style="position:relative;left:15px;width:95%">';
				echo '<input type="checkbox" id="page_act_' . $page->ID . '" name="page_act[]" value="' . $page->ID . '" ' . ( isset($page_act) && count($page_act) > 0 && in_array($page->ID, $page_act) ? 'checked="checked"' : '' ) . ' onchange="chkChild(\'page\', ' . $pid . ')" /> <label for="page_act_' . $page->ID . '">' . $page->post_title . ' ' . ( get_option('show_on_front') == 'page' && isset($static_page[$page->ID]) ? '(' . $static_page[$page->ID] . ')' : '' ) . '</label><br />';

				echo '<div style="position:relative;left:15px;">';
				echo '<input type="checkbox" id="page_childs_act_' . $pid . '" name="page_childs_act[]" value="' . $pid . '" ' . ( isset($page_childs_act) && count($page_childs_act) > 0 && in_array($pid, $page_childs_act) ? 'checked="checked"' : '' ) . ' onchange="chkParent(\'page\', ' . $pid . ')" /> <label for="page_childs_act_' . $pid . '"><em>' . __('All childs', DW_L10N_DOMAIN) . '</em></label><br />';
				echo '</div>';

				if ( count($childs) > 0 ) {
					self::prtPgs($childs, $page_act, $page_childs_act, $static_page);
				}
				echo '</div>';
			}
		}

		public static function lsPages($pages, $static_page, $page_act) {
			echo '<div style="position:relative;left:15px;width:95%">';
			foreach ( $pages as $page ) {
				echo '<input type="checkbox" id="page_act_' . $page->ID . '" name="page_act[]" value="' . $page->ID . '" ' . ( count($page_act) > 0 && in_array($page->ID, $page_act) ? 'checked="checked"' : '' ) . ' /> <label for="page_act_' . $page->ID . '">' . $page->post_title . ' ' . ( get_option('show_on_front') == 'page' && isset($static_page[$page->ID]) ? '(' . $static_page[$page->ID] . ')' : '' ) . '</label><br />';
			}
			echo '</div>';
		}
	}
?>
