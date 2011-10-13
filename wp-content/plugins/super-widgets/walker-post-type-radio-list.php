<?php

// Walker class used by Super Widgets
class Walker_Post_Type_Radio_List extends Walker {
	var $tree_type = array('post_type', 'post', 'page', 'custom');
	var $db_fields = array('parent' => 'post_parent', 'id' => 'ID');
	var $object_to_use;
	var $title_var;

	function start_lvl(&$output, $depth, $args) {
		$output .= "\n<ul>\n";
	}

	function end_lvl(&$output, $depth, $args) {
		$output .= "\n</ul>\n";
	}

	function start_el(&$output, $object, $depth, $args) {
		$title = $object->{$this->title_var};
		$id = $object->{$this->db_fields['id']};
		$name = $args['control_name'];
		$object_to_use = $args['instance'][$this->object_to_use];
		$before = $after = '';
		if ($depth == 0) {
			$before = '<strong>';
			$after = '</strong>';
		}
		$output .= "<li><label><input name='$name' type='radio' value='$id'".checked($id, $object_to_use, false)." />{$before}{$title}{$after}</label>";
	}

	function end_el(&$output, $object, $depth, $args) {
		$output .= '</li>';
	}
}

?>