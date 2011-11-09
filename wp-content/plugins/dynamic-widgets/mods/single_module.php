<?php
/**
 * Single Post Module
 *
 * @version $Id: single_module.php 418888 2011-08-03 18:36:48Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	$opt_single = $DW->getDWOpt($_GET['id'], 'single');

	// -- Author
	if ( function_exists('get_users') ) {
		$authors = get_users( array('who' => 'author') );
	} else {
		$authors = dwGetAuthors();
	}

	if ( count($authors) > DW_LIST_LIMIT ) {
    $author_condition_select_style = DW_LIST_STYLE;
  }

	$js_count = 0;
	$opt_single_author = $DW->getDWOpt($_GET['id'], 'single-author');
	$js_author_array = array();
	if ( $opt_single_author->count > 0 ) {
		$js_count = $js_count + $opt_single_author->count - 1;
	}

	// -- Category
	$category = get_categories( array('hide_empty' => FALSE) );
	if ( count($category) > DW_LIST_LIMIT ) {
    $category_condition_select_style = DW_LIST_STYLE;
  }

	// For JS
	$js_category_array = array();
	foreach ( $category as $cat ) {
		$js_category_array[ ] = '\'single_category_act_' . $cat->cat_ID . '\'';
	}

	$catmap = getCatChilds(array(), 0, array());

	$opt_single_category = $DW->getDWOpt($_GET['id'], 'single-category');
	if ( $opt_single_category->count > 0 ) {
		$js_count = $js_count + $opt_single_category->count - 1;
	}

	// -- Individual / Posts / Tags
	$opt_individual = $DW->getDWOpt($_GET['id'], 'individual');
	if ( $opt_individual->count > 0 ) {
			$individual = TRUE;
			$count_individual = '(' . __('Posts: ', DW_L10N_DOMAIN) . $opt_single_post->count . ', ' . __('Tags: ', DW_L10N_DOMAIN) . $opt_single_tag->count . ')';
	}
	$opt_single_post = $DW->getDWOpt($_GET['id'], 'single-post');
	$opt_single_tag = $DW->getDWOpt($_GET['id'], 'single-tag');
?>

<h4><b><?php _e('Single Posts', DW_L10N_DOMAIN); ?></b><?php echo ( $opt_single->count > 0 || $opt_single_author->count > 0 || $opt_single_category->count > 0 || $opt_single_post->count > 0 || $opt_single_tag->count > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on single posts?', DW_L10N_DOMAIN) ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="<?php _e('Click to toggle info', DW_L10N_DOMAIN) ?>" onclick="divToggle('single')" /><br />
<?php $DW->dumpOpt($opt_single); ?>
<div>
	<div id="single" class="infotext">
  <?php _e('When you use an author <b>AND</b> a category exception, both rules in the condition must be met. Otherwise the exception rule won\'t be applied.
  					If you want to use the rules in a logical OR condition. Add the same widget again and apply the other rule to that.
  					', DW_L10N_DOMAIN); ?>
	</div>
</div>
<input type="radio" name="single" value="yes" id="single-yes" <?php echo ( $opt_single->selectYes() ) ? $opt_single->checked : ''; ?> /> <label for="single-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="single" value="no" id="single-no" <?php echo ( $opt_single->selectNo() ) ? $opt_single->checked : ''; ?> /> <label for="single-no"><?php _e('No'); ?></label><br />
<?php $DW->dumpOpt($opt_individual); ?>
<input type="checkbox" id="individual" name="individual" value="1" <?php echo ( $individual ) ? 'checked="checked"' : ''; ?> onclick="chkInPosts()" />
<label for="individual"><?php _e('Make exception rule available to individual posts and tags.', DW_L10N_DOMAIN) ?> <?php echo ( $opt_individual->count > 0 ) ? $count_individual : ''; ?></label>
<img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="<?php _e('Click to toggle info', DW_L10N_DOMAIN) ?>" onclick="divToggle('individual_post_tag')" />
<div>
	<div id="individual_post_tag" class="infotext">
  <?php _e('When you enable this option, you have the ability to apply the exception rule for <em>Single Posts</em> to tags and individual posts.
						You can set the exception rule for tags in the single Edit Tag Panel (go to <a href="edit-tags.php?taxonomy=post_tag">Post Tags</a>,
						click a tag), For individual posts in the <a href="post-new.php">New</a> or <a href="edit.php">Edit</a> Posts panel.
						Exception rules for tags and individual posts in any combination work independantly, but will always be counted as one exception.<br />
  					Please note when exception rules are set for Author and/or Category, these will be removed.
  				', DW_L10N_DOMAIN); ?>
	</div>
</div>
<?php foreach ( $opt_single_post->act as $singlepost ) { ?>
<input type="hidden" name="single_post_act[]" value="<?php echo $singlepost; ?>" />
<?php } ?>
<?php foreach ( $opt_single_tag->act as $tag ) { ?>
<input type="hidden" name="single_tag_act[]" value="<?php echo $tag; ?>" />
<?php } ?>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td valign="top">
    <?php _e('Except the posts by author', DW_L10N_DOMAIN); ?>:
    <?php $DW->dumpOpt($opt_single_author); ?>
    <div id="single-author-select" class="condition-select" <?php echo ( isset($author_condition_select_style) ) ? $author_condition_select_style : ''; ?>>
    <?php foreach ( $authors as $author ) { ?>
    <?php $js_author_array[ ] = '\'single_author_act_' . $author->ID . '\''; ?>
    <input type="checkbox" id="single_author_act_<?php echo $author->ID; ?>" name="single_author_act[]" value="<?php echo $author->ID; ?>" <?php echo ( $opt_single_author->count > 0 && in_array($author->ID,$opt_single_author->act) ) ? 'checked="checked"' : '';  ?> onclick="ci('single_author_act_<?php echo $author->ID; ?>')" /> <label for="single_author_act_<?php echo $author->ID; ?>"><?php echo $author->display_name; ?></label><br />
    <?php } ?>
    </div>
  </td>
  <td style="width:10px"></td>
  <td valign="top">
    <?php _e('Except the posts in category', DW_L10N_DOMAIN); ?>: <?php echo ( $DW->wpml ) ? $wpml_icon : ''; ?>
    <?php $DW->dumpOpt($opt_single_category); ?>
    <div id="single-category-select" class="condition-select" <?php echo ( isset($category_condition_select_style) ) ? $category_condition_select_style : ''; ?>>
		<?php prtCat($catmap, $opt_single_category->act, TRUE); ?>
    </div>
  </td>
</tr>
</table>
</div><!-- end dynwid_conf -->
