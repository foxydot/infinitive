<?php
/**
 * Category Module
 *
 * @version $Id: category_module.php 402236 2011-06-28 20:46:55Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	$opt_category = $DW->getDWOpt($_GET['id'], 'category');
	$category = get_categories( array('hide_empty' => FALSE) );
	if ( count($category) > DW_LIST_LIMIT ) {
		$category_condition_select_style = DW_LIST_STYLE;
	}
	$catmap = getCatChilds(array(), 0, array());
?>

<h4><b><?php _e('Category Pages', DW_L10N_DOMAIN); ?></b> <?php echo ( ($opt_category->count > 0) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . ( ($DW->wpml) ? $wpml_icon : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on category pages?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_category); ?>
<input type="radio" name="category" value="yes" id="category-yes" <?php echo ( $opt_category->selectYes() ) ? $opt_category->checked : ''; ?> /> <label for="category-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="category" value="no" id="category-no" <?php echo ( $opt_category->selectNo() ) ? $opt_category->checked : ''; ?> /> <label for="category-no"><?php _e('No'); ?></label><br />
<?php _e('Except the categories', DW_L10N_DOMAIN); ?>:<br />
<div id="category-select" class="condition-select" <?php echo ( isset($category_condition_select_style) ) ? $category_condition_select_style : ''; ?>>
<?php prtCat($catmap, $opt_category->act); ?>
</div>
</div><!-- end dynwid_conf -->
