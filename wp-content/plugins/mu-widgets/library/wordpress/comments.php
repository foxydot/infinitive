<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv46v_comments extends bv46v_base {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function child_ids($comment_id) {
		$sql = "SELECT `comment_ID` FROM `%s` WHERE `comment_parent` = %d;";
		$sql = sprintf ( $sql, $this->table ( 'comments' )->name (), $comment_id );
		$ids = $this->table ()->execute ( $sql );
		$ids = $this->table ()->first_column ( $ids );
		return $ids;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function meta_ids($comment_id) {
		$sql = "SELECT `meta_id` FROM `%s` WHERE `comment_id` = %d;";
		$sql = sprintf ( $sql, $this->table ( 'commentmeta' )->name (), $comment_id );
		$ids = $this->table ()->execute ( $sql );
		$ids = $this->table ()->first_column ( $ids );
		return $ids;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	function get_comment_meta_by_id($meta_id) {
		$sql = "SELECT * FROM `%s` WHERE `meta_id` = %d";
		$sql = sprintf ( $sql, $this->table ( 'commentmeta' )->name (), $meta_id );
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