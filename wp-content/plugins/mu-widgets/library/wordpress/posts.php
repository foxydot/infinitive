<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv45v_posts extends bv45v_base {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function post_tree($post_id) {
		$tree = new stdClass ();
		$tree->ID = $post_id;
		$tree->posts = $this->_post_tree_posts ( $post_id );
		$tree->postmeta = $this->_post_tree_postmeta ( $post_id );
		$tree->comments = $this->_post_tree_comments ( $post_id );
		return $tree;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function _post_tree_posts($parent) {
		$tree = array ();
		$sql = "SELECT `ID` from `%s` WHERE `post_parent` = %d;";
		$sql = sprintf ( $sql, $this->table ( 'posts' )->name (), $parent );
		$data = $this->table ()->execute ( $sql );
		foreach ( $data as $datum ) {
			$tree [] = $this->post_tree ( $datum ['ID'] );
		}
		return $tree;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function _post_tree_comments($post_id) {
		$tree = array ();
		$sql = "SELECT `comment_ID` from `%s` WHERE `comment_post_ID` = %d;";
		$sql = sprintf ( $sql, $this->table ( 'comments' )->name (), $post_id );
		$data = $this->table ()->execute ( $sql );
		foreach ( $data as $datum ) {
			$leaf = new stdClass ();
			$leaf->ID = $datum ['comment_ID'];
			$leaf->commentmeta = $this->_post_tree_commentmeta ( $datum ['comment_ID'] );
			$tree [] = $leaf;
		}
		return $tree;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function _post_tree_commentmeta($comment_id) {
		$tree = array ();
		$sql = "SELECT `meta_id` from `%s` WHERE `comment_id` = %d;";
		$sql = sprintf ( $sql, $this->table ( 'commentmeta' )->name (), $comment_id );
		$data = $this->table ()->execute ( $sql );
		foreach ( $data as $datum ) {
			$tree [] = $datum ['meta_id'];
		}
		return $tree;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function _post_tree_postmeta($post_id) {
		$tree = array ();
		$sql = "SELECT `meta_id` from `%s` WHERE `post_id` = %d;";
		$sql = sprintf ( $sql, $this->table ( 'postmeta' )->name (), $post_id );
		$data = $this->table ()->execute ( $sql );
		foreach ( $data as $datum ) {
			$tree [] = $datum ['meta_id'];
		}
		return $tree;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function child_ids($post_id) {
		$sql = "SELECT `ID` FROM `%s` WHERE `post_parent` = %d;";
		$sql = sprintf ( $sql, $this->table ( 'posts' )->name (), $post_id );
		$ids = $this->table ()->execute ( $sql );
		$ids = $this->table ()->first_column ( $ids );
		return $ids;
	
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function meta_ids($post_id) {
		$sql = "SELECT `meta_id` FROM `%s` WHERE `post_id` = %d;";
		$sql = sprintf ( $sql, $this->table ( 'postmeta' )->name (), $post_id );
		$ids = $this->table ()->execute ( $sql );
		$ids = $this->table ()->first_column ( $ids );
		return $ids;
	
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function get_by_title($title,$type='post') {
		$sql = "SELECT ID FROM `%s` WHERE `post_title` = '%s' AND post_type='%s'";
		$sql = sprintf ( $sql, $this->table ( 'posts' )->name (), $title,$type );
		$ids = $this->table ()->execute ( $sql );
		$return = $this->table()->first_row($this->table ()->first_column ( $ids ));
		if ($return!==false)
		{
			$return = get_post_to_edit ( $return );
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function comment_ids($post_id, $comment_parent = 0) {
		$sql = "SELECT `comment_ID` FROM `%s` WHERE `comment_post_ID` = %d AND `comment_parent`=%d;";
		$sql = sprintf ( $sql, $this->table ( 'comments' )->name (), $post_id, $comment_parent );
		$ids = $this->table ()->execute ( $sql );
		$ids = $this->table ()->first_column ( $ids );
		return $ids;
	
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	function get_post_meta_by_id($meta_id) {
		$sql = "SELECT * FROM `%s` WHERE `meta_id` = %d";
		$sql = sprintf ( $sql, $this->table ( 'postmeta' )->name (), $meta_id );
		$data = $this->table ()->execute ( $sql );
		$data = $this->table ()->first_row ( $data );
		if ($data === false) {
			return false;
		}
		if (is_serialized_string ( $data ['meta_value'] ))
			$data ['meta_value'] = maybe_unserialize ( $data ['meta_value'] );
		return $data;
	}

}